<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\SupportTicket;
use App\Models\AdReport;
use App\Models\CustomerReport;
use App\Models\EmailTemplate;
use App\Mail\PaymentCompletedMail;
use App\Mail\SubscriptionExpiringMail;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\SupportTicketResponseMail;
use App\Mail\AdminNotificationMail;
use App\Mail\WelcomeMail;
use App\Mail\NewAdCreatedMail;
use App\Mail\AdUpdatedMail;
use App\Mail\PaymentInstructionsMail;
use Illuminate\Support\Facades\Mail;
use App\Helpers\AdminNotificationHelper;

class NotificationService
{
    // Registrácia a nové účty
    public function userRegistered(User $user)
    {
        \Log::info("NotificationService: userRegistered volaná", [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);

        $notification = Notification::createForUser(
            $user->id,
            'system',
            'Vitajte na DiskretneDnes.sk!',
            'Váš účet bol úspešne vytvorený. Môžete začať vytvárať inzeráty a využívať všetky naše služby.',
            [
                'icon' => 'ri-user-add-line',
                'color' => 'green',
                'action_url' => route('ads.create'),
                'action_text' => 'Vytvoriť inzerát',
                'data' => [
                    'welcome_notification' => true,
                    'registration_date' => now()->toISOString()
                ]
            ]
        );

        \Log::info("NotificationService: Notifikácia vytvorená, posielam uvítací email");

        // Poslať uvítací email
        $this->sendTemplateEmail('welcome', $user->email, [
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);

        \Log::info("NotificationService: Uvítací email odoslaný, posielam admin notifikáciu");

        // Admin notifikácia o novej registrácii
        $this->newUserRegistered($user);

        \Log::info("NotificationService: userRegistered dokončená");

        return $notification;
    }

    public function newUserRegistered(User $user)
    {
        $notification = Notification::createForAllAdmins(
            'system',
            'Nová registrácia používateľa',
            "Nový používateľ {$user->name} ({$user->email}) sa zaregistroval na stránke.",
            [
                'icon' => 'ri-user-add-line',
                'color' => 'blue',
                'action_url' => route('admin.pouzivatelia.index'),
                'action_text' => 'Zobraziť používateľov',
                'data' => [
                    'user_id' => $user->id,
                    'registration_date' => $user->created_at->toISOString()
                ]
            ]
        );

        // Poslať email všetkým adminom
        $this->sendTemplateEmailToAdmins('admin_new_user', [
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);

        return $notification;
    }

    // Nové inzeráty
    public function adCreated(Ad $ad)
    {
        \Log::info("NotificationService: adCreated volaná", [
            'ad_id' => $ad->id,
            'user_id' => $ad->user_id,
            'nickname' => $ad->nickname,
            'user_email' => $ad->user->email ?? 'neznamy'
        ]);

        $notification = Notification::createForUser(
            $ad->user_id,
            'ad',
            'Inzerát bol vytvorený',
            "Váš inzerát #{$ad->id} ({$ad->nickname}) bol úspešne vytvorený. " . 
            ($ad->status === 'draft' ? 'Dokončite ho a aktivujte.' : 'Čaká na schválenie.'),
            [
                'icon' => 'ri-article-line',
                'color' => $ad->status === 'draft' ? 'yellow' : 'blue',
                'action_url' => $ad->status === 'draft' ? route('ads.edit', $ad->id) : route('ads.index'),
                'action_text' => $ad->status === 'draft' ? 'Dokončiť inzerát' : 'Zobraziť inzeráty',
                'data' => [
                    'ad_id' => $ad->id,
                    'ad_status' => $ad->status
                ]
            ]
        );

        \Log::info("NotificationService: Notifikácia pre adCreated vytvorená, posielam email používateľovi");

        // Poslať email používateľovi
        $this->sendTemplateEmail('ad_created', $ad->user->email, [
            'user_name' => $ad->user->name,
            'ad_id' => $ad->id,
            'ad_nickname' => $ad->nickname,
            'ad_type' => $ad->type,
            'ad_city' => $ad->city_label
        ]);

        \Log::info("NotificationService: Email používateľovi odoslaný, posielam admin notifikáciu");

        // Admin notifikácia o novom inzeráte
        $this->newAdForAdmin($ad);

        \Log::info("NotificationService: adCreated dokončená");

        return $notification;
    }

    public function newAdForAdmin(Ad $ad)
    {
        $notification = Notification::createForAllAdmins(
            'ad',
            'Nový inzerát vytvorený',
            "Nový inzerát #{$ad->id} od {$ad->user->name} - {$ad->nickname} ({$ad->city})",
            [
                'icon' => 'ri-article-line',
                'color' => 'purple',
                'action_url' => route('admin.inzeraty.show', $ad->id),
                'action_text' => 'Preskúmať inzerát',
                'data' => [
                    'ad_id' => $ad->id,
                    'user_id' => $ad->user_id,
                    'ad_status' => $ad->status
                ]
            ]
        );

        // Poslať email všetkým adminom cez templating systém v administrácii
        $this->sendTemplateEmailToAdmins('admin_new_ad', [
            'user_name' => $ad->user->name,
            'user_email' => $ad->user->email,
            'ad_id' => $ad->id,
            'ad_nickname' => $ad->nickname,
            'ad_city' => $ad->city_label,
            'ad_url' => route('admin.inzeraty.show', $ad->id),
        ]);

        return $notification;
    }

    public function adUpdated(Ad $ad)
    {
        $notification = Notification::createForUser(
            $ad->user_id,
            'ad',
            'Inzerát bol aktualizovaný',
            "Váš inzerát #{$ad->id} ({$ad->nickname}) bol úspešne aktualizovaný.",
            [
                'icon' => 'ri-edit-line',
                'color' => 'green',
                'action_url' => route('ads.index'),
                'action_text' => 'Zobraziť inzeráty',
                'data' => [
                    'ad_id' => $ad->id
                ]
            ]
        );

        // Poslať email používateľovi
        $this->sendTemplateEmail('ad_updated', $ad->user->email, [
            'user_name' => $ad->user->name,
            'ad_id' => $ad->id,
            'ad_nickname' => $ad->nickname
        ]);

        // Admin notifikácia o aktualizácii inzerátu
        $this->adUpdatedForAdmin($ad);

        return $notification;
    }

    public function adUpdatedForAdmin(Ad $ad)
    {
        $notification = Notification::createForAllAdmins(
            'ad',
            'Inzerát bol aktualizovaný',
            "Inzerát #{$ad->id} od {$ad->user->name} - {$ad->nickname} bol aktualizovaný",
            [
                'icon' => 'ri-edit-line',
                'color' => 'orange',
                'action_url' => route('admin.inzeraty.show', $ad->id),
                'action_text' => 'Preskúmať zmeny',
                'data' => [
                    'ad_id' => $ad->id,
                    'user_id' => $ad->user_id
                ]
            ]
        );

        // Poslať email všetkým adminom
        $this->sendTemplateEmailToAdmins('admin_ad_updated', [
            'user_name' => $ad->user->name,
            'ad_id' => $ad->id,
            'ad_nickname' => $ad->nickname
        ]);

        return $notification;
    }

    // Platby a predplatné
    public function paymentCompleted(AdPayment $payment)
    {
        $notification = Notification::createForUser(
            $payment->user_id,
            'payment',
            'Platba úspešne spracovaná',
            "Vaša platba {$payment->formatted_amount} za predplatné inzerátu #{$payment->ad_id} bola úspešne spracovaná.",
            [
                'icon' => 'ri-check-circle-line',
                'color' => 'green',
                'action_url' => route('ads.payment.status', $payment->payment_id),
                'action_text' => 'Zobraziť platbu',
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'ad_id' => $payment->ad_id,
                    'amount' => $payment->amount
                ]
            ]
        );

        // Poslať email používateľovi
        try {
            Mail::to($payment->user->email)->send(new PaymentCompletedMail($payment));
        } catch (\Exception $e) {
            \Log::error('Chyba pri posielaní emailu pre úspešnú platbu: ' . $e->getMessage());
        }

        return $notification;
    }

    public function paymentFailed(AdPayment $payment)
    {
        return Notification::createForUser(
            $payment->user_id,
            'payment',
            'Platba neúspešná',
            "Platba {$payment->formatted_amount} za predplatné inzerátu #{$payment->ad_id} sa nepodarila. Skúste to znovu.",
            [
                'icon' => 'ri-error-warning-line',
                'color' => 'red',
                'action_url' => route('ads.index'),
                'action_text' => 'Zobraziť inzeráty',
                'priority' => 'high',
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'ad_id' => $payment->ad_id
                ]
            ]
        );
    }

    public function subscriptionExpiring(Ad $ad, $daysLeft)
    {
        $message = $daysLeft == 1 
            ? "Predplatné vášho inzerátu #{$ad->id} vyprší zajtra!"
            : "Predplatné vášho inzerátu #{$ad->id} vyprší za {$daysLeft} dní.";

        $notification = Notification::createForUser(
            $ad->user_id,
            'ad',
            'Predplatné čoskoro vyprší',
            $message,
            [
                'icon' => 'ri-time-line',
                'color' => 'yellow',
                'action_url' => route('ads.index'),
                'action_text' => 'Zobraziť inzeráty',
                'priority' => $daysLeft <= 3 ? 'high' : 'normal',
                'data' => [
                    'ad_id' => $ad->id,
                    'days_left' => $daysLeft
                ]
            ]
        );

        // Poslať email používateľovi
        $this->sendTemplateEmail('subscription_expiring', $ad->user->email, [
            'user_name' => $ad->user->name,
            'ad_id' => $ad->id,
            'ad_nickname' => $ad->nickname,
            'expiry_date' => $ad->subscription_expires_at->format('d.m.Y'),
            'days_left' => $daysLeft
        ]);

        return $notification;
    }

    public function subscriptionExpired(Ad $ad)
    {
        $notification = Notification::createForUser(
            $ad->user_id,
            'ad',
            'Predplatné vypršalo',
            "Predplatné vášho inzerátu #{$ad->id} vypršalo. Inzerát už nie je aktívny.",
            [
                'icon' => 'ri-alarm-warning-line',
                'color' => 'red',
                'action_url' => route('ads.index'),
                'action_text' => 'Zobraziť inzeráty',
                'priority' => 'high',
                'data' => [
                    'ad_id' => $ad->id
                ]
            ]
        );

        // Poslať email používateľovi s bankovými údajmi
        $this->sendTemplateEmail('subscription_expired', $ad->user->email, [
            'ad_id' => $ad->id,
            'ad_nickname' => $ad->nickname,
            'ad_views' => number_format($ad->views ?? 0, 0, ',', '.'),
            'package_price' => '40'
        ]);

        return $notification;
    }

    // Support tickety
    public function supportTicketResponse(SupportTicket $ticket)
    {
        return Notification::createForUser(
            $ticket->user_id,
            'support',
            'Odpoveď na váš support ticket',
            "Admin odpovedal na váš ticket #{$ticket->id} - {$ticket->subject}",
            [
                'icon' => 'ri-customer-service-line',
                'color' => 'green',
                'action_url' => route('support.ticket.show', $ticket->id),
                'action_text' => 'Zobraziť odpoveď',
                'priority' => 'normal',
                'data' => [
                    'ticket_id' => $ticket->id
                ]
            ]
        );
    }

    // Admin notifikácie
    public function newSupportTicket(SupportTicket $ticket)
    {
        $title = $this->removeEmojis('Nový support ticket');
        $message = $this->removeEmojis("Nový support ticket #{$ticket->id} od {$ticket->name} - {$ticket->subject}");
        $notification = Notification::createForAllAdmins(
            'support',
            $title,
            $message,
            [
                'icon' => 'ri-customer-service-line',
                'color' => 'blue',
                'action_url' => route('admin.support-tickets.show', $ticket->id),
                'action_text' => 'Zobraziť ticket',
                'priority' => 'normal',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->user_id
                ]
            ]
        );

        // Poslať email všetkým adminom
        $this->sendAdminEmail(
            'Nový support ticket',
            "Nový support ticket #{$ticket->id} od {$ticket->name} - {$ticket->subject}",
            'support',
            'normal',
            route('admin.support-tickets.show', $ticket->id),
            'Zobraziť ticket'
        );

        return $notification;
    }

