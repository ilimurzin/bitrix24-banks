<?php

declare(strict_types=1);

namespace App\Command;

use App\ED807Downloader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:get-banks',
    description: 'Fetches banks and save to file',
)]
final class GetBanksCommand extends Command
{
    public function __construct(
        private readonly ED807Downloader $downloader,
        private readonly Filesystem $filesystem,
        #[Autowire('%kernel.project_dir%/var/banks.xml')]
        private readonly string $filePath,
        private readonly string $url,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Fetching without saving');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');
        }

        $xml = $this->downloader->fetchXml($this->url);

        if (!$input->getOption('dry-run')) {
            $this->filesystem->dumpFile($this->filePath, $xml);
        }

        return Command::SUCCESS;
    }
}
