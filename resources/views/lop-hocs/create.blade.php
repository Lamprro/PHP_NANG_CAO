@extends('layout1')
@section('title', isset($lopHoc) ? 'Cập nhật lớp học' : 'Thêm lớp học')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                {{ isset($lopHoc) ? 'Cập Nhật Lớp Học: ' . $lopHoc->ten_lop : 'Thêm Mới Lớp Học' }}
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($lopHoc) ? route('lop-hocs.update', $lopHoc) : route('lop-hocs.store') }}" method="POST">
                @csrf
                @if(isset($lopHoc))
                    @method('PUT')
                @endif

                <div class="row">
                    <!-- Mã Lớp -->
                    <div class="col-md-6 mb-3">
                        <label for="ma_lop" class="form-label">Mã Lớp <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('ma_lop') is-invalid @enderror" 
                               id="ma_lop" 
                               name="ma_lop" 
                               value="{{ old('ma_lop', $lopHoc->ma_lop ?? '') }}" 
                               placeholder="VD: LH10A1" 
                               maxlength="6"
                               required>
                        @error('ma_lop')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tên Lớp -->
                    <div class="col-md-6 mb-3">
                        <label for="ten_lop" class="form-label">Tên Lớp <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('ten_lop') is-invalid @enderror" 
                               id="ten_lop" 
                               name="ten_lop" 
                               value="{{ old('ten_lop', $lopHoc->ten_lop ?? '') }}" 
                               placeholder="VD: Lớp 10A1" 
                               required>
                        @error('ten_lop')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Giáo Viên -->
                    <div class="col-md-5 mb-3">
                        <label for="giao_vien" class="form-label">Giáo Viên Chủ Nhiệm</label>
                        <input type="text" 
                               class="form-control @error('giao_vien') is-invalid @enderror" 
                               id="giao_vien" 
                               name="giao_vien" 
                               value="{{ old('giao_vien', $lopHoc->giao_vien ?? '') }}" 
                               placeholder="VD: Nguyễn Văn A" maxlength="255" required>
                        @error('giao_vien')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Số Điện Thoại -->
                    <div class="col-md-4 mb-3">
                        <label for="so_dien_thoai_gvien" class="form-label">SĐT Giáo Viên</label>
                        <input type="text" 
                               class="form-control @error('so_dien_thoai_gvien') is-invalid @enderror" 
                               id="so_dien_thoai_gvien" 
                               name="so_dien_thoai_gvien" 
                               value="{{ old('so_dien_thoai_gvien', $lopHoc->so_dien_thoai_gvien ?? '') }}" 
                               placeholder="VD: 0912345678">
                        @error('so_dien_thoai_gvien')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sĩ Số -->
                    <div class="col-md-3 mb-3">
                        <label for="si_so" class="form-label">Sĩ Số</label>
                        <input type="number" 
                               class="form-control @error('si_so') is-invalid @enderror" 
                               id="si_so" 
                               name="si_so" 
                               value="{{ old('si_so', $lopHoc->si_so ?? 1) }}"
                               min="1" required>
                        @error('si_so')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Trạng Thái -->
                <div class="mb-3">
                    <label class="form-label d-block">Trạng Thái</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" 
                               type="radio" 
                               name="trang_thai" 
                               id="status_active" 
                               value="1" 
                               {{ old('trang_thai', $lopHoc->trang_thai ?? 1) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label text-success font-weight-bold" for="status_active">Hoạt động</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" 
                               type="radio" 
                               name="trang_thai" 
                               id="status_inactive" 
                               value="0" 
                               {{ old('trang_thai', $lopHoc->trang_thai ?? 1) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="status_inactive">Ngưng hoạt động</label>
                    </div>
                </div>

                <!-- Ghi Chú -->
                <div class="mb-3">
                    <label for="ghi_chu" class="form-label">Ghi Chú</label>
                    <textarea class="form-control @error('ghi_chu') is-invalid @enderror" 
                              id="ghi_chu" 
                              name="ghi_chu" 
                              rows="3">{{ old('ghi_chu', $lopHoc->ghi_chu ?? '') }}</textarea>
                    @error('ghi_chu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nút thao tác -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('lop-hocs.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">
                        {{ isset($lopHoc) ? 'Cập Nhật' : 'Thêm Mới' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
