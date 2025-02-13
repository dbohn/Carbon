<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Carbon;

use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Tests\AbstractTestCase;

class CreateFromTimeTest extends AbstractTestCase
{
    public function testCreateWithTestNow()
    {
        Carbon::setTestNow($testNow = Carbon::create(2011, 1, 1, 12, 13, 14));
        $dt = Carbon::create(null, null, null, null, null, null);

        $this->assertCarbon($dt, 2011, 1, 1, 12, 13, 14);
        $this->assertTrue($testNow->eq($dt));
    }

    public function testCreateFromDateWithDefaults()
    {
        $d = Carbon::createFromTime();
        $this->assertSame($d->timestamp, Carbon::create(null, null, null, 0, 0, 0)->timestamp);
    }

    public function testCreateFromDateWithNull()
    {
        $d = Carbon::createFromTime(null, null, null);
        $this->assertSame($d->timestamp, Carbon::create(null, null, null, null, null, null)->timestamp);
    }

    public function testCreateFromTime()
    {
        $d = Carbon::createFromTime(23, 5, 21);
        $this->assertCarbon($d, Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 23, 5, 21);
    }

    public function testCreateFromTimeWithTestNow()
    {
        Carbon::setTestNow();
        $testTime = Carbon::createFromTime(12, 0, 0, 'GMT-25');
        $today = Carbon::today('GMT-25')->modify('12:00');
        $this->assertSame('12:00 -25:00', $testTime->format('H:i e'));
        $this->assertSame($testTime->toIso8601String(), $today->toIso8601String());

        $knownDate = Carbon::instance(new DateTimeImmutable('now UTC'));
        Carbon::setTestNow($knownDate);

        $testTime = Carbon::createFromTime(12, 0, 0, 'GMT-25');
        $today = Carbon::today('GMT-25')->modify('12:00');
        $this->assertSame('12:00 -25:00', $testTime->format('H:i e'));
        $this->assertSame($testTime->toIso8601String(), $today->toIso8601String());
    }

    public function testCreateFromTimeGreaterThan99()
    {
        $this->expectExceptionObject(new InvalidArgumentException(
            'second must be between 0 and 99, 100 given'
        ));

        Carbon::createFromTime(23, 5, 100);
    }

    public function testCreateFromTimeWithHour()
    {
        $d = Carbon::createFromTime(22);
        $this->assertSame(22, $d->hour);
        $this->assertSame(0, $d->minute);
        $this->assertSame(0, $d->second);
    }

    public function testCreateFromTimeWithMinute()
    {
        $d = Carbon::createFromTime(null, 5);
        $this->assertSame(5, $d->minute);
    }

    public function testCreateFromTimeWithSecond()
    {
        $d = Carbon::createFromTime(null, null, 21);
        $this->assertSame(21, $d->second);
    }

    public function testCreateFromTimeWithDateTimeZone()
    {
        $d = Carbon::createFromTime(12, 0, 0, new DateTimeZone('Europe/London'));
        $this->assertCarbon($d, Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 12, 0, 0);
        $this->assertSame('Europe/London', $d->tzName);
    }

    public function testCreateFromTimeWithTimeZoneString()
    {
        $d = Carbon::createFromTime(12, 0, 0, 'Europe/London');
        $this->assertCarbon($d, Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 12, 0, 0);
        $this->assertSame('Europe/London', $d->tzName);
    }

    public function testCreateFromTimeWithTimeZoneOnNow()
    {
        // disable test for now
        // because we need Carbon::now() in Carbon::create() to work with given TZ
        $test = Carbon::getTestNow();
        Carbon::setTestNow();

        $tz = 'Etc/GMT+12';
        $now = Carbon::now($tz);
        $dt = Carbon::createFromTime($now->hour, $now->minute, $now->second, $tz);

        // re-enable test
        Carbon::setTestNow($test);

        // tested without microseconds
        // because they appear withing calls to Carbon
        $this->assertSame($now->format('c'), $dt->format('c'));
    }
}
