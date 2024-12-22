<?php
namespace ToskSh\Tosk\Entity;

use ToskSh\Tosk\Collection\StepCollection;
use ToskSh\Tosk\Trait\EntityDateTimeTrait;
use ToskSh\Tosk\Trait\EntityDurationTrait;
use ToskSh\Tosk\Trait\EntityUnserializerTrait;

class Commit {
    use EntityDateTimeTrait;
    use EntityDurationTrait;
    use EntityUnserializerTrait;

    private string $id;
    private string|null $message = null;

    private StepCollection $steps;

    public function __construct() {
        $this
            ->setSteps(new StepCollection())
        ;
    }

    public function setId(string $id): self {
        $this->id = $id;

        return $this;
    }

    public function getId(): string {
        return $this->id;
    }

    public function setMessage(string|null $message): self {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string|null {
        return $this->message;
    }

    public function setSteps(StepCollection $steps): self {
        $this->steps = $steps;

        return $this;
    }

    public function getSteps(): StepCollection {
        return $this->steps;
    }

    public function addStep(Step $step): self {
        if (!$this->getSteps()->contains($step)):
            $this->getSteps()->add($step);
        endif;

        return $this;
    }
}
