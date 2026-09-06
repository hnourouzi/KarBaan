<?php

namespace Tests\Unit;

use App\Services\JalaliDateFormatter;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JalaliDateFormatterTest extends TestCase
{
    public function test_it_converts_jalali_input_to_a_gregorian_date(): void
    {
        $formatter = new JalaliDateFormatter;
        $gregorian = $formatter->toGregorianDate('1403/01/15');

        $this->assertSame('2024-04-03', $gregorian);
    }

    public function test_it_accepts_an_already_gregorian_date(): void
    {
        $formatter = new JalaliDateFormatter;

        $this->assertSame('2026-09-06', $formatter->toGregorianDate('2026-09-06'));
    }

    public function test_it_formats_persian_day_and_month_names(): void
    {
        $formatter = new JalaliDateFormatter;
        $date = Carbon::parse('2024-04-03');

        $this->assertSame('1403/01/15', $formatter->date($date));
        $this->assertStringContainsString('فروردین', $formatter->full($date));
        $this->assertNotSame('—', $formatter->dayName($date));
    }
}
