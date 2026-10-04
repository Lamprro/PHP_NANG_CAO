@extends('layout1')
@section('title', 'Chi tiết lớp học')

@section('content')
    <h2>Chi tiết lớp: {{ $lopHoc->ten_lop }}</h2>
    <div class="card mb-4"><div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Mã lớp</dt><dd class="col-sm-9">{{ $lopHoc->ma_lop }}</dd>
            <dt class="col-sm-3">Sĩ số khai báo</dt><dd class="col-sm-9">{{ $lopHoc->si_so }}</dd>
            <dt class="col-sm-3">Sinh viên đã nhập</dt><dd class="col-sm-9">{{ $sinhViens->total() }}</dd>
            <dt class="col-sm-3">Giáo viên</dt><dd class="col-sm-9">{{ $lopHoc->giao_vien }}</dd>
            <dt class="col-sm-3">SĐT giáo viên</dt><dd class="col-sm-9">{{ $lopHoc->so_dien_thoai_gvien ?: 'Chưa có' }}</dd>
            <dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">{{ $lopHoc->trang_thai ? 'Hoạt động' : 'Ngừng hoạt động' }}</dd>
            <dt class="col-sm-3">Ghi chú</dt><dd class="col-sm-9" style="white-space: pre-wrap">{{ $lopHoc->ghi_chu ?: 'Chưa có' }}</dd>
        </dl>
    </div></div>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a class="btn btn-secondary" href="{{ route('lop-hocs.index') }}">Danh sách lớp</a>
        <a class="btn btn-primary" href="{{ route('lop-hocs.edit', $lopHoc) }}">Sửa lớp học</a>
        <a class="btn btn-outline-primary" href="{{ route('sinhvien.create', ['lop_hoc_id' => $lopHoc->id]) }}">Thêm sinh viên vào lớp</a>
    </div>
    <h3 class="fs-5">Sinh viên trong lớp</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Tên</th><th>Tuổi</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($sinhViens as $sinhVien)
                    <tr><td>{{ $sinhVien->id }}</td><td>{{ $sinhVien->name }}</td><td>{{ $sinhVien->age }}</td><td><a href="{{ route('sinhvien.show', $sinhVien) }}">Chi tiết</a> | <a href="{{ route('sinhvien.edit', $sinhVien) }}">Sửa</a></td></tr>
                @empty
                    <tr><td colspan="4">Chưa có sinh viên trong lớp.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $sinhViens->links() }}
@endsection
