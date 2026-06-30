<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReport extends Model
{
    protected $fillable = [
        'phone_number',
        'reason',
        'anonymous',
        'user_id',
        'ip_address'
    ];

    protected $casts = [
        'anonymous' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Získanie úrovne nebezpečenstva na základe počtu nahlásení
    public static function getDangerLevel($reportsCount): string
    {
        if ($reportsCount >= 20) {
            return 'very_dangerous';
        } elseif ($reportsCount >= 10) {
            return 'dangerous';
        } else {
            return 'suspicious';
        }
    }

    // Získanie agregovaných dát pre zobrazenie
    public static function getAggregatedReports($limit = null)
    {
        $query = self::selectRaw('
                phone_number,
                COUNT(*) as reports_count,
                MIN(created_at) as first_report_date
            ')
            ->groupBy('phone_number')
            ->orderByDesc('reports_count')
            ->orderByDesc('first_report_date');
            
        if ($limit) {
            $query->limit($limit);
        }
            
        return $query->get()
            ->map(function ($report) {
                $date = \Carbon\Carbon::parse($report->first_report_date);
                return [
                    'number' => $report->phone_number,
                    'date' => $date->format('d.m.Y'),
                    'reports' => $report->reports_count,
                    'level' => self::getDangerLevel($report->reports_count)
                ];
            });
    }

    // Vyhľadávanie čísla
    public static function searchPhoneNumber($phoneNumber)
    {
        $reports = self::selectRaw('
                phone_number,
                COUNT(*) as reports_count
            ')
            ->where('phone_number', 'LIKE', "%{$phoneNumber}%")
            ->groupBy('phone_number')
            ->first();

        if ($reports) {
            return [
                'found_number' => $reports->phone_number,
                'reports_count' => $reports->reports_count,
                'level' => self::getDangerLevel($reports->reports_count)
            ];
        }

        return null;
    }
}
