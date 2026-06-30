<?php

use App\Helpers\AdminNotificationHelper;
use App\Mail\AdminNotificationMail;

if (!function_exists('notify_admins')) {
    /**
     * Pošle notifikáciu na všetky admin adresy
     * 
     * @param string $title Nadpis notifikácie
     * @param string $message Obsah správy
     * @param string $type Typ notifikácie (info, success, warning, error)
     * @param string $priority Priorita (normal, high, urgent)
     * @param string|null $actionUrl URL pre akciu
     * @param string|null $actionText Text pre tlačidlo akcie
     */
    function notify_admins(string $title, string $message, string $type = 'info', string $priority = 'normal', ?string $actionUrl = null, ?string $actionText = null)
    {
        $mailable = new AdminNotificationMail($title, $message, $type, $priority, $actionUrl, $actionText);
        AdminNotificationHelper::sendToAdmins($mailable);
    }
}

if (!function_exists('get_admin_emails')) {
    /**
     * Vráti zoznam admin email adries z nastavení
     * 
     * @return array
     */
    function get_admin_emails(): array
    {
        return AdminNotificationHelper::getAdminEmails();
    }
}

if (!function_exists('send_simple_admin_notification')) {
    /**
     * Pošle jednoduchú text notifikáciu na admin adresy
     * 
     * @param string $subject Predmet emailu
     * @param string $message Obsah správy
     */
    function send_simple_admin_notification(string $subject, string $message)
    {
        AdminNotificationHelper::sendSimpleNotification($subject, $message);
    }
} 