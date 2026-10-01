<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Staf Loket.
     */
    public function index()
    {
        $today = now()->toDateString();

        $totalSurat    = IncomingLetter::count();
        $suratHariIni  = IncomingLetter::whereDate('tanggal_diterima', $today)
                            ->orWhereDate('created_at', $today)
                            ->count();
        $suratBaru     = IncomingLetter::where('status', 'baru')->count();
        $suratProses   = IncomingLetter::whereIn('status', ['didistribusikan', 'dalam_tindak_lanjut'])->count();
        $suratSelesai  = IncomingLetter::where('status', 'selesai')->count();
        $suratSegera   = IncomingLetter::whereIn('sifat', ['Sangat Segera', 'Segera', 'Penting'])
                            ->where('status', '!=', 'selesai')
                            ->count();

        // 6 Surat Masuk Terakhir
        $recentLetters = IncomingLetter::with(['creator', 'assignments.user', 'assignments.department'])
                            ->latest()
                            ->take(6)
                            ->get();

        return view('staf.dashboard', compact(
            'totalSurat',
            'suratHariIni',
            'suratBaru',
            'suratProses',
            'suratSelesai',
            'suratSegera',
            'recentLetters'
        ));
    }
}
