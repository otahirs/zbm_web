<?php
namespace Grav\Plugin\Dotace;

/**
 * Logs in to members_api v3 with the web's service account and caches the
 * access token in a file (each login creates a new device in members, tokens
 * last 90 days). The token is renewed 7 days before it expires or on demand.
 */
class MembersAuth
{
    const RENEW_BEFORE = 7 * 86400;

    /** @var callable fn(string $url, string $jsonBody, array $headers): array{status:int, body:string} */
    private $httpPost;
    /** @var callable fn(): int */
    private $clock;

    public function __construct(
        callable $httpPost,
        private string $username,
        private string $password,
        private string $cacheFile,
        private string $baseUrl = 'https://members.eob.cz/api/3/zbm',
        ?callable $clock = null
    ) {
        $this->httpPost = $httpPost;
        $this->clock = $clock ?? fn() => time();
    }

    /** Valid access token; logs in when there is no usable cached one or when $renew is set. */
    public function token(bool $renew = false): string
    {
        $now = ($this->clock)();
        if (!$renew) {
            $cached = $this->readCache();
            if ($cached !== null && $cached['expiration'] - self::RENEW_BEFORE > $now) {
                return $cached['access_token'];
            }
        }
        return $this->login();
    }

    private function login(): string
    {
        $res = ($this->httpPost)(
            rtrim($this->baseUrl, '/') . '/system/login',
            json_encode(['username' => $this->username, 'password' => $this->password, 'device_name' => 'zabiny.club web']),
            ['Content-Type: application/json', 'Accept: application/json']
        );
        $status = (int)($res['status'] ?? 0);
        if ($status !== 200) {
            throw new \RuntimeException('members: přihlášení selhalo (HTTP ' . $status . ')');
        }
        $data = json_decode((string)($res['body'] ?? ''), true);
        if (!is_array($data) || !is_string($data['access_token'] ?? null) || $data['access_token'] === '' || !is_int($data['expiration'] ?? null)) {
            throw new \RuntimeException('members: neplatná odpověď na přihlášení');
        }
        $this->writeCache($data['access_token'], $data['expiration']);
        return $data['access_token'];
    }

    /** @return array{access_token:string, expiration:int}|null */
    private function readCache(): ?array
    {
        if (!is_file($this->cacheFile)) {
            return null;
        }
        $data = json_decode((string)@file_get_contents($this->cacheFile), true);
        if (!is_array($data) || !is_string($data['access_token'] ?? null) || !is_int($data['expiration'] ?? null)) {
            return null;
        }
        return $data;
    }

    /** Best effort: a failed write only means the next request logs in again. */
    private function writeCache(string $token, int $expiration): void
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir) && !@mkdir($dir, 0770, true)) {
            return;
        }
        $tmp = @tempnam($dir, 'tok');
        if ($tmp === false) {
            return;
        }
        @chmod($tmp, 0600);
        if (@file_put_contents($tmp, json_encode(['access_token' => $token, 'expiration' => $expiration])) === false || !@rename($tmp, $this->cacheFile)) {
            @unlink($tmp);
        }
    }

    /** Real transport via ext-curl (not unit-tested). */
    public static function curlPost(): callable
    {
        return function (string $url, string $body, array $headers): array {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
            ]);
            $out = curl_exec($ch);
            $status = $out === false ? 0 : (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            return ['status' => $status, 'body' => $out === false ? '' : (string)$out];
        };
    }
}
