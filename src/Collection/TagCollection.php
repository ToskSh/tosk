<?php

namespace ToskSh\Tosk\Collection;

use Ramsey\Collection\AbstractCollection;
use ToskSh\Tosk\Entity\Tag;

class TagCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Tag::class;
    }

    /**
     * @return Tag|null
     */
    public function last(): Tag|null {
        return $this->data[array_key_last($this->data)] ?? null;
    }

    /**
     * @return Tag|null
     */
    public function first(): Tag|null {
        return $this->data[array_key_first($this->data)] ?? null;
    }
}