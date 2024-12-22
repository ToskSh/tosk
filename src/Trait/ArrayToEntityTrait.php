<?php

namespace ToskSh\Tosk\Trait;

use ToskSh\Tosk\Entity\Commit;
use ToskSh\Tosk\Entity\Config;
use ToskSh\Tosk\Entity\Step;
use ToskSh\Tosk\Entity\Task;

trait ArrayToEntityTrait {
    public function arrayToEntity(
        array $array,
        string $className
    ): Config|Task|Commit|Step {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(
                serialize($array),
                ':'
            )
        ), [
            Config::class,
            Task::class,
            Commit::class,
            Step::class,
        ]);
    }
}