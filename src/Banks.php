<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class Banks
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/var/banks.xml')]
        private string $filePath,
    ) {}

    /**
     * @return Bank[]
     */
    public function all(): array
    {
        // for now get from file
        // later we automate this
        $xmlElement = new \SimpleXMLElement(file_get_contents($this->filePath));

        $banks = [];

        foreach ($xmlElement->BICDirectoryEntry as $BICDirectoryEntry) {
            $bic = (string) $BICDirectoryEntry['BIC'];
            $participantInfo = $BICDirectoryEntry->ParticipantInfo;
            $name = (string) $participantInfo['NameP'];

            $banks[] = new Bank(
                $bic,
                $name,
            );
        }

        return $banks;
    }
}
