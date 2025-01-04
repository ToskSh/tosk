<?php

namespace ToskSh\Tosk\Service;

use ToskSh\Tosk\Collection\TaskCollection;
use ToskSh\Tosk\Entity\Task;
use ToskSh\Tosk\Exception\CommitNotFoundException;
use ToskSh\Tosk\Exception\FileNotFoundException;
use ToskSh\Tosk\Exception\JsonDecodeException;
use ToskSh\Tosk\Exception\CommandMissingLeastOnceOptionException;
use ToskSh\Tosk\Exception\DurationStrToTimeException;
use ToskSh\Tosk\Exception\TaskNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class HandlerService {
    private Task|null $task = null;

    public function __construct(
        private readonly ConfigService $configService,
        private readonly TaskService $taskService,
        private readonly CommitService $commitService,
        private readonly SerializerService $serializerService,
    ) {
        $this->getConfigService()
            ->setTaskService($taskService)
            ->setFilesystem(new Filesystem())
            ->setSerializerService($serializerService);
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     * @throws \JsonException
     */
    public function init(
        string|false $configPath = false,
        string|false $taskDirectory = false,
        string|false $editor = false,
        string|null $taskId = null,
    ): self {
        
        $this->getConfigService()
            ->initConfig(
                $configPath,
                $taskDirectory,
                $editor,
                $taskId,
            )->write();

        return $this;
    }

    public function getConfigService(): ConfigService {
        return $this->configService;
    }

    private function setTask(Task|null $task): self {
        $this->task = $task;

        return $this;
    }

    /**
     * @throws TaskNotFoundException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function getTask(bool $createItIfNotExist = false): Task {
        if ($this->task):
            return $this->task;
        endif;

        return $this->taskService->getTask($createItIfNotExist);
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function getTasks(): TaskCollection {
        return $this->taskService->getTasks();
    }

    /**
     * @throws TaskNotFoundException
     * @throws DurationStrToTimeException
     * @throws RemainingStrToTimeException
     * @throws FileNotFoundException
     * @throws JsonDecodeException
     */
    public function taskStart(
        string|false $name = false,
        string|false $duration = false,
        string|false $remaining = false,
        array $tags = [],
    ): self {
        return $this->setTask(
            $this->taskService
                ->update($name, $duration, $remaining, $tags)
                ->start()
                ->getTask()
        )->writeTask();
    }

    /**
     * @throws TaskNotFoundException
     * @throws DurationStrToTimeException
     * @throws RemainingStrToTimeException
     * @throws FileNotFoundException
     * @throws JsonDecodeException
     */
    public function taskStop(
        string|false $name = false,
        string|false $duration = false,
        string|false $remaining = false,
    ): self {
        return $this->setTask(
            $this->taskService
                ->update($name, $duration, $remaining)
                ->stop()
                ->getTask()
        )->writeTask();
    }

    /**
     * @throws TaskNotFoundException
     * @throws DurationStrToTimeException
     * @throws RemainingStrToTimeException
     * @throws FileNotFoundException
     * @throws JsonDecodeException
     */
    public function taskArchive(
        string|false $name = false,
        string|false $duration = false,
        string|false $remaining = false,
    ): self {
        return $this->setTask(
            $this->taskService
                ->update($name, $duration, $remaining)
                ->archive()
                ->getTask()
        )->writeTask();
    }

    /**
     * @throws TaskNotFoundException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function taskDelete(): self {
        $this->setTask($this->taskService->getTask());
        $this->taskService->delete();

        return $this->setTask(null);
    }

    /**
     * @throws TaskNotFoundException
     * @throws DurationStrToTimeException
     * @throws FileNotFoundException
     * @throws JsonDecodeException
     */
    public function commit(
        string|null $message = null,
        string|null $duration = null,
        bool $editor = false,
    ): self {
        $this->commitService
            ->setTask($this->getTask(createItIfNotExist: true))
            ->create(
                $message,
                $duration,
                $editor,
            );

        return $this->setTask($this->commitService->getTask())->writeTask();
    }

    /**
     * @throws TaskNotFoundException
     * @throws CommitNotFoundException
     * @throws DurationStrToTimeException
     * @throws FileNotFoundException
     * @throws JsonDecodeException
     * @throws CommandMissingLeastOnceOptionException
     */
    public function commitEdit(
        string $commitId,
        string|false $message = false,
        string|false $duration = false,
        bool $editor = false,
    ): self {
        $this->commitService
            ->setTask($this->getTask())
            ->edit(
                $commitId,
                $message,
                $duration,
                $editor,
            );

        return $this->setTask($this->commitService->getTask())->writeTask();
    }

    /**
     * @throws TaskNotFoundException
     * @throws FileNotFoundException
     * @throws CommitNotFoundException
     * @throws JsonDecodeException
     */
    public function commitDelete(
        string $commitId,
    ): self {
        $this->commitService
            ->setTask($this->getTask())
            ->delete($commitId);

        return $this->setTask($this->commitService->getTask())->writeTask();
    }

    public function updateTask(
        string|false $name = false,
        string|false $duration = false,
        string|false $remaining = false,
    ): self {
        if ($name === false && $duration === false && $remaining === false):
            return $this;
        endif;

        return $this->setTask(
            $this->taskService
                ->update(
                    name: $name,
                    duration: $duration,
                    remaining: $remaining,
            )->getTask()
        )->writeTask();
    }

    /**
     * @throws TaskNotFoundException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    private function writeTask(): self {
        $this->serializerService
            ->writeTask(
                $this->taskService->getTaskFilepath(),
                $this->getTask()->setLastUpdateDate(time())
            );

        return $this;
    }
}