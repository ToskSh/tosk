<?php

namespace ToskSh\Tosk\Trait;

use ToskSh\Tosk\Entity\Commit;
use ToskSh\Tosk\Entity\Config;
use ToskSh\Tosk\Entity\Step;
use ToskSh\Tosk\Entity\Task;

trait EntityDateTimeTrait {
    public function getStartDate(): int|null {
        switch (self::class) {
            case Task::class:
                $startDate =
                    $this->getCommits()?->first()?->getStartDate()
                    ?? $this->getSteps()?->first()?->getStartDate()
                ;
                break;
            case Commit::class:
                $startDate = $this->getSteps()?->first()?->getStartDate();
                break;
            case Step::class:
                $startDate = $this->startDate;
                break;
        }

        return $startDate;
    }

    /** @deprecated 0.1.3 */
    public function getEndDate(): int|null {
        $endDate = null;

        switch (self::class) {
            case Task::class:
                if (
                    $this->getSteps()->last()?->getEndDate() !== null
                    && ($steps = $this
                        ->getSteps()
                        ->filter(static fn (Step $step) => $step->getEndDate())
                    )->count() > 0
                ):
                    $endDate = $steps->last()->getEndDate();
                elseif (($commits = $this->getCommits())->count() > 0):
                    $endDate = $commits->last()->getEndDate();
                endif;
                break;
            case Commit::class:
                $endDate = $this->getSteps()?->last()?->getEndDate();
                break;
            case Step::class:
                $endDate = $this->endDate;
                break;
        }

        return $endDate;
    }
}