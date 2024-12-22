<?php

namespace ToskSh\Tosk\Tests\Service;

use ToskSh\Tosk\Entity\Task;
use ToskSh\Tosk\Exception\FileNotFoundException;
use ToskSh\Tosk\Exception\JsonDecodeException;
use ToskSh\Tosk\Service\SerializerService;
use Symfony\Component\Filesystem\Filesystem;

class SerializerServiceTest extends AbstractServiceTestCase {
    private SerializerService $serializerService;

    private string $testFilepath;

    public function setUp(): void {
        parent::setUp();

        $this->serializerService = new SerializerService(new Filesystem());
        $this->testFilepath = $this->toskDirectory . DIRECTORY_SEPARATOR . 'test.json';
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function testReadSuccess(): void {
        $taskArray = ['name' => 'Test Task', 'run' => false];
        file_put_contents($this->testFilepath, json_encode($taskArray));

        $result = $this->serializerService->read($this->testFilepath, Task::class);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals('Test Task', $result->getName());
        $this->assertFalse($result->isRun());
    }

    /**
     * @throws JsonDecodeException
     */
    public function testReadFileNotFound(): void {
        $this->expectException(FileNotFoundException::class);
        $this->serializerService->read('nonexistent.json', Task::class);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testReadJsonDecodeError(): void {
        file_put_contents($this->testFilepath, 'invalid_json');

        $this->expectException(JsonDecodeException::class);
        $this->serializerService->read($this->testFilepath, Task::class);
    }

    public function testWriteTask(): void {
        $task = new Task();
        $task->setName('Test Task');
        $task->setRun(false);

        $this->serializerService->writeTask($this->testFilepath, $task);

        $this->assertFileExists($this->testFilepath);

        $content = file_get_contents($this->testFilepath);
        $decodedContent = json_decode($content, true);

        $this->assertEquals('Test Task', $decodedContent['name']);
        $this->assertEquals(false, $decodedContent['run']);
    }
}