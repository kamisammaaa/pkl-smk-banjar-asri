<?php

namespace Tests\Unit;

use App\Http\Controllers\Pembimbing\JurnalController;
use PHPUnit\Framework\TestCase;

class JurnalControllerTest extends TestCase
{
    public function test_disetujui_requires_nilai_for_review(): void
    {
        $controller = new JurnalController();

        $rules = $controller->getApprovalValidationRules('disetujui');

        $this->assertArrayHasKey('nilai', $rules);
        $this->assertStringContainsString('required', $rules['nilai']);
    }

    public function test_revisi_allows_blank_nilai_for_review(): void
    {
        $controller = new JurnalController();

        $rules = $controller->getApprovalValidationRules('revisi');

        $this->assertArrayHasKey('nilai', $rules);
        $this->assertStringContainsString('nullable', $rules['nilai']);
    }
}
