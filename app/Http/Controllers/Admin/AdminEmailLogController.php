<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminEmailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::query()->orderBy('created_at', 'desc');

        // Filtrovanie podľa typu emailu
        if ($request->filled('type') && $request->type !== 'all') {
            $query->ofType($request->type);
        }

        // Filtrovanie podľa statusu
        if ($request->filled('status') && $request->status !== 'all') {
            $query->withStatus($request->status);
        }

        // Vyhľadávanie podľa príjemcu
        if ($request->filled('search')) {
            $query->forRecipient($request->search);
        }

        // Filtrovanie podľa dátumu
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $emails = $query->paginate(20)->withQueryString();

        // Štatistiky
        $stats = [
            'total' => EmailLog::count(),
            'sent' => EmailLog::withStatus('sent')->count(),
            'failed' => EmailLog::withStatus('failed')->count(),
            'queued' => EmailLog::withStatus('queued')->count(),
            'today' => EmailLog::whereDate('created_at', today())->count(),
            'this_week' => EmailLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => EmailLog::whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->count(),
            'this_year' => EmailLog::whereYear('created_at', now()->year)->count(),
        ];

        // Typy emailov pre filter
        $types = [
            'all' => 'Všetky typy',
            'welcome' => 'Uvítacie emaily',
            'notification' => 'Notifikácie',
            'support' => 'Support tickets',
            'payment' => 'Platby',
            'ad_created' => 'Nové inzeráty',
            'ad_updated' => 'Aktualizácie inzerátov',
            'subscription_expiring' => 'Expirujúce predplatné',
            'subscription_expired' => 'Expirované predplatné',
            'admin_notification' => 'Admin notifikácie',
            'test' => 'Test emaily',
            'general' => 'Všeobecné emaily'
        ];

        return view('admin.email-log.index', compact('emails', 'stats', 'types'));
    }

    /**
     * Zobrazenie detailu emailu
     */
    public function show(EmailLog $emailLog)
    {
        return view('admin.email-log.show', compact('emailLog'));
    }

    /**
     * Vymazanie emailu z logu
     */
    public function destroy(EmailLog $emailLog)
    {
        $emailLog->delete();
        
        return redirect()->route('admin.email-log.index')
                        ->with('success', 'Email log bol vymazaný.');
    }

    /**
     * Hromadné vymazanie starých emailov
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'older_than' => 'required|integer|min:1|max:365'
        ]);

        $count = EmailLog::where('created_at', '<', now()->subDays($request->older_than))->count();
        EmailLog::where('created_at', '<', now()->subDays($request->older_than))->delete();

        return redirect()->route('admin.email-log.index')
                        ->with('success', "Vymazané {$count} starých email logov.");
    }

    /**
     * Vymazanie všetkých email logov
     */
    public function deleteAll(Request $request)
    {
        $count = \App\Models\EmailLog::count();
        \App\Models\EmailLog::truncate();
        return redirect()->route('admin.email-log.index')
            ->with('success', "Vymazaných všetkých {$count} email logov.");
    }

    /**
     * Export email logov
     */
    public function export(Request $request)
    {
        $query = EmailLog::query()->orderBy('created_at', 'desc');

        // Aplikovanie filtrov
        if ($request->filled('type') && $request->type !== 'all') {
            $query->ofType($request->type);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->withStatus($request->status);
        }
        if ($request->filled('search')) {
            $query->forRecipient($request->search);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $emails = $query->limit(1000)->get();

        $filename = 'email-log-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($emails) {
            $handle = fopen('php://output', 'w');
            
            // CSV hlavička
            fputcsv($handle, [
                'ID',
                'Príjemca',
                'Odosielateľ',
                'Predmet',
                'Typ',
                'Status',
                'Dátum odoslania',
                'Chyba'
            ]);

            // CSV riadky
            foreach ($emails as $email) {
                fputcsv($handle, [
                    $email->id,
                    $email->to_email,
                    $email->from_email,
                    $email->subject,
                    $email->type_name,
                    $email->status_name,
                    $email->sent_at ? $email->sent_at->format('d.m.Y H:i:s') : '',
                    $email->error_message
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
} 