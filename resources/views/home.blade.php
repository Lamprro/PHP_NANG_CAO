@extends('layout1')
@section('title', 'Tổng quan quản lý lớp học')

@section('content')
    <h2>Tổng quan</h2>
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><h3 class="fs-6">Tổng lớp học</h3><p class="fs-3">{{ $tongLop }}</p><a href="{{ route('lop-hocs.index') }}">Quản lý lớp học</a></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><h3 class="fs-6">Lớp hoạt động</h3><p class="fs-3">{{ $lopHoatDong }}</p><a href="{{ route('lop-hocs.index', ['trang_thai' => 1]) }}">Xem lớp hoạt động</a></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><h3 class="fs-6">Sinh viên đã nhập</h3><p class="fs-3">{{ $tongSinhVien }}</p><a href="{{ route('sinhvien.index') }}">Quản lý sinh viên</a></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><h3 class="fs-6">Menu</h3><p class="fs-3">{{ $tongMenu }}</p><a href="{{ route('menus.index') }}">Quản lý Menu</a></div></div></div>
    </div>
    <h3 class="fs-5">Lớp học mới nhất</h3>
    <div class="table-responsive"><table class="table table-striped">
        <thead><tr><th>Mã lớp</th><th>Tên lớp</th><th>Giáo viên</th><th>Sinh viên đã nhập</th><th>Trạng thái</th></tr></thead>
        <tbody>
            @forelse($lopHocs as $lopHoc)
                <tr><td>{{ $lopHoc->ma_lop }}</td><td><a href="{{ route('lop-hocs.show', $lopHoc) }}">{{ $lopHoc->ten_lop }}</a></td><td>{{ $lopHoc->giao_vien }}</td><td>{{ $lopHoc->sinh_viens_count }}</td><td>{{ $lopHoc->trang_thai ? 'Hoạt động' : 'Ngừng hoạt động' }}</td></tr>
            @empty
                <tr><td colspan="5">Chưa có lớp học. <a href="{{ route('lop-hocs.create') }}">Thêm lớp học đầu tiên</a>.</td></tr>
            @endforelse
        </tbody>
    </table></div>
@endsection
