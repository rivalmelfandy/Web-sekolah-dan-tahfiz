<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman dasar dashboard ustadz.
     */
    public function index(Request $request): View
    {
        return view('ustadz.dashboard', ['user' => $request->user()]);
    }
}
