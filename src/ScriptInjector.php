<?php

namespace TalkeCRM;

if (!defined('ABSPATH')) {
    exit;
}

class ScriptInjector
{
    private const HANDLE = 'talke-crm-tracker';

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_filter('script_loader_tag', [$this, 'addTokenAttribute'], 10, 2);
    }

    public function enqueue(): void
    {
        if (is_admin()) {
            return;
        }
        if (!TokenStore::getToken()) {
            return;
        }
        wp_enqueue_script(
            self::HANDLE,
            TALKE_CRM_API_BASE . '/tracker.js',
            [],
            TALKE_CRM_VERSION,
            true
        );
    }

    public function addTokenAttribute(string $tag, string $handle): string
    {
        if ($handle !== self::HANDLE) {
            return $tag;
        }
        $token = TokenStore::getToken();
        if (!$token) {
            return $tag;
        }
        $attrs = ' data-token="' . esc_attr($token) . '" defer';
        return str_replace(' src=', $attrs . ' src=', $tag);
    }
}
