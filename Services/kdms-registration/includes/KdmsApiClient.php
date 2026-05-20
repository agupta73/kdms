<?php

declare(strict_types=1);

namespace KdmsRegistration;

final class KdmsApiClient
{
    /**
     * @param array<string, mixed> $payload
     * @return array{ok: bool, data: array<string, mixed>|null, http_code: int}
     */
    public static function postJson(string $endpoint, array $payload): array
    {
        $base = reg_api_base();
        $key = reg_service_key();
        if ($base === '' || $key === '') {
            return ['ok' => false, 'data' => null, 'http_code' => 0];
        }

        $url = $base . ltrim($endpoint, '/');
        $body = json_encode($payload);
        $ch = curl_init($url);
        if ($ch === false) {
            return ['ok' => false, 'data' => null, 'http_code' => 0];
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-KDMS-SERVICE-KEY: ' . $key,
            ],
        ]);

        $response = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = null;
        if (is_string($response) && $response !== '') {
            $decoded = json_decode($response, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        return ['ok' => $code >= 200 && $code < 300, 'data' => $data, 'http_code' => $code];
    }

    /**
     * @param array<string, mixed> $fields
     * @return array{ok: bool, survivor_key: string, action: string, merge_score: int}
     */
    public static function deduplicate(array $fields): array
    {
        $candidate = strtoupper(trim((string) ($fields['Devotee_Key'] ?? $fields['devotee_key'] ?? '')));
        $res = self::postJson('deduplicateDevotee.php', $fields);
        if (!$res['ok'] || !is_array($res['data']) || empty($res['data']['status'])) {
            kdms_log('ERROR', 'Dedup endpoint failed', [
                'http_code' => $res['http_code'],
                'body' => $res['data'],
            ]);

            return [
                'ok' => false,
                'survivor_key' => $candidate,
                'action' => 'error',
                'merge_score' => 0,
            ];
        }

        $survivor = (string) ($res['data']['Devotee_Key'] ?? $candidate);
        $action = (string) ($res['data']['action'] ?? 'inserted');
        $mergeScore = (int) ($res['data']['merge_score'] ?? 0);

        $mapped = match ($action) {
            'merged' => 'merged',
            'flagged_new' => 'flagged_new',
            default => 'new',
        };

        return [
            'ok' => true,
            'survivor_key' => strtoupper($survivor),
            'action' => $mapped,
            'merge_score' => $mergeScore,
        ];
    }

    public static function addToPrintQueue(string $devoteeKey, string $eventId): bool
    {
        $payload = [
            'devotee_key' => $devoteeKey,
            'eventId' => $eventId,
        ];

        $attempt = static function () use ($payload): bool {
            $res = self::postJson('addToPrintQueue.php', $payload);

            return $res['ok'];
        };

        if ($attempt()) {
            return true;
        }
        sleep(2);

        if ($attempt()) {
            return true;
        }

        kdms_log('ERROR', 'addToPrintQueue failed after retry', ['devotee_key' => $devoteeKey]);

        return false;
    }
}
