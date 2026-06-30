<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin-page.articles.index');
    }

    public function create()
    {
        return view('admin-page.articles.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementovať ukladanie článku
    }

    public function edit($id)
    {
        return view('admin-page.articles.edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implementovať aktualizáciu článku
    }

    public function destroy($id)
    {
        // TODO: Implementovať mazanie článku
    }
} 