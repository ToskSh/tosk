<?php
namespace ToskSh\Tosk\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'git:gitignore',
    description: 'Adding <comment>.tosk</comment> && <comment>tosk.json</comment> rule into <comment>.gitgnore</comment>',
    aliases: ['gitignore', 'git'],
)]
class GitGitignoreCommand extends Command {
    protected function execute(InputInterface $input, OutputInterface $output): int {
        if (file_exists($filepath = './.gitignore')):
            $currentContent = file_get_contents($filepath);
            if (str_contains($currentContent, '.tosk') && str_contains($currentContent, 'tosk.json')):
                $output->writeln("<info><comment>.gitignore</comment> contains already <comment>.tosk</comment> && <comment>tosk.json</comment> rule.</info>");
                return Command::SUCCESS;
            endif;
        endif;

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('<question>Adding <comment>.tosk</comment> rule into <comment>.gitgnore</comment> ?</question> <comment>[Y/n]</comment>', true);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $content = "";
        if (file_exists($filepath)):
            $content = "\n\n";
        else:
            $output->writeln('<info><comment>'.$filepath.'</comment> creating</info>');
        endif;
        $content .= "###> tosk/tosk ###\n.tosk\ntosk.json\n###< tosk/tosk ###\n";

        file_put_contents($filepath, $content, FILE_APPEND);

        $output->writeln('<info>Adding <comment>.tosk</comment> && <comment>tosk.json</comment> rule into <comment>'.$filepath.'</comment></info>');

        return Command::SUCCESS;
    }
}
