<?php

namespace App\Services;

class UPIPaymentService
{
    private $upiId;
    private $name;
    
    public function __construct()
    {
        // Using personal UPI ID
        $this->upiId = config('services.upi.personal_id');
        $this->name = config('services.upi.name');
    }
    
    public function generateUPIString($amount, $transactionId, $description)
    {
        // Basic UPI parameters for personal payments
        $upiData = [
            'pa' => $this->upiId,          // Your personal UPI ID
            'pn' => $this->name,           // Your name
            'tn' => $description,          // Transaction note
            'am' => $amount,               // Amount
            'cu' => 'INR',                // Currency (Indian Rupee)
            'tr' => $transactionId,        // Transaction reference
        ];
        
        return 'upi://pay?' . http_build_query($upiData);
    }
}