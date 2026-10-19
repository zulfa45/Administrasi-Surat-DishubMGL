<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Verifikasi akses token untuk monitor TV atau sesi login yang sah.
     */
    protected function authorizeAccess(Request $request, Department $department): bool
    {
        // 1. Cek token URL
        $token = $request->query('token');
        if (!empty($token) && hash_equals($department->monitor_token, $token)) {
            return true;
        }

        // 2. Cek apakah user sedang login dan memiliki hak akses
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('admin') || $user->department_id === $department->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tampilkan halaman utama Layar Monitor TV Bidang (Fullscreen To-Do List).
     */
    public function show(Request $request, Department $department)
    {
        if (!$this->authorizeAccess($request, $department)) {
            abort(403, 'Akses Layar Monitor tidak sah. Silakan gunakan link resmi yang dilengkapi token akses.');
        }

        $data = $this->getMonitorData($department);

        return view('monitor.index', [
            'department'  => $department,
            'members'     => $data['members'],
            'unassigned'  => $data['unassigned'],
            'counts'      => $data['counts'],
            'token'       => $request->query('token', $department->monitor_token),
        ]);
    }

    /**
     * Endpoint API JSON untuk pembaruan realtime & deteksi tugas baru (Polling).
     */
    public function data(Request $request, Department $department)
    {
        if (!$this->authorizeAccess($request, $department)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $this->getMonitorData($department);

        return response()->json([
            'status'     => 'success',
            'department' => [
                'id'   => $department->id,
                'name' => $department->name,
                'slug' => $department->slug,
            ],
            'server_time' => Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y - H:i:s') . ' WIB',
            'counts'      => $data['counts'],
            'members'     => $data['members'],
            'unassigned'  => $data['unassigned'],
            'task_ids'    => $data['all_task_ids'],
            'latest_task' => $data['latest_task'],
        ]);
    }

    /**
     * Helper untuk mengambil dan memformat seluruh data monitor bidang.
     */
    protected function getMonitorData(Department $department): array
    {
        // Karyawan aktif dalam bidang ini
        $users = User::role('karyawan')
            ->where('department_id', $department->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Seluruh penugasan aktif (belum selesai) untuk bidang ini
        $activeAssignments = Assignment::with(['incomingLetter', 'user'])
            ->where('department_id', $department->id)
            ->where('status', '!=', 'selesai')
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC')
            ->latest('id')
            ->get();

        // Surat yang belum didisposisikan ke karyawan oleh Kabid
        $unassigned = $activeAssignments->whereNull('user_id')->values()->map(function ($task) {
            return $this->formatTaskData($task);
        });

        // Pengelompokan tugas per karyawan
        $membersData = $users->map(function ($user) use ($activeAssignments) {
            $userTasks = $activeAssignments->where('user_id', $user->id)->values()->map(function ($task) {
                return $this->formatTaskData($task);
            });

            return [
                'id'       => $user->id,
                'name'     => $user->name,
                'nip'      => $user->nip ?: '-',
                'jabatan'  => $user->jabatan ?: 'Staf Pelaksana',
                'avatar'   => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'tasks'    => $userTasks,
                'total'    => $userTasks->count(),
            ];
        });

        // Statistik metrik ringkas
        $counts = [
            'total'               => $activeAssignments->count(),
            'belum_dibaca'        => $activeAssignments->where('status', 'belum_dibaca')->count(),
            'dikerjakan'          => $activeAssignments->whereIn('status', ['dibaca', 'dikerjakan', 'perlu_revisi'])->count(),
            'menunggu_verifikasi' => $activeAssignments->where('status', 'menunggu_verifikasi_kabid')->count(),
            'belum_diteruskan'    => $unassigned->count(),
            'mendekati_tenggat'   => $activeAssignments->filter(function ($a) {
                return $a->deadline && Carbon::parse($a->deadline)->isPast() || ($a->deadline && Carbon::parse($a->deadline)->isToday());
            })->count(),
        ];

        $latestTask = $activeAssignments->first();

        return [
            'members'      => $membersData,
            'unassigned'   => $unassigned,
            'counts'       => $counts,
            'all_task_ids' => $activeAssignments->pluck('id')->values()->all(),
            'latest_task'  => $latestTask ? [
                'id'           => $latestTask->id,
                'nomor_surat'  => $latestTask->incomingLetter?->nomor_surat,
                'perihal'      => $latestTask->incomingLetter?->perihal,
                'penerima'     => $latestTask->user?->name ?? 'Belum Ditunjuk',
                'created_at'   => $latestTask->created_at?->diffForHumans(),
            ] : null,
        ];
    }

    /**
     * Format struktur data satu tugas agar rapi dan ringan.
     */
    protected function formatTaskData(Assignment $task): array
    {
        $letter = $task->incomingLetter;
        $isOverdue = $task->deadline && Carbon::parse($task->deadline)->isPast() && !Carbon::parse($task->deadline)->isToday();
        $isToday = $task->deadline && Carbon::parse($task->deadline)->isToday();

        return [
            'id'             => $task->id,
            'status'         => $task->status,
            'status_label'   => strtoupper(str_replace('_', ' ', $task->status)),
            'nomor_surat'    => $letter?->nomor_surat ?? '-',
            'asal_surat'     => $letter?->asal_surat ?? '-',
            'perihal'        => $letter?->perihal ?? '-',
            'sifat'          => $letter?->sifat ?? 'biasa',
            'catatan_kabid'  => $task->catatan_kabid ?: $task->catatan,
            'deadline'       => $task->deadline ? Carbon::parse($task->deadline)->locale('id')->translatedFormat('d M Y') : 'Tanpa Tenggat',
            'is_overdue'     => $isOverdue,
            'is_today'       => $isToday,
            'assigned_at'    => $task->tanggal_disposisi ? Carbon::parse($task->tanggal_disposisi)->locale('id')->translatedFormat('d M Y') : '-',
        ];
    }
}
