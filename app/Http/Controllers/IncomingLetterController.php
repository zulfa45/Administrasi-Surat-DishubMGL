<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncomingLetterController extends Controller
{
    /**
     * Tampilkan daftar surat masuk dengan filter pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $query = IncomingLetter::with(['creator', 'assignments']);

        // Filter berdasarkan kata kunci (nomor surat, asal surat, perihal)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('asal_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter sifat surat
        if ($request->filled('sifat')) {
            $query->where('sifat', $request->sifat);
        }

        // Filter rentang tanggal diterima
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        $letters = $query->latest('tanggal_diterima')
                         ->latest('id')
                         ->paginate(10)
                         ->withQueryString();

        return view('surat-masuk.index', compact('letters'));
    }

    /**
     * Tampilkan formulir tambah surat masuk baru.
     */
    public function create()
    {
        $departments = \App\Models\Department::where('status', 'active')->get();
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['karyawan', 'staf-loket', 'admin']);
        })->get();

        return view('surat-masuk.create', compact('departments', 'users'));
    }

    /**
     * Simpan data surat masuk baru ke database beserta file lampiran.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat'      => 'required|string|max:255|unique:incoming_letters,nomor_surat',
            'asal_surat'       => 'required|string|max:255',
            'perihal'          => 'required|string|max:255',
            'tanggal_surat'    => 'required|date',
            'tanggal_diterima' => 'required|date',
            'sifat'            => 'required|in:biasa,penting,segera,rahasia',
            'keterangan'       => 'nullable|string',
            'file_lampiran'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Direct disposition fields
            'tujuan_tipe'      => 'nullable|in:none,department,user',
            'department_id'    => 'nullable|required_if:tujuan_tipe,department|exists:departments,id',
            'user_id'          => 'nullable|required_if:tujuan_tipe,user|exists:users,id',
            'catatan_disposisi'=> 'nullable|string',
            'deadline'         => 'nullable|date|after_or_equal:today',
        ], [
            'nomor_surat.required'      => 'Nomor surat wajib diisi.',
            'nomor_surat.unique'        => 'Nomor surat sudah terdaftar di sistem.',
            'asal_surat.required'       => 'Asal surat wajib diisi.',
            'perihal.required'          => 'Perihal surat wajib diisi.',
            'tanggal_surat.required'    => 'Tanggal surat wajib diisi.',
            'tanggal_diterima.required' => 'Tanggal diterima wajib diisi.',
            'sifat.required'            => 'Sifat surat wajib dipilih.',
            'file_lampiran.mimes'       => 'File lampiran harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_lampiran.max'         => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = 'surat-masuk/' . $filename;
            Storage::disk('google')->put($path, file_get_contents($file->getRealPath()));
            $validated['file_lampiran'] = $path;
        }

        // Generate Nomor Agenda (Format: AG-YYYYMMDD-ID)
        $today = now()->format('Ymd');
        $lastLetter = \App\Models\IncomingLetter::whereDate('created_at', now()->toDateString())->orderBy('id', 'desc')->first();
        $sequence = $lastLetter ? intval(substr($lastLetter->nomor_agenda, -4)) + 1 : 1;
        $validated['nomor_agenda'] = 'AG-' . $today . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $validated['status'] = 'baru';
        $validated['created_by'] = auth()->id();

        $letter = \App\Models\IncomingLetter::create(collect($validated)->except(['tujuan_tipe', 'department_id', 'user_id', 'catatan_disposisi', 'deadline'])->toArray());

        // Process Direct Disposition if selected
        if ($request->tujuan_tipe && $request->tujuan_tipe !== 'none') {
            $assignmentData = [
                'incoming_letter_id' => $letter->id,
                'tanggal_disposisi'  => now()->toDateString(),
                'status'             => 'belum_dibaca',
                'catatan'            => $request->catatan_disposisi,
                'deadline'           => $request->deadline,
            ];

            if ($request->tujuan_tipe === 'user') {
                $assignmentData['user_id'] = $request->user_id;
            } else if ($request->tujuan_tipe === 'department') {
                $assignmentData['department_id'] = $request->department_id;
            }

            $assignment = \App\Models\Assignment::create($assignmentData);
            
            if ($request->tujuan_tipe === 'department') {
                $letter->update(['status' => 'menunggu_disposisi_kabid']);
            } else {
                $letter->update(['status' => 'didistribusikan']);
            }

            // Send Notifications
            if (isset($assignmentData['user_id'])) {
                $recipient = \App\Models\User::find($assignmentData['user_id']);
                if ($recipient) {
                    $recipient->notify(new \App\Notifications\DispositionNotification($assignment));
                }
            } elseif (isset($assignmentData['department_id'])) {
                $deptMembers = \App\Models\User::where('department_id', $assignmentData['department_id'])->get();
                foreach ($deptMembers as $member) {
                    $member->notify(new \App\Notifications\DispositionNotification($assignment));
                }
            }
        }

        return redirect()->route('surat-masuk.show', $letter)
            ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail surat masuk beserta riwayat disposisi.
     */
    public function show(IncomingLetter $surat_masuk)
    {
        $surat_masuk->load([
            'creator',
            'assignments.user',
            'assignments.department'
        ]);

        return view('surat-masuk.show', [
            'letter' => $surat_masuk,
        ]);
    }

    /**
     * Tampilkan formulir edit surat masuk.
     */
    public function edit(IncomingLetter $surat_masuk)
    {
        return view('surat-masuk.edit', [
            'letter' => $surat_masuk,
        ]);
    }

    /**
     * Perbarui data surat masuk dan ganti file lampiran jika diunggah file baru.
     */
    public function update(Request $request, IncomingLetter $surat_masuk)
    {
        $validated = $request->validate([
            'nomor_surat'      => 'required|string|max:255|unique:incoming_letters,nomor_surat,' . $surat_masuk->id,
            'asal_surat'       => 'required|string|max:255',
            'perihal'          => 'required|string|max:255',
            'tanggal_surat'    => 'required|date',
            'tanggal_diterima' => 'required|date',
            'sifat'            => 'required|in:biasa,penting,segera,rahasia',
            'keterangan'       => 'nullable|string',
            'file_lampiran'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nomor_surat.required'      => 'Nomor surat wajib diisi.',
            'nomor_surat.unique'        => 'Nomor surat sudah terdaftar di sistem.',
            'asal_surat.required'       => 'Asal surat wajib diisi.',
            'perihal.required'          => 'Perihal surat wajib diisi.',
            'tanggal_surat.required'    => 'Tanggal surat wajib diisi.',
            'tanggal_diterima.required' => 'Tanggal diterima wajib diisi.',
            'sifat.required'            => 'Sifat surat wajib dipilih.',
            'file_lampiran.mimes'       => 'File lampiran harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_lampiran.max'         => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        if ($request->hasFile('file_lampiran')) {
            // Hapus file lama jika ada
            if ($surat_masuk->file_lampiran && Storage::disk('google')->exists($surat_masuk->file_lampiran)) {
                Storage::disk('google')->delete($surat_masuk->file_lampiran);
            }

            $file = $request->file('file_lampiran');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = 'surat-masuk/' . $filename;
            Storage::disk('google')->put($path, file_get_contents($file->getRealPath()));
            $validated['file_lampiran'] = $path;
        }

        $surat_masuk->update($validated);

        return redirect()->route('surat-masuk.show', $surat_masuk)
            ->with('success', 'Data surat masuk berhasil diperbarui.');
    }

    /**
     * Hapus surat masuk beserta file lampirannya.
     */
    public function destroy(IncomingLetter $surat_masuk)
    {
        // Hapus file lampiran jika ada di storage (G-Drive atau S3)
        if ($surat_masuk->file_lampiran) {
            if (Storage::disk('google')->exists($surat_masuk->file_lampiran)) {
                Storage::disk('google')->delete($surat_masuk->file_lampiran);
            } elseif (Storage::disk('s3')->exists($surat_masuk->file_lampiran)) {
                Storage::disk('s3')->delete($surat_masuk->file_lampiran);
            }
        }

        $surat_masuk->delete();

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }

    /**
     * Buka atau unduh file lampiran surat secara aman.
     */
    public function previewFile(IncomingLetter $surat_masuk)
    {
        if (!$surat_masuk->file_lampiran) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        // Cek di Google Drive dulu (untuk file baru)
        if (Storage::disk('google')->exists($surat_masuk->file_lampiran)) {
            return Storage::disk('google')->response($surat_masuk->file_lampiran);
        }
        
        // Jika tidak ada di G-Drive, cari di S3 (file lama sebelum fix)
        if (Storage::disk('s3')->exists($surat_masuk->file_lampiran)) {
            return redirect(Storage::disk('s3')->url($surat_masuk->file_lampiran));
        }

        abort(404, 'File lampiran tidak ditemukan di penyimpanan mana pun.');
    }
}
