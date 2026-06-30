<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SmsPaymentController;

class DebugController extends Controller
{
    public function testSms()
    {
        return view('debug.test-sms');
    }
    
    public function testSmsSubmit(Request $request)
    {
        try {
            $controller = app(SmsPaymentController::class);
            $response = $controller->verifyCode($request);
            
            return response()->json([
                'debug' => true,
                'user_authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'N/A',
                'original_response' => $response->getData(),
                'original_status' => $response->getStatusCode()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'debug_error' => true,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_authenticated' => auth()->check(),
                'user_id' => auth()->id()
            ], 500);
        }
    }
} 