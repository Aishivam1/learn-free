<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure only logged-in users can access this controller
    }

    // ✅ Serve Video Files
    public function streamVideo($id)
    {
        $material = Material::where('id', $id)->where('type', 'video')->firstOrFail();

        if (!Storage::exists('public/' . $material->file_path)) {
            abort(404, 'Video not found.');
        }

        return response()->file(Storage::path('public/' . $material->file_path), [
            'Content-Type' => 'video/mp4'
        ]);
    }

    // ✅ Serve PDFs
    public function viewPdf($id)
    {
        $material = Material::where('id', $id)->where('type', 'pdf')->firstOrFail();

        if (!Storage::exists('public/' . $material->file_path)) {
            abort(404, 'PDF not found.');
        }

        return response()->file(Storage::path('public/' . $material->file_path), [
            'Content-Type' => 'application/pdf'
        ]);
    }
}
