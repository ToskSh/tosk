<?php

namespace ToskSh\Tosk\Exception;

class DurationStrToTimeException extends \Exception {
    public function __construct(
        string $duration,
    ) {
        parent::__construct(
            "Duration set [". $duration ."] is not in correct format.",
            400,
            null
        );
    }
}