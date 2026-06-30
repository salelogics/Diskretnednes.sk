<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReport;
use Illuminate\Http\Request;

class AdminCustomerReportsController extends Controller
{
    public function index(Request $request)
    {
        // Získať agregované reporty (zoskupené podľa telefónneho čísla)
        $query = CustomerReport::selectRaw('
                phone_number,
                COUNT(*) as reports_count,
                MIN(created_at) as first_report_date,
                MAX(created_at) as last_report_date
            ')
            ->groupBy('phone_number')
            ->orderByDesc('reports_count')
            ->orderByDesc('last_report_date');

        // Filtrovanie podľa telefónneho čísla
        if ($request->filled('phone_search')) {
            $query->where('phone_number', 'LIKE', '%' . $request->phone_search . '%');
        }

        // Filtrovanie podľa úrovne nebezpečenstva
        if ($request->filled('danger_level')) {
            $dangerLevel = $request->danger_level;
            if ($dangerLevel === 'very_dangerous') {
                $query->havingRaw('COUNT(*) >= 20');
            } elseif ($dangerLevel === 'dangerous') {
                $query->havingRaw('COUNT(*) >= 10 AND COUNT(*) < 20');
            } elseif ($dangerLevel === 'suspicious') {
                $query->havingRaw('COUNT(*) < 10');
            }
        }

        $reports = $query->paginate(20)->appends($request->query());

        // Pridať úroveň nebezpečenstva ku každému reportu
        $reports->getCollection()->transform(function ($report) {
            $report->danger_level = CustomerReport::getDangerLevel($report->reports_count);
            $report->first_report_date = \Carbon\Carbon::parse($report->first_report_date);
            $report->last_report_date = \Carbon\Carbon::parse($report->last_report_date);
            
            return $report;
        });

        // Štatistiky - opravené queries
        $totalReports = CustomerReport::count();
        $uniqueNumbers = CustomerReport::distinct('phone_number')->count();
        
        // Počet veľmi nebezpečných čísel (20+ nahlásení)
        $veryDangerous = CustomerReport::selectRaw('phone_number')
            ->groupBy('phone_number')
            ->havingRaw('COUNT(*) >= 20')
            ->get()
            ->count();
            
        // Počet nebezpečných čísel (10-19 nahlásení)
        $dangerous = CustomerReport::selectRaw('phone_number')
            ->groupBy('phone_number')
            ->havingRaw('COUNT(*) >= 10 AND COUNT(*) < 20')
            ->get()
            ->count();

        $stats = [
            'total_reports' => $totalReports,
            'unique_numbers' => $uniqueNumbers,
            'very_dangerous' => $veryDangerous,
            'dangerous' => $dangerous,
        ];

        return view('admin.customer-reports.index', compact('reports', 'stats'));
    }

    public function show($phoneNumber)
    {
        // Získať všetky reporty pre dané telefónne číslo
        $reports = CustomerReport::where('phone_number', $phoneNumber)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        if ($reports->isEmpty()) {
            abort(404, 'Žiadne reporty pre toto telefónne číslo neboli nájdené.');
        }

        $stats = [
            'total_reports' => $reports->count(),
            'first_report' => $reports->last()->created_at,
            'last_report' => $reports->first()->created_at,
            'danger_level' => CustomerReport::getDangerLevel($reports->count()),
            'unique_reasons' => $reports->pluck('reason')->unique()->values(),
        ];

        return view('admin.customer-reports.show', compact('reports', 'stats', 'phoneNumber'));
    }

    public function destroy($phoneNumber)
    {
        $deletedCount = CustomerReport::where('phone_number', $phoneNumber)->delete();

        return redirect()->route('admin.nahlasenia-zakaznikov.index')
            ->with('success', "Vymazaných {$deletedCount} nahlásení pre číslo {$phoneNumber}.");
    }
} 