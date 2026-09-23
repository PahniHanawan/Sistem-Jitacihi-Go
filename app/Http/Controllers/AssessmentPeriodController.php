<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use Illuminate\Http\Request;

class AssessmentPeriodController extends Controller
{
    public function index()
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        return view('periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:aktif,selesai',
        ]);

        if ($validated['status'] === 'aktif') {
            AssessmentPeriod::where('status', 'aktif')->update(['status' => 'selesai']);
        }

        AssessmentPeriod::create($validated);

        return redirect()->route('periods.index')->with('success', 'Periode Penilaian berhasil ditambahkan.');
    }

    public function edit(AssessmentPeriod $period)
    {
        return view('periods.edit', compact('period'));
    }

    public function update(Request $request, AssessmentPeriod $period)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:aktif,selesai',
        ]);

        if ($validated['status'] === 'aktif') {
            AssessmentPeriod::where('period_id', '!=', $period->period_id)
                ->where('status', 'aktif')
                ->update(['status' => 'selesai']);
        }

        $period->update($validated);

        return redirect()->route('periods.index')->with('success', 'Periode Penilaian berhasil diperbarui.');
    }

    public function destroy(AssessmentPeriod $period)
    {
        $period->delete();
        return redirect()->route('periods.index')->with('success', 'Periode Penilaian berhasil dihapus.');
    }
}
