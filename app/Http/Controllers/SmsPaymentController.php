<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SmsVerification;
use App\Services\SmsPaymentService;
use Carbon\Carbon;

class SmsPaymentController extends Controller
{
    protected $smsService;

    public function __construct(SmsPaymentService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Spracuje prijatú SMS - SPRÁVNY FORMÁT PRE PLATBAMOBILOM.SK
     */
    public function receiveSms(Request $request)
    {
        try {
            $msisdn = $request->input('msisdn');
            $text = $request->input('text');
            $smsId = $request->input('id');

            $result = $this->smsService->processSmsReceived($msisdn, $text, $smsId);

            // SPRÁVNY FORMÁT: text/plain s dvoma riadkami
            // Prvý riadok: cena
            // Druhý riadok: text SMS správy
            $price = number_format($result['price'], 1, '.', '');
            $message = $result['message'];
            
            // Odstránime diakritiku a skrátime na 160 znakov
            $message = $this->removeDiacritics($message);
            $message = mb_substr($message, 0, 160, 'UTF-8');
            
            return response($price . "\n" . $message, 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);

        } catch (\Exception $e) {
            Log::error('SMS receive error: ' . $e->getMessage());
            
            // V prípade chyby vrátime cenu 0 a chybovú správu
            return response("0\nSluzba je docasne nedostupna. Skuste to neskor.", 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }
    }

    /**
     * Potvrdí platbu - SPRÁVNY FORMÁT PRE PLATBAMOBILOM.SK
     */
    public function confirmPayment(Request $request)
    {
        try {
            $smsId = $request->input('id');
            $result = $request->input('res');

            $response = $this->smsService->confirmPayment($smsId, $result);

            // SPRÁVNY FORMÁT: text/plain s "OK"
            return response('OK', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);

        } catch (\Exception $e) {
            Log::error('SMS confirm payment error: ' . $e->getMessage());
            return response('OK', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }
    }

    /**
     * Overí kód a aktivuje predplatné
     */
    public function verifyCode(Request $request)
    {
        try {
            $code = $request->input('code');
            
            if (!$code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chýba verifikačný kód'
                ], 400);
            }

            $result = $this->smsService->verifyCode($code);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('SMS verify code error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri overovaní kódu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simuluje SMS pre testovanie
     */
    public function simulateSms(Request $request)
    {
        try {
            $packageCode = $request->input('package_code', 'FXO');
            $msisdn = $request->input('msisdn', '421905123456');
            $smsId = $request->input('sms_id', 'TEST-' . time());

            // Simulujeme SMS s balíčkom
            $result = $this->smsService->processSmsReceived($msisdn, "ERO {$packageCode}", $smsId);

            return response()->json([
                'success' => true,
                'simulated_sms' => [
                    'msisdn' => $msisdn,
                    'text' => "ERO {$packageCode}",
                    'sms_id' => $smsId
                ],
                'response' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Odstráni diakritiku zo slovenského textu
     */
    private function removeDiacritics($text)
    {
        $diacritics = [
            'á' => 'a', 'ä' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'í' => 'i',
            'ĺ' => 'l', 'ľ' => 'l', 'ň' => 'n', 'ó' => 'o', 'ô' => 'o', 'ŕ' => 'r',
            'š' => 's', 'ť' => 't', 'ú' => 'u', 'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Ä' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E', 'Í' => 'I',
            'Ĺ' => 'L', 'Ľ' => 'L', 'Ň' => 'N', 'Ó' => 'O', 'Ô' => 'O', 'Ŕ' => 'R',
            'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U', 'Ý' => 'Y', 'Ž' => 'Z'
        ];

        return strtr($text, $diacritics);
    }
} 