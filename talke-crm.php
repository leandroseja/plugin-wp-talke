<?php
/**
 * Plugin Name:       Talke CRM
 * Plugin URI:        https://github.com/leandroseja/plugin-wp-talke
 * Description:       Captura leads do seu site WordPress automaticamente para o Talke CRM. Inclui tracker, formulários do Elementor e WooCommerce.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Talke
 * Author URI:        https://crm.talke.com.br
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       talke-crm
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TALKE_CRM_VERSION', '1.0.1');
define('TALKE_CRM_FILE', __FILE__);
define('TALKE_CRM_DIR', plugin_dir_path(__FILE__));
define('TALKE_CRM_URL', plugin_dir_url(__FILE__));
define('TALKE_CRM_API_BASE', 'https://crm.talke.com.br');

// Autoload manual (sem Composer)
spl_autoload_register(function ($class) {
    if (strpos($class, 'TalkeCRM\\') !== 0) {
        return;
    }
    $relative = str_replace('TalkeCRM\\', '', $class);
    $path = TALKE_CRM_DIR . 'src/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

add_action('plugins_loaded', function () {
    TalkeCRM\Plugin::instance()->boot();
});

register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('talke_crm_flush_queue')) {
        wp_schedule_event(time() + 3600, 'hourly', 'talke_crm_flush_queue');
    }
});

register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('talke_crm_flush_queue');
});
