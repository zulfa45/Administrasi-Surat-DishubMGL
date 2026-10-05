<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Kepala Bidang.
     */
    public function index()
    {
        $user = auth()->user();
        $departmentId = $user->department_id;

        if (!$departmentId) {
            return view('kabid.dashboard', [
                'error' => 'Anda belum terdaftar di bidang manapun. Silakan hubungi Administrator.'
            ]);
        }

        // Statistik
        $totalSurat = Assignment::where('department_id', $departmentId)->count();
        
        $belumDisposisi = Assignment::where('department_id', $departmentId)
            ->whereNull('user_id') // Belum ditugaskan ke karyawan
            ->count();
            
        $sedangProses = Assignment::where('department_id', $departmentId)
            ->whereNotNull('user_id')
            ->whereIn('status', ['dibaca', 'dikerjakan', 'belum_dibaca'])
            ->count();
            
        $menungguVerifikasi = Assignment::where('department_id', $departmentId)
            ->whereNotNull('user_id')
            ->where('status', 'menunggu_verifikasi_kabid') // We need to add this status
            ->count();

        $selesai = Assignment::where('department_id', $departmentId)
            ->where('status', 'selesai')
            ->count();

        $recentLetters = Assignment::with(['incomingLetter', 'user'])
            ->where('department_id', $departmentId)
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('kabid.dashboard', compact(
            'totalSurat',
            'belumDisposisi',
            'sedangProses',
            'menungguVerifikasi',
            'selesai',
            'recentLetters'
        ));
    }
}
