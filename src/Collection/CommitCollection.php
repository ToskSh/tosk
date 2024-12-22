<?php

namespace ToskSh\Tosk\Collection;

use ToskSh\Tosk\Entity\Commit;
use Ramsey\Collection\AbstractCollection;

class CommitCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Commit::class;
    }

    public function last(): Commit|null {
        return $this->data[array_key_last($this->data)] ?? null;
    }

    public function first(): Commit|null {
        return $this->data[array_key_first($this->data)] ?? null;
    }

    public function getKey(Commit $commit): mixed {
        return array_search($commit, $this->data, true);
    }

    public function allPrevious(Commit $commit): self {
        $currentKey = $this->getKey($commit);
        return new self(
            array_filter(
                array_map(
                    static fn (Commit $commit, int $key) => $key < $currentKey ? $commit : null,
                    $this->data,
                    array_keys($this->data),
                ),
                static fn (Commit|null $commit) => $commit instanceof Commit,
            )
        );
    }

    public function findOneBy(callable $callback): Commit|null {
        return $this->filter($callback)?->first();
    }
}