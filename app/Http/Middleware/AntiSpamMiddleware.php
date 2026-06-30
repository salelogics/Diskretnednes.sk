<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AntiSpamMiddleware
{
    /**
     * Handle an incoming request for contact forms
     */
    public function handle(Request $request, Closure $next)
    {
        // Len pre POST requesty (odosielanie formulárov)
        if ($request->isMethod('post')) {
            
            // 1. HONEYPOT CHECK - skryté pole ktoré boti vyplnia
            if ($request->filled('website') || $request->filled('url') || $request->filled('homepage')) {
                Log::warning('Spam detected - Honeypot triggered', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'honeypot_field' => $request->only(['website', 'url', 'homepage'])
                ]);
                
                // Pre boty - vráť "úspech" ale nič neurobí
                if ($request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Správa bola odoslaná']);
                }
                return back()->with('success', 'Ďakujeme za vašu správu');
            }

            // 2. TIME-BASED PROTECTION - minimálne 5 sekúnd na vyplnenie
            $formStartTime = $request->input('form_start_time');
            if ($formStartTime) {
                $timeDiff = time() - (int)$formStartTime;
                if ($timeDiff < 5) {
                    Log::warning('Spam detected - Form submitted too quickly', [
                        'ip' => $request->ip(),
                        'time_diff' => $timeDiff
                    ]);
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Formulár bol odoslaný príliš rýchlo. Počkajte chvíľu a skúste znovu.']);
                    }
                    return back()->withErrors(['spam' => 'Formulár bol odoslaný príliš rýchlo. Počkajte chvíľu a skúste znovu.']);
                }
            }

            // 3. RATE LIMITING - max 3 správy za 10 minút z jednej IP
            $ip = $request->ip();
            $rateLimitKey = 'contact_form_rate_limit:' . $ip;
            $attempts = Cache::get($rateLimitKey, 0);
            
            if ($attempts >= 3) {
                Log::warning('Spam detected - Rate limit exceeded', [
                    'ip' => $ip,
                    'attempts' => $attempts
                ]);
                
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Prekročili ste limit správ. Skúste to znovu za 10 minút.']);
                }
                return back()->withErrors(['spam' => 'Prekročili ste limit správ. Skúste to znovu za 10 minút.']);
            }

            // 4. SIMPLE MATH CAPTCHA CHECK
            $mathAnswer = $request->input('math_answer');
            $mathQuestion = $request->input('math_question');
            
            if ($mathAnswer && $mathQuestion) {
                // Dekóduj otázku z base64
                $decodedQuestion = base64_decode($mathQuestion);
                
                // Bezpečne spracuj matematickú otázku (namiesto eval)
                $correctAnswer = $this->calculateMathAnswer($decodedQuestion);
                
                if ($correctAnswer === null || (int)$mathAnswer !== $correctAnswer) {
                    Log::warning('Spam detected - Wrong math answer', [
                        'ip' => $ip,
                        'provided_answer' => $mathAnswer,
                        'correct_answer' => $correctAnswer,
                        'question' => $decodedQuestion
                    ]);
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Nesprávna odpoveď na matematickú otázku.']);
                    }
                    return back()->withErrors(['math_answer' => 'Nesprávna odpoveď na matematickú otázku.'])->withInput();
                }
            }

            // Ak prešiel všetky kontroly, zvýš počítadlo rate limitu
            Cache::put($rateLimitKey, $attempts + 1, 600); // 10 minút
        }

        return $next($request);
    }

    /**
     * Bezpečne vypočítaj odpoveď na matematickú otázku
     */
    private function calculateMathAnswer($question)
    {
        // Očakávame formát ako "5 + 3" alebo "8 - 2"
        $question = trim($question);
        
        // Regex pre jednoduché matematické operácie
        if (preg_match('/^(\d+)\s*\+\s*(\d+)$/', $question, $matches)) {
            return (int)$matches[1] + (int)$matches[2];
        }
        
        if (preg_match('/^(\d+)\s*\-\s*(\d+)$/', $question, $matches)) {
            return (int)$matches[1] - (int)$matches[2];
        }
        
        // Ak sa nepodará parsovať, vráť null
        return null;
    }
} 