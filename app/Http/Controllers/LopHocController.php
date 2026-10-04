<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use Illuminate\Http\Request;

class LopHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|boolean',
            'si_so_min' => 'nullable|integer|min:1',
            'si_so_max' => 'nullable|integer|min:1'.($request->filled('si_so_min') ? '|gte:si_so_min' : ''),
            'sort' => 'nullable|in:id,ten_lop,ma_lop,si_so,giao_vien,trang_thai',
            'direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|in:5,10,20',
        ]);
        $query = LopHoc::query();
        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('ten_lop', 'like', '%'.$search.'%')
                    ->orWhere('ma_lop', 'like', '%'.$search.'%')
                    ->orWhere('giao_vien', 'like', '%'.$search.'%')
                    ->orWhere('so_dien_thoai_gvien', 'like', '%'.$search.'%');
            });
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $filters['trang_thai']);
        }
        if ($request->filled('si_so_min')) {
            $query->where('si_so', '>=', $filters['si_so_min']);
        }
        if ($request->filled('si_so_max')) {
            $query->where('si_so', '<=', $filters['si_so_max']);
        }
        $sort = $filters['sort'] ?? 'id';
        $query->orderBy($sort, $filters['direction'] ?? 'asc');
        if ($sort !== 'id') {
            $query->orderBy('id');
        }
        $lopHocs = $query->paginate((int) ($filters['per_page'] ?? 10))->withQueryString();

        return view('lop-hocs.index', compact('lopHocs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('lop-hocs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'ten_lop' => 'required|string|max:255',
            'ma_lop' => 'required|string|max:6|unique:lop_hocs,ma_lop',
            'si_so' => 'required|integer|min:1',
            'giao_vien' => 'required|string|max:255',
            'so_dien_thoai_gvien' => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'ghi_chu' => 'nullable|string|max:500',
            'trang_thai' => 'nullable|boolean',
        ]);
        /*$lophoc = new LopHoc();
        $lophoc->ten_lop = $request->input('ten_lop');
        $lophoc->ma_lop = $request->input('ma_lop');
        $lophoc->si_so = $request->input('si_so');
        $lophoc->giao_vien = $request->input('giao_vien');
        $lophoc->so_dien_thoai_gvien = $request->input('so_dien_thoai_gvien');
        $lophoc->ghi_chu = $request->input('ghi_chu');
        $lophoc->trang_thai = $request->input('trang_thai') === 'on' ? true : false;
        $lophoc->save();
        */
        try {

            $lophoc = LopHoc::create($validated);

            return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được tạo thành công.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra khi tạo lớp học. Vui lòng thử lại.']);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(LopHoc $lopHoc)
    {
        $sinhViens = $lopHoc->sinhViens()->orderBy('name')->orderBy('id')->paginate(10);

        return view('lop-hocs.show', compact('lopHoc', 'sinhViens'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $lopHoc = LopHoc::findOrFail($id);

        return view('lop-hocs.create', ['lopHoc' => $lopHoc]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LopHoc $lopHoc)
    {
        $validated = $request->validate([
            'ten_lop' => 'required|string|max:255',
            'ma_lop' => 'required|string|max:6|unique:lop_hocs,ma_lop,'.$lopHoc->id,
            'si_so' => 'required|integer|min:1',
            'giao_vien' => 'required|string|max:255',
            'so_dien_thoai_gvien' => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'ghi_chu' => 'nullable|string|max:500',
            'trang_thai' => 'nullable|boolean',
        ]);

        $lopHoc->update($validated);

        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LopHoc $lopHoc)
    {
        if ($lopHoc->sinhViens()->exists()) {
            return redirect()->route('lop-hocs.index')->withErrors([
                'error' => 'Không thể xóa lớp đang có sinh viên. Hãy chuyển sinh viên sang lớp khác hoặc xóa sinh viên trước.',
            ]);
        }
        $lopHoc->delete();

        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được xóa thành công.');
    }
}
