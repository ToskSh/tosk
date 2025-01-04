<?php
namespace ToskSh\Tosk\Entity;

class Tag {
    private string|null $name = null;

    public function setName(string $name): self {
        $this->name = $name;
        
        return $this;
    }

    public function getName(): string {        
        return $this->name;
    }
}
