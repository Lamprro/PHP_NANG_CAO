<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SinhVienApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_api_crud_uses_standard_response_shape(): void
    {
        $lopHoc = LopHoc::factory()->create();

        $createResponse = $this->postJson('/api/sinh-viens', [
            'name' => 'Nguyễn Văn Nam',
            'age' => 21,
            'lop_hoc_id' => $lopHoc->id,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 201)
            ->assertJsonPath('message', 'Tạo sinh viên thành công.')
            ->assertJsonPath('data.name', 'Nguyễn Văn Nam')
            ->assertJsonPath('error', null);

        $studentId = $createResponse->json('data.id');

        $this->getJson('/api/sinh-viens/'.$studentId)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $studentId);

        $this->putJson('/api/sinh-viens/'.$studentId, [
            'name' => 'Nguyễn Văn Nam Updated',
            'age' => 22,
            'lop_hoc_id' => $lopHoc->id,
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Nguyễn Văn Nam Updated');

        $this->deleteJson('/api/sinh-viens/'.$studentId)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('sinh_viens', ['id' => $studentId]);
    }

    public function test_student_api_validation_errors_use_standard_response_shape(): void
    {
        $this->postJson('/api/sinh-viens', [
            'name' => '',
            'age' => 0,
            'lop_hoc_id' => 999999,
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 422)
            ->assertJsonPath('data', null)
            ->assertJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'error',
            ]);
    }
}
