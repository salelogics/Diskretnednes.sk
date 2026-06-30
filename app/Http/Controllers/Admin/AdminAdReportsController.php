<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdReport;
use Illuminate\Http\Request;

class AdminAdReportsController extends Controller
{
    public function index()
    {
        $adReports = AdReport::with(['ad' => function($query) {
            $query->with('user');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
        $stats = [
            'total_reports' => AdReport::count(),
            'pending_reports' => AdReport::where('status', 'pending')->count(),
            'reviewed_reports' => AdReport::where('status', 'reviewed')->count(),
            'resolved_reports' => AdReport::where('status', 'resolved')->count(),
            'dismissed_reports' => AdReport::where('status', 'dismissed')->count(),
        ];

        return view('admin.ad-reports.index', compact('adReports', 'stats'));
    }

    public function show($id)
    {
        $report = AdReport::with(['ad' => function($query) {
            $query->with('user');
        }])->findOrFail($id);
        
        // AJAX požiadavka - vrátiме HTML pre modal
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.ad-reports.partials.detail-modal', compact('report'))->render()
            ]);
        }
        
        // Štandardná stránka
        return view('admin.ad-reports.show', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $report = AdReport::findOrFail($id);
        
        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Nahlásenie bolo úspešne aktualizované.');
    }

    public function destroy($id)
    {
        $report = AdReport::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.nahlasenia-inzeratov.index')
            ->with('success', 'Nahlásenie bolo úspešne vymazané.');
    }
} 