<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('barangs')->latest()->paginate(15);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $kodeOtomatis = Category::buatKodeOtomatis();
        return view('categories.create', compact('kodeOtomatis'));
    }

    public function store(Request $permintaan)
    {
        if (!$permintaan->filled('code')) {
            $permintaan->merge(['code' => Category::buatKodeOtomatis()]);
        }

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:categories,code',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Category::create($permintaan->all());

        return redirect()->route('categories.index')->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $permintaan, Category $category)
    {
        $permintaan->merge(['code' => $category->code]);

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $category->update($permintaan->all());

        return redirect()->route('categories.index')->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->barangs()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori barang berhasil dihapus.');
    }
}
