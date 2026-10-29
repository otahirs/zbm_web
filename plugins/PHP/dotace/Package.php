<?php
namespace Grav\Plugin\Dotace;

class Package
{
    /**
     * Builds the ZIP at $zipPath (an existing, empty file) and returns notes about skipped documents.
     *
     * @param list<array{id:string,event:array,title:string}> $camps
     * @param list<string> $docs variant keys plus 'seznam'; unknown keys are ignored
     * @param array<string,array{label:string,line:string}> $variants
     * @param callable $markdown fn(string): string
     * @param callable $pdf fn(string $html): string
     * @return list<string>
     */
    public static function build(
        array $camps,
        array $docs,
        array $variants,
        array $defaults,
        MembersClient $members,
        callable $markdown,
        callable $pdf,
        string $zipPath,
        int $currentYear
    ): array {
        if ($camps === []) {
            throw new \InvalidArgumentException('Nevybrali jste žádnou akci.');
        }
        $chosenVariants = array_values(array_filter(array_keys($variants), fn($k) => in_array($k, $docs, true)));
        $wantSeznam = in_array('seznam', $docs, true);
        if ($chosenVariants === [] && !$wantSeznam) {
            throw new \InvalidArgumentException('Nevybrali jste žádný dokument.');
        }

        $files = [];
        $notes = [];
        foreach ($camps as $camp) {
            $id = $camp['id'];
            $title = $camp['title'];

            $race = null;
            $raceId = MembersClient::raceIdFromEventId($id);
            if ($raceId !== null) {
                try {
                    $race = $members->race($raceId);
                } catch (\RuntimeException $e) {
                    if ($wantSeznam) {
                        $notes[] = $title . ': seznam nevygenerován (' . $e->getMessage() . ')';
                    }
                }
            } elseif ($wantSeznam) {
                $notes[] = $title . ': akce není z members, seznam nevygenerován';
            }

            $rozpis = Rozpis::build($camp['event'], $race, $defaults, $title);
            foreach ($chosenVariants as $key) {
                $html = Rozpis::html($rozpis, $variants[$key]['line'], $markdown);
                $files[$id . '_rozpis_' . $key . '.pdf'] = $pdf($html);
            }

            if ($wantSeznam && $race !== null) {
                $files[$id . '_seznam_ucastniku.ods'] = OdsWriter::write(
                    $title . ' - seznam účastníků soustředění',
                    ['#', 'Jméno a Příjmení', 'Rok narození', 'oddíl'],
                    Seznam::rows($race['everyone'], $currentYear)
                );
            }
        }
        if ($notes !== []) {
            $files['README.txt'] = implode("\n", $notes) . "\n";
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Nepodařilo se vytvořit ZIP.');
        }
        foreach ($files as $name => $bytes) {
            $zip->addFromString($name, $bytes);
        }
        if (!$zip->close()) {
            throw new \RuntimeException('Nepodařilo se uložit ZIP.');
        }
        return $notes;
    }
}
