<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Mail\SupportTicketResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class SupportTicketController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $tickets = SupportTicket::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.support-tickets.index', compact('tickets'));
    }

    public function adminIndex()
    {
        $tickets = SupportTicket::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.support-tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.support-tickets.show', compact('ticket'));
    }

    public function adminShow(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.support-tickets.show', compact('ticket'));
    }

    public function sendResponse(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'response_message' => 'required|string|min:10'
        ]);

        // Update ticket with admin response
        $ticket->update([
            'admin_response' => $request->response_message,
            'status' => 'answered',
            'responded_at' => now(),
            'responded_by' => auth()->id()
        ]);

        try {
            // OPRAVENÉ: Používame AdminNotificationHelper pre správne SMTP nastavenia
            $mailable = new SupportTicketResponse($ticket);
            \App\Helpers\AdminNotificationHelper::sendMailableToEmail($mailable, $ticket->email);
            
            Log::info('Support ticket response sent via AdminNotificationHelper', [
                'ticket_id' => $ticket->id,
                'customer_email' => $ticket->email,
                'mail_driver' => config('mail.default'),
                'smtp_host' => config('mail.mailers.smtp.host')
            ]);

            return redirect()->route('admin.support-tickets.show', $ticket)
                ->with('success', 'Odpoveď bola odoslaná zákazníkovi.');
        } catch (\Exception $e) {
            Log::error('Failed to send support ticket response', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.support-tickets.show', $ticket)
                ->with('error', 'Nepodarilo sa odoslať odpoveď. Skúste to znovu.');
        }
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed'
        ]);

        $ticket->update([
            'status' => $request->status
        ]);

        return redirect()->back()
            ->with('success', 'Status bol úspešne aktualizovaný.');
    }
}
