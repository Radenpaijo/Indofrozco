<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ShortcutController extends Controller
{
    /**
     * Tampilkan halaman panduan pintasan keyboard.
     */
    public function index(): View
    {
        return view('shortcuts.index');
    }
}
