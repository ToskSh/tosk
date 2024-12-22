<?php

namespace ToskSh\Tosk\Tests\Service;

use ToskSh\Tosk\Entity\Config;
use ToskSh\Tosk\Service\ConfigService;
use ToskSh\Tosk\Service\SerializerService;
use ToskSh\Tosk\Service\StepService;
use ToskSh\Tosk\Service\TaskService;
use ToskSh\Tosk\Service\TimestampService;
use Symfony\Component\Filesystem\Filesystem;

class ConfigServiceTest extends AbstractServiceTestCase {
    private ConfigService|null $configService = null;

    public function setUp(): void {
        parent::setUp();

        $this->configService = new ConfigService();
        $this->configService
            ->setTaskService(
                new TaskService(
                    new StepService($timestampService = new TimestampService()),
                    $timestampService,
                    $serializer = new SerializerService($filesystem = new Filesystem()),
                    $filesystem,
                    $this->configService,
                )
            )->setSerializerService($serializer)->setFilesystem($filesystem);
    }

    /**
     * @throws \JsonException
     */
    public function testWrite(): void {
        $config = $this->configService->initConfig(
            configPath: $this->configPath,
            taskDirectory: $this->taskDirectory,
            taskId: $taskId = 'taskId',
        )->write()->getConfig();

        $this->assertFileExists($this->configPath);
        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals($this->taskDirectory, $config->getTaskDirectory());
        $this->assertEquals($taskId, $config->getTaskId());
    }

    public function testIsDebug(): void {
        $this->assertTrue($this->configService->isDebug());
    }

    public function testIsTest(): void {
        $this->assertTrue($this->configService->isTest());
    }

    /**
     * @throws \JsonException
     */
    public function testGetLastTaskDirectory(): void {
        $lastTaskDirectory = $this->configService->getLastTaskDirectory();

        $this->assertEquals(Config::TASKS_DIRECTORY, $lastTaskDirectory);
    }

    /**
     * @throws \JsonException
     */
    public function testGetLastTaskIdByDirectory(): void {
        $lastTaskId = $this->configService->getLastTaskIdByDirectory(taskDirectory: $this->taskDirectory);

        $this->assertNotEquals('12345', $lastTaskId);
    }

    /**
     * @throws \JsonException
     */
    public function testGetLastTaskIdByConfig(): void {
        $lastTaskId = $this->configService->getLastTaskIdByConfig(taskDirectory: $this->taskDirectory);

        $this->assertNotEquals('12345', $lastTaskId);
    }
}