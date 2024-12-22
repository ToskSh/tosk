<?php
namespace ToskSh\Tosk\Service;

use ToskSh\Tosk\Exception\SymfonyStyleNotFoundException;
use ToskSh\Tosk\Collection\TaskCollection;
use ToskSh\Tosk\Entity\Commit;
use ToskSh\Tosk\Entity\Config;
use ToskSh\Tosk\Entity\Task;
use PhpPkg\CliMarkdown\CliMarkdown;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Emoji\EmojiTransliterator;

class OutputService {
    public function __construct(
        private TimestampService $timestampService,
    ) {
        // $this->output = new ConsoleOutput();

        $this->cliMarkdown = new CliMarkdown();
        $this->cliMarkdown->setTheme([
            'inlineCode' => 'cyan-bg',
        ]);

        $this->transliterator = EmojiTransliterator::create('text-emoji');
    }

    private InputInterface $input;
    private OutputInterface $output;
    private SymfonyStyle|null $symfonyStyle = null;

    private CliMarkdown $cliMarkdown;
    private EmojiTransliterator $transliterator;

    private Config $config;
    private TaskCollection|Task $task;
    private int|null $maxWidthOfColumn = null;

    public function setIO(InputInterface $input, OutputInterface $output): self {
        $this->input = $input;
        $this->output = $output;

        $white = new OutputFormatterStyle('white');
        $this->output->getFormatter()->setStyle('white', $white);
        $whiteBold = new OutputFormatterStyle('white', options: ['bold']);
        $this->output->getFormatter()->setStyle('white-bold', $whiteBold);
        $whiteBlink = new OutputFormatterStyle('white', options: ['blink']);
        $this->output->getFormatter()->setStyle('white-blink', $whiteBlink);
        $whiteBoldBlink = new OutputFormatterStyle('white', options: ['bold', 'blink']);
        $this->output->getFormatter()->setStyle('white-bold-blink', $whiteBoldBlink);
        $red = new OutputFormatterStyle('red');
        $this->output->getFormatter()->setStyle('red', $red);
        $redBold = new OutputFormatterStyle('red', options: ['bold']);
        $this->output->getFormatter()->setStyle('red-bold', $redBold);
        $redBlink = new OutputFormatterStyle('red', options: ['blink']);
        $this->output->getFormatter()->setStyle('red-blink', $redBlink);
        $redBoldBlink = new OutputFormatterStyle('red', options: ['bold', 'blink']);
        $this->output->getFormatter()->setStyle('red-bold-blink', $redBoldBlink);
        $green = new OutputFormatterStyle('green');
        $this->output->getFormatter()->setStyle('green', $green);
        $greenBold = new OutputFormatterStyle('green', options: ['bold']);
        $this->output->getFormatter()->setStyle('green-bold', $greenBold);
        $greenBlink = new OutputFormatterStyle('green', options: ['blink']);
        $this->output->getFormatter()->setStyle('green-blink', $greenBlink);
        $greenBoldBlink = new OutputFormatterStyle('green', options: ['bold', 'blink']);
        $this->output->getFormatter()->setStyle('green-bold-blink', $greenBoldBlink);
        $yellow = new OutputFormatterStyle('yellow');
        $this->output->getFormatter()->setStyle('yellow', $yellow);
        $yellowBold = new OutputFormatterStyle('yellow', options: ['bold']);
        $this->output->getFormatter()->setStyle('yellow-bold', $yellowBold);
        $yellowBlink = new OutputFormatterStyle('yellow', options: ['blink']);
        $this->output->getFormatter()->setStyle('yellow-blink', $yellowBlink);
        $blue = new OutputFormatterStyle('blue');
        $this->output->getFormatter()->setStyle('blue', $blue);
        $blueBold = new OutputFormatterStyle('blue', options: ['bold']);
        $this->output->getFormatter()->setStyle('blue-bold', $blueBold);
        $blueBlink = new OutputFormatterStyle('blue', options: ['blink']);
        $this->output->getFormatter()->setStyle('blue-blink', $blueBlink);
        $magenta = new OutputFormatterStyle('magenta');
        $this->output->getFormatter()->setStyle('magenta', $magenta);
        $magentaBold = new OutputFormatterStyle('magenta', options: ['bold']);
        $this->output->getFormatter()->setStyle('magenta-bold', $magentaBold);
        $magentaBlink = new OutputFormatterStyle('magenta', options: ['blink']);
        $this->output->getFormatter()->setStyle('magenta-blink', $magentaBlink);
        $cyan = new OutputFormatterStyle('cyan');
        $this->output->getFormatter()->setStyle('cyan', $cyan);
        $cyanBold = new OutputFormatterStyle('cyan', options: ['bold']);
        $this->output->getFormatter()->setStyle('cyan-bold', $cyanBold);
        $cyanBlink = new OutputFormatterStyle('cyan', options: ['blink']);
        $this->output->getFormatter()->setStyle('cyan-blink', $cyanBlink);
        $black = new OutputFormatterStyle('black');
        $this->output->getFormatter()->setStyle('black', $black);
        $blackBold = new OutputFormatterStyle('black', options: ['bold']);
        $this->output->getFormatter()->setStyle('black-bold', $blackBold);
        $blackBlink = new OutputFormatterStyle('black', options: ['blink']);
        $this->output->getFormatter()->setStyle('black-blink', $blackBlink);


        $whiteBackground = new OutputFormatterStyle('black', 'white');
        $this->output->getFormatter()->setStyle('white-bg', $whiteBackground);
        $blackBackground = new OutputFormatterStyle('white', 'black');
        $this->output->getFormatter()->setStyle('black-bg', $blackBackground);
        $cyanBackground = new OutputFormatterStyle('black', 'cyan');
        $this->output->getFormatter()->setStyle('cyan-bg', $cyanBackground);
        $magentaBackground = new OutputFormatterStyle('black', 'magenta');
        $this->output->getFormatter()->setStyle('magenta-bg', $magentaBackground);
        $yellowBackground = new OutputFormatterStyle('black', 'yellow');
        $this->output->getFormatter()->setStyle('yellow-bg', $yellowBackground);
        $blueBackground = new OutputFormatterStyle('black', 'blue');
        $this->output->getFormatter()->setStyle('blue-bg', $blueBackground);
        $redBackground = new OutputFormatterStyle('black', 'red');
        $this->output->getFormatter()->setStyle('red-bg', $redBackground);
        $greenBackground = new OutputFormatterStyle('black', 'green');
        $this->output->getFormatter()->setStyle('green-bg', $greenBackground);

        $bold = new OutputFormatterStyle('default', options: ['bold']);
        $this->output->getFormatter()->setStyle('bold', $bold);

        $this->symfonyStyle = new SymfonyStyle($this->input, $this->output);

        return $this;
    }
    
