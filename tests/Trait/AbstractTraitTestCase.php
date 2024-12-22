<?php

namespace ToskSh\Tosk\Tests\Trait;

use ToskSh\Tosk\Entity\Commit;
use ToskSh\Tosk\Entity\Step;
use ToskSh\Tosk\Entity\Task;
use PHPUnit\Framework\TestCase;

class AbstractTraitTestCase extends TestCase {
    public Task $task;
    public Commit $commit;
    public Step $step;

    protected function setUp(): void
    {
        $this->task = new Task();
        $this->commit = new Commit();
        $this->step = new Step();
    }
}