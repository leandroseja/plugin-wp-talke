<?php

namespace TalkeCRM;

class Api
{
    private const OPT_QUEUE = 'talke_crm_failed_queue';
    private const QUEUE_MAX = 50;

    public static function capture(array $payload): void
    {
        $token = TokenStore::getToken();
        if (!$token) {
            return;
        }

        $payload['token'] = $token;
        $payload['source'] = 'wordpress_plugin';

        if (!empty($payload['email']) || !empty($payload['phone'])) {
            $payload['client_fingerprint'] = hash(
                'sha256',
                ($payload['email'] ?? '') . '|' . ($payload['phone'] ?? '') . '|' . ($payload['form_name'] ?? '')
            );
        }

        $response = wp_remote_post(TALKE_CRM_API_BASE . '/api/capture', [
            'timeout'  => 5,
            'blocking' => false,
            'body'     => wp_json_encode($payload),
            'headers'  => ['Content-Type' => 'application/json'],
        ]);

        if (is_wp_error($response)) {
            self::enqueue($payload);
        }
    }

    private static function enqueue(array $payload): void
    {
        $queue = get_option(self::OPT_QUEUE, []);
        if (!is_array($queue)) {
            $queue = [];
        }
        $queue[] = $payload;
        if (count($queue) > self::QUEUE_MAX) {
            $queue = array_slice($queue, -self::QUEUE_MAX);
        }
        update_option(self::OPT_QUEUE, $queue, false);
    }

    public static function flushQueue(): void
    {
        $queue = get_option(self::OPT_QUEUE, []);
        if (!is_array($queue) || empty($queue)) {
            return;
        }
        $remaining = [];
        foreach ($queue as $payload) {
            $response = wp_remote_post(TALKE_CRM_API_BASE . '/api/capture', [
                'timeout'  => 5,
                'blocking' => true,
                'body'     => wp_json_encode($payload),
                'headers'  => ['Content-Type' => 'application/json'],
            ]);
            if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) >= 500) {
                $remaining[] = $payload;
            }
        }
        update_option(self::OPT_QUEUE, $remaining, false);
    }
}