    // public function setInput(InputInterface $input): self {
    //     $this->symfonyStyle = new SymfonyStyle($input, $this->output);
    //     $this->input = $input;

    //     return $this;
    // }

    /**
     * @throws SymfonyStyleNotFoundException
     */
    private function getSymfonyStyle(): SymfonyStyle {
        if (!$this->symfonyStyle instanceof SymfonyStyle):
            throw new SymfonyStyleNotFoundException();
        endif;

        return $this->symfonyStyle;
    }

    private function getInput(): InputInterface {
        return $this->input;
    }

    private function getOutput(): OutputInterface {
        return $this->output;
    }

    public function setConfig(Config $config): self {
        $this->config = $config;

        return $this;
    }

    private function getConfig(): Config {
        return $this->config;
    }

    public function setTask(TaskCollection|Task $task): self {
        $this->task = $task;

        return $this;
    }

    private function getTask(): TaskCollection|Task {
        return $this->task;
    }

    private function getTransliterator(): EmojiTransliterator {
        return $this->transliterator;
    }

    public function setMaxWidthOfColumn(): self {
        stripos(PHP_OS_FAMILY, 'WIN') === 0
            ? $terminalWidth = (int) shell_exec('powershell -Command "&{(Get-Host).UI.RawUI.WindowSize.Width}"')
            : $terminalWidth = (int) shell_exec('tput cols')
        ;

        $this->maxWidthOfColumn = $terminalWidth - (int) ($terminalWidth / 5);

        return $this;
    }

    public function getMaxWidthOfColumn(): int {
        return $this->maxWidthOfColumn;
    }

