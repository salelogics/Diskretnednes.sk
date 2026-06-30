<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Club;
use App\Models\Article;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'usersCount' => User::count(),
            'clubsCount' => Club::count(),
            'articlesCount' => Article::count(),
        ];

        return view('admin-page.dashboard', $data);
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin-page.users', compact('users'));
    }

    public function clubs()
    {
        $clubs = Club::paginate(10);
        return view('admin-page.clubs', compact('clubs'));
    }

    public function articles()
    {
        $articles = Article::paginate(10);
        return view('admin-page.articles', compact('articles'));
    }
} 