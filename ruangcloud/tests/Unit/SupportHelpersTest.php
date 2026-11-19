<?php

namespace Tests\Unit;

use App\Support\Fmt;
use App\Support\UniqueName;
use PHPUnit\Framework\TestCase;

/** Uji logika murni helper pendukung (tanpa database). */
class SupportHelpersTest extends TestCase
{
    public function test_fmt_bytes_uses_indonesian_number_format(): void
    {
        $this->assertSame('0 B', Fmt::bytes(0));
        $this->assertSame('512 B', Fmt::bytes(512));
        $this->assertSame('1,0 KB', Fmt::bytes(1024));
        $this->assertSame('1,5 KB', Fmt::bytes(1536));
        $this->assertSame('5,0 MB', Fmt::bytes(5 * 1024 * 1024));
        $this->assertSame('5,0 GB', Fmt::bytes(5 * 1024 * 1024 * 1024));
    }

    public function test_unique_name_returns_name_when_free(): void
    {
        $result = UniqueName::resolve('laporan.pdf', fn (string $n): bool => false);

        $this->assertSame('laporan.pdf', $result);
    }

    public function test_unique_name_appends_counter_before_extension(): void
    {
        $taken = ['laporan.pdf', 'laporan (1).pdf'];

        $result = UniqueName::resolve(
            'laporan.pdf',
            fn (string $n): bool => in_array($n, $taken, true)
        );

        $this->assertSame('laporan (2).pdf', $result);
    }

    public function test_unique_name_continues_existing_counter(): void
    {
        $result = UniqueName::resolve(
            'laporan (1).pdf',
            fn (string $n): bool => $n === 'laporan (1).pdf'
        );

        $this->assertSame('laporan (2).pdf', $result);
    }
}
