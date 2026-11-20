<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman dasar dashboard siswa.
     */
    public function index(Request $request): View
    {
        return view('siswa.dashboard', ['user' => $request->user()]);
    }
}
