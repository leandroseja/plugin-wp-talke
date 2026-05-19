<?php

namespace TalkeCRM;

class TokenStore
{
    private const OPT_TOKEN = 'talke_crm_token';
    private const OPT_CONTEXT_NAME = 'talke_crm_context_name';
    private const OPT_STATE = 'talke_crm_oauth_state';
    private const STATE_TTL = 600; // 10 min

    public static function getToken(): ?string
    {
        $t = get_option(self::OPT_TOKEN);
        return $t ?: null;
    }

    public static function saveToken(string $token, string $contextName): void
    {
        update_option(self::OPT_TOKEN, sanitize_text_field($token), false);
        update_option(self::OPT_CONTEXT_NAME, sanitize_text_field($contextName), false);
    }

    public static function deleteToken(): void
    {
        delete_option(self::OPT_TOKEN);
        delete_option(self::OPT_CONTEXT_NAME);
    }

    public static function getContextName(): ?string
    {
        $n = get_option(self::OPT_CONTEXT_NAME);
        return $n ?: null;
    }

    public static function isConnected(): bool
    {
        return !empty(self::getToken());
    }

    public static function generateState(): string
    {
        $state = bin2hex(random_bytes(16));
        update_option(self::OPT_STATE, [
            'value' => $state,
            'expires_at' => time() + self::STATE_TTL,
        ], false);
        return $state;
    }

    public static function validateState(string $candidate): bool
    {
        $stored = get_option(self::OPT_STATE);
        if (!is_array($stored) || empty($stored['value']) || empty($stored['expires_at'])) {
            return false;
        }
        $valid = hash_equals($stored['value'], $candidate) && $stored['expires_at'] >= time();
        delete_option(self::OPT_STATE);
        return $valid;
    }
}
