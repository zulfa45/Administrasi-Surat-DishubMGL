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
        return view('surat-masuk.create');
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
            $path = $file->storeAs('surat-masuk', $filename, 'public');
            $validated['file_lampiran'] = $path;
        }

        $validated['status'] = 'baru';
        $validated['created_by'] = auth()->id();

        $letter = IncomingLetter::create($validated);

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
            if ($surat_masuk->file_lampiran && Storage::disk('public')->exists($surat_masuk->file_lampiran)) {
                Storage::disk('public')->delete($surat_masuk->file_lampiran);
            }

            $file = $request->file('file_lampiran');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat-masuk', $filename, 'public');
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
        // Hapus file lampiran jika ada di storage
        if ($surat_masuk->file_lampiran && Storage::disk('public')->exists($surat_masuk->file_lampiran)) {
            Storage::disk('public')->delete($surat_masuk->file_lampiran);
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
        if (!$surat_masuk->file_lampiran || !Storage::disk('public')->exists($surat_masuk->file_lampiran)) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($surat_masuk->file_lampiran);
        $mime = Storage::disk('public')->mimeType($surat_masuk->file_lampiran);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
