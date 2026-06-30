<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminEmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('type')->orderBy('name')->get();
        
        return view('admin.email-templates.index', compact('templates'));
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $emailTemplate->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Email template bol úspešne aktualizovaný.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        // Ukážkové dáta pre náhľad
        $sampleData = $this->getSampleData($emailTemplate->key);
        
        $content = $emailTemplate->renderContent($sampleData);
        $subject = $emailTemplate->renderSubject($sampleData);
        
        return view('admin.email-templates.preview', compact('emailTemplate', 'content', 'subject', 'sampleData'));
    }

    public function previewRaw(EmailTemplate $emailTemplate)
    {
        // Ukážkové dáta pre náhľad
        $sampleData = $this->getSampleData($emailTemplate->key);
        
        $content = $emailTemplate->renderContent($sampleData);
        
        // Vrátime čistý HTML pre iframe s povolenými iframe headers
        return response($content)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('Content-Security-Policy', "frame-ancestors 'self'");
    }

    public function test(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        $sampleData = $this->getSampleData($emailTemplate->key);
        
        try {
            // Dočasne nastavíme queue na sync pre okamžité odoslanie
            config(['queue.default' => 'sync']);
            
            // ODSTRÁNENÉ: Problematická logika ktorá prepísala SMTP na log
            // Necháme pôvodný mail driver (smtp) bez zmeny
            
            \Log::info('Odoslanie test emailu z template', [
                'template_key' => $emailTemplate->key,
                'template_name' => $emailTemplate->name,
                'recipient' => $request->test_email,
                'sample_data' => $sampleData,
                'mail_driver' => config('mail.default')
            ]);
            
            $content = $emailTemplate->renderContent($sampleData);
            $subject = '[TEST] ' . $emailTemplate->renderSubject($sampleData);
            
            \Log::info('Rendered email content', [
                'subject' => $subject,
                'content_length' => strlen($content),
                'content_preview' => substr(strip_tags($content), 0, 100)
            ]);
            
            // Používame priamo Mail::html() pretože admin má správne SMTP nastavenia 
            // načítané cez AppServiceProvider z databázy
            Mail::html(
                $content,
                function ($message) use ($subject, $request) {
                    $message->to($request->test_email)
                           ->subject($subject)
                           ->from(config('mail.from.address', 'noreply@erotikon.sk'), config('mail.from.name', 'Erotikon.sk'));
                }
            );

            \Log::info('Test email úspešne odoslaný', [
                'recipient' => $request->test_email,
                'template' => $emailTemplate->name,
                'mail_driver' => config('mail.default')
            ]);

            return back()->with('success', 'Test email bol úspešne odoslaný na ' . $request->test_email . ' cez ' . config('mail.default') . ' driver');
            
        } catch (\Exception $e) {
            \Log::error('Chyba pri odosielaní test emailu z template', [
                'template_key' => $emailTemplate->key,
                'recipient' => $request->test_email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Chyba pri odosielaní test emailu: ' . $e->getMessage());
        }
    }

    public function duplicate(EmailTemplate $emailTemplate)
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->key = $emailTemplate->key . '_copy_' . time();
        $newTemplate->name = $emailTemplate->name . ' (Kópia)';
        $newTemplate->is_active = false;
        $newTemplate->save();

        return redirect()
            ->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'Template bol skopírovaný. Upravte kľúč a názov.');
    }

    private function getSampleData(string $key): array
    {
        $baseData = [
            'user_name' => 'Mária Nováková',
            'user_email' => 'maria@example.com',
            'ad_id' => '16021',
            'ad_nickname' => 'Sexy Mária',
            'ad_type' => 'Escort',
            'ad_city' => 'Bratislava',
            'ad_views' => '173,856',
            'expiry_date' => '25.12.2024',
            'days_left' => '3',
            'package_price' => '40',
            'payment_id' => 'PAY123456',
            'amount' => '40,00',
            'due_date' => '29.12.2024'
        ];

        return $baseData;
    }
} 