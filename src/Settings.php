<?php

namespace TalkeCRM;

class Settings
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_init', [$this, 'handleActions']);
    }

    public function addMenu(): void
    {
        add_menu_page(
            __('Talke CRM', 'talke-crm'),
            __('Talke CRM', 'talke-crm'),
            'manage_options',
            'talke-crm',
            [$this, 'renderPage'],
            TALKE_CRM_URL . 'assets/menu-icon.png',
            58
        );
    }

    public function handleActions(): void
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }
        if (($_GET['page'] ?? '') !== 'talke-crm') {
            return;
        }

        // Disconnect
        if (($_GET['action'] ?? '') === 'disconnect' && check_admin_referer('talke_crm_disconnect')) {
            TokenStore::deleteToken();
            wp_safe_redirect(admin_url('admin.php?page=talke-crm&disconnected=1'));
            exit;
        }

        // OAuth callback: query params talke_token, state, context_name
        if (isset($_GET['talke_token'], $_GET['state'], $_GET['context_name'])) {
            $state = sanitize_text_field(wp_unslash($_GET['state']));
            $token = sanitize_text_field(wp_unslash($_GET['talke_token']));
            $contextName = sanitize_text_field(wp_unslash($_GET['context_name']));

            if (TokenStore::validateState($state)) {
                TokenStore::saveToken($token, $contextName);
                wp_safe_redirect(admin_url('admin.php?page=talke-crm&connected=1'));
                exit;
            }
            wp_die(esc_html__('Token inválido ou expirado. Tente conectar de novo.', 'talke-crm'));
        }
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $connected = TokenStore::isConnected();
        $contextName = TokenStore::getContextName();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Talke CRM', 'talke-crm') . '</h1>';

        if (!empty($_GET['connected'])) {
            echo '<div class="notice notice-success"><p>' . esc_html__('Conectado com sucesso!', 'talke-crm') . '</p></div>';
        }
        if (!empty($_GET['disconnected'])) {
            echo '<div class="notice notice-info"><p>' . esc_html__('Desconectado.', 'talke-crm') . '</p></div>';
        }

        if ($connected) {
            printf(
                '<p>%s <strong>%s</strong></p>',
                esc_html__('Conectado como:', 'talke-crm'),
                esc_html($contextName ?: '')
            );
            $disconnectUrl = wp_nonce_url(
                admin_url('admin.php?page=talke-crm&action=disconnect'),
                'talke_crm_disconnect'
            );
            echo '<a href="' . esc_url($disconnectUrl) . '" class="button">' . esc_html__('Desconectar', 'talke-crm') . '</a>';
        } else {
            $connectUrl = Connection::buildAuthorizeUrl();
            echo '<p>' . esc_html__('Conecte este site ao seu Talke CRM para começar a capturar leads.', 'talke-crm') . '</p>';
            echo '<a href="' . esc_url($connectUrl) . '" class="button button-primary">' . esc_html__('Conectar com Talke CRM', 'talke-crm') . '</a>';
        }

        echo '</div>';
    }
}
