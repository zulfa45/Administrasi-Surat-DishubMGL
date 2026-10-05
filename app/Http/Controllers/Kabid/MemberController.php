<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Tampilkan daftar anggota di bidang yang sama.
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->department_id) {
            return back()->with('error', 'Anda belum memiliki bidang.');
        }

        // Ambil semua user yang satu bidang, kecuali admin
        $members = User::where('department_id', $user->department_id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'karyawan');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('kabid.anggota.index', compact('members'));
    }
}
