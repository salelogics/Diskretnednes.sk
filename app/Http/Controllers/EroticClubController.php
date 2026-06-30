<?php

namespace App\Http\Controllers;

use App\Models\EroticClub;
use Illuminate\Http\Request;

class EroticClubController extends Controller
{
    public function index()
    {
        $clubs = EroticClub::where('is_active', true)
            ->orderBy('position')
            ->get();

        return view('pages.erotic-clubs', compact('clubs'));
    }

    public function show($slug)
    {
        $club = EroticClub::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.erotic-club-detail', compact('club'));
    }
} 