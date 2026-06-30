<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerReport;
use App\Services\NotificationService;

class CustomerReportController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        // Získanie reálnych dát z databázy - všetky nahlásené čísla
        $allReports = CustomerReport::getAggregatedReports();
        
        // Dynamické štatistiky pre bezpečnosť komunity
        $totalReportedNumbers = CustomerReport::distinct('phone_number')->count();
        $totalReports = CustomerReport::count();
        $dangerousNumbers = CustomerReport::selectRaw('phone_number, COUNT(*) as reports_count')
            ->groupBy('phone_number')
            ->havingRaw('COUNT(*) >= 20')
            ->count();

        return view('customer-report.index', compact('allReports', 'totalReportedNumbers', 'totalReports', 'dangerousNumbers'));
    }

    public function search(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        
        // Vyhľadávanie v databáze
        $foundReport = CustomerReport::searchPhoneNumber($phoneNumber);

        if ($foundReport) {
            $levelText = [
                'very_dangerous' => 'Veľmi nebezpečné',
                'dangerous' => 'Nebezpečné', 
                'suspicious' => 'Podozrivé'
            ];
            
            return response()->json([
                'phone_number' => $phoneNumber,
                'is_dangerous' => true,
                'found_number' => $foundReport['found_number'],
                'level' => $foundReport['level'],
                'reports_count' => $foundReport['reports_count'],
                'message' => "Nájdené číslo: {$foundReport['found_number']} - {$levelText[$foundReport['level']]} ({$foundReport['reports_count']} nahlásení)"
            ]);
        }
        
        return response()->json([
            'phone_number' => $phoneNumber,
            'is_dangerous' => false,
            'message' => 'Číslo nebolo nájdené v databáze nahlásených čísel.'
        ]);
    }

    public function report(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|min:10|max:15',
            'reason' => 'required|string|max:500'
        ], [
            'phone_number.required' => 'Telefónne číslo je povinné.',
            'phone_number.min' => 'Telefónne číslo musí mať aspoň 10 znakov.',
            'phone_number.max' => 'Telefónne číslo môže mať maximálne 15 znakov.',
            'reason.required' => 'Dôvod nahlásenia je povinný.',
            'reason.max' => 'Dôvod nahlásenia môže mať maximálne 500 znakov.',
        ]);

        // Uloženie nahlásenia do databázy
        $report = CustomerReport::create([
            'phone_number' => $request->phone_number,
            'reason' => $request->reason,
            'anonymous' => $request->has('anonymous'),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip()
        ]);

        // Vytvor notifikáciu pre adminov
        $this->notificationService->newCustomerReport($report);
        
        // Ak je AJAX request, vrátime JSON response pre modal
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ďakujeme za nahlásenie!',
                'details' => 'Číslo ' . $request->phone_number . ' bolo úspešne nahlásené. Pomáhate tak chrániť našu komunitu.'
            ]);
        }
        
        return redirect()->route('customer-report.index')->with('success', 'Číslo bolo úspešne nahlásené. Ďakujeme za upozornenie!');
    }
} 