    public function newAdReport(AdReport $report)
    {
        $notification = Notification::createForAllAdmins(
            'moderation',
            'Nové nahlásenie inzerátu',
            "Inzerát #{$report->ad_id} bol nahlásený z dôvodu: {$report->reason}",
            [
                'icon' => 'ri-flag-line',
                'color' => 'red',
                'action_url' => route('admin.nahlasenia-inzeratov.index'),
                'action_text' => 'Preskúmať',
                'priority' => 'high',
                'data' => [
                    'report_id' => $report->id,
                    'ad_id' => $report->ad_id
                ]
            ]
        );

        // Poslať email všetkým adminom - POUŽIJEM TEMPLATE SYSTÉM
        $adminEmails = get_admin_emails();
        foreach ($adminEmails as $email) {
            $this->sendTemplateEmail('ad_report', $email, [
                'ad_id' => $report->ad_id,
                'ad_title' => $report->ad->nickname ?? 'Neznámy inzerát',
                'reason' => $report->reason,
                'details' => $report->details ?? 'Žiadne ďalšie detaily',
                'reporter_email' => $report->reporter_email ?? 'Neznámy email',
                'report_date' => $report->created_at->format('d.m.Y H:i:s')
            ]);
        }

        return $notification;
    }

    public function newCustomerReport(CustomerReport $report)
    {
        $reportsCount = CustomerReport::where('phone_number', $report->phone_number)->count();
        $priority = $reportsCount >= 10 ? 'urgent' : ($reportsCount >= 5 ? 'high' : 'normal');

        $notification = Notification::createForAllAdmins(
            'moderation',
            'Nové nahlásenie zákazníka',
            "Telefónne číslo {$report->phone_number} bolo nahlásené ({$reportsCount}. nahlásenie)",
            [
                'icon' => 'ri-phone-line',
                'color' => 'orange',
                'action_url' => route('admin.nahlasenia-zakaznikov.index'),
                'action_text' => 'Zobraziť reporty',
                'priority' => $priority,
                'data' => [
                    'report_id' => $report->id,
                    'phone_number' => $report->phone_number,
                    'reports_count' => $reportsCount
                ]
            ]
        );

        // Poslať email všetkým adminom
        $this->sendAdminEmail(
            'Nové nahlásenie zákazníka',
            "Telefónne číslo {$report->phone_number} bolo nahlásené ({$reportsCount}. nahlásenie)",
            'moderation',
            $priority,
            route('admin.nahlasenia-zakaznikov.index'),
            'Zobraziť reporty'
        );

        return $notification;
    }

