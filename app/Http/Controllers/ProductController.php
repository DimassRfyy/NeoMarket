<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('creator_id', Auth::id())->get();
        return view('admin.products.index', [
            'products' => $products
        ]);
    }

    /** 
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cover' => ['required', 'image', 'mimes:png,jpg,jpeg,webp'],
            'path_file' => ['required', 'file', 'mimes:zip,rar,pdf,docx'],
            'about' => ['required', 'string', 'max:65535'],
            'category_id' => ['required', 'integer',],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover')) {
                $coverpath = $request->file('cover')->store('product_covers', 'public');
                $validated['cover'] = $coverpath;
            }
            if ($request->hasFile('path_file')) {
                $path_filepath = $request->file('path_file')->store('product_files', 'public');
                $validated['path_file'] = $path_filepath;
            }
            $validated['slug'] = Str::slug($request->name);
            $validated['creator_id'] = Auth::id(); //siapa yang upload (creator)
            $newProduct = Product::create($validated);

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            DB::rollBack(); // utk tdk ada kecacatan data

            $error = ValidationException::withMessages([
                'system_error' => ['System Error!' . $e->getMessage()]
            ]);

            throw $error;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {   
        $categories = Category::all();

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cover' => ['sometimes', 'image', 'mimes:png,jpg,jpeg,webp'],
            'path_file' => ['sometimes', 'file', 'mimes:zip,rar,pdf,docx'],
            'about' => ['required', 'string', 'max:65535'],
            'category_id' => ['required', 'integer',],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover')) {
                $coverpath = $request->file('cover')->store('product_covers', 'public');
                $validated['cover'] = $coverpath;
            }
            if ($request->hasFile('path_file')) {
                $path_filepath = $request->file('path_file')->store('product_files', 'public');
                $validated['[path_file]'] = $path_filepath;
            }
            $validated['slug'] = Str::slug($request->name);
            $validated['creator_id'] = Auth::id(); //siapa yang upload (creator)

            $product->update($validated);
            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            DB::rollBack(); // utk tdk ada kecacatan data

            $error = ValidationException::withMessages([
                'system_error' => ['System Error!' . $e->getMessage()]
            ]);

            throw $error;
        }    
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Product Deleted Successfully');
        }
        catch(\Exception $e){

            $error = ValidationException::withMessages([
                'system_error' => ['System Error!' . $e->getMessage()]
            ]);

            throw $error;
        }
    }
}
