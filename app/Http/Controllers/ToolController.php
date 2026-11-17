<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $tools = Tool::with('category')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('tools_name', 'like', "%{$search}%")
                        ->orWhere('tools_code', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('id_category_tools', $c))
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $categories = ToolCategory::orderBy('category_name')->get();

        return view('master.tools-data', compact('tools', 'perPage', 'categories'));
    }

    public function create()
    {
        $categories = ToolCategory::orderBy('category_name')->get();

        // kode berikutnya per kategori, untuk pratinjau di form
        $nextCodes = $categories->mapWithKeys(fn($c) => [$c->id => $this->generateCode($c)]);

        return view('master.add-tools', compact('categories', 'nextCodes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $category = ToolCategory::findOrFail($data['id_category_tools']);

        $photo = $request->hasFile('photo')
            ? $request->file('photo')->store('tools', 'public')
            : null;

        Tool::create([
            'id_category_tools' => $category->id,
            'tools_code'        => $this->generateCode($category),
            'tools_name'        => $data['tools_name'],
            'tools_qty'         => $data['tools_qty'],
            'available'         => $data['tools_qty'],   // alat baru: semua tersedia
            'tools_condition'   => $data['tools_condition'],
            'status'            => 'Available',
            'storage_location'  => $data['storage_location'] ?? null,
            'description'       => $data['description'] ?? null,
            'photo'             => $photo,
        ]);

        return redirect()->route('master.tools-data')
            ->with('success', 'Tool successfully added.');
    }

    public function edit(Tool $tool)
    {
        $categories = ToolCategory::orderBy('category_name')->get();

        return view('master.edit-tools', compact('tool', 'categories'));
    }

    public function update(Request $request, Tool $tool)
    {
        $data = $request->validate($this->rules());

        // jumlah alat yang sedang dipinjam = total - tersedia
        $borrowed = $tool->tools_qty - $tool->available;

        if ($data['tools_qty'] < $borrowed) {
            return back()->withInput()->withErrors([
                'tools_qty' => "Total qty cannot be lower than the amount currently borrowed ({$borrowed}).",
            ]);
        }

        // jumlah tersedia ikut bergeser sesuai selisih total
        $available = $data['tools_qty'] - $borrowed;

        $status = $tool->status === 'Maintenance'
            ? 'Maintenance'
            : ($available === 0 ? 'Borrowed' : 'Available');

        $photo = $tool->photo;

        if ($request->hasFile('photo')) {
            if ($photo) {
                Storage::disk('public')->delete($photo);
            }
            $photo = $request->file('photo')->store('tools', 'public');
        }

        // kode alat tidak berubah, walaupun kategorinya dipindah
        $tool->update([
            'id_category_tools' => $data['id_category_tools'],
            'tools_name'        => $data['tools_name'],
            'tools_qty'         => $data['tools_qty'],
            'available'         => $available,
            'tools_condition'   => $data['tools_condition'],
            'status'            => $status,
            'storage_location'  => $data['storage_location'] ?? null,
            'description'       => $data['description'] ?? null,
            'photo'             => $photo,
        ]);

        return redirect()->route('master.tools-data')
            ->with('success', 'Tool successfully updated.');
    }

    public function destroy(Tool $tool)
    {
        try {
            $tool->delete();
        } catch (QueryException $e) {
            // misalnya alat masih tercatat di peminjaman
            return redirect()->route('master.tools-data')
                ->with('error', 'Failed to delete this data. It is still used by other records.');
        }

        if ($tool->photo) {
            Storage::disk('public')->delete($tool->photo);
        }

        return redirect()->route('master.tools-data')
            ->with('success', 'Successfully deleted this data');
    }

    private function rules(): array
    {
        return [
            'id_category_tools' => ['required', 'exists:tool_categories,id'],
            'tools_name'        => ['required', 'string', 'max:255'],
            'tools_qty'         => ['required', 'integer', 'min:0'],
            'tools_condition'   => ['required', Rule::in(['Good', 'Fair', 'Damaged'])],
            'storage_location'  => ['nullable', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'photo'             => ['nullable', 'image', 'max:2048'],
        ];
    }

    // kode alat: kode kategori utuh + nomor urut, misalnya KATAKEL-1, KATAKEL-2
    private function generateCode(ToolCategory $category): string
    {
        $prefix = $category->category_code;
        $number = Tool::where('id_category_tools', $category->id)->count() + 1;

        // kalau ada alat yang pernah dihapus, lewati nomor yang sudah terpakai
        while (Tool::where('tools_code', $prefix . '-' . $number)->exists()) {
            $number++;
        }

        return $prefix . '-' . $number;
    }
}
