<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Mail\ContactFormMail;
use App\Helpers\AdminNotificationHelper;
use App\Models\EmailLog;
use App\Services\NotificationService;

class ContactController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        Log::info('Contact form submission started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        // ANTI-SPAM VALIDATION
        $spamCheck = $this->checkForSpam($request);
        if ($spamCheck !== true) {
            Log::warning('Contact form spam detected', [
                'ip' => $request->ip(),
                'spam_reason' => $spamCheck
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $spamCheck]);
            }
            return back()->withErrors(['spam' => $spamCheck])->withInput();
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'terms' => 'required|accepted',
                'math_answer' => 'required|integer',
                'math_question' => 'required|string'
            ]);

            Log::info('Contact form validation passed', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Contact form validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Formulár obsahuje chyby. Skontrolujte všetky povinné polia.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            // Odoslanie emailu na admin adresy pomocou AdminNotificationHelper
            $this->notificationService->sendContactFormEmail($validated);
            
            Log::info('Contact form email sent via AdminNotificationHelper', [
                'from' => $validated['email'],
                'subject' => $validated['subject']
            ]);

            // Rate limiting - zvýš počet pokusov
            Cache::put('contact_form_rate_limit:' . $request->ip(), 
                       Cache::get('contact_form_rate_limit:' . $request->ip(), 0) + 1, 
                       600); // 10 minút

        } catch (\Exception $e) {
            Log::error('Failed to send contact form email', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'validated_data' => $validated
            ]);
            
            // Log failed email attempt
            $adminEmails = AdminNotificationHelper::getAdminEmails();
            EmailLog::logFailedEmail(
                implode(', ', $adminEmails),
                'Kontaktný formulár - ' . $validated['subject'],
                $e->getMessage(),
                'contact',
                ['form_data' => $validated]
            );
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nastala chyba pri odosielaní emailu. Skúste to prosím znovu alebo nás kontaktujte priamo.'
                ], 500);
            }
            
            return back()->withErrors(['email' => 'Nastala chyba pri odosielaní správy. Skúste to prosím znovu.'])->withInput();
        }
        
        // Ak je to AJAX požiadavka, vráť JSON odpoveď
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ďakujeme za vašu správu!',
                'details' => 'Vaša správa bola úspešne odoslaná. Odpovieme vám čo najskôr.'
            ]);
        }
        
        return back()->with('success', 'Ďakujeme za vašu správu. Budeme vás kontaktovať čo najskôr.');
    }

    /**
     * Anti-spam kontrola
     */
    private function checkForSpam(Request $request)
    {
        $ip = $request->ip();
        
        // 1. HONEYPOT CHECK
        if ($request->filled('website') || $request->filled('url') || $request->filled('homepage')) {
            Log::warning('Spam detected - Honeypot triggered', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'honeypot_field' => $request->only(['website', 'url', 'homepage'])
            ]);
            return true; // Pre boty vráť "úspech"
        }

        // 2. TIME-BASED PROTECTION
        $formStartTime = $request->input('form_start_time');
        if ($formStartTime) {
            $timeDiff = time() - (int)$formStartTime;
            if ($timeDiff < 5) {
                Log::warning('Spam detected - Form submitted too quickly', [
                    'ip' => $ip,
                    'time_diff' => $timeDiff
                ]);
                return 'Formulár bol odoslaný príliš rýchlo. Počkajte chvíľu a skúste znovu.';
            }
        }

        // 3. RATE LIMITING
        $rateLimitKey = 'contact_form_rate_limit:' . $ip;
        $attempts = Cache::get($rateLimitKey, 0);
        
        if ($attempts >= 3) {
            Log::warning('Spam detected - Rate limit exceeded', [
                'ip' => $ip,
                'attempts' => $attempts
            ]);
            return 'Prekročili ste limit správ. Skúste to znovu za 10 minút.';
        }

        // 4. MATH CAPTCHA CHECK
        $mathAnswer = $request->input('math_answer');
        $mathQuestion = $request->input('math_question');
        
        if ($mathAnswer && $mathQuestion) {
            $decodedQuestion = base64_decode($mathQuestion);
            $correctAnswer = $this->calculateMathAnswer($decodedQuestion);
            
            if ($correctAnswer === null || (int)$mathAnswer !== $correctAnswer) {
                Log::warning('Spam detected - Wrong math answer', [
                    'ip' => $ip,
                    'provided_answer' => $mathAnswer,
                    'correct_answer' => $correctAnswer,
                    'question' => $decodedQuestion
                ]);
                return 'Nesprávna odpoveď na matematickú otázku.';
            }
        }

        // Ak prešiel všetky kontroly, zvýš počítadlo rate limitu
        Cache::put($rateLimitKey, $attempts + 1, 600); // 10 minút
        
        return true;
    }

    /**
     * Bezpečne vypočítaj odpoveď na matematickú otázku
     */
    private function calculateMathAnswer($question)
    {
        $question = trim($question);
        
        if (preg_match('/^(\d+)\s*\+\s*(\d+)$/', $question, $matches)) {
            return (int)$matches[1] + (int)$matches[2];
        }
        
        if (preg_match('/^(\d+)\s*\-\s*(\d+)$/', $question, $matches)) {
            return (int)$matches[1] - (int)$matches[2];
        }
        
        return null;
    }
} 