    public function write(string|null $message = null): self {
        $this->getSymfonyStyle()->write($message);
        return $this;
    }

    public function writeln(string|null $message = null): self {
        $this->getSymfonyStyle()->writeln($message);
        return $this;
    }

    public function outputRenderTask(): self
    {
        $taskArray = $this->taskToArray($task = $this->getTask());
        foreach ($task->getCommits() ?? [] as $commit):
            $this->renderCommit(
                $commit,
                $task->getCommits()->getKey($commit) + 1,
                array_sum(
                    $task
                        ->getCommits()
                        ->allPrevious($commit)
                        ->map(static fn (Commit $previousCommit) => $previousCommit->getSeconds())
                        ->toArray(),
                ),
            );
        endforeach;
        $this->getSymfonyStyle()->text(
            "   " . ($taskArray['running']
                ? "🏃"
                : ($taskArray['archived']
                    ? "🏁"
                    : ($taskArray['duration'] ? "⏸ " : "🎉")
                )
            )
            . ($taskArray['name'] ? " <cyan>" . $taskArray['name'] . "</cyan> " : " ")
            . ($taskArray['duration'] ? "<green-bold>" . $taskArray['duration'] . "</green-bold> " : "")
            . ((!empty($taskArray['current_steps']) && $taskArray['current_steps'] !== $taskArray['duration']) ? "<magenta>(+". $taskArray['current_steps'] . ")</magenta> " : "")
            . ($taskArray['remaining'] ? "🏋️" . $taskArray['remaining'] . " " : "")
            . ($taskArray['commits'] ? "🍻 <yellow>" . $taskArray['commits'] . "</yellow> " : "")
            . "<blue>[" . $taskArray['id']."]</blue>"
        );

        return $this;
    }

    public function outputRenderTasks(): self {
        if (($tasks = $this->getTask()->orderByDayAsc())->isEmpty()):
            $this->getSymfonyStyle()->text([
                "No task(s) found.",
                "",
                "    <info>`<comment>tosk task:start</comment>` to start a new task.</info>",
                " or",
                "    <info>`<comment>tosk commit:create \"Commit message...\"</comment>` to start a new task and create a new commit.</info>",
                "",
            ]);
            return $this;
        endif;

        $dateFiltered = false;
        if (
            ($input = $this->getInput())->getOption('today')
            || $input->getOption('yesterday')
            || $input->getOption('weekly')
            || $input->getOption('monthly')
        ):
            $dateFiltered = true;
        endif;

        foreach ($tasks ?? [] as $task):
            $lastUpdateDate = (new \DateTime())->setTimestamp($task->getLastUpdateDate());
            $startDate = (new \DateTime())->setTimestamp($task->getStartDate());
            $now = new \DateTime();
            if (
                (
                    $lastUpdateDate->format('Y-m-d') === $now->format('Y-m-d')
                    || $startDate->format('Y-m-d') === $now->format('Y-m-d')
                ) && (
                    $dateFiltered === false
                    || $input->getOption('today')
                    || $input->getOption('yesterday')
                    || $input->getOption('weekly')
                    || $input->getOption('monthly')
                ) && $input->getOption('archived') === $task->isArchived()
            ):
                $tables[0][] = $task;
            elseif (
                (
                    $lastUpdateDate->format('Y-m-d') === ($yesterday = $now->modify('-1day'))->format('Y-m-d')
                    || $startDate->format('Y-m-d') === $yesterday->format('Y-m-d')
                ) && (
                    $dateFiltered === false
                    || $input->getOption('yesterday')
                    || $input->getOption('weekly')
                    || $input->getOption('monthly')
                ) && $input->getOption('archived') === $task->isArchived()
            ):
                $tables[1][] = $task;
            elseif (
                (
                    $task->getLastUpdateDate() >= ($week = $now->modify('-1week')->getTimestamp())
                    || $task->getStartDate() >= $week
                ) && (
                    $dateFiltered === false
                    || $input->getOption('weekly')
                    || $input->getOption('monthly')
                ) && $input->getOption('archived') === $task->isArchived()
            ):
                $tables[2][] = $task;
            elseif (
                (
                    $task->getLastUpdateDate() >= ($month = $now->modify('-1month')->getTimestamp())
                    || $task->getStartDate() >= $month
                )
                && ($dateFiltered === false || $input->getOption('monthly'))
                && $input->getOption('archived') === $task->isArchived()
            ):
                $tables[3][] = $task;
            elseif (
                $dateFiltered === false
                && $input->getOption('archived') === $task->isArchived()
            ):
                $tables[4][] = $task;
            endif;
        endforeach;

        if (isset($tables)):
            krsort($tables);
            foreach ($tables as $index => $tasks):
                $this->getSymfonyStyle()->title(
                    $index === 0
                        ? "Today"
                        : ($index === 1
                            ? "Yesterday"
                            : ($index=== 2
                                ? "Last week"
                                : ($index === 3
                                    ? "Last month"
                                    : "Older"
                                )
                            )
                    )
                );

                foreach ($tasks as $task):
                    $task = $this->taskToArray($task);
                    $this->getSymfonyStyle()->text(
                        ($task['id'] === $this->getConfig()->getTaskId() ? "🚩 " : "   ")
                        . ($task['running']
                            ? "🏃"
                            : ($task['archived']
                                ? "🏁"
                                : ($task['duration'] ? "⏸ " : "🎉")
                            )
                        )
                        . ($task['name'] ? " <cyan>" . $task['name'] . "</cyan> " : " ")
                        . ($task['duration'] ? "<green-bold>" . $task['duration'] . "</green-bold> " : "")
                        . ((!empty($task['current_steps']) && $task['current_steps'] !== $task['duration']) ? "<magenta>(+". $task['current_steps'] . ")</magenta> " : "")
                        . ($task['remaining'] ? "🏋️" . $task['remaining'] . " " : "")
                        . ($task['commits'] ? "🍻 <yellow>" . $task['commits'] . "</yellow> " : "")
                        . "<blue>[" . $task['id']."]</blue>"
                    );
                endforeach;
            endforeach;
        endif;

        return $this;
    }

