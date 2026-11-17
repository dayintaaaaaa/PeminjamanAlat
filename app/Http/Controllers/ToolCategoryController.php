<?php

namespace App\Http\Controllers;

use App\Models\ToolCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ToolCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $categories = ToolCategory::withSum('tools', 'tools_qty')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('category_name', 'like', "%{$search}%")
                      ->orWhere('category_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('master.tools-category', compact('categories', 'perPage'));
    }

    public function store(Request $request)
    {
        $data = $request->validateWithBag('addCategory', [
            'code'        => ['required', 'string', 'max:50', 'unique:tool_categories,category_code'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        ToolCategory::create([
            'category_code' => $data['code'],
            'category_name' => $data['name'],
            'description'   => $data['description'] ?? null,
        ]);

        return redirect()->route('master.tools-category')
            ->with('success', 'Category successfully added.');
    }

    public function update(Request $request, ToolCategory $category)
    {
        // kode kategori tidak boleh diubah, jadi tidak divalidasi
        $data = $request->validateWithBag('editCategory', [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update([
            'category_name' => $data['name'],
            'description'   => $data['description'] ?? null,
        ]);

        return redirect()->route('master.tools-category')
            ->with('success', 'Category successfully updated.');
    }

    public function destroy(ToolCategory $category)
    {
        if ($category->tools()->exists()) {
            return redirect()->route('master.tools-category')
                ->with('error', 'Failed to delete this data. This category still has tools.');
        }

        try {
            $category->delete();
        } catch (QueryException $e) {
            return redirect()->route('master.tools-category')
                ->with('error', 'Failed to delete this data. Please try again.');
        }

        return redirect()->route('master.tools-category')
            ->with('success', 'Successfully deleted this data');
    }
}