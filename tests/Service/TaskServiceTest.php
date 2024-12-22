<?php

namespace ToskSh\Tosk\Tests\Service;

use ToskSh\Tosk\Entity\Config;
use ToskSh\Tosk\Entity\Task;
use ToskSh\Tosk\Exception\FileNotFoundException;
use ToskSh\Tosk\Exception\JsonDecodeException;
use ToskSh\Tosk\Exception\TaskNotFoundException;
use ToskSh\Tosk\Service\ConfigService;
use ToskSh\Tosk\Service\SerializerService;
use ToskSh\Tosk\Service\StepService;
use ToskSh\Tosk\Service\TaskService;
use ToskSh\Tosk\Service\TimestampService;
use Symfony\Component\Filesystem\Filesystem;

class TaskServiceTest extends AbstractServiceTestCase {
    private TaskService|null $taskService = null;

    public function setUp(): void {
        parent::setUp();

        $this->taskService = new TaskService(
            new StepService($timestampService = new TimestampService()),
            $timestampService,
            new SerializerService($filesystem = new Filesystem()),
            $filesystem,
            new ConfigService(
                $this->taskService,
                new SerializerService($filesystem),
                $filesystem,
            )
        );
    }

    public function testSetAndGetConfig(): void {
        $config = new Config();
        $this->taskService->setConfig($config);

        $this->assertSame($config, $this->taskService->getConfig());
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function testGetTasks(): void {
        file_put_contents(
            $this->taskDirectory . DIRECTORY_SEPARATOR . 'testTask.json',
            json_encode(['id' => 'testTask', 'name' => 'Test Task'])
        );
        file_put_contents(
            $this->taskDirectory . DIRECTORY_SEPARATOR . 'testTask2.json',
            json_encode(['id' => 'testTask2', 'name' => 'Test Task 2'])
        );

        $config = (new Config())->setTaskDirectory($this->taskDirectory);
        $this->taskService->setConfig($config);

        $tasks = $this->taskService->getTasks();

        $this->assertCount(2, $tasks);
        $this->assertInstanceOf(Task::class, $tasks->first());
        $this->assertEquals('testTask', $tasks->first()->getId());
        $this->assertEquals('Test Task', $tasks->first()->getName());
        $this->assertInstanceOf(Task::class, $tasks->last());
        $this->assertEquals('testTask2', $tasks->last()->getId());
        $this->assertEquals('Test Task 2', $tasks->last()->getName());
    }

    /**
     * @throws TaskNotFoundException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function testGetTask(): void {
        $taskFilePath = $this->taskDirectory . DIRECTORY_SEPARATOR . 'testTask.json';
        file_put_contents($taskFilePath, json_encode(['id' => 'testTask', 'name' => 'Test Task']));

        $config = (new Config())->setTaskDirectory($this->taskDirectory)->setTaskId('testTask');
        $this->taskService->setConfig($config);

        $task = $this->taskService->getTask();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('testTask', $task->getId());
        $this->assertEquals('Test Task', $task->getName());
    }

    /**
     * @throws TaskNotFoundException
     * @throws \JsonException
     */
    public function testCreateTask(): void {
        $config = (new Config())->setTaskDirectory($this->taskDirectory);
        $this->taskService->setConfig($config);

        $taskData = ['name' => 'New Task'];
        $this->taskService->create($taskData);

        $this->assertFileExists($this->taskService->getTaskFilepath());

        $task = json_decode(file_get_contents($this->taskService->getTaskFilepath()), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals('New Task', $task['name']);
    }

    /**
     * @throws TaskNotFoundException
     */
    public function testDeleteTask(): void {
        // Create a task file for testing
        $taskFilePath = $this->taskDirectory . DIRECTORY_SEPARATOR . 'testTask.json';
        file_put_contents($taskFilePath, json_encode(['id' => 'testTask', 'name' => 'Test Task']));

        $config = (new Config())->setTaskDirectory($this->taskDirectory)->setTaskId('testTask');
        $this->taskService->setConfig($config);

        $this->taskService->delete();

        $this->assertFalse(file_exists($taskFilePath));
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function testTaskNotFoundException(): void {
        $this->expectException(TaskNotFoundException::class);

        $config = (new Config())->setTaskDirectory($this->taskDirectory)->setTaskId('nonexistentTask');
        $this->taskService->setConfig($config);

        $this->taskService->getTask();
    }
}