    /**
     * Bankový prevod ostáva 'pending', kým ho admin ručne neschváli - dovtedy
     * ho nič inde neupozorní, že vôbec existuje (newPayment() nižšie sa volá
     * až z AdPayment::markAsCompleted(), teda až PO schválení). Bez tohto by
     * admin o novej platbe čakajúcej na spracovanie vôbec nevedel.
     */
    public function paymentAwaitingApproval(AdPayment $payment)
    {
        $notification = Notification::createForAllAdmins(
            'payment',
            'Platba čaká na schválenie',
            "Nová platba {$payment->formatted_amount} od {$payment->user->name} za inzerát #{$payment->ad_id} ({$payment->payment_method_label}) čaká na schválenie. Variabilný symbol: {$payment->payment_id}.",
            [
                'icon' => 'ri-time-line',
                'color' => 'orange',
                'action_url' => route('admin.platby.show', $payment->id),
                'action_text' => 'Skontrolovať platbu',
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'ad_id' => $payment->ad_id,
                    'amount' => $payment->amount
                ]
            ]
        );

        $this->sendAdminEmail(
            'Platba čaká na schválenie',
            "Nová platba {$payment->formatted_amount} od {$payment->user->name} za inzerát #{$payment->ad_id} ({$payment->payment_method_label}) čaká na schválenie. Variabilný symbol: {$payment->payment_id}.",
            'payment',
            'normal',
            route('admin.platby.show', $payment->id),
            'Skontrolovať platbu'
        );

