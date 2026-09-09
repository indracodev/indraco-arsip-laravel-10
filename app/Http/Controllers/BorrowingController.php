<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\BorrowingLog;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = BorrowingLog::with(['archive.department', 'borrower', 'picGudang']);

        if ($user->isPicDept()) {
            $query->where('borrower_user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        // Get archives available for borrowing (status in_warehouse)
        $archivesQuery = Archive::where('status', 'in_warehouse');

        if ($user->isPicDept()) {
            $archivesQuery->where('department_id', $user->department_id);
        }

        $archives = $archivesQuery->get();
        $selectedArchiveId = $request->query('archive_id');

        return view('borrowings.create', compact('archives', 'selectedArchiveId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'archive_id' => 'required|exists:archives,id',
            'purpose' => 'required|string|max:500',
            'expected_return_date' => 'required|date|after:today',
        ]);

        $archive = Archive::findOrFail($validated['archive_id']);

        if ($archive->status !== 'in_warehouse') {
            return back()->with('error', 'Arsip dokumen saat ini tidak tersedia di gudang untuk dipinjam.');
        }

        BorrowingLog::create([
            'archive_id' => $archive->id,
            'borrower_user_id' => $user->id,
            'request_date' => now(),
            'expected_return_date' => $validated['expected_return_date'],
            'purpose' => $validated['purpose'],
            'status' => 'requested',
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', 'Permintaan peminjaman berkas arsip telah diajukan ke PIC Gudang.');
    }

    public function approve(BorrowingLog $borrowing)
    {
        if (!auth()->user()->isPicGudang() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $borrowing->update([
            'status' => 'approved',
            'pic_gudang_id' => auth()->id(),
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', 'Permintaan peminjaman disetujui. Silakan persiapkan berkas fisik untuk diserahkan.');
    }

    public function dispatch(BorrowingLog $borrowing)
    {
        if (!auth()->user()->isPicGudang() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $borrowing->update([
            'status' => 'dispatched',
            'borrow_date' => now(),
            'pic_gudang_id' => auth()->id(),
        ]);

        $borrowing->archive->update([
            'status' => 'borrowed',
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', 'Pengeluaran berkas disahkan! Status arsip diubah menjadi "Sedang Dipinjam".');
    }

    public function returnArchive(BorrowingLog $borrowing, Request $request)
    {
        if (!auth()->user()->isPicGudang() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $borrowing->update([
            'status' => 'returned',
            'actual_return_date' => now(),
            'notes' => $request->notes ?? 'Berkas fisik dikembalikan dalam kondisi baik.',
        ]);

        $borrowing->archive->update([
            'status' => 'in_warehouse',
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', 'Pengembalian berkas dikonfirmasi! Status arsip kembali "Tersimpan di Gudang".');
    }
}
