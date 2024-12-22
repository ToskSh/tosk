<?php

namespace ToskSh\Tosk\Tests\Service;

use ToskSh\Tosk\Entity\Step;
use ToskSh\Tosk\Exception\DurationStrToTimeException;
use ToskSh\Tosk\Service\StepService;
use ToskSh\Tosk\Service\TimestampService;

class StepServiceTest extends AbstractServiceTestCase {
    private StepService $stepService;

    public function setUp(): void {
        parent::setUp();

        $this->stepService = new StepService(new TimestampService());
    }

    public function testCreateWithoutDates(): void {
        $step = $this->stepService->create();

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
    }

    public function testCreateWithCustomStartDate(): void {
        $customStartDate = strtotime('2023-01-01');
        $step = $this->stepService->create($customStartDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertEquals($customStartDate, $step->getStartDate());
    }

    public function testCreateWithEndDate(): void {
        $endDate = strtotime('2023-02-01');
        $step = $this->stepService->create(null, $endDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
    }

    public function testCreateStepWithCustomDates(): void {
        $startDate = (new \DateTime('-1 day'))->getTimestamp();
        $endDate = (new \DateTime())->getTimestamp();

        $step = $this->stepService->create($startDate, $endDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertEquals($startDate, $step->getStartDate());
        $this->assertEquals(86400, $step->getSeconds());
        $this->assertEquals('1d', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateStepWithCustomDuration(): void {
        $startDate = (new \DateTime('-1 day'))->getTimestamp();
        $duration = '+2 day';
        $endDate = strtotime($duration, $startDate);

        $step = $this->stepService->createWithCustomDuration($duration, $startDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertEquals($startDate, $step->getStartDate());
        $this->assertEquals(172800, $step->getSeconds());
        $this->assertEquals('2d', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDuration(): void {
        $customDuration = '+5 minutes';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(300, $step->getSeconds());
        $this->assertEquals('5min', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationComplexe(): void {
        $customDuration = '2days +1hour 5 minutes';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(176700, $step->getSeconds());
        $this->assertEquals('2d 1h 5min', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationComplexeBeyondMonth(): void {
        $customDuration = '6weeks 1day +1h 5 minutes -10s';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(3719090, $step->getSeconds());
        $this->assertEquals('43d 1h 4min 50s', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationWeeksNormalizer(): void {
        $customDuration = '1w';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(604800, $step->getSeconds());
        $this->assertEquals('7d', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationDaysNormalizer(): void {
        $customDuration = '1d';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(86400, $step->getSeconds());
        $this->assertEquals('1d', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationHoursNormalizer(): void {
        $customDuration = '1h';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(3600, $step->getSeconds());
        $this->assertEquals('1h', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationMinutes(): void {
        $customDuration = '1min';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(60, $step->getSeconds());
        $this->assertEquals('1min', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationSeconds(): void {
        $customDuration = '1s';
        $step = $this->stepService->createWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals(1, $step->getSeconds());
        $this->assertEquals('1s', $step->getDuration());
    }

    /**
     * @throws DurationStrToTimeException
     */
    public function testCreateWithCustomDurationAndStartDate(): void {
        $customDuration = '+2 hours';
        $customStartDate = strtotime('2023-03-01');
        $step = $this->stepService->createWithCustomDuration($customDuration, $customStartDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertEquals($customStartDate, $step->getStartDate());
        $this->assertEquals(7200, $step->getSeconds());
        $this->assertEquals('2h', $step->getDuration());
    }

    public function testStrToTimeException(): void {
        $this->expectException(DurationStrToTimeException::class);

        // Pass an invalid duration format to trigger StrToTimeException
        $this->stepService->createWithCustomDuration('invalid_duration_format');
    }
}