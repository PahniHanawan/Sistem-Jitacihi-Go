<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::orderBy('criterion_code')->get();
        return view('criteria.index', compact('criteria'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'criterion_code' => 'required|string|max:5|unique:criteria,criterion_code',
            'criterion_name' => 'required|string|max:50',
            'type' => 'required|in:benefit,cost',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Criterion::create($validated);

        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, Criterion $criterion)
    {
        $validated = $request->validate([
            'criterion_code' => 'required|string|max:5|unique:criteria,criterion_code,' . $criterion->criterion_id . ',criterion_id',
            'criterion_name' => 'required|string|max:50',
            'type' => 'required|in:benefit,cost',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $criterion->update($validated);

        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();
        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
