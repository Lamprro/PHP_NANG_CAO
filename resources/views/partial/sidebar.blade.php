<aside class="sidebar">
    <h2>Menu</h2>
    <ul>
        <li><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Tổng quan</a></li>
        <li><a href="{{ route('sinhvien.index') }}" @if(request()->routeIs('sinhvien.*')) aria-current="page" @endif>Sinh viên</a></li>
        <li><a href="{{ route('lop-hocs.index') }}" @if(request()->routeIs('lop-hocs.*')) aria-current="page" @endif>Lớp học</a></li>
        <li><a href="{{ route('menus.index') }}" @if(request()->routeIs('menus.*')) aria-current="page" @endif>Quản lý Menu</a></li>
    </ul>
</aside>
