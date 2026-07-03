<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->search($request->input('search'))
            ->when($request->input('category'), fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($request->input('condition'), fn ($query, $condition) => $query->where('condition', $condition))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'borrowingDetails.borrowing']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $activeBorrowings = $product->borrowingDetails()
            ->whereHas('borrowing', fn ($query) => $query->where('status', 'dipinjam'))
            ->exists();

        if ($activeBorrowings) {
            return redirect()->route('products.index')
                ->with('error', 'Barang tidak dapat dihapus karena sedang dipinjam.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
