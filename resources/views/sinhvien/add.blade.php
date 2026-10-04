@extends('layout1')
@section('title', isset($sinhVien) ? 'Cập nhật sinh viên' : 'Thêm sinh viên')

@section('content')
    @include('partial.filter-errors')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($sinhVien) ? 'Cập nhật sinh viên: '.$sinhVien->name : 'Thêm sinh viên mới' }}</h5>
        </div>
        <div class="card-body">
            @if($lopHocs->isEmpty())
                <div class="alert alert-info">Chưa có lớp học. <a href="{{ route('lop-hocs.create') }}">Thêm lớp học</a> trước khi thêm sinh viên.</div>
            @endif
            <form action="{{ isset($sinhVien) ? route('sinhvien.update', $sinhVien) : route('sinhvien.store') }}" method="POST">
                @csrf
                @if(isset($sinhVien))
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sinh viên <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" maxlength="255" required value="{{ old('name', $sinhVien->name ?? '') }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="age" class="form-label">Tuổi <span class="text-danger">*</span></label>
                    <input class="form-control @error('age') is-invalid @enderror" type="number" id="age" name="age" min="1" max="120" required value="{{ old('age', $sinhVien->age ?? '') }}">
                    @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="lop_hoc_id" class="form-label">Lớp học <span class="text-danger">*</span></label>
                    <select class="form-select @error('lop_hoc_id') is-invalid @enderror" id="lop_hoc_id" name="lop_hoc_id" required>
                        <option value="">Chọn lớp học</option>
                        @foreach($lopHocs as $lopHoc)
                            <option value="{{ $lopHoc->id }}" @selected((string) old('lop_hoc_id', $sinhVien->lop_hoc_id ?? request('lop_hoc_id')) === (string) $lopHoc->id)>{{ $lopHoc->ten_lop }} ({{ $lopHoc->ma_lop }}){{ $lopHoc->trang_thai ? '' : ' — Ngừng hoạt động' }}</option>
                        @endforeach
                    </select>
                    @error('lop_hoc_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sinhvien.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                    <button class="btn btn-primary" type="submit" @disabled($lopHocs->isEmpty())>{{ isset($sinhVien) ? 'Cập nhật' : 'Lưu sinh viên' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
