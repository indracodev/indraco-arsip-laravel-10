<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\DestructionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DestructionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Expired or expiring soon archives
        $expiredArchives = Archive::whereNotNull('retention_expiry_date')
            ->where('status', '!=', 'destroyed')
            ->when($user->isPicDept(), fn($q) => $q->where('department_id', $user->department_id))
            ->with(['department', 'location.warehouse'])
            ->orderBy('retention_expiry_date', 'asc')
            ->get();

        // Destruction Logs history
        $destructionLogs = DestructionLog::with(['archive.department', 'proposedBy', 'approvedBy'])
            ->latest()
            ->paginate(10);

        return view('destructions.index', compact('expiredArchives', 'destructionLogs'));
    }

    public function proposeForm(Archive $archive)
    {
        $autoBapNumber = 'BAP/IND/' . date('Y') . '/' . str_pad($archive->id, 5, '0', STR_PAD_LEFT);
        return view('destructions.propose', compact('archive', 'autoBapNumber'));
    }

    public function propose(Request $request, Archive $archive)
    {
        if (!auth()->user()->isPicGudang() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'bap_number' => 'required|string|max:100',
            'destruction_date' => 'required|date',
            'method' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $certPath = null;
        if ($request->hasFile('certificate_file')) {
            $certPath = $request->file('certificate_file')->store('bap_certificates', 'public');
        }

        $archive->update([
            'status' => 'destroyed',
        ]);

        // Decrement capacity count of warehouse location if assigned
        if ($archive->warehouse_location_id) {
            $archive->location()->decrement('current_box_count');
        }

        DestructionLog::create([
            'archive_id' => $archive->id,
            'proposed_by_user_id' => auth()->id(),
            'approved_by_dept_pic_id' => auth()->id(),
            'bap_number' => $validated['bap_number'],
            'destruction_date' => $validated['destruction_date'],
            'method' => $validated['method'],
            'certificate_file' => $certPath,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('destructions.index')
            ->with('success', "Proses pemusnahan berkas ({$archive->title}) telah disahkan dengan No. BAP {$validated['bap_number']}.");
    }

    public function showBap(DestructionLog $destructionLog)
    {
        $destructionLog->load(['archive.department', 'proposedBy', 'approvedBy', 'archive.location']);
        return view('destructions.bap', compact('destructionLog'));
    }
}
