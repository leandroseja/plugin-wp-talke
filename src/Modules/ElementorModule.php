<?php

namespace TalkeCRM\Modules;

if (!defined('ABSPATH')) {
    exit;
}

use TalkeCRM\Api;

class ElementorModule
{
    public function register(): void
    {
        if (!did_action('elementor_pro/init')) {
            return;
        }
        add_action('elementor_pro/forms/new_record', [$this, 'onSubmit'], 10, 2);
    }

    public function onSubmit($record, $handler): void
    {
        $rawFields = $record->get('fields');
        $fields = [];
        if (is_array($rawFields)) {
            foreach ($rawFields as $key => $field) {
                $fields[$key] = is_array($field) ? ($field['value'] ?? '') : $field;
            }
        }
        $formName = $record->get_form_settings('form_name') ?: 'Elementor Form';

        $email = $fields['email'] ?? $fields['e-mail'] ?? null;
        $name  = $fields['name'] ?? $fields['nome'] ?? null;
        $phone = $fields['phone'] ?? $fields['telefone'] ?? $fields['celular'] ?? null;

        Api::capture([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'form_name'     => $formName,
            'extra_fields'  => $fields,
            'source_detail' => 'elementor_form_submit',
            'page_url'      => wp_get_referer(),
        ]);
    }
}
