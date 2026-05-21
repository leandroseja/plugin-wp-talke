<?php

namespace TalkeCRM;

if (!defined('ABSPATH')) {
    exit;
}

class Plugin
{
    private static ?Plugin $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function boot(): void
    {
        if (is_admin()) {
            (new Settings())->register();
        }

        (new ScriptInjector())->register();
        (new Modules\ElementorModule())->register();
        (new Modules\WooModule())->register();

        if (!wp_next_scheduled('talke_crm_flush_queue')) {
            wp_schedule_event(time() + 3600, 'hourly', 'talke_crm_flush_queue');
        }
        add_action('talke_crm_flush_queue', [Api::class, 'flushQueue']);
    }
}
