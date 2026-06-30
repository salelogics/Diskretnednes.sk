<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return view('support.index');
    }

    public function submit(Request $request)
    {
        // ANTI-SPAM VALIDATION
        $spamCheck = $this->checkForSpam($request);
        if ($spamCheck !== true) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $spamCheck]);
            }
            return back()->withErrors(['spam' => $spamCheck])->withInput();
        }

        $request->validate([
            'problem_type' => 'required|in:inzercia,predplatne,ina_chyba',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'math_answer' => 'required|integer',
            'math_question' => 'required|string'
        ], [
            'problem_type.required' => 'Typ problému je povinný.',
            'problem_type.in' => 'Neplatný typ problému.',
            'name.required' => 'Meno je povinné.',
            'name.max' => 'Meno môže mať maximálne 255 znakov.',
            'email.required' => 'Email je povinný.',
            'email.email' => 'Email musí byť platný.',
            'email.max' => 'Email môže mať maximálne 255 znakov.',
            'phone.max' => 'Telefón môže mať maximálne 20 znakov.',
            'subject.required' => 'Predmet je povinný.',
            'subject.max' => 'Predmet môže mať maximálne 255 znakov.',
            'message.required' => 'Správa je povinná.',
            'message.max' => 'Správa môže mať maximálne 2000 znakov.',
            'math_answer.required' => 'Matematická odpoveď je povinná.',
            'math_answer.integer' => 'Matematická odpoveď musí byť číslo.',
            'math_question.required' => 'Matematická otázka je povinná.',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'problem_type' => $request->problem_type,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'new'
        ]);

        // Vytvor notifikáciu pre adminov
        $this->notificationService->newSupportTicket($ticket);

        // Ak je to AJAX požiadavka, vráť JSON odpoveď
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Správa bola úspešne odoslaná!',
                'details' => 'Váš support ticket bol úspešne odoslaný. Odpovieme vám čo najskôr.'
            ]);
        }

        return redirect()->route('support.index')->with('success', 'Váš support ticket bol úspešne odoslaný. Odpovieme vám čo najskôr.');
    }

    /**
     * Zobrazenie detailu support ticketu
     */
    public function show(SupportTicket $ticket)
    {
        // Skontrolovať že ticket patrí aktuálnemu používateľovi
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Nemáte oprávnenie na zobrazenie tohto ticketu.');
        }

        return view('support.show', compact('ticket'));
    }

    /**
     * Odpoveď používateľa na support ticket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        // Skontrolovať že ticket patrí aktuálnemu používateľovi
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Nemáte oprávnenie na odpoveď na tento ticket.');
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ], [
            'message.required' => 'Správa je povinná.',
            'message.max' => 'Správa môže mať maximálne 2000 znakov.',
        ]);

        // Aktualizovať ticket s odpoveďou používateľa
        $ticket->update([
            'message' => $ticket->message . "\n\n--- Odpoveď používateľa (" . now()->format('d.m.Y H:i') . ") ---\n" . $request->message,
            'status' => 'in_progress', // Zmeniť status na "v riešení"
        ]);

        // Vytvor notifikáciu pre adminov o novej odpovedi
        $this->notificationService->userRepliedToTicket($ticket);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Odpoveď bola úspešne odoslaná!',
                'details' => 'Vaša odpoveď bola pridaná k support ticketu.'
            ]);
        }

        return redirect()->route('support.ticket.show', $ticket)->with('success', 'Vaša odpoveď bola úspešne odoslaná.');
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
        $rateLimitKey = 'support_form_rate_limit:' . $ip;
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