<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SinhVienController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
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

        try {
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

            $students = $query
                ->paginate((int) ($filters['per_page'] ?? 10))
                ->withQueryString();

            return $this->successResponse(
                $students,
                'Lấy danh sách sinh viên thành công.'
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->errorResponse(
                'Không thể lấy danh sách sinh viên.',
                config('app.debug') ? $exception->getMessage() : null
            );
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatedData($request);

        try {
            $student = SinhVien::create($validated)->load('lopHoc');

            return $this->successResponse(
                $student,
                'Tạo sinh viên thành công.',
                201
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->errorResponse(
                'Không thể tạo sinh viên.',
                config('app.debug') ? $exception->getMessage() : null
            );
        }
    }

    public function show(SinhVien $sinhVien): JsonResponse
    {
        try {
            $sinhVien->load('lopHoc');

            return $this->successResponse(
                $sinhVien,
                'Lấy thông tin sinh viên thành công.'
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->errorResponse(
                'Không thể lấy thông tin sinh viên.',
                config('app.debug') ? $exception->getMessage() : null
            );
        }
    }

    public function update(Request $request, SinhVien $sinhVien): JsonResponse
    {
        $validated = $this->validatedData($request);

        try {
            $sinhVien->update($validated);
            $sinhVien->load('lopHoc');

            return $this->successResponse(
                $sinhVien,
                'Cập nhật sinh viên thành công.'
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->errorResponse(
                'Không thể cập nhật sinh viên.',
                config('app.debug') ? $exception->getMessage() : null
            );
        }
    }

    public function destroy(SinhVien $sinhVien): JsonResponse
    {
        try {
            $sinhVien->delete();

            return $this->successResponse(
                null,
                'Xóa sinh viên thành công.'
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->errorResponse(
                'Không thể xóa sinh viên.',
                config('app.debug') ? $exception->getMessage() : null
            );
        }
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
