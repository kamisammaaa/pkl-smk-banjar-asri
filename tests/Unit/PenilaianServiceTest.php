<?php

namespace Tests\Unit;

use App\Services\PenilaianService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class PenilaianServiceTest extends TestCase
{
    public function test_it_returns_zero_for_jurnal_when_no_approved_journal_exists(): void
    {
        $service = new PenilaianService();

        $result = $service->calculateNilaiJurnal(new Collection(), 5);

        $this->assertSame(0, $result);
    }

    public function test_it_calculates_final_score_consistently_without_default_eighty(): void
    {
        $service = new PenilaianService();

        $result = $service->calculateNilaiAkhir(85, 0, 80);

        $this->assertSame(50, $result);
    }
}
