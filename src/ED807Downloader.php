<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ED807Downloader
{
    public function __construct(
        private HttpClientInterface $client,
        private Filesystem $filesystem,
    ) {}

    public function fetchXml(string $url): string
    {
        $tempFilePath = $this->filesystem->tempnam(sys_get_temp_dir(), 'ed807_', '.zip');

        try {
            $this->filesystem->dumpFile($tempFilePath, $this->fetchArchive($url));

            return $this->getXmlFromArchive($tempFilePath);
        } finally {
            $this->filesystem->remove($tempFilePath);
        }
    }

    private function fetchArchive(string $url): string
    {
        return $this->client->request('GET', $url)->getContent();
    }

    private function getXmlFromArchive(string $filePath): string
    {
        $archive = new \ZipArchive();

        $result = $archive->open($filePath);

        if ($result !== true) {
            throw new \RuntimeException('Failure opening archive: ' . $result);
        }

        if ($archive->count() === 0) {
            throw new \RuntimeException('Ни одного файла в архиве');
        }

        if ($archive->count() > 1) {
            throw new \RuntimeException('Больше одного файла в архиве, непонятно какой брать');
        }

        $content = $archive->getFromIndex(0);

        if (simplexml_load_string($content) === false) {
            throw new \RuntimeException('Невалидный xml');
        }

        return $content;
    }
}
