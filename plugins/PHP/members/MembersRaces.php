<?php
namespace Grav\Plugin\Members;

/**
 * Turns races from members_api v3 `GET /races` into web events
 * (front matter used by Events::ImportEvents) and home page entry deadlines.
 * No Grav dependency, so it is unit-testable.
 */
class MembersRaces
{
    const RACES_URL = 'https://members.eob.cz/api/3/zbm/races';

    /** members race type name -> code stored in web events; only these are imported */
    const IMPORTED_TYPES = [
        'Závod' => 'Z',
        'Soustředění' => 'S',
        'Trénink' => 'T',
        'Sportovní vyšetření' => 'V',
    ];

    /** ranking name -> web groups the race is shown to */
    const RANKING_GROUPS = [
        'Celostátní' => ['dorost', 'hobby'],
        'Morava' => ['dorost', 'hobby', 'zaci2', 'zaci1'],
        'Oblastní' => ['dorost', 'hobby', 'zaci2', 'zaci1', 'pulci2', 'pulci1', 'zabicky'],
        'Mistrovství' => ['dorost', 'hobby'],
        'Štafety' => ['dorost', 'hobby', 'zaci2', 'zaci1'],
    ];

    /** members transport/accommodation codes: 0 no, 1 yes, 2 auto yes, 3 shared (transport only) */
    const SHARED = 3;

    const ORIS_PREFIX = 'https://oris.ceskyorientak.cz/Zavod?id=';

    /** members stores "no date" as 0, which v3 renders as 1970-01-01 */
    const NO_DATE = '1970-01-01';

    /** Web event id "YYYY-race_<id>" (year of the first day). */
    public static function eventId(array $race): string
    {
        return substr((string)$race['dates'][0], 0, 4) . '-race_' . (int)$race['race_id'];
    }

    public static function isImported(array $race): bool
    {
        return isset($race['race_id'], $race['dates'][0], self::IMPORTED_TYPES[$race['type'] ?? '']);
    }

    /** Event front matter for an imported race (call only when isImported()). */
    public static function toEvent(array $race): array
    {
        $event = [
            'id' => self::eventId($race),
            'start' => $race['dates'][0],
            'end' => $race['dates'][1] ?? $race['dates'][0],
            'title' => (string)($race['name'] ?? ''),
            'link' => self::link($race['link'] ?? null),
        ];
        if (str_starts_with($event['link'], self::ORIS_PREFIX)) {
            $event['orisid'] = substr($event['link'], strlen(self::ORIS_PREFIX));
        }
        $event['place'] = (string)($race['place'] ?? '');
        $event['type'] = self::IMPORTED_TYPES[$race['type']];

        foreach ((array)($race['rankings'] ?? []) as $ranking) {
            foreach (self::RANKING_GROUPS[$ranking] ?? [] as $group) {
                $event[$group] = '1';
            }
        }
        if (str_contains($event['title'], 'D+')) {
            $event['dorost'] = '1';
        }

        $event['note'] = (string)($race['note'] ?? '');
        $transport = (int)($race['transport'] ?? 0);
        if ($transport === self::SHARED) {
            $event['transport'] = 'auty dle přihláškového systému';
        } elseif ($transport > 0) {
            $event['transport'] = 'společná doprava';
        }
        if ((int)($race['accommodation'] ?? 0) > 0) {
            $event['accomodation'] = 'společné ubytování';
        }
        $leader = trim(($race['leader']['name'] ?? '') . ' ' . ($race['leader']['surname'] ?? ''));
        if ($leader !== '') {
            $event['leader'] = $leader;
        }
        return $event;
    }

    /**
     * Upcoming entry deadlines for the home page: [Y-m-d => [[name, date], ...]],
     * first deadline of each non-cancelled race.
     */
    public static function entriesByDate(array $races): array
    {
        $entries = [];
        foreach ($races as $race) {
            if (!is_array($race) || !empty($race['cancelled'])) {
                continue;
            }
            $entry = $race['entries'][0] ?? null;
            if (!is_string($entry) || $entry === self::NO_DATE) {
                continue;
            }
            $entries[$entry][] = ['name' => (string)($race['name'] ?? ''), 'date' => $race['dates'][0] ?? null];
        }
        return $entries;
    }

    /** Scheme-less links get https:// (v3 adds http://, upgrade it the same way). */
    private static function link($value): string
    {
        $link = trim((string)$value);
        if ($link === '') {
            return '';
        }
        if (stripos($link, 'http://') === 0) {
            return 'https://' . substr($link, 7);
        }
        return stripos($link, 'https://') === 0 ? $link : 'https://' . $link;
    }
}
