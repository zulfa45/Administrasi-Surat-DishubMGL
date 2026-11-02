<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers           = User::count();
        $totalDepartments     = Department::count();
        $totalSuratMasuk      = IncomingLetter::count();
        $totalTugasLangsung   = Assignment::whereNull('incoming_letter_id')->count();
        $tugasLangsungSelesai = Assignment::whereNull('incoming_letter_id')->where('status', 'selesai')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDepartments',
            'totalSuratMasuk',
            'totalTugasLangsung',
            'tugasLangsungSelesai'
        ));
    }
}
