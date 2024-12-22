<?php

namespace ToskSh\Tosk\Exception;

class SymfonyStyleNotFoundException extends \Exception {
    public function __construct() {
        parent::__construct(
            "The SymfonyStyle object is not found. Use OutputService->setInput(InputInterface) to initialize it.",
            500,
        );
    }
}