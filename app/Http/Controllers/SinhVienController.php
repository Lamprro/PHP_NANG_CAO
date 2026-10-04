<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use App\Models\SinhVien;
use Illuminate\Http\Request;

class SinhVienController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'lop_hoc_id' => 'nullable|integer|exists:lop_hocs,id',
            'age_min' => 'nullable|integer|min:1|max:120',
            'age_max' => 'nullable|integer|min:1|max:120'.($request->filled('age_min') ? '|gte:age_min' : ''),
            'sort' => 'nullable|in:id,name,age',
            'direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|in:5,10,20',
        ]);
        $query = SinhVien::with('lopHoc');
        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('lopHoc', function ($query) use ($search) {
                        $query->where('ten_lop', 'like', '%'.$search.'%')
                            ->orWhere('ma_lop', 'like', '%'.$search.'%');
                    });
            });
        }
        if ($request->filled('lop_hoc_id')) {
            $query->where('lop_hoc_id', $filters['lop_hoc_id']);
        }
        if ($request->filled('age_min')) {
            $query->where('age', '>=', $filters['age_min']);
        }
        if ($request->filled('age_max')) {
            $query->where('age', '<=', $filters['age_max']);
        }
        $sort = $filters['sort'] ?? 'id';
        $query->orderBy($sort, $filters['direction'] ?? 'asc');
        if ($sort !== 'id') {
            $query->orderBy('id');
        }
        $students = $query->paginate((int) ($filters['per_page'] ?? 10))->withQueryString();
        $lopHocs = LopHoc::orderBy('ten_lop')->get();

        return view('sinhvien.index', compact('students', 'lopHocs'));
    }

    public function add(Request $request)
    {
        $request->validate(['lop_hoc_id' => 'nullable|integer|exists:lop_hocs,id']);
        $lopHocs = LopHoc::orderBy('ten_lop')->get();

        return view('sinhvien.add', compact('lopHocs'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        SinhVien::create($validated);

        return redirect()->route('sinhvien.index')->with('success', 'Sinh viên đã được tạo thành công.');
    }

    public function show(SinhVien $sinhVien)
    {
        $sinhVien->load('lopHoc');

        return view('sinhvien.show', compact('sinhVien'));
    }

    public function edit(SinhVien $sinhVien)
    {
        $lopHocs = LopHoc::orderBy('ten_lop')->get();

        return view('sinhvien.add', compact('sinhVien', 'lopHocs'));
    }

    public function update(Request $request, SinhVien $sinhVien)
    {
        $validated = $this->validatedData($request);
        $sinhVien->update($validated);

        return redirect()->route('sinhvien.index')->with('success', 'Sinh viên đã được cập nhật thành công.');
    }

    public function destroy(SinhVien $sinhVien)
    {
        $sinhVien->delete();

        return redirect()->route('sinhvien.index')->with('success', 'Sinh viên đã được xóa thành công.');
    }

    // Giữ đường dẫn bài học cũ, nhưng đọc sinh viên từ cơ sở dữ liệu.
    public function getID($id = null)
    {
        $sinhVien = SinhVien::findOrFail($id);

        return $this->show($sinhVien);
    }

    public function show2($name = '', $tuoi = 0)
    {
        return response('Đây là sinh viên có tên là: '.$name.' và tuổi là: '.$tuoi)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:120',
            'lop_hoc_id' => 'required|integer|exists:lop_hocs,id',
        ], [
            'name.required' => 'Vui lòng nhập tên sinh viên.',
            'age.required' => 'Vui lòng nhập tuổi.',
            'age.integer' => 'Tuổi phải là số nguyên.',
            'age.min' => 'Tuổi phải từ 1 đến 120.',
            'age.max' => 'Tuổi phải từ 1 đến 120.',
            'lop_hoc_id.required' => 'Vui lòng chọn lớp học.',
            'lop_hoc_id.exists' => 'Lớp học không tồn tại.',
        ]);
    }
}
