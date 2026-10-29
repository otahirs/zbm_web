<?php
namespace Grav\Plugin\Dotace;

class MembersClient
{
    /** @var callable */
    private $httpGet;

    public function __construct(
        callable $httpGet,
        private ?MembersAuth $auth = null,
        private string $baseUrl = 'https://members.eob.cz/api/3/zbm'
    ) {
        $this->httpGet = $httpGet;
    }

    public static function raceIdFromEventId(string $eventId): ?int
    {
        return preg_match('/^\d{4}-race_(\d+)\z/', $eventId, $m) ? (int) $m[1] : null;
    }

    /** Race detail incl. `everyone`; with an auth, a 401 renews the token and retries once. */
    public function race(int $raceId): array
    {
        $url = rtrim($this->baseUrl, '/') . '/race/' . $raceId;
        $res = ($this->httpGet)($url, $this->headers(false));
        if ((int)($res['status'] ?? 0) === 401 && $this->auth !== null) {
            $res = ($this->httpGet)($url, $this->headers(true));
        }
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

    private function headers(bool $renewToken): array
    {
        $headers = ['Accept: application/json'];
        if ($this->auth !== null) {
            $headers[] = 'Authorization: Bearer ' . $this->auth->token($renewToken);
        }
        return $headers;
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
