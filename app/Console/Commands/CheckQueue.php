<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;
use App\Models\EmailLog;
use Exception;

class CheckQueue extends Command
{
    protected $signature = 'queue:check-email
                            {--clear : Clear all pending jobs}
                            {--failed : Show failed jobs}
                            {--retry : Retry failed jobs}
                            {--worker : Start queue worker}
                            {--status : Show queue status}';
    
    protected $description = 'Check and manage email queue system';

    public function handle()
    {
        $this->info("📋 Queue Management - Email System");
        $this->line("==================================");
        
        if ($this->option('clear')) {
            return $this->clearQueue();
        }
        
        if ($this->option('failed')) {
            return $this->showFailedJobs();
        }
        
        if ($this->option('retry')) {
            return $this->retryFailedJobs();
        }
        
        if ($this->option('worker')) {
            return $this->startWorker();
        }
        
        if ($this->option('status')) {
            return $this->showStatus();
        }
        
        // Defaultne zobrazí všetky informácie
        $this->showQueueInfo();
        
        return 0;
    }
    
    private function showQueueInfo()
    {
        $this->info("📊 Queue Information:");
        $this->line("==================");
        
        // Základné informácie
        $queueDriver = config('queue.default');
        $this->line("Queue Driver: {$queueDriver}");
        
        if ($queueDriver === 'database') {
            $this->checkDatabaseQueue();
        } elseif ($queueDriver === 'redis') {
            $this->checkRedisQueue();
        } elseif ($queueDriver === 'sync') {
            $this->line("✅ Synchronous queue - emaily sa odosielaju okamžite");
        } else {
            $this->warn("⚠️  Neznámy queue driver: {$queueDriver}");
        }
        
        // Email Log štatistiky
        $this->line("");
        $this->info("📧 Email Log Statistics:");
        $this->line("=======================");
        
        try {
            $totalEmails = EmailLog::count();
            $todayEmails = EmailLog::whereDate('created_at', today())->count();
            $failedEmails = EmailLog::where('status', 'failed')->count();
            $sentEmails = EmailLog::where('status', 'sent')->count();
            
            $this->line("Total emails: {$totalEmails}");
            $this->line("Today emails: {$todayEmails}");
            $this->line("Sent emails: {$sentEmails}");
            $this->line("Failed emails: {$failedEmails}");
            
            if ($failedEmails > 0) {
                $this->warn("⚠️  {$failedEmails} failed emails found!");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Error reading email logs: " . $e->getMessage());
        }
        
        $this->line("");
        $this->info("🔧 Available Options:");
        $this->line("php artisan queue:check-email --clear    # Clear pending jobs");
        $this->line("php artisan queue:check-email --failed   # Show failed jobs");
        $this->line("php artisan queue:check-email --retry    # Retry failed jobs");
        $this->line("php artisan queue:check-email --worker   # Start queue worker");
        $this->line("php artisan queue:check-email --status   # Show detailed status");
    }
    
    private function checkDatabaseQueue()
    {
        $this->line("📊 Database Queue Status:");
        
        try {
            // Kontrola existencie tabuliek
            $jobsTable = DB::getSchemaBuilder()->hasTable('jobs');
            $failedJobsTable = DB::getSchemaBuilder()->hasTable('failed_jobs');
            
            if (!$jobsTable) {
                $this->error("❌ Table 'jobs' doesn't exist. Run: php artisan queue:table && php artisan migrate");
                return;
            }
            
            if (!$failedJobsTable) {
                $this->error("❌ Table 'failed_jobs' doesn't exist. Run: php artisan queue:failed-table && php artisan migrate");
                return;
            }
            
            // Počet jobov
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            $this->line("Pending jobs: {$pendingJobs}");
            $this->line("Failed jobs: {$failedJobs}");
            
            if ($pendingJobs > 0) {
                $this->warn("⚠️  {$pendingJobs} jobs waiting in queue!");
                $this->line("Run: php artisan queue:work to process them");
                
                // Zobrazíme prvých 5 jobov
                $jobs = DB::table('jobs')->limit(5)->get(['id', 'queue', 'payload', 'created_at']);
                
                $this->line("");
                $this->info("📋 Recent jobs:");
                foreach ($jobs as $job) {
                    $payload = json_decode($job->payload, true);
                    $className = $payload['displayName'] ?? 'Unknown';
                    $this->line("  #{$job->id} - {$className} - {$job->created_at}");
                }
            }
            
            if ($failedJobs > 0) {
                $this->error("❌ {$failedJobs} failed jobs found!");
                $this->line("Run: php artisan queue:check-email --failed to see details");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Error checking database queue: " . $e->getMessage());
        }
    }
    
    private function checkRedisQueue()
    {
        $this->line("📊 Redis Queue Status:");
        // TODO: Implementovať Redis queue kontrolu
        $this->warn("⚠️  Redis queue check not implemented yet");
    }
    
    private function clearQueue()
    {
        $this->info("🧹 Clearing queue...");
        
        try {
            if (config('queue.default') === 'database') {
                $count = DB::table('jobs')->count();
                DB::table('jobs')->delete();
                $this->info("✅ Cleared {$count} jobs from database queue");
            } else {
                $this->warn("⚠️  Queue clearing only supported for database driver");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Error clearing queue: " . $e->getMessage());
        }
        
        return 0;
    }
    
    private function showFailedJobs()
    {
        $this->info("💥 Failed Jobs:");
        $this->line("==============");
        
        try {
            if (config('queue.default') === 'database') {
                $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->limit(10)->get();
                
                if ($failedJobs->isEmpty()) {
                    $this->info("✅ No failed jobs found!");
                    return 0;
                }
                
                foreach ($failedJobs as $job) {
                    $payload = json_decode($job->payload, true);
                    $className = $payload['displayName'] ?? 'Unknown';
                    
                    $this->line("#{$job->id} - {$className}");
                    $this->line("  Failed: {$job->failed_at}");
                    $this->line("  Error: " . substr($job->exception, 0, 100) . "...");
                    $this->line("  ----");
                }
                
            } else {
                $this->warn("⚠️  Failed jobs check only supported for database driver");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Error showing failed jobs: " . $e->getMessage());
        }
        
        return 0;
    }
    
    private function retryFailedJobs()
    {
        $this->info("🔄 Retrying failed jobs...");
        
        try {
            $exitCode = Artisan::call('queue:retry', ['id' => ['all']]);
            
            if ($exitCode === 0) {
                $this->info("✅ All failed jobs have been queued for retry");
            } else {
                $this->error("❌ Error retrying failed jobs");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Error retrying failed jobs: " . $e->getMessage());
        }
        
        return 0;
    }
    
    private function startWorker()
    {
        $this->info("🚀 Starting queue worker...");
        $this->line("Press Ctrl+C to stop");
        $this->line("");
        
        // Spustíme queue worker
        $exitCode = Artisan::call('queue:work', [
            '--verbose' => true,
            '--tries' => 3,
            '--timeout' => 90,
            '--sleep' => 3,
            '--max-jobs' => 1000,
            '--max-time' => 3600,
        ]);
        
        return $exitCode;
    }
    
    private function showStatus()
    {
        $this->info("📊 Detailed Queue Status:");
        $this->line("==========================");
        
        // Konfigurácia
        $this->line("Queue Driver: " . config('queue.default'));
        $this->line("Queue Connection: " . config('queue.connections.' . config('queue.default')));
        
        // Mailable triedy s queue
        $this->line("");
        $this->info("📧 Mailable classes with queue:");
        $this->line("WelcomeMail - implements ShouldQueue");
        $this->line("PaymentCompletedMail - implements ShouldQueue");
        $this->line("SubscriptionExpiredMail - implements ShouldQueue");
        
        $this->line("");
        $this->info("📧 Mailable classes without queue:");
        $this->line("AdminNotificationMail - direct send");
        $this->line("AdUpdatedMail - direct send");
        $this->line("SupportTicketResponse - direct send");
        $this->line("PaymentInstructionsMail - direct send");
        $this->line("NewAdCreatedMail - direct send");
        $this->line("SubscriptionExpiringMail - direct send");
        
        // Proces informácie
        $this->line("");
        $this->info("🔧 Queue Worker Status:");
        
        // Skontrolujeme či beží queue worker
        if (function_exists('shell_exec')) {
            $processes = shell_exec('ps aux | grep "queue:work" | grep -v grep');
            if ($processes) {
                $this->info("✅ Queue worker is running");
                $this->line(trim($processes));
            } else {
                $this->warn("⚠️  No queue worker found running");
                $this->line("Start with: php artisan queue:work");
            }
        } else {
            $this->warn("⚠️  Cannot check queue worker status (shell_exec disabled)");
        }
        
        return 0;
    }
} 