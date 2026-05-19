<?php

namespace TalkeCRM;

class Connection
{
    public static function buildAuthorizeUrl(): string
    {
        $state = TokenStore::generateState();
        $returnUrl = admin_url('admin.php?page=talke-crm');

        return add_query_arg([
            'state'      => $state,
            'return_url' => $returnUrl,
            'site_name'  => get_bloginfo('name'),
            'site_url'   => home_url(),
        ], TALKE_CRM_API_BASE . '/integrations/wordpress/authorize');
    }
}
