<?php

namespace TalkeCRM;

class ScriptInjector
{
    public function register(): void
    {
        add_action('wp_head', [$this, 'inject'], 1);
    }

    public function inject(): void
    {
        if (is_admin()) {
            return;
        }
        $token = TokenStore::getToken();
        if (!$token) {
            return;
        }

        printf(
            '<script src="%s/tracker.js" data-token="%s" defer></script>' . "\n",
            esc_url(TALKE_CRM_API_BASE),
            esc_attr($token)
        );
    }
}
