<?php

declare(strict_types=1);

namespace App\Command;

use App\BanksConverter;
use App\ED807Downloader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        private readonly BanksConverter $converter,
        private readonly Filesystem $filesystem,
        #[Autowire('%kernel.project_dir%/public/v1/banks.json')]
        private readonly string $filePath,
        private readonly string $url,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $xml = $this->downloader->fetchXml($this->url);

        $banks = $this->converter->convert($xml);

        $this->filesystem->dumpFile($this->filePath, json_encode($banks));

        return Command::SUCCESS;
    }
}
