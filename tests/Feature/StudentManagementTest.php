<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\Menu;
use App\Models\SinhVien;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_crud_and_class_transfer(): void
    {
        $firstClass = LopHoc::factory()->create();
        $secondClass = LopHoc::factory()->create();
        $data = ['name' => 'Nguyễn An', 'age' => 20, 'lop_hoc_id' => $firstClass->id];
        $this->get('/sinhvien/add?lop_hoc_id='.$firstClass->id)->assertOk()->assertSee('selected', false);
        $this->post('/sinhvien', $data)->assertRedirect(route('sinhvien.index'))->assertSessionHas('success');
        $student = SinhVien::firstOrFail();
        $this->get(route('sinhvien.show', $student))->assertOk()->assertSee('Nguyễn An')->assertSee($firstClass->ten_lop);
        $this->get('/sinhvien/show/'.$student->id)->assertOk()->assertSee('Nguyễn An');
        $this->get(route('sinhvien.edit', $student))->assertOk()->assertSee('Nguyễn An');
        $this->put(route('sinhvien.update', $student), ['name' => 'Nguyễn Bình', 'age' => 21, 'lop_hoc_id' => $secondClass->id])
            ->assertRedirect(route('sinhvien.index'));
        $this->assertDatabaseHas('sinh_viens', ['id' => $student->id, 'name' => 'Nguyễn Bình', 'lop_hoc_id' => $secondClass->id]);
        $this->get(route('lop-hocs.show', $firstClass))->assertOk()->assertSee('Chưa có sinh viên trong lớp.');
        $this->get(route('lop-hocs.show', $secondClass))->assertOk()->assertSee('Nguyễn Bình');
        $this->delete(route('sinhvien.destroy', $student))->assertRedirect(route('sinhvien.index'));
        $this->assertDatabaseMissing('sinh_viens', ['id' => $student->id]);
    }

    public function test_student_validation_preserves_input_and_database(): void
    {
        $this->from('/sinhvien/add')->post('/sinhvien', ['name' => 'Tên đang nhập', 'age' => -1, 'lop_hoc_id' => 999])
            ->assertRedirect('/sinhvien/add')->assertSessionHasErrors(['age', 'lop_hoc_id'])
            ->assertSessionHasInput('name', 'Tên đang nhập');
        $this->post('/sinhvien', ['name' => '', 'age' => 121])->assertSessionHasErrors(['name', 'age', 'lop_hoc_id']);
        $this->assertDatabaseCount('sinh_viens', 0);
        $class = LopHoc::factory()->create();
        $student = SinhVien::create(['name' => 'An', 'age' => 20, 'lop_hoc_id' => $class->id]);
        $this->put(route('sinhvien.update', $student), ['name' => 'Thay đổi', 'age' => 'abc', 'lop_hoc_id' => 999])
            ->assertSessionHasErrors(['age', 'lop_hoc_id']);
        $this->assertDatabaseHas('sinh_viens', ['id' => $student->id, 'name' => 'An', 'age' => 20, 'lop_hoc_id' => $class->id]);
    }

    public function test_student_filters_sort_and_pagination_are_combined(): void
    {
        $class = LopHoc::factory()->create(['ten_lop' => 'Lớp tin học', 'ma_lop' => 'TH01']);
        $other = LopHoc::factory()->create();
        foreach (range(18, 24) as $age) {
            SinhVien::create(['name' => 'Sinh viên '.$age, 'age' => $age, 'lop_hoc_id' => $class->id]);
        }
        SinhVien::create(['name' => 'Sinh viên khác', 'age' => 20, 'lop_hoc_id' => $other->id]);
        $url = '/sinhvien?search=TH01&lop_hoc_id='.$class->id.'&age_min=18&age_max=24&sort=age&direction=desc&per_page=5';
        $this->get($url)->assertOk()->assertViewHas('students', function ($rows) use ($class) {
            return $rows->total() === 7 && $rows->pluck('age')->all() === [24, 23, 22, 21, 20]
                && $rows->first()->relationLoaded('lopHoc')
                && str_contains($rows->nextPageUrl(), 'lop_hoc_id='.$class->id)
                && str_contains($rows->nextPageUrl(), 'search=TH01');
        });
        $this->get($url.'&page=2')->assertOk()->assertViewHas('students', fn ($rows) => $rows->pluck('age')->all() === [19, 18]);
        $this->get('/sinhvien?search=missing')->assertOk()->assertSee('Không tìm thấy sinh viên phù hợp.');
    }

    public function test_invalid_student_filters_and_missing_records(): void
    {
        $this->from('/sinhvien')->get('/sinhvien?sort=bad&per_page=0&lop_hoc_id=999&age_min=30&age_max=10')
            ->assertRedirect('/sinhvien')->assertSessionHasErrors(['sort', 'per_page', 'lop_hoc_id', 'age_max']);
        foreach (['/sinhvien/999', '/sinhvien/999/edit', '/sinhvien/show/999', '/lop-hocs/999'] as $url) {
            $this->get($url)->assertNotFound();
        }
        $this->delete('/sinhvien/999')->assertNotFound();
        $this->put('/sinhvien/999', [])->assertNotFound();
    }

    public function test_class_with_students_cannot_be_deleted_until_students_are_moved(): void
    {
        $class = LopHoc::factory()->create();
        $other = LopHoc::factory()->create();
        $student = SinhVien::create(['name' => 'An', 'age' => 20, 'lop_hoc_id' => $class->id]);
        $this->delete(route('lop-hocs.destroy', $class))->assertRedirect(route('lop-hocs.index'))->assertSessionHasErrors('error');
        $this->assertDatabaseHas('lop_hocs', ['id' => $class->id]);
        $this->assertDatabaseHas('sinh_viens', ['id' => $student->id, 'lop_hoc_id' => $class->id]);
        $this->put(route('sinhvien.update', $student), ['name' => 'An', 'age' => 20, 'lop_hoc_id' => $other->id])
            ->assertRedirect(route('sinhvien.index'));
        $this->delete(route('lop-hocs.destroy', $class))->assertRedirect(route('lop-hocs.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('lop_hocs', ['id' => $class->id]);
    }

    public function test_foreign_key_also_prevents_deleting_an_occupied_class(): void
    {
        $class = LopHoc::factory()->create();
        SinhVien::create(['name' => 'An', 'age' => 20, 'lop_hoc_id' => $class->id]);
        $this->expectException(QueryException::class);
        $class->delete();
    }

    public function test_class_detail_paginates_only_its_own_students(): void
    {
        $class = LopHoc::factory()->create();
        $other = LopHoc::factory()->create();
        foreach (range(1, 12) as $number) {
            SinhVien::create(['name' => 'Sinh viên '.$number, 'age' => 20, 'lop_hoc_id' => $class->id]);
        }
        SinhVien::create(['name' => 'Sinh viên lớp khác', 'age' => 21, 'lop_hoc_id' => $other->id]);
        $this->get(route('lop-hocs.show', $class))->assertOk()
            ->assertViewHas('sinhViens', fn ($rows) => $rows->total() === 12 && $rows->count() === 10)
            ->assertDontSee('Sinh viên lớp khác');
        $this->get(route('lop-hocs.show', $class).'?page=2')->assertOk()
            ->assertViewHas('sinhViens', fn ($rows) => $rows->count() === 2);
    }

    public function test_home_statistics_and_empty_student_form(): void
    {
        $this->get('/')->assertOk()->assertSee('Thêm lớp học đầu tiên');
        $this->get('/sinhvien/add')->assertOk()->assertSee('Chưa có lớp học.')->assertSee('disabled', false);
        $class = LopHoc::factory()->create(['trang_thai' => 1]);
        LopHoc::factory()->create(['trang_thai' => 0]);
        SinhVien::create(['name' => 'An', 'age' => 20, 'lop_hoc_id' => $class->id]);
        Menu::create(['slug' => 'lop-hoc', 'tenhienthi' => 'Lớp học', 'trangthai' => 1]);
        $this->get('/')->assertOk()->assertViewHas('tongLop', 2)->assertViewHas('lopHoatDong', 1)
            ->assertViewHas('tongSinhVien', 1)->assertViewHas('tongMenu', 1)
            ->assertViewHas('lopHocs', fn ($rows) => $rows->firstWhere('id', $class->id)->sinh_viens_count === 1);
    }

    public function test_student_names_are_escaped_and_legacy_demo_returns_plain_text(): void
    {
        $class = LopHoc::factory()->create();
        $student = SinhVien::create(['name' => '<script>alert(1)</script>', 'age' => 20, 'lop_hoc_id' => $class->id]);
        $this->get(route('sinhvien.show', $student))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/sinhvien/show2/An/20')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
