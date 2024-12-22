<?php

namespace ToskSh\Tosk\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractCommandTestCase extends KernelTestCase {
    public string $toskDirectory;
    public string $configPath;
    public string $taskDirectory;

    public Application $application;

    public function setUp(): void {
        parent::setUp();
        
        $this->toskDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tosk';
        $this->configPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tosk.json';
        $this->taskDirectory = $this->toskDirectory;

        @mkdir($this->toskDirectory, recursive: true);
        @mkdir($this->taskDirectory, recursive: true);

        self::bootKernel();
        $this->application = new Application(self::$kernel);
    }

    public function tearDown(): void {
        $this->rmdir($this->toskDirectory);
    }

    private function rmdir(string $directory): void {
        if (is_dir($directory)) {
            $directories = scandir($directory);
            foreach ($directories as $object) {
                if ($object !== "." && $object !== "..") {
                    if (is_dir($directory. DIRECTORY_SEPARATOR .$object) && !is_link($directory."/".$object))
                        $this->rmdir($directory. DIRECTORY_SEPARATOR .$object);
                    else
                        unlink($directory. DIRECTORY_SEPARATOR .$object);
                }
            }
            rmdir($directory);
        }
    }
}