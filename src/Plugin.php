<?php

namespace TalkeCRM;

class Plugin
{
    private static ?Plugin $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function boot(): void
    {
        load_plugin_textdomain('talke-crm', false, dirname(plugin_basename(TALKE_CRM_FILE)) . '/languages');

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
