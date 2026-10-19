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

        // Statistik Surat & Tugas
        $totalSurat = Assignment::where('department_id', $departmentId)
            ->whereNotNull('incoming_letter_id')
            ->count();

        $totalTugasLangsung = Assignment::where('department_id', $departmentId)
            ->whereNull('incoming_letter_id')
            ->count();
        
        $belumDisposisi = Assignment::where('department_id', $departmentId)
            ->whereNotNull('incoming_letter_id')
            ->whereNull('user_id')
            ->where('status', '!=', 'selesai')
            ->count();
            
        $sedangProses = Assignment::where('department_id', $departmentId)
            ->whereNotNull('user_id')
            ->whereIn('status', ['dibaca', 'dikerjakan', 'belum_dibaca', 'perlu_revisi'])
            ->count();
            
        $menungguVerifikasi = Assignment::where('department_id', $departmentId)
            ->where('status', 'menunggu_verifikasi_kabid')
            ->count();

        $selesai = Assignment::where('department_id', $departmentId)
            ->where('status', 'selesai')
            ->count();

        $recentLetters = Assignment::with(['incomingLetter', 'user', 'creator'])
            ->where('department_id', $departmentId)
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('kabid.dashboard', compact(
            'totalSurat',
            'totalTugasLangsung',
            'belumDisposisi',
            'sedangProses',
            'menungguVerifikasi',
            'selesai',
            'recentLetters'
        ));
    }
}
