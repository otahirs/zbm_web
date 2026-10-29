<?php
namespace Grav\Plugin\Dotace;

class MembersClient
{
    /** @var callable */
    private $httpGet;

    public function __construct(
        callable $httpGet,
        private ?string $token = null,
        private string $baseUrl = 'https://members.eob.cz/api/3/zbm'
    ) {
        $this->httpGet = $httpGet;
    }

    public static function raceIdFromEventId(string $eventId): ?int
    {
        return preg_match('/^\d{4}-race_(\d+)$/', $eventId, $m) ? (int) $m[1] : null;
    }

    public function race(int $raceId): array
    {
        $headers = ['Accept: application/json'];
        if ($this->token !== null && $this->token !== '') {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        $res = ($this->httpGet)(rtrim($this->baseUrl, '/') . '/race/' . $raceId, $headers);
        $status = (int) ($res['status'] ?? 0);
        if ($status !== 200) {
            throw new \RuntimeException('members: HTTP ' . $status);
        }
        $data = json_decode((string) ($res['body'] ?? ''), true);
        if (!is_array($data) || !isset($data['everyone']) || !is_array($data['everyone'])) {
            throw new \RuntimeException('members: neplatná odpověď');
        }
        return $data;
    }

    /** Real transport via Grav's HTTP client (needs Grav; not unit-tested). */
    public static function gravHttpGet(): callable
    {
        return function (string $url, array $headers): array {
            if (!class_exists('\Grav\Common\HTTP\Response')) {
                return ['status' => 0, 'body' => ''];
            }
            try {
                $body = \Grav\Common\HTTP\Response::get($url, ['headers' => $headers]);
                return ['status' => 200, 'body' => (string) $body];
            } catch (\Throwable $e) {
                $code = 0;
                if (method_exists($e, 'getResponse')) {
                    $r = $e->getResponse();
                    if (is_object($r) && method_exists($r, 'getStatusCode')) {
                        $code = (int) $r->getStatusCode();
                    }
                }
                return ['status' => $code, 'body' => ''];
            }
        };
    }
}
