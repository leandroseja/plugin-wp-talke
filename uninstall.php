<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('talke_crm_token');
delete_option('talke_crm_oauth_state');
delete_option('talke_crm_context_name');
delete_option('talke_crm_failed_queue');
