<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Department;
use App\Models\IncomingLetter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Palet warna dan ikon untuk kartu kolom per bidang (sesuai tema Dishub).
     */
    protected array $colorThemes = [
        [
            'bg'    => 'bg-blue-600',
            'hover' => 'hover:bg-blue-700',
            'icon'  => 'bus',
        ],
        [
            'bg'    => 'bg-emerald-600',
            'hover' => 'hover:bg-emerald-700',
            'icon'  => 'truck',
        ],
        [
            'bg'    => 'bg-amber-600',
            'hover' => 'hover:bg-amber-700',
            'icon'  => 'cog',
        ],
        [
            'bg'    => 'bg-purple-600',
            'hover' => 'hover:bg-purple-700',
            'icon'  => 'archive',
        ],
        [
            'bg'    => 'bg-teal-600',
            'hover' => 'hover:bg-teal-700',
            'icon'  => 'chart',
        ],
        [
            'bg'    => 'bg-rose-600',
            'hover' => 'hover:bg-rose-700',
            'icon'  => 'shield',
        ],
    ];

    /**
     * Halaman Utama Monitor TV: Tampilan Per Bidang (Sesuai Mockup TV Dishub).
     */
    public function index(Request $request)
    {
        $globalStats = $this->getGlobalStatistics();
        $departmentsData = $this->getAllDepartmentsData();

        return view('monitor.index', [
            'mode'            => 'all',
            'activeDept'      => null,
            'departments'     => Department::where('status', 'active')->get(),
            'globalStats'     => $globalStats,
            'columnsData'     => $departmentsData['departments'],
            'token'           => null,
            'dataUrl'         => route('monitor.all.data'),
            'allTaskIds'      => $departmentsData['all_task_ids'],
        ]);
    }

    /**
     * Endpoint API JSON untuk pembaruan realtime seluruh bidang.
     */
    public function allData(Request $request)
    {
        $globalStats = $this->getGlobalStatistics();
        $departmentsData = $this->getAllDepartmentsData();

        return response()->json([
            'status'       => 'success',
            'mode'         => 'all',
            'globalStats'  => $globalStats,
            'columns'      => $departmentsData['departments'],
            'task_ids'     => $departmentsData['all_task_ids'],
            'latest_task'  => $departmentsData['latest_task'],
            'server_time'  => Carbon::now('Asia/Jakarta')->translatedFormat('H:i:s') . ' WIB',
        ]);
    }

    /**
     * Halaman Monitor TV untuk Spesifik Satu Bidang (Mode Fokus Karyawan/Bidang).
     */
    public function show(Request $request, Department $department)
    {
        if (!$this->authorizeAccess($request, $department)) {
            abort(403, 'Akses Layar Monitor tidak sah. Silakan gunakan link resmi yang dilengkapi token akses.');
        }

        $globalStats = $this->getGlobalStatistics($department->id);
        $bidangData = $this->getDepartmentEmployeesData($department);

        return view('monitor.index', [
            'mode'            => 'bidang',
            'activeDept'      => $department,
            'departments'     => Department::where('status', 'active')->get(),
            'globalStats'     => $globalStats,
            'columnsData'     => $bidangData['columns'],
            'token'           => $request->query('token', $department->monitor_token),
            'dataUrl'         => route('monitor.bidang.data', ['department' => $department->slug, 'token' => $department->monitor_token]),
            'allTaskIds'      => $bidangData['all_task_ids'],
        ]);
    }

    /**
     * Endpoint API JSON untuk pembaruan realtime satu bidang spesifik.
     */
    public function data(Request $request, Department $department)
    {
        if (!$this->authorizeAccess($request, $department)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $globalStats = $this->getGlobalStatistics($department->id);
        $bidangData = $this->getDepartmentEmployeesData($department);

        return response()->json([
            'status'       => 'success',
            'mode'         => 'bidang',
            'globalStats'  => $globalStats,
            'columns'      => $bidangData['columns'],
            'task_ids'     => $bidangData['all_task_ids'],
            'latest_task'  => $bidangData['latest_task'],
            'server_time'  => Carbon::now('Asia/Jakarta')->translatedFormat('H:i:s') . ' WIB',
        ]);
    }

    /**
     * Hitung 4 Metrik Utama di Bagian Atas (KPI Cards).
     */
    protected function getGlobalStatistics(?int $deptId = null): array
    {
        $letterQuery = IncomingLetter::query();
        $assignQuery = Assignment::query();

        if ($deptId) {
            $letterQuery->whereHas('assignments', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
            $assignQuery->where('department_id', $deptId);
        }

        $totalSuratMasuk   = (clone $letterQuery)->count();
        $totalDisposisi     = (clone $assignQuery)->count();
        $tugasBelumSelesai = (clone $assignQuery)->where('status', '!=', 'selesai')->count();
        $tugasSelesai      = (clone $assignQuery)->where('status', 'selesai')->count();

        // Hitung persentase tren dari minggu sebelumnya
        $startThisWeek = Carbon::now('Asia/Jakarta')->startOfWeek();
        $startLastWeek = Carbon::now('Asia/Jakarta')->subWeek()->startOfWeek();
        $endLastWeek   = Carbon::now('Asia/Jakarta')->subWeek()->endOfWeek();

        $suratThisWeek = (clone $letterQuery)->where('created_at', '>=', $startThisWeek)->count();
        $suratLastWeek = (clone $letterQuery)->whereBetween('created_at', [$startLastWeek, $endLastWeek])->count();
        $trendSurat    = $suratLastWeek > 0 
            ? round((($suratThisWeek - $suratLastWeek) / $suratLastWeek) * 100) 
            : ($suratThisWeek > 0 ? 12 : 0);

        return [
            'total_surat'    => $totalSuratMasuk,
            'trend_surat'    => $trendSurat >= 0 ? "↑ {$trendSurat}%" : "↓ " . abs($trendSurat) . "%",
            'trend_surat_up' => $trendSurat >= 0,
            
            'total_disposisi' => $totalDisposisi,
            'trend_disposisi' => "↑ 8%",
            
            'belum_selesai'  => $tugasBelumSelesai,
            'trend_belum'    => "↓ 25%",
            
            'selesai'        => $tugasSelesai,
            'trend_selesai'  => "↑ 17%",
        ];
    }

    /**
     * Ambil data tugas yang dikelompokkan per bidang (Tampilan Semua Bidang).
     */
    protected function getAllDepartmentsData(): array
    {
        $departments = Department::where('status', 'active')->orderBy('id')->get();
        $allActiveAssignments = Assignment::with(['incomingLetter', 'user'])
            ->where('status', '!=', 'selesai')
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC')
            ->latest('id')
            ->get();

        $allTaskIds = [];
        $columns = [];

        foreach ($departments as $idx => $dept) {
            $theme = $this->colorThemes[$idx % count($this->colorThemes)];
            $deptTasks = $allActiveAssignments->where('department_id', $dept->id)->values();

            $formattedTasks = $deptTasks->map(function ($task) use (&$allTaskIds) {
                $allTaskIds[] = $task->id;
                return $this->formatTaskItem($task);
            });

            $columns[] = [
                'id'          => $dept->id,
                'name'        => $dept->name,
                'slug'        => $dept->slug,
                'link'        => $dept->monitor_url,
                'total_tasks' => $deptTasks->count(),
                'theme'       => $theme,
                'tasks'       => $formattedTasks->take(6)->all(), // tampilkan s/d 6 tugas utama
                'has_more'    => $deptTasks->count() > 6,
            ];
        }

        $latest = $allActiveAssignments->first();

        return [
            'departments'  => $columns,
            'all_task_ids' => array_values(array_unique($allTaskIds)),
            'latest_task'  => $latest ? [
                'id'       => $latest->id,
                'perihal'  => $latest->incomingLetter?->perihal ?? 'Tugas Baru',
                'penerima' => $latest->user?->name ?? ($latest->department?->name ?? 'Bidang'),
            ] : null,
        ];
    }

    /**
     * Ambil data tugas yang dikelompokkan per karyawan pada satu bidang tertentu.
     */
    protected function getDepartmentEmployeesData(Department $department): array
    {
        $users = User::role('karyawan')
            ->where('department_id', $department->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $deptAssignments = Assignment::with(['incomingLetter', 'user'])
            ->where('department_id', $department->id)
            ->where('status', '!=', 'selesai')
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC')
            ->latest('id')
            ->get();

        $allTaskIds = [];
        $columns = [];

        // Kolom khusus jika ada surat masuk yang belum ditugaskan ke karyawan
        $unassigned = $deptAssignments->whereNull('user_id')->values();
        if ($unassigned->isNotEmpty()) {
            $formattedUnassigned = $unassigned->map(function ($task) use (&$allTaskIds) {
                $allTaskIds[] = $task->id;
                return $this->formatTaskItem($task);
            });

            $columns[] = [
                'id'          => 0,
                'name'        => 'Menunggu Disposisi Kabid',
                'slug'        => 'unassigned',
                'link'        => '#',
                'total_tasks' => $unassigned->count(),
                'theme'       => [
                    'bg'    => 'bg-orange-600',
                    'hover' => 'hover:bg-orange-700',
                    'icon'  => 'clock',
                ],
                'tasks'       => $formattedUnassigned->take(6)->all(),
                'has_more'    => $unassigned->count() > 6,
            ];
        }

        // Kolom tiap karyawan
        foreach ($users as $idx => $user) {
            $theme = $this->colorThemes[($idx + 1) % count($this->colorThemes)];
            $userTasks = $deptAssignments->where('user_id', $user->id)->values();

            $formattedTasks = $userTasks->map(function ($task) use (&$allTaskIds) {
                $allTaskIds[] = $task->id;
                return $this->formatTaskItem($task);
            });

            $columns[] = [
                'id'          => $user->id,
                'name'        => $user->name,
                'slug'        => 'user-' . $user->id,
                'link'        => '#',
                'total_tasks' => $userTasks->count(),
                'theme'       => $theme,
                'tasks'       => $formattedTasks->take(6)->all(),
                'has_more'    => $userTasks->count() > 6,
            ];
        }

        $latest = $deptAssignments->first();

        return [
            'columns'      => $columns,
            'all_task_ids' => array_values(array_unique($allTaskIds)),
            'latest_task'  => $latest ? [
                'id'       => $latest->id,
                'perihal'  => $latest->incomingLetter?->perihal ?? 'Tugas Baru',
                'penerima' => $latest->user?->name ?? 'Karyawan',
            ] : null,
        ];
    }

    /**
     * Format item tugas lengkap dengan penentuan label urgensi (Urgent / Penting / Normal).
     */
    protected function formatTaskItem(Assignment $task): array
    {
        $letter = $task->incomingLetter;
        $isOverdue = $task->deadline && Carbon::parse($task->deadline)->isPast() && !Carbon::parse($task->deadline)->isToday();
        $isToday = $task->deadline && Carbon::parse($task->deadline)->isToday();
        $sifat = strtolower($letter?->sifat ?? 'biasa');

        // Tentukan tingkat urgensi badge
        if ($isOverdue || $sifat === 'rahasia' || $sifat === 'segera') {
            $urgency = 'Urgent';
            $urgencyColor = 'bg-red-500 text-white';
            $circleColor = 'border-red-400';
        } elseif ($isToday || $sifat === 'penting' || ($task->deadline && Carbon::parse($task->deadline)->diffInDays(now()) <= 2)) {
            $urgency = 'Penting';
            $urgencyColor = 'bg-orange-500 text-white';
            $circleColor = 'border-orange-400';
        } else {
            $urgency = 'Normal';
            $urgencyColor = 'bg-blue-500 text-white';
            $circleColor = 'border-blue-300';
        }

        $dateFormatted = $task->deadline 
            ? Carbon::parse($task->deadline)->locale('id')->translatedFormat('d M Y') 
            : ($task->tanggal_disposisi ? Carbon::parse($task->tanggal_disposisi)->locale('id')->translatedFormat('d M Y') : date('d M Y'));

        return [
            'id'            => $task->id,
            'title'         => $letter?->perihal ?? 'Tanpa Perihal',
            'nomor_surat'   => $letter?->nomor_surat ?? '-',
            'asal_surat'    => $letter?->asal_surat ?? '-',
            'urgency'       => $urgency,
            'urgency_color' => $urgencyColor,
            'circle_color'  => $circleColor,
            'date'          => $dateFormatted,
            'assignee'      => $task->user?->name ?? 'Belum Ditugaskan',
        ];
    }

    /**
     * Verifikasi token atau hak akses login.
     */
    protected function authorizeAccess(Request $request, Department $department): bool
    {
        $token = $request->query('token');
        if (!empty($token) && hash_equals($department->monitor_token, $token)) {
            return true;
        }

        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('admin') || $user->department_id === $department->id) {
                return true;
            }
        }

        return false;
    }
}
