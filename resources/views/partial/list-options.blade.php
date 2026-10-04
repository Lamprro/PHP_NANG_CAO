<div class="col-md-3">
    <label for="sort" class="form-label">Sắp xếp theo</label>
    <select class="form-select" name="sort" id="sort">
        @foreach($columns as $column => $label)
            <option value="{{ $column }}" @selected(request('sort', 'id') === $column)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <label for="direction" class="form-label">Thứ tự</label>
    <select class="form-select" name="direction" id="direction">
        <option value="asc" @selected(request('direction', 'asc') === 'asc')>Tăng dần</option>
        <option value="desc" @selected(request('direction') === 'desc')>Giảm dần</option>
    </select>
</div>
<div class="col-md-2">
    <label for="filter_per_page" class="form-label">Số bản ghi/trang</label>
    <select class="form-select" name="per_page" id="filter_per_page">
        @foreach([5, 10, 20] as $size)
            <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-4 d-flex align-items-end gap-2">
    <button type="submit" class="btn btn-primary">Áp dụng</button>
    <a class="btn btn-secondary" href="{{ $resetUrl }}">Xóa bộ lọc</a>
</div>
