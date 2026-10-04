<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_filters_are_combined_and_inactive_status_is_supported(): void
    {
        $wanted = LopHoc::factory()->create(['ten_lop' => 'Lớp phù hợp', 'giao_vien' => 'An', 'trang_thai' => false, 'si_so' => 30]);
        LopHoc::factory()->create(['giao_vien' => 'An', 'trang_thai' => true, 'si_so' => 30]);
        LopHoc::factory()->create(['giao_vien' => 'An', 'trang_thai' => false, 'si_so' => 50]);
        LopHoc::factory()->create(['giao_vien' => 'Bình', 'trang_thai' => false, 'si_so' => 30]);

        $this->get('/lop-hocs?search=An&trang_thai=0&si_so_min=25&si_so_max=35')
            ->assertOk()->assertViewHas('lopHocs', fn ($rows) => $rows->total() === 1 && $rows->first()->id === $wanted->id);
    }

    public function test_class_sort_and_pagination_keep_filters(): void
    {
        foreach (range(1, 7) as $size) {
            LopHoc::factory()->create(['si_so' => $size, 'trang_thai' => false]);
        }
        $this->get('/lop-hocs?trang_thai=0&sort=si_so&direction=desc&per_page=5')
            ->assertOk()->assertViewHas('lopHocs', function ($rows) {
                return $rows->pluck('si_so')->all() === [7, 6, 5, 4, 3]
                    && str_contains($rows->nextPageUrl(), 'trang_thai=0')
                    && str_contains($rows->nextPageUrl(), 'sort=si_so');
            });
        $this->get('/lop-hocs?trang_thai=0&sort=si_so&direction=desc&per_page=5&page=2')
            ->assertOk()->assertViewHas('lopHocs', fn ($rows) => $rows->pluck('si_so')->all() === [2, 1]);
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->from('/lop-hocs')->get('/lop-hocs?sort=unknown&per_page=0&direction=wrong&si_so_min=30&si_so_max=20')
            ->assertRedirect('/lop-hocs')->assertSessionHasErrors(['sort', 'per_page', 'direction', 'si_so_max']);
        $this->from('/menus')->get('/menus?sort=unknown&per_page=-1&trangthai=2')
            ->assertRedirect('/menus')->assertSessionHasErrors(['sort', 'per_page', 'trangthai']);
    }

    public function test_class_crud_keeps_existing_routes_and_validation(): void
    {
        $data = ['ma_lop' => 'LH01', 'ten_lop' => 'Lớp 01', 'si_so' => 30, 'giao_vien' => 'Nguyễn An', 'so_dien_thoai_gvien' => '0912345678', 'trang_thai' => 1];
        $this->get(route('lop-hocs.create'))->assertOk();
        $this->post('/lop-hocs/store', $data)->assertRedirect(route('lop-hocs.index'));
        $class = LopHoc::firstOrFail();
        $this->get(route('lop-hocs.edit', $class))->assertOk()->assertSee('LH01');
        $this->put(route('lop-hocs.update', $class), array_merge($data, ['trang_thai' => 0]))->assertRedirect(route('lop-hocs.index'));
        $this->assertDatabaseHas('lop_hocs', ['id' => $class->id, 'trang_thai' => 0]);
        $this->post('/lop-hocs', $data)->assertSessionHasErrors('ma_lop');
        $this->delete(route('lop-hocs.destroy', $class))->assertRedirect(route('lop-hocs.index'));
        $this->assertDatabaseMissing('lop_hocs', ['id' => $class->id]);
    }

    public function test_menu_migration_and_crud(): void
    {
        $this->assertEqualsCanonicalizing(['id', 'slug', 'tenhienthi', 'trangthai'], Schema::getColumnListing('menus'));
        $this->get(route('menus.create'))->assertOk();
        $data = ['slug' => 'lop-hoc', 'tenhienthi' => 'Lớp học', 'trangthai' => 1];
        $this->post(route('menus.store'), $data)->assertRedirect(route('menus.index'))->assertSessionHas('success');
        $menu = Menu::firstOrFail();
        $this->get(route('menus.edit', $menu))->assertOk()->assertSee('lop-hoc');
        $this->put(route('menus.update', $menu), array_merge($data, ['tenhienthi' => 'Quản lý lớp học', 'trangthai' => 0]))->assertRedirect(route('menus.index'));
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'tenhienthi' => 'Quản lý lớp học', 'trangthai' => 0]);
        $this->delete(route('menus.destroy', $menu))->assertRedirect(route('menus.index'));
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    public function test_menu_validation_preserves_input_and_prevents_duplicate_slugs(): void
    {
        $menu = Menu::create(['slug' => 'lop-hoc', 'tenhienthi' => 'Lớp học', 'trangthai' => 1]);
        $other = Menu::create(['slug' => 'sinh-vien', 'tenhienthi' => 'Sinh viên', 'trangthai' => 1]);
        $this->from('/menus/create')->post(route('menus.store'), ['slug' => 'lop-hoc', 'tenhienthi' => 'Tên mới', 'trangthai' => 0])
            ->assertRedirect('/menus/create')->assertSessionHasErrors('slug')->assertSessionHasInput('tenhienthi', 'Tên mới');
        $this->put(route('menus.update', $other), ['slug' => $menu->slug, 'tenhienthi' => 'Trùng slug', 'trangthai' => 1])->assertSessionHasErrors('slug');
        $this->post(route('menus.store'), ['slug' => '../bad slug', 'tenhienthi' => '', 'trangthai' => 2])->assertSessionHasErrors(['slug', 'tenhienthi', 'trangthai']);
        $this->assertDatabaseCount('menus', 2);
        $this->get('/menus/9999/edit')->assertNotFound();
        $this->delete('/menus/9999')->assertNotFound();
    }

    public function test_menu_filters_sort_and_pagination(): void
    {
        foreach (range(1, 7) as $number) {
            Menu::create(['slug' => 'menu-'.$number, 'tenhienthi' => 'Mục '.$number, 'trangthai' => 0]);
        }
        Menu::create(['slug' => 'menu-active', 'tenhienthi' => 'Mục hoạt động', 'trangthai' => 1]);
        $this->get('/menus?search=menu&trangthai=0&sort=slug&direction=desc&per_page=5')
            ->assertOk()->assertViewHas('menus', fn ($rows) => $rows->total() === 7 && $rows->first()->slug === 'menu-7'
                && str_contains($rows->nextPageUrl(), 'trangthai=0') && str_contains($rows->nextPageUrl(), 'search=menu'));
        $this->get('/menus?search=missing')->assertOk()->assertSee('Không tìm thấy menu phù hợp.');
        $this->get('/lop-hocs')->assertOk()->assertSee('Không tìm thấy lớp học phù hợp.');
    }
}
