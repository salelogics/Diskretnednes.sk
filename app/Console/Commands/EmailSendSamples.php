<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EmailSendSamples extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-samples {--to=mailpit@localhost : Cieľová adresa (Mailpit)} {--smtp-host=127.0.0.1} {--smtp-port=1025}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Odošle ukážky všetkých e-mailových šablón do Mailpit pre vizuálnu kontrolu. Nepoužíva DB templaty.';

    public function handle(): int
    {
        $to = (string) $this->option('to');

        // Nastavíme SMTP na Mailpit (lokálne)
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => (string) $this->option('smtp-host'),
            'mail.mailers.smtp.port' => (int) $this->option('smtp-port'),
            'mail.mailers.smtp.encryption' => null,
            'mail.from.address' => 'test@diskretnednes.local',
            'mail.from.name' => 'Diskrétne Dnes',
        ]);

        // Test dáta (bez závislosti na DB templatoch)
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'dev@diskretnednes.local',
        ]);

        $ad = Ad::first() ?? Ad::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'Test inzerát',
            'subscription_expires_at' => Carbon::now()->addDays(7),
        ]);

        $payment = AdPayment::first() ?? AdPayment::create([
            'user_id' => $user->id,
            'ad_id' => $ad->id,
            'payment_package_id' => null,
            'payment_id' => 'TEST-' . uniqid(),
            'amount' => 40,
            'currency' => 'EUR',
            'payment_method' => 'bank_transfer',
            'status' => 'completed',
            'duration_days' => 25,
            'subscription_starts_at' => Carbon::now(),
            'subscription_ends_at' => Carbon::now()->addDays(25),
            'is_featured' => false,
            'is_top_ad' => false,
        ]);

        $ticket = SupportTicket::first() ?? SupportTicket::create([
            'user_id' => $user->id,
            'subject' => 'Testovací ticket',
            'message' => 'Mám otázku k službe.',
            'status' => 'open',
            'admin_response' => 'Ďakujeme za správu, čoskoro sa ozveme.',
        ]);

        $sent = 0;

        $send = function (string $view, string $subject, array $data = []) use ($to, &$sent) {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            $this->line("✔️ Odoslané: {$subject}");
            $sent++;
        };

        // Odoslanie všetkých existujúcich Blade šablón (bez DB templátov)
        $send('emails.payment-instructions', 'PREVIEW: Platobné inštrukcie', [
            'payment' => $payment,
            'user' => $user,
        ]);

        $send('emails.payment-completed', 'PREVIEW: Platba úspešne spracovaná', [
            'payment' => $payment,
            'user' => $user,
        ]);

        $send('emails.subscription-expiring', 'PREVIEW: Predplatné čoskoro vyprší', [
            'ad' => $ad,
            'user' => $user,
            'daysLeft' => 3,
        ]);

        $send('emails.subscription-expired', 'PREVIEW: Predplatné vypršalo', [
            'ad' => $ad,
            'user' => $user,
        ]);

        $send('emails.welcome', 'PREVIEW: Vitajte na DiskretneDnes.sk', [
            'user' => $user,
        ]);

        $send('emails.support-ticket-response', 'PREVIEW: Odpoveď na support ticket', [
            'ticket' => $ticket,
            'user' => $user,
        ]);

        $send('emails.admin-notification', 'PREVIEW: Admin notifikácia', [
            'title' => 'Nová platba',
            'content' => 'Nová platba €40,00 od ' . ($user->name ?? 'Používateľ'),
            'type' => 'payment',
            'priority' => 'normal',
            'actionUrl' => url('/admin/inzeraty/' . $ad->id),
            'actionText' => 'Zobraziť v administrácii',
        ]);

        $send('emails.new-ad-created', 'PREVIEW: Nový inzerát vytvorený', [
            'ad' => $ad,
            'user' => $user,
        ]);

        $send('emails.ad-updated', 'PREVIEW: Inzerát bol aktualizovaný', [
            'ad' => $ad,
            'user' => $user,
        ]);

        $send('emails.contact-form', 'PREVIEW: Nová správa z kontaktu', [
            'name' => $user->name ?? 'Používateľ',
            'email' => $user->email,
            'subject' => 'Otázka k službe',
            'submittedAt' => now()->format('d.m.Y H:i'),
            'messageContent' => 'Testovacia správa z kontaktného formulára.',
        ]);

        $this->info("Spolu odoslaných náhľadov: {$sent}");

        $this->line('Skontrolujte Mailpit: http://localhost:8025/');

        return self::SUCCESS;
    }
}


