<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'trangthai' => 'nullable|boolean',
            'sort' => 'nullable|in:id,slug,tenhienthi,trangthai',
            'direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|in:5,10,20',
        ]);
        $query = Menu::query();
        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('slug', 'like', '%'.$search.'%')
                    ->orWhere('tenhienthi', 'like', '%'.$search.'%');
            });
        }
        if ($request->filled('trangthai')) {
            $query->where('trangthai', $filters['trangthai']);
        }
        $sort = $filters['sort'] ?? 'id';
        $query->orderBy($sort, $filters['direction'] ?? 'asc');
        if ($sort !== 'id') {
            $query->orderBy('id');
        }
        $menus = $query->paginate((int) ($filters['per_page'] ?? 10))->withQueryString();

        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.create');
    }

    public function store(Request $request)
    {
        Menu::create($this->validatedData($request));

        return redirect()->route('menus.index')->with('success', 'Menu đã được tạo thành công.');
    }

    public function edit(Menu $menu)
    {
        return view('menus.create', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $menu->update($this->validatedData($request, $menu));

        return redirect()->route('menus.index')->with('success', 'Menu đã được cập nhật thành công.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu đã được xóa thành công.');
    }

    private function validatedData(Request $request, ?Menu $menu = null): array
    {
        return $request->validate([
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('menus', 'slug')->ignore($menu)],
            'tenhienthi' => ['required', 'string', 'max:255'],
            'trangthai' => ['required', 'boolean'],
        ], [
            'slug.regex' => 'Slug chỉ gồm chữ thường không dấu, số và dấu gạch ngang giữa các từ.',
            'slug.unique' => 'Slug đã được sử dụng.',
            'slug.required' => 'Vui lòng nhập slug.',
            'tenhienthi.required' => 'Vui lòng nhập tên hiển thị.',
            'trangthai.required' => 'Vui lòng chọn trạng thái.',
        ]);
    }
}
