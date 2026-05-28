=== Talke CRM ===
Contributors: talke
Tags: crm, lead capture, elementor, woocommerce, marketing
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically capture leads from your WordPress site to Talke CRM. Supports tracker, Elementor Pro and WooCommerce.

== Description ==

Talke CRM connects your WordPress site to [Talke CRM](https://crm.talke.com.br) and captures leads automatically, with no need to configure webhooks or copy tokens manually.

**Features:**

* Injects the Talke CRM tracker on every front-end page
* Native capture of Elementor Pro form submissions (via the `elementor_pro/forms/new_record` hook)
* Captures new WooCommerce orders and customer registrations
* OAuth-like connection: the admin clicks "Connect", authorizes on Talke CRM and the token is stored automatically
* Retry queue via wp_cron in case the API is temporarily unavailable

**Requirements:** an active Talke CRM account at [https://crm.talke.com.br](https://crm.talke.com.br).

== Installation ==

1. Install and activate the plugin from the WordPress admin
2. Open the "Talke CRM" item in the admin sidebar
3. Click "Connect with Talke CRM"
4. Sign in to your Talke account and choose which account/client to link to this site
5. From this moment on, captured leads will be sent automatically

== Frequently Asked Questions ==

= Do I need a Talke CRM account? =

Yes. Create one at [https://crm.talke.com.br](https://crm.talke.com.br) before installing the plugin.

= Does the plugin work without Elementor or WooCommerce? =

Yes. Without them, the plugin still injects the tracker (which auto-detects form submissions on the front-end via JavaScript).

= What data is sent to Talke CRM? =

See the "External services" section below for the complete list.

= Where is the authentication token stored? =

Stored in `wp_options` under the key `talke_crm_token`. Automatically removed when the plugin is uninstalled.

== External services ==

This plugin connects to the **Talke CRM** service (operated by Talke, at `https://crm.talke.com.br`) to send data from leads captured on your WordPress site.

**When data is sent:**

* Only **after** the site administrator clicks "Connect with Talke CRM" and explicitly authorizes the integration.
* Before authorization, no data is sent to any external server.

**What data is sent:**

* During the initial authorization (OAuth-like): site URL (`home_url()`) and site name (`get_bloginfo('name')`) are passed as query parameters when the admin is redirected to Talke CRM.
* On every lead capture (Elementor Pro / WooCommerce / tracker JS): name, email, phone filled in the form, page URL, UTM parameters, referrer, and a non-PII browser fingerprint used for deduplication.
* On WooCommerce orders: customer data (name, email, phone) and order summary (order ID, total, number of items).
* When the tracker script is loaded by the browser: standard HTTP request headers (User-Agent, Referer, IP) reach the Talke CRM server, the same way any third-party JavaScript request does.

**Endpoints contacted:**

* `https://crm.talke.com.br/integrations/wordpress/authorize` — browser redirect during the initial authorization (started by the admin clicking "Connect with Talke CRM").
* `https://crm.talke.com.br/api/capture` — server-to-server POST request used for every lead capture (Elementor Pro form submissions, WooCommerce new orders, WooCommerce new customers).
* `https://crm.talke.com.br/tracker.js` — front-end tracker script loaded on every public page via `wp_enqueue_script` once the site is connected. The script captures form submissions and page views from the visitor's browser.

**Service provided by:** Talke.

* Terms of service: [https://talke.com.br/termos.html](https://talke.com.br/termos.html)
* Privacy policy: [https://talke.com.br/privacidade.html](https://talke.com.br/privacidade.html)

== Privacy ==

This plugin does not send any data to external servers before the administrator clicks "Connect with Talke CRM" and authorizes the integration. After connecting, lead data captured from forms (Elementor Pro, WooCommerce) and via the tracker JavaScript is sent to `https://crm.talke.com.br`. See the "External services" section for full details.

== Changelog ==

= 1.0.1 =
* Fix Plugin URI to point to the correct public repository (https://github.com/leandroseja/plugin-wp-talke)
* Document the external Talke CRM service in the readme (data sent, endpoints, Terms of Service and Privacy Policy links)

= 1.0.0 =
* Initial release
* Tracker injected via wp_enqueue_script
* Elementor Pro module (capture via new_record)
* WooCommerce module (new_order + created_customer)
* OAuth-like connection from the admin screen
* Retry queue with wp_cron

== Upgrade Notice ==

= 1.0.1 =
Addresses the WordPress.org plugin review feedback: corrected Plugin URI and added the External services / Privacy disclosure section.

= 1.0.0 =
Initial plugin release.
