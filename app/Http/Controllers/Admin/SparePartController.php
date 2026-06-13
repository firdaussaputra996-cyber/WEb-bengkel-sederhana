<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SparePartController extends Controller
{
    public function index(): View
    {
        return view('admin.spareparts.index', [
            'spareParts' => SparePart::latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.spareparts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        SparePart::create($this->validated($request));

        return redirect()->route('admin.spareparts.index')->with('success', 'Suku cadang berhasil ditambahkan.');
    }

    public function edit(SparePart $sparepart): View
    {
        return view('admin.spareparts.edit', ['sparePart' => $sparepart]);
    }

    public function update(Request $request, SparePart $sparepart): RedirectResponse
    {
        $sparepart->update($this->validated($request));

        return redirect()->route('admin.spareparts.index')->with('success', 'Suku cadang berhasil diperbarui.');
    }

    public function destroy(SparePart $sparepart): RedirectResponse
    {
        $sparepart->delete();

        return back()->with('success', 'Suku cadang berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
