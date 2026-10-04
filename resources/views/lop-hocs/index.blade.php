@extends('layout1')
@section('title', 'Danh sách lớp học')
@push('styles')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .per-page-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pagination {
            margin: 0;
        }
    </style>
@endpush

@section('content')
    <a class="btn btn-primary mb-3" href="{{ route('lop-hocs.create') }}">Thêm lớp học</a>
    <h2>Danh sách lớp học</h2>
    @include('partial.filter-errors')
    <form method="GET" action="{{ route('lop-hocs.index') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="search" class="form-label">Tìm tên lớp, mã lớp, giáo viên hoặc SĐT</label>
            <input class="form-control" id="search" name="search" value="{{ request('search') }}" maxlength="255">
        </div>
        <div class="col-md-2">
            <label for="trang_thai" class="form-label">Trạng thái</label>
            <select class="form-select" id="trang_thai" name="trang_thai">
                <option value="">Tất cả</option>
                <option value="1" @selected(request('trang_thai') === '1')>Hoạt động</option>
                <option value="0" @selected(request('trang_thai') === '0')>Ngừng hoạt động</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="si_so_min" class="form-label">Sĩ số từ</label>
            <input type="number" min="1" class="form-control" id="si_so_min" name="si_so_min" value="{{ request('si_so_min') }}">
        </div>
        <div class="col-md-3">
            <label for="si_so_max" class="form-label">Sĩ số đến</label>
            <input type="number" min="1" class="form-control" id="si_so_max" name="si_so_max" value="{{ request('si_so_max') }}">
        </div>
        @include('partial.list-options', ['columns' => ['id' => 'ID', 'ten_lop' => 'Tên lớp', 'ma_lop' => 'Mã lớp', 'si_so' => 'Sĩ số', 'giao_vien' => 'Giáo viên', 'trang_thai' => 'Trạng thái'], 'resetUrl' => route('lop-hocs.index')])
    </form>
    <p>Tổng số: {{ $lopHocs->total() }} lớp học</p>
    <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên lớp</th>
                <th>Mã lớp</th>
                <th>Sĩ số</th>
                <th>Giáo viên</th>
                <th>Số điện thoại giáo viên</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lopHocs as $lopHoc)
           <tr>
                <td>{{ $lopHoc['id'] }}</td>
                <td>{{ $lopHoc['ten_lop'] }}</td>
                <td>{{ $lopHoc['ma_lop'] }}</td>
                <td>{{ $lopHoc['si_so'] }}</td>
                <td>{{ $lopHoc['giao_vien'] }}</td>
                <td>{{ $lopHoc['so_dien_thoai_gvien'] }}</td>
                <td>{{ $lopHoc['trang_thai'] ? 'Hoạt động' : 'Ngừng hoạt động' }}</td>
                <td>{{ $lopHoc['ghi_chu'] }}</td>
                <!-- dùng form để gửi yêu cầu xóa  để tránh việc sử dụng link trực tiếp -->
                <td> 
                    <a href="{{ route('lop-hocs.show', $lopHoc['id']) }}">Chi tiết</a>
                    <a href="{{ route('lop-hocs.edit', $lopHoc['id']) }}">Sửa</a> 
                    <form action="{{ route('lop-hocs.destroy', $lopHoc['id']) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa lớp học này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
                <tr><td colspan="9" class="text-center">Không tìm thấy lớp học phù hợp.</td></tr>
            @endforelse
            
        </tbody>
        
    </table>
    </div>
    <div class="pagination-container">

        <form method="GET" class="per-page-form">
            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                @if(is_scalar($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <label for="per_page">Số bản ghi/trang:</label>

            <select name="per_page" id="per_page" onchange="this.form.submit()">
                <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>
                    5
                </option>

                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                    10
                </option>

                <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>
                    20
                </option>
            </select>
        </form>

        <div class="pagination">
            {{ $lopHocs->links() }}
        </div>

    </div>
@endsection