        return $notification;
    }

    public function newPayment(AdPayment $payment)
    {
        $notification = Notification::createForAllAdmins(
            'payment',
            'Nová platba',
            "Nová platba {$payment->formatted_amount} od {$payment->user->name} za inzerát #{$payment->ad_id}",
            [
                'icon' => 'ri-money-dollar-circle-line',
                'color' => 'green',
                'action_url' => route('admin.platby.show', $payment->id),
                'action_text' => 'Zobraziť platbu',
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'ad_id' => $payment->ad_id,
                    'amount' => $payment->amount
                ]
            ]
        );

        // Poslať email všetkým adminom
        $this->sendAdminEmail(
            'Nová platba',
            "Nová platba {$payment->formatted_amount} od {$payment->user->name} za inzerát #{$payment->ad_id}",
            'payment',
            'normal',
            route('admin.platby.show', $payment->id),
            'Zobraziť platbu'
        );

        return $notification;
    }

    public function newAd(Ad $ad)
    {
        return Notification::createForAllAdmins(
            'ad',
            'Nový inzerát',
            "Nový inzerát #{$ad->id} od {$ad->user->name} čaká na schválenie",
            [
                'icon' => 'ri-article-line',
                'color' => 'purple',
                'action_url' => route('admin.inzeraty.show', $ad->id),
                'action_text' => 'Preskúmať',
                'data' => [
                    'ad_id' => $ad->id,
                    'user_id' => $ad->user_id
                ]
            ]
        );
    }

    // Systémové notifikácie
    public function systemMaintenance($title, $message, $scheduledAt = null)
    {
        $allUsers = User::all();
        $notifications = [];

        foreach ($allUsers as $user) {
            $notifications[] = Notification::createForUser(
                $user->id,
                'system',
                $title,
                $message,
                [
                    'icon' => 'ri-tools-line',
                    'color' => 'blue',
                    'priority' => 'high',
                    'expires_at' => $scheduledAt ? $scheduledAt->addDays(1) : now()->addDays(7),
                    'data' => [
                        'scheduled_at' => $scheduledAt?->toISOString()
                    ]
                ]
            );
        }

        return $notifications;
    }

    public function newFeature($title, $message, $actionUrl = null)
    {
        $allUsers = User::all();
        $notifications = [];

        foreach ($allUsers as $user) {
            $notifications[] = Notification::createForUser(
                $user->id,
                'system',
                $title,
                $message,
                [
                    'icon' => 'ri-star-line',
                    'color' => 'purple',
                    'action_url' => $actionUrl,
                    'action_text' => $actionUrl ? 'Vyskúšať' : null,
                    'expires_at' => now()->addDays(30),
                    'data' => [
                        'feature_announcement' => true
                    ]
                ]
            );
        }

        return $notifications;
    }

    // Bulk operácie
    public function markAllAsRead($userId, $isAdmin = false)
    {
        return Notification::markAllAsReadForUser($userId, $isAdmin);
    }

    public function deleteExpiredNotifications()
    {
        return Notification::deleteExpired();
    }

    public function getUnreadCount($userId, $isAdmin = false)
    {
        return Notification::getUnreadCountForUser($userId, $isAdmin);
    }

    public function getRecentNotifications($userId, $isAdmin = false, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->where('is_admin', $isAdmin)
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Automatické notifikácie pre expirujúce predplatné
    public function checkExpiringSubscriptions()
    {
        $notifications = [];

        // Predplatné vyprší za 7 dní
        $expiring7Days = Ad::where('subscription_status', 'active')
            ->whereBetween('subscription_expires_at', [
                now()->addDays(7)->startOfDay(),
                now()->addDays(7)->endOfDay()
            ])
            ->get();

        foreach ($expiring7Days as $ad) {
            $notifications[] = $this->subscriptionExpiring($ad, 7);
        }

        // Predplatné vyprší za 3 dni
        $expiring3Days = Ad::where('subscription_status', 'active')
            ->whereBetween('subscription_expires_at', [
                now()->addDays(3)->startOfDay(),
                now()->addDays(3)->endOfDay()
            ])
            ->get();

        foreach ($expiring3Days as $ad) {
            $notifications[] = $this->subscriptionExpiring($ad, 3);
        }

        // Predplatné vyprší zajtra
        $expiring1Day = Ad::where('subscription_status', 'active')
            ->whereBetween('subscription_expires_at', [
                now()->addDay()->startOfDay(),
                now()->addDay()->endOfDay()
            ])
            ->get();

        foreach ($expiring1Day as $ad) {
            $notifications[] = $this->subscriptionExpiring($ad, 1);
        }

        // Predplatné už vypršalo
        $expired = Ad::where('subscription_status', 'active')
            ->where('subscription_expires_at', '<', now())
            ->get();

        foreach ($expired as $ad) {
            $notifications[] = $this->subscriptionExpired($ad);
            // Aktualizuj stav inzerátu
            $ad->update(['subscription_status' => 'expired']);
        }

        return $notifications;
    }

    private function sendAdminEmail($title, $message, $type, $priority, $actionUrl, $actionText)
    {
        $adminEmails = get_admin_emails();
        \Log::info("NotificationService: Začínam posielanie admin emailu", [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'priority' => $priority,
            'to' => implode(', ', $adminEmails)
        ]);

        // Použiť nové helper funkcie pre admin notifikácie
        try {
            notify_admins($title, $message, $type, $priority, $actionUrl, $actionText);
            \Log::info("NotificationService: Admin email úspešne odoslaný na: " . implode(', ', $adminEmails));
        } catch (\Exception $e) {
            \Log::error("NotificationService: Chyba pri posielaní admin emailu", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_emails' => $adminEmails
            ]);
        }
    }

    /**
     * Notifikácia pre adminov keď používateľ odpovie na support ticket
     */
    public function userRepliedToTicket(SupportTicket $ticket)
    {
        $notification = Notification::createForAllAdmins(
            'support',
            'Odpoveď na support ticket',
            "Používateľ {$ticket->name} odpovedal na ticket #{$ticket->id} - {$ticket->subject}",
            [
                'icon' => 'ri-reply-line',
                'color' => 'blue',
                'action_url' => route('admin.support-tickets.show', $ticket->id),
                'action_text' => 'Zobraziť odpoveď',
                'priority' => 'normal',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->user_id
                ]
            ]
        );

        // Poslať email všetkým adminom
        $this->sendAdminEmail(
            'Odpoveď na support ticket',
            "Používateľ {$ticket->name} odpovedal na ticket #{$ticket->id} - {$ticket->subject}",
            'support',
            'normal',
            route('admin.support-tickets.show', $ticket->id),
            'Zobraziť odpoveď'
        );

        return $notification;
    }

    /**
     * Pošle email na základe template kľúča
     */
    private function sendTemplateEmail(string $templateKey, string $email, array $data = [])
    {
        \Log::info("NotificationService: Začínam posielanie emailu", [
            'template_key' => $templateKey,
            'email' => $email,
            'data' => $data
        ]);

        try {
            // NAJPRV skúsime template systém z databázy
            $template = EmailTemplate::getByKey($templateKey);
            
            if ($template) {
                \Log::info("NotificationService: Našiel som template v databáze", [
                    'template_key' => $templateKey,
                    'template_name' => $template->name,
                    'template_id' => $template->id
                ]);
                
                $content = $template->renderContent($data);
                $subject = $template->renderSubject($data);

                // OPRAVENÉ: Používame AdminNotificationHelper pre správne SMTP nastavenia
                AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                
                \Log::info("NotificationService: Template email úspešne odoslaný cez AdminNotificationHelper", [
                    'email' => $email,
                    'subject' => $subject,
                    'template_key' => $templateKey
                ]);
                return true;
            }
            
            \Log::warning("NotificationService: Template v databáze nebol nájdený, používam fallback", [
                'template_key' => $templateKey
            ]);

            // Fallback na Mailable triedy ak template neexistuje
            switch ($templateKey) {
                case 'welcome':
                    \Log::info("NotificationService: Posielam uvítací email (fallback)", ['email' => $email]);
                    if (isset($data['user_name'])) {
                        $user = new User(['name' => $data['user_name'], 'email' => $email]);
                        
                        // OPRAVENÉ: WelcomeMail už má vlastnú logiku pre template systém
                        // Ale aj tak používame AdminNotificationHelper pre jednotnosť
                        $mailable = new WelcomeMail($user);
                        AdminNotificationHelper::sendMailableToEmail($mailable, $email);
                        \Log::info("NotificationService: WelcomeMail úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
                        return true;
                    }
                    break;
                    
                case 'ad_created':
                    \Log::info("NotificationService: Posielam email pre nový inzerát (fallback)", ['email' => $email, 'ad_id' => $data['ad_id'] ?? 'unknown']);
                    // Detailnejší fallback email
                    $content = "Dobrý deň,\n\n";
                    $content .= "Váš inzerát #{$data['ad_id']} (" . ($data['ad_nickname'] ?? 'bez názvu') . ") bol úspešne vytvorený.\n\n";
                    $content .= "Detaily inzerátu:\n";
                    $content .= "- ID: #{$data['ad_id']}\n";
                    $content .= "- Názov: " . ($data['ad_nickname'] ?? 'N/A') . "\n";
                    $content .= "- Typ: " . ($data['ad_type'] ?? 'N/A') . "\n";
                    $content .= "- Mesto: " . ($data['ad_city'] ?? 'N/A') . "\n\n";
                    $content .= "Ďakujeme za využívanie našich služieb!\n\n";
                    $content .= "S pozdravom,\nTím DiskretneDnes.sk";
                    
                    $adId = $data['ad_id'] ?? 'N/A';
                    $subject = 'Váš inzerát #' . $adId . ' bol vytvorený - DiskretneDnes.sk';
                    
                    // OPRAVENÉ: Používame AdminNotificationHelper
                    AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                    \Log::info("NotificationService: Email pre nový inzerát úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
                    return true;
                    
                case 'ad_updated':
                    \Log::info("NotificationService: Posielam email pre aktualizáciu inzerátu (fallback)", ['email' => $email]);
                    $content = "Dobrý deň,\n\n";
                    $content .= "Váš inzerát #{$data['ad_id']} (" . ($data['ad_nickname'] ?? 'bez názvu') . ") bol úspešne aktualizovaný.\n\n";
                    $content .= "Ďakujeme za využívanie našich služieb!\n\n";
                    $content .= "S pozdravom,\nTím DiskretneDnes.sk";
                    
                    $adId = $data['ad_id'] ?? 'N/A';
                    $subject = 'Inzerát #' . $adId . ' bol aktualizovaný - DiskretneDnes.sk';
                    
                    // OPRAVENÉ: Používame AdminNotificationHelper
                    AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                    \Log::info("NotificationService: Email pre aktualizáciu úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
                    return true;
                    
                case 'admin_new_user':
                case 'admin_new_ad':
                case 'admin_notification':
                    \Log::info("NotificationService: Posielam admin email (fallback)", ['template_key' => $templateKey, 'email' => $email]);
                    $content = "ADMIN NOTIFIKÁCIA\n\n";
                    $content .= "Typ: " . $templateKey . "\n\n";
                    $content .= "Detaily:\n";
                    foreach ($data as $key => $value) {
                        $content .= "- {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
                    }
                    $content .= "\nČas: " . now()->format('d.m.Y H:i:s') . "\n";
                    
                    $subject = 'Admin Notifikácia - ' . $templateKey . ' - DiskretneDnes.sk';
                    
                    // OPRAVENÉ: Používame AdminNotificationHelper
                    AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                    \Log::info("NotificationService: Admin email úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
                    return true;

                case 'contact':
                    // Fallback pre kontaktný formulár, ak DB šablóna 'contact' chýba -
                    // bez tohto by sa poslala len prázdna notifikácia bez mena/emailu/správy.
                    \Log::info("NotificationService: Posielam kontaktný email (fallback)", ['email' => $email]);
                    $name = htmlspecialchars($data['name'] ?? '', ENT_QUOTES, 'UTF-8');
                    $senderEmail = htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8');
                    $messageSubject = htmlspecialchars($data['subject'] ?? '', ENT_QUOTES, 'UTF-8');
                    $messageBody = nl2br(htmlspecialchars($data['messageContent'] ?? '', ENT_QUOTES, 'UTF-8'));
                    $submittedAt = htmlspecialchars($data['submittedAt'] ?? now()->format('d.m.Y H:i:s'), ENT_QUOTES, 'UTF-8');

                    $content = "<div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\">";
                    $content .= "<h2>Nová správa z kontaktného formulára</h2>";
                    $content .= "<p><strong>Predmet:</strong> {$messageSubject}</p>";
                    $content .= "<p><strong>Od:</strong> {$name} ({$senderEmail})</p>";
                    $content .= "<p><strong>Odoslané:</strong> {$submittedAt}</p>";
                    $content .= "<div style=\"margin-top:16px;padding:16px;border:1px solid #e0e0e0;border-radius:8px;\">{$messageBody}</div>";
                    $content .= "</div>";

                    $subject = 'Nová správa z kontaktného formulára - ' . ($data['subject'] ?? '');

                    AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                    \Log::info("NotificationService: Kontaktný email úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
                    return true;

                default:
                    \Log::info("NotificationService: Posielam general email (fallback)", ['template_key' => $templateKey, 'email' => $email]);
                    $content = "NOTIFIKÁCIA Z DISKRETNEDNES.SK\n\n";
                    $content .= "Typ: " . $templateKey . "\n\n";
                    if (!empty($data)) {
                        $content .= "Detaily:\n";
                        foreach ($data as $key => $value) {
                            $content .= "- {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
                        }
                    }
                    $content .= "\nČas: " . now()->format('d.m.Y H:i:s') . "\n";
                    
                    $subject = 'Notifikácia - ' . $templateKey . ' - DiskretneDnes.sk';
                    
                    // OPRAVENÉ: Používame AdminNotificationHelper
                    AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                    \Log::info("NotificationService: General email úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
                    return true;
            }

            \Log::warning("NotificationService: Switch nemal zhodu pre template", ['template_key' => $templateKey]);
            
            // Posledný fallback
            $content = "Notifikácia z DiskretneDnes.sk\n\nTyp: {$templateKey}\n\nČas: " . now()->format('d.m.Y H:i:s');
            $subject = 'Notifikácia - ' . $templateKey . ' - DiskretneDnes.sk';
            
            // OPRAVENÉ: Používame AdminNotificationHelper
            AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
            \Log::info("NotificationService: Posledný fallback email úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
            return true;
            
        } catch (\Exception $e) {
            \Log::error("NotificationService: CHYBA pri posielaní emailu", [
                'template_key' => $templateKey,
                'email' => $email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Posledný fallback - jednoduchý text email
            try {
                \Log::info("NotificationService: Pokúšam sa o posledný fallback email", ['email' => $email]);
                $content = "Notifikácia z DiskretneDnes.sk\n\nNastala chyba pri spracovaní emailu.";
                $subject = 'Notifikácia - DiskretneDnes.sk';
                
                // OPRAVENÉ: Používame AdminNotificationHelper
                AdminNotificationHelper::sendHtmlToEmail($content, $subject, $email);
                \Log::info("NotificationService: Posledný fallback email úspešne odoslaný cez AdminNotificationHelper", ['email' => $email]);
            } catch (\Exception $fallbackError) {
                \Log::error("NotificationService: Aj fallback email zlyhal", [
                    'email' => $email,
                    'fallback_error' => $fallbackError->getMessage(),
                    'fallback_trace' => $fallbackError->getTraceAsString()
                ]);
            }
            
            return false;
        }
    }

    /**
     * Pošle email template všetkým adminom
     */
    private function sendTemplateEmailToAdmins(string $templateKey, array $data = [])
    {
        // Použiť nové helper funkcie pre admin notifikácie
        $adminEmails = get_admin_emails();
        foreach ($adminEmails as $email) {
            $this->sendTemplateEmail($templateKey, $email, $data);
        }
    }

    /**
     * Pošle email z kontaktného formulára pomocou template systému
     */
    public function sendContactFormEmail(array $contactData)
    {
        \Log::info("NotificationService: Sending contact form email", [
            'from' => $contactData['email'],
            'subject' => $contactData['subject']
        ]);

        // Pošli email na všetky admin adresy
        $adminEmails = get_admin_emails();
        foreach ($adminEmails as $email) {
            $this->sendTemplateEmail('contact', $email, [
                'name' => $contactData['name'],
                'email' => $contactData['email'],
                'subject' => $contactData['subject'],
                'messageContent' => $contactData['message'],
                'submittedAt' => now()->format('d.m.Y H:i:s')
            ]);
        }
    }

    private function removeEmojis($text)
    {
        // Odstráni všetky emoji a špeciálne znaky
        return preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1FA70}-\x{1FAFF}\x{1F018}-\x{1F270}\x{238C}-\x{2454}\x{20D0}-\x{20FF}]/u', '', $text);
    }
} 