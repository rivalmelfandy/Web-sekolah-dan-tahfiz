<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman dasar dashboard admin.
     */
    public function index(Request $request): View
    {
        return view('admin.dashboard', ['user' => $request->user()]);
    }
}
