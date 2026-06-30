<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CheckExpiringSubscriptions extends Command
{
    protected $signature = 'notifications:check-expiring';
    protected $description = 'Kontrola expirujúcich predplatných a odoslanie notifikácií';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Kontrolujem expirujúce predplatné...');

        $notifications = $this->notificationService->checkExpiringSubscriptions();

        $this->info('Vytvorených ' . count($notifications) . ' notifikácií pre expirujúce predplatné.');

        // Vymazanie starých notifikácií
        $deleted = $this->notificationService->deleteExpiredNotifications();
        $this->info('Vymazaných ' . $deleted . ' expirovaných notifikácií.');

        return 0;
    }
} 