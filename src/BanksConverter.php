<?php

declare(strict_types=1);

namespace App;

final readonly class BanksConverter
{
    /**
     * @return array<array{bic: string, name: string}>
     */
    public function convert(string $xml): array
    {
        $xmlElement = new \SimpleXMLElement($xml);

        $banks = [];

        foreach ($xmlElement->BICDirectoryEntry as $BICDirectoryEntry) {
            $participantInfo = $BICDirectoryEntry->ParticipantInfo;

            $bank = [
                'bic' => (string) $BICDirectoryEntry['BIC'],
                'name' => (string) $participantInfo['NameP'],
            ];

            $banks[] = $bank;
        }

        return $banks;
    }
}
