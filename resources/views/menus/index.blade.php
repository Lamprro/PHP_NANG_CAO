@extends('layout1')
@section('title', 'Quản lý Menu')

@section('content')
    <a href="{{ route('menus.create') }}" class="btn btn-primary mb-3">Thêm Menu</a>
    <h2>Danh sách Menu</h2>
    @include('partial.filter-errors')
    <form method="GET" action="{{ route('menus.index') }}" class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="search" class="form-label">Tìm slug hoặc tên hiển thị</label>
            <input class="form-control" id="search" name="search" maxlength="255" value="{{ request('search') }}">
        </div>
        <div class="col-md-6">
            <label for="trangthai" class="form-label">Trạng thái</label>
            <select class="form-select" id="trangthai" name="trangthai">
                <option value="">Tất cả</option>
                <option value="1" @selected(request('trangthai') === '1')>Hoạt động</option>
                <option value="0" @selected(request('trangthai') === '0')>Ngừng hoạt động</option>
            </select>
        </div>
        @include('partial.list-options', ['columns' => ['id' => 'ID', 'slug' => 'Slug', 'tenhienthi' => 'Tên hiển thị', 'trangthai' => 'Trạng thái'], 'resetUrl' => route('menus.index')])
    </form>
    <p>Tổng số: {{ $menus->total() }} menu</p>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead><tr><th>ID</th><th>Slug</th><th>Tên hiển thị</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($menus as $menu)
                    <tr>
                        <td>{{ $menu->id }}</td>
                        <td>{{ $menu->slug }}</td>
                        <td>{{ $menu->tenhienthi }}</td>
                        <td>{{ $menu->trangthai ? 'Hoạt động' : 'Ngừng hoạt động' }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('menus.edit', $menu) }}">Sửa</a>
                            <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa menu này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Không tìm thấy menu phù hợp.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $menus->links() }}
@endsection
