@extends('layout1')
@section('title', 'Chi tiết sinh viên')

@section('content')
    <h2>Chi tiết sinh viên</h2>
    <div class="card mb-3"><div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">ID</dt><dd class="col-sm-9">{{ $sinhVien->id }}</dd>
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $sinhVien->name }}</dd>
            <dt class="col-sm-3">Tuổi</dt><dd class="col-sm-9">{{ $sinhVien->age }}</dd>
            <dt class="col-sm-3">Lớp học</dt><dd class="col-sm-9"><a href="{{ route('lop-hocs.show', $sinhVien->lopHoc) }}">{{ $sinhVien->lopHoc->ten_lop }} ({{ $sinhVien->lopHoc->ma_lop }})</a></dd>
        </dl>
    </div></div>
    <a class="btn btn-secondary" href="{{ route('sinhvien.index') }}">Danh sách sinh viên</a>
    <a class="btn btn-primary" href="{{ route('sinhvien.edit', $sinhVien) }}">Sửa sinh viên</a>
@endsection