    public function taskToArray(Task $task): array {
        return [
            'id' => $task->getId(),
            'name' => $task->getName(),
            'commits' => $task->getCommits()->count(),
            'remaining' => $this->timestampService->uglyRemainingDateTime($task),
            'duration' => $task->getDuration(),
            'current_steps' => $task->getDuration(onlyCurrentSteps: true),
            'running' => $task->isRun(),
            'archived' => $task->isArchived(),
        ];
    }

    public function renderCommit(Commit $commit, int $index, int $totalSeconds): self {
        $this->getSymfonyStyle()->text(
            " 🍺 "
            . "<green-bold>" . $commit->getDuration(totalSeconds: $totalSeconds) . "</green-bold>"
            . ($commit->getDuration() ? " <magenta>(+" . $commit->getDuration() . ")</magenta>" : "")
            . " <blue>[" . $commit->getId() . "]</blue>"
        );

        if ($message = $commit->getMessage()):
            $this->getSymfonyStyle()->write($this->renderMessage($message));
        endif;

        return $this;
    }

    private function renderMessage(string $message): string {
        return htmlspecialchars_decode(
            $this->wordWrap(
                $this->markdownRender(
                    $this->getTransliterator()->transliterate($message) ?? $message
                ),
                $this->getMaxWidthOfColumn()
            )
        );
    }

    public function markdownRender(string $markdown): string {
        return $this->cliMarkdown->render($markdown);
    }

    public function wordWrap(string $string, int $maxWidth) {
        $lines = explode("\n", $string);
        $wrappedLines = [];
        $isNewParagraph = false;

        foreach ($lines as $line):
            $words = preg_split('/\s+/', $line);
            $currentLine = '';

            foreach ($words as $word):
                if (strlen($currentLine . ' ' . $word) <= $maxWidth):
                    $currentLine .= ($isNewParagraph ? ' ' : '') . $word . ' ';
                    $isNewParagraph = false;
                else:
                    $wrappedLines[] = $currentLine;
                    $currentLine = " " . $word;
                    $isNewParagraph = true;
                endif;
            endforeach;

            if (!empty($currentLine)):
                $wrappedLines[] = $currentLine;
            endif;

            $isNewParagraph = false;
        endforeach;

        return implode("\n", $wrappedLines);
    }
}
