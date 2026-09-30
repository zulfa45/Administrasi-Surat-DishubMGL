<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\IncomingLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Konversi file gambar menjadi format Base64 agar render DomPDF optimal.
     */
    protected function getBase64Image(?string $path): ?string
    {
        if ($path && file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }

    /**
     * Halaman Menu Laporan Surat Masuk dengan filter (Tahap U-14).
     */
    public function index(Request $request)
    {
        $departments = Department::where('status', 'active')->orderBy('name')->get();

        $query = IncomingLetter::with(['creator', 'assignments.department', 'assignments.user']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $deptId = $request->department_id;
            $query->whereHas('assignments', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $letters = $query->latest('tanggal_diterima')
                         ->latest('id')
                         ->paginate(15)
                         ->withQueryString();

        return view('laporan.index', compact('letters', 'departments'));
    }

    /**
     * Cetak Laporan PDF Surat Masuk dengan stream inline preview (Tahap U-14).
     */
    public function printPdf(Request $request)
    {
        $query = IncomingLetter::with(['creator', 'assignments.department', 'assignments.user']);

        $filterTexts = [];

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_sampai')) {
            $periodeText = \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_sampai)->translatedFormat('d M Y');
        } elseif ($request->filled('tanggal_mulai')) {
            $periodeText = 'Mulai ' . \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d M Y');
        } elseif ($request->filled('tanggal_sampai')) {
            $periodeText = 'Sampai ' . \Carbon\Carbon::parse($request->tanggal_sampai)->translatedFormat('d M Y');
        } else {
            $periodeText = 'Seluruh Periode';
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $filterTexts[] = 'Status: ' . ucfirst(str_replace('_', ' ', $request->status));
        }

        if ($request->filled('department_id')) {
            $deptId = $request->department_id;
            $query->whereHas('assignments', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
            $dept = Department::find($deptId);
            if ($dept) {
                $filterTexts[] = 'Bagian: ' . $dept->name;
            }
        }

        $letters = $query->latest('tanggal_diterima')->latest('id')->get();

        $kotaLogo   = $this->getBase64Image(public_path('images/kota.png'));
        $dishubLogo = $this->getBase64Image(public_path('images/logo.png'));

        $pdf = Pdf::loadView('pdf.laporan-surat', [
            'letters'     => $letters,
            'periodeText' => $periodeText,
            'filterInfo'  => implode(' | ', $filterTexts),
            'kotaLogo'    => $kotaLogo,
            'dishubLogo'  => $dishubLogo,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Surat-Masuk-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Cetak Lembar Disposisi PDF dengan stream inline preview (Tahap U-13).
     */
    public function printDispositionPdf(IncomingLetter $surat_masuk)
    {
        $surat_masuk->load([
            'creator',
            'assignments.user',
            'assignments.department'
        ]);

        $kotaLogo   = $this->getBase64Image(public_path('images/kota.png'));
        $dishubLogo = $this->getBase64Image(public_path('images/logo.png'));

        $pdf = Pdf::loadView('pdf.lembar-disposisi', [
            'letter'     => $surat_masuk,
            'kotaLogo'   => $kotaLogo,
            'dishubLogo' => $dishubLogo,
        ])->setPaper('a4', 'portrait');

        $filename = 'Lembar-Disposisi-' . Str::slug($surat_masuk->nomor_surat) . '.pdf';

        return $pdf->stream($filename);
    }
}
