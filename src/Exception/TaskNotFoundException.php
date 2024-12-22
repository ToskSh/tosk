<?php

namespace ToskSh\Tosk\Exception;

class TaskNotFoundException extends \Exception {
    public function __construct(
        string|null $taskId = null,
    ) {
        parent::__construct(
            "Task ". ($taskId ? "[". $taskId ."] " : "") ."session was not found",
            404,
            null,
        );
    }
}