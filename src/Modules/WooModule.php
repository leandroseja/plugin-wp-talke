<?php

namespace TalkeCRM\Modules;

use TalkeCRM\Api;

class WooModule
{
    public function register(): void
    {
        if (!class_exists('WooCommerce')) {
            return;
        }
        add_action('woocommerce_new_order', [$this, 'onNewOrder']);
        add_action('woocommerce_created_customer', [$this, 'onCreatedCustomer'], 10, 3);
    }

    public function onNewOrder($orderId): void
    {
        $order = wc_get_order($orderId);
        if (!$order) {
            return;
        }

        $fullName = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());

        Api::capture([
            'name'          => $fullName ?: null,
            'email'         => $order->get_billing_email() ?: null,
            'phone'         => $order->get_billing_phone() ?: null,
            'form_name'     => 'WooCommerce Pedido #' . $orderId,
            'source_detail' => 'woo_order',
            'extra_fields'  => [
                'order_id' => $orderId,
                'total'    => (float) $order->get_total(),
                'items'    => count($order->get_items()),
            ],
            'page_url'      => $order->get_checkout_order_received_url(),
        ]);
    }

    public function onCreatedCustomer($customerId, $data = null, $passwordGenerated = null): void
    {
        $user = get_userdata($customerId);
        if (!$user) {
            return;
        }
        Api::capture([
            'name'          => $user->display_name,
            'email'         => $user->user_email,
            'form_name'     => 'WooCommerce Cadastro',
            'source_detail' => 'woo_customer_register',
        ]);
    }
}
