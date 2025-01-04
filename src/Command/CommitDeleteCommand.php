<?php
namespace ToskSh\Tosk\Command;

use ToskSh\Tosk\Service\HandlerService;
use ToskSh\Tosk\Service\OutputService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'commit:delete',
    description: '<comment>Deleting</comment> the commit from task',
)]
class CommitDeleteCommand extends Command {
    protected function configure(): void {
        $this
            ->addArgument('commit-id', InputArgument::REQUIRED, '<comment>Commit ID</comment>')
            ->addOption('task-id', 't', InputOption::VALUE_REQUIRED, 'Task <comment>ID</comment> or <comment>name</comment>', null)

            // Config
            ->addOption('config-path', 'C', InputOption::VALUE_REQUIRED, 'Set <comment>/file/path/to/json/config</comment>', false)
            ->addOption('config-task-dir', 'D', InputOption::VALUE_REQUIRED, 'Set <comment>/directory/path/to/tasks</comment> containing a reports', false)
            ->addOption('config-editor', 'P', InputOption::VALUE_REQUIRED, 'Set default <comment>editor</comment> (ex: "<comment>nano</comment>", "<comment>vim</comment>")', false)
        ;
    }

    public function __construct(
        private readonly HandlerService $handlerService,
        private readonly OutputService $outputService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            // Preload max width output
            $this->outputService->setMaxWidthOfColumn();

            // Handler
            $this->handlerService->init(
                $input->getOption('config-path'),
                $input->getOption('config-task-dir'),
                $input->getOption('config-editor'),
                $input->getOption('task-id'),
            )->commitDelete(
                $input->getArgument('commit-id'),
            );

            // Output render into terminal
            $this->outputService
                ->setConfig($this->handlerService->getConfigService()->getConfig())
                ->setTask($this->handlerService->getTask())
                ->setIO($input, $output)
                ->outputRenderTask();

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $helper = new DescriptorHelper();
            $helper->describe($output, $this);

            if ($this->handlerService->getConfigService()->isDebug()):
                $output->writeln("");
                $output->writeln($exception->getTraceAsString());
            endif;

            $output->writeln("");
            $output->writeln('<error>' . $exception->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }
}
