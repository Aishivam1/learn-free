<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\UPIPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PaymentController extends Controller
{
    protected $defaultPrice = 1;
    protected $upiPaymentService;
    protected $validBankHandles = [
        'okaxis', 'okhdfcbank', 'okicici', 'oksbi', 'ybl', 'paytm', 'upi'
    ];

    public function __construct(UPIPaymentService $upiPaymentService)
    {
        $this->middleware('auth');
        $this->upiPaymentService = $upiPaymentService;
    }

    // Keep existing showPayment method as is...
    public function showPayment(Course $course)
    {
        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.my')
                ->with('warning', 'You are already enrolled in this course.');
        }

        // Generate payment reference
        $paymentReference = Str::uuid();

        // Store payment data in cache
        $paymentData = [
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'amount' => $this->defaultPrice,
            'created_at' => now(),
            'expires_at' => now()->addHours(24)
        ];

        Cache::put(
            "payment_" . Auth::id() . "_" . $paymentReference,
            $paymentData,
            now()->addHours(24)
        );

        // Generate UPI payment string
        $upiString = $this->upiPaymentService->generateUPIString(
            $this->defaultPrice,
            $paymentReference,
            "Course: {$course->title}"
        );

        // Generate QR code
        $qrCode = QrCode::size(300)->generate($upiString);

        // Add current timestamp for display
        $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
        $expiryTime = Carbon::now('UTC')->addHours(24)->format('Y-m-d H:i:s');

        return view('learning.payment', compact(
            'course',
            'qrCode',
            'upiString',
            'paymentReference',
            'currentTime',
            'expiryTime',
            'paymentData'
        ));
    }
    public function handleCallback(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'ref' => 'required|string|uuid',
            'transaction_id' => [
                'required',
                'string',
                'min:8',
                'max:30',
                'regex:/^[A-Za-z0-9]+$/',
                function ($attribute, $value, $fail) {
                    if (Cache::has('transaction_' . $value)) {
                        $fail('This transaction ID has already been used.');
                    }
                },
            ],
            'payment_screenshot' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
                function ($attribute, $file, $fail) {
                    if ($file) {
                        $image = getimagesize($file);
                        if (!$image) {
                            $fail('Invalid image file.');
                            return;
                        }
                        if ($image[0] < 300 || $image[1] < 300) {
                            $fail('Image is too small. Minimum dimensions are 300x300 pixels.');
                        }
                    }
                },
            ],
            'upi_id' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+$/',
                function ($attribute, $value, $fail) {
                    $bankHandle = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($bankHandle), $this->validBankHandles)) {
                        $fail('Invalid UPI bank handle. Valid handles: ' . implode(', ', $this->validBankHandles));
                    }
                },
            ],
            'current_time' => [
                'required',
                'date_format:Y-m-d H:i:s',
                function ($attribute, $value, $fail) {
                    $submittedTime = Carbon::parse($value);
                    $now = Carbon::now();
                    if ($submittedTime->diffInMinutes($now) > 15) {
                        $fail('The submitted time is not within the valid range.');
                    }
                },
            ],
        ]);

        $reference = $validatedData['ref'];
        $userId = Auth::id();
        $username = Auth::user()->username;
        $transactionId = $validatedData['transaction_id'];
        $submittedUpiId = $validatedData['upi_id'];

        // Get payment data from cache
        $cacheKey = "payment_{$userId}_{$reference}";
        $paymentData = Cache::get($cacheKey);

        if (!$paymentData) {
            return redirect()->route('courses.index')
                ->with('error', 'Invalid or expired payment reference.');
        }

        try {
            // Extract and verify screenshot data
            $screenshot = $request->file('payment_screenshot');
            $extractedData = $this->extractTextFromScreenshot($screenshot);

            Log::info('Extracted Payment Data:', [
                'raw_data' => $extractedData,
                'user' => $username,
                'transaction' => $transactionId
            ]);

            // Verify the extracted data
            $verificationResult = $this->verifyExtractedData(
                $extractedData,
                $transactionId,
                $submittedUpiId,
                Carbon::parse($validatedData['current_time']),
                $paymentData['amount'],
                $username
            );

            if (!$verificationResult['success']) {
                return redirect()->back()
                    ->with('error', $verificationResult['message']);
            }

            // Store verification data
            $verificationKey = "verification_{$transactionId}";
            $verificationData = [
                'user_id' => $userId,
                'username' => $username,
                'course_id' => $paymentData['course_id'],
                'amount' => $paymentData['amount'],
                'transaction_id' => $transactionId,
                'upi_id' => $submittedUpiId,
                'payment_time' => $validatedData['current_time'],
                'verification_time' => now()->format('Y-m-d H:i:s'),
                'ocr_data' => $extractedData
            ];

            // Cache verification data and transaction ID
            Cache::put($verificationKey, $verificationData, now()->addDays(30));
            Cache::put('transaction_' . $transactionId, true, now()->addDays(30));

            // Create enrollment
            $enrollment = new Enrollment([
                'user_id' => $userId,
                'course_id' => $paymentData['course_id'],
                'progress' => 0,
            ]);
            $enrollment->save();

            // Clear payment cache
            Cache::forget($cacheKey);

            return redirect()->route('courses.my')
                ->with('success', 'Payment verified and enrolled successfully!');

        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage(), [
                'reference' => $reference,
                'user_id' => $userId,
                'transaction_id' => $transactionId,
                'extracted_data' => $extractedData ?? null
            ]);

            return redirect()->back()
                ->with('error', 'Payment verification failed. Please try again or contact support.');
        }
    }

    private function extractTextFromScreenshot($file)
    {
        try {
            // Run OCR on the image
            $text = (new TesseractOCR($file->path()))
                ->lang('eng')
                ->run();

            Log::info('Raw OCR Text:', ['text' => $text]);

            return $this->parsePaymentText($text);
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function parsePaymentText($text)
    {
        $data = [
            'transaction_id' => null,
            'upi_id' => null,
            'amount' => null,
            'timestamp' => null,
            'username' => null
        ];

        // Convert text to lowercase for matching
        $text = strtolower($text);

        // Look for Transaction ID
        if (preg_match('/(?:transaction|txn|ref|id|utr)(?:\s+)?(?:id|number|#)?[:.]?\s*([A-Za-z0-9]{8,})/', $text, $matches)) {
            $data['transaction_id'] = $matches[1];
        }

        // Look for UPI ID
        if (preg_match('/([a-zA-Z0-9._-]+@[a-zA-Z0-9-]+)/', $text, $matches)) {
            $data['upi_id'] = $matches[1];
        }

        // Look for amount
        if (preg_match('/(?:rs|₹|inr)?\.?\s*(\d+(?:\.\d{2})?)/', $text, $matches)) {
            $data['amount'] = floatval($matches[1]);
        }

        // Look for username (Aishivam1 format)
        if (preg_match('/([a-zA-Z0-9]+)@/', $text, $matches)) {
            $data['username'] = $matches[1];
        }

        // Look for UTC timestamp (2025-04-11 14:17:20 format)
        if (preg_match('/(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $text, $matches)) {
            $data['timestamp'] = $matches[1];
        }

        Log::info('Parsed Payment Data:', $data);

        return $data;
    }

    private function verifyExtractedData($extractedData, $submittedTransactionId, $submittedUpiId, $submittedTime, $expectedAmount, $username)
    {
        Log::info('Verifying Payment Data:', [
            'extracted' => $extractedData,
            'submitted' => [
                'transaction_id' => $submittedTransactionId,
                'upi_id' => $submittedUpiId,
                'time' => $submittedTime,
                'amount' => $expectedAmount,
                'username' => $username
            ]
        ]);

        // Verify Transaction ID
        if (!$extractedData['transaction_id'] || 
            strtolower($extractedData['transaction_id']) !== strtolower($submittedTransactionId)) {
            return [
                'success' => false,
                'message' => 'Transaction ID in the screenshot does not match.'
            ];
        }

        // Verify UPI ID
        if (!$extractedData['upi_id'] || 
            strtolower($extractedData['upi_id']) !== strtolower($submittedUpiId)) {
            return [
                'success' => false,
                'message' => 'UPI ID in the screenshot does not match.'
            ];
        }

        // Verify Amount
        if (!$extractedData['amount'] || $extractedData['amount'] != $expectedAmount) {
            return [
                'success' => false,
                'message' => 'Payment amount does not match.'
            ];
        }

        // Verify Username if present
        if ($extractedData['username'] && 
            strtolower($extractedData['username']) !== strtolower($username)) {
            return [
                'success' => false,
                'message' => 'Username in the screenshot does not match.'
            ];
        }

        // Verify Timestamp
        if ($extractedData['timestamp']) {
            $screenshotTime = Carbon::parse($extractedData['timestamp']);
            $timeDiff = abs($screenshotTime->diffInMinutes($submittedTime));
            
            if ($timeDiff > 15) {
                return [
                    'success' => false,
                    'message' => 'Payment timestamp is not within the valid range.'
                ];
            }
        }

        return ['success' => true];
    }
}