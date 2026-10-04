@extends('layout1')
@section('title', 'Danh sách sinh viên')

@section('content')
    <a href="{{ route('sinhvien.create') }}" class="btn btn-primary mb-3">Thêm sinh viên</a>
    <h2>Danh sách sinh viên</h2>
    @include('partial.filter-errors')
    <form method="GET" action="{{ route('sinhvien.index') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="search" class="form-label">Tìm tên sinh viên, tên hoặc mã lớp</label>
            <input class="form-control" id="search" name="search" maxlength="255" value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <label for="lop_hoc_id" class="form-label">Lớp học</label>
            <select class="form-select" id="lop_hoc_id" name="lop_hoc_id">
                <option value="">Tất cả lớp</option>
                @foreach($lopHocs as $lopHoc)
                    <option value="{{ $lopHoc->id }}" @selected((string) request('lop_hoc_id') === (string) $lopHoc->id)>{{ $lopHoc->ten_lop }} ({{ $lopHoc->ma_lop }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="age_min" class="form-label">Tuổi từ</label>
            <input type="number" class="form-control" id="age_min" name="age_min" min="1" max="120" value="{{ request('age_min') }}">
        </div>
        <div class="col-md-2">
            <label for="age_max" class="form-label">Tuổi đến</label>
            <input type="number" class="form-control" id="age_max" name="age_max" min="1" max="120" value="{{ request('age_max') }}">
        </div>
        @include('partial.list-options', ['columns' => ['id' => 'ID', 'name' => 'Tên sinh viên', 'age' => 'Tuổi'], 'resetUrl' => route('sinhvien.index')])
    </form>
    <p>Tổng số: {{ $students->total() }} sinh viên</p>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead><tr><th>ID</th><th>Tên</th><th>Tuổi</th><th>Lớp</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->age }}</td>
                        <td><a href="{{ route('lop-hocs.show', $student->lopHoc) }}">{{ $student->lopHoc->ten_lop }}</a></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('sinhvien.show', $student) }}">Chi tiết</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('sinhvien.edit', $student) }}">Sửa</a>
                            <form class="d-inline" action="{{ route('sinhvien.destroy', $student) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Không tìm thấy sinh viên phù hợp.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $students->links() }}
@endsection
