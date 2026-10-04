@extends('layout1')
@section('title', isset($menu) ? 'Cập nhật Menu' : 'Thêm Menu')

@section('content')
    @include('partial.filter-errors')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($menu) ? 'Cập nhật Menu: '.$menu->tenhienthi : 'Thêm mới Menu' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($menu) ? route('menus.update', $menu) : route('menus.store') }}" method="POST">
                @csrf
                @if(isset($menu))
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" maxlength="255" required pattern="[a-z0-9]+(-[a-z0-9]+)*" value="{{ old('slug', $menu->slug ?? '') }}" aria-describedby="slug-help">
                    <div id="slug-help" class="form-text">Ví dụ: quan-ly-lop-hoc. Dùng chữ thường không dấu, số và dấu gạch ngang.</div>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="tenhienthi" class="form-label">Tên hiển thị <span class="text-danger">*</span></label>
                    <input class="form-control @error('tenhienthi') is-invalid @enderror" id="tenhienthi" name="tenhienthi" maxlength="255" required value="{{ old('tenhienthi', $menu->tenhienthi ?? '') }}">
                    @error('tenhienthi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <fieldset class="mb-3">
                    <legend class="fs-6">Trạng thái</legend>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trangthai" id="status_active" value="1" @checked((int) old('trangthai', $menu->trangthai ?? 1) === 1)>
                        <label class="form-check-label" for="status_active">Hoạt động</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trangthai" id="status_inactive" value="0" @checked((int) old('trangthai', $menu->trangthai ?? 1) === 0)>
                        <label class="form-check-label" for="status_inactive">Ngừng hoạt động</label>
                    </div>
                </fieldset>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('menus.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">{{ isset($menu) ? 'Cập nhật' : 'Thêm mới' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
