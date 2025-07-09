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

            if (isset($participantInfo['EnglName'])) {
                $bank['nameInEnglish'] = (string) $participantInfo['EnglName'];
            }

            if (isset($participantInfo['RegN'])) {
                $bank['registryNumber'] = (string) $participantInfo['RegN'];
            }

            $postalIndex = isset($participantInfo['Ind']) ? ((string) $participantInfo['Ind']) : null;
            $localityType = isset($participantInfo['Tnp']) ? ((string) $participantInfo['Tnp']) : null;
            $localityName = isset($participantInfo['Nnp']) ? ((string) $participantInfo['Nnp']) : null;
            $address = isset($participantInfo['Adr']) ? ((string) $participantInfo['Adr']) : null;

            $addressCombined = implode(
                ', ',
                array_filter([
                    $postalIndex,
                    implode(
                        ' ',
                        array_filter([
                            $localityType,
                            $localityName,
                        ]),
                    ),
                    $address,
                ]),
            );

            if ($postalIndex) {
                $bank['postalIndex'] = $postalIndex;
            }

            if ($localityType) {
                $bank['localityType'] = $localityType;
            }

            if ($localityName) {
                $bank['localityName'] = $localityName;
            }

            if ($address) {
                $bank['address'] = $address;
            }

            if ($addressCombined) {
                $bank['addressCombined'] = $addressCombined;
            }

            $banks[] = $bank;
        }

        return $banks;
    }
}
