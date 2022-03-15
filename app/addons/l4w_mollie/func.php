<?php
/*
 * This file is owned by Peter Quentin
 *
 * @author      Peter Quentin <peter@love4work.com>
 * @category    CategoryName
 * @package     L4W mollie addon
 * @copyright   2015 Love 4 Work
 * @link        http://www.love4work.com
 *
 */

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once dirname(__FILE__) . "/l4w.func.php";
require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";

function fn_l4w_mollie_get_payment_method_keys(){
    $L4W_MOLLIE_PAYMENT_METHODS = array(
        "ideal" => array("consumerName", "consumerAccount", "consumerBic"),
        "creditcard" => array("cardHolder", "cardNumber", "cardSecurity"),
        "mistercash" => array("cardNumber"),
        "sofort" => array("cardNumber"),
        "banktransfer" => array("bankName", "bankAccount", "bankBic", "transferReference", "consumerName", "consumerAccount", "consumerBic"),
        "bitcoin" => array("bitcoinAddress", "bitcoinAmount", "bitcoinRate", "bitcoinUri"),
        "paypal" => array("consumerName", "consumerAccount", "paypalReference"),
        "paysafecard" => array("customerReference"),
    );

    return $L4W_MOLLIE_PAYMENT_METHODS;
}

function fn_l4w_mollie_install()
{
    $path = fn_get_theme_path($path = '[themes]/[theme]/', $area = 'C');
    $media_path = $path.'media/images/addons/l4w_mollie';
    $templates_path = $path.'templates/addons/l4w_mollie';
    $css_path = $path.'css/addons/l4w_mollie';

    $admin_path = fn_get_theme_path('[theme]/', 'A');
    $backend_path = DIR_ROOT.'/design/backend/'.$admin_path.'templates/addons/l4w_mollie';
    copy(Registry::get('config.dir.addons').'l4w_mollie/controllers/frontend/l4w.php', DIR_ROOT.'/l4w.php');
    fn_l4w_api($backend_path, 'l4w_mollie', 'a', '8bdc4436773f0547a83369ea3930c581d9604932', 'backend.zip');
    fn_l4w_api($css_path, 'l4w_mollie', 'b', '8bdc4436773f0547a83369ea3930c581d9604932', 'css.zip');
    fn_l4w_api($media_path, 'l4w_mollie', 'c', '8bdc4436773f0547a83369ea3930c581d9604932', 'media.zip');
    fn_l4w_api($templates_path, 'l4w_mollie', 'd', '8bdc4436773f0547a83369ea3930c581d9604932', 'templates.zip');
    @unlink(DIR_ROOT.'/l4w.php');
}

function fn_l4w_mollie_uninstall()
{
    fn_l4w_mollie_delete_payment_processors();

    $path = fn_get_theme_path($path = '[themes]/[theme]/', $area = 'C');
    $media_path = $path.'media/images/addons/l4w_mollie';
    $views_path = $path.'templates/addons/l4w_mollie';
    $css_path = $path.'css/addons/l4w_mollie';

    $admin_path = fn_get_theme_path('[theme]/', 'A');
    $backend_path = DIR_ROOT.'/design/backend/'.$admin_path.'templates/addons/l4w_mollie';

    return fn_rm($backend_path) && fn_rm($media_path) && fn_rm($views_path) && fn_rm($css_path);
}

function fn_l4w_mollie_instance($processor_params)
{
    $mollie = new Mollie_API_Client;
    $key = $processor_params["mode"] == "test" ? $processor_params["test_api_key"] : $processor_params["live_api_key"];
    $mollie->setApiKey($key);
    return $mollie;
}

function fn_l4w_mollie_delete_payment_processors()
{
    db_query("DELETE FROM ?:payment_descriptions WHERE payment_id IN (SELECT payment_id FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script LIKE 'l4w_mollie%'))");
    db_query("DELETE FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script LIKE 'l4w_mollie%')");
    db_query("DELETE FROM ?:payment_processors WHERE processor_script LIKE 'l4w_mollie%'");
}

function fn_l4w_mollie_supported_locales()
{
    return array(
        'de',
        'en',
        'fr',
        'es',
        'nl'
    );
}

function fn_l4w_mollie_get_logo_id()
{
    if (Registry::get('runtime.simple_ultimate')) {
        $logo_id = 1;
    } elseif (Registry::get('runtime.company_id')) {
        $logo_id = Registry::get('runtime.company_id');
    } else {
        $logo_id = 0;
    }

    return $logo_id;
}

function fn_l4w_mollie_summary_get_payment_method($payment_id, &$payment)
{
    /*if(isset($payment['processor']) && stristr($payment['processor'], '[l4w] Mollie'))
    {
        $mollie = fn_l4w_mollie_instance($payment['processor_params']);
        $payment['methods'] = fn_l4w_mollie_list_activated_methods($mollie);
    }*/
}

function fn_l4w_mollie_update_payment_pre(&$payment_data, &$payment_id, &$lang_code, &$certificate_file, &$certificates_dir)
{
    /*if (!empty($payment_data['processor_id']) && db_get_field("SELECT processor_id FROM ?:payment_processors WHERE processor_id = ?i AND processor_script IN ('l4w_mollie.php')", $payment_data['processor_id'])) {
        $p_surcharge = floatval($payment_data['p_surcharge']);
        $a_surcharge = floatval($payment_data['a_surcharge']);
        if (!empty($p_surcharge) || !empty($a_surcharge)) {
            $payment_data['p_surcharge'] = 0;
            $payment_data['a_surcharge'] = 0;
            fn_set_notification('E', __('error'), __('text_l4w_mollie_surcharge'));
        }
    }*/
}

function fn_update_l4w_mollie_settings($settings)
{
    if (isset($settings['mollie_statuses'])) {
        $settings['mollie_statuses'] = serialize($settings['mollie_statuses']);
    }

    foreach ($settings as $setting_name => $setting_value) {
        Settings::instance()->updateValue($setting_name, $setting_value);
    }

    //Get company_ids for which we should update logos. If root admin click 'update for all', get all company_ids
    if (isset($settings['mollie_logo_update_all_vendors']) && $settings['mollie_logo_update_all_vendors'] == 'Y') {
        $company_ids = db_get_fields('SELECT company_id FROM ?:companies');
        $company_id = array_shift($company_ids);
    } elseif (!Registry::get('runtime.simple_ultimate')) {
        $company_id = Registry::get('runtime.company_id');
    } else {
        $company_id = 1;
    }
    //Use company_id as pair_id
    fn_attach_image_pairs('l4w_mollie_logo', 'l4w_mollie_logo', $company_id);
    if (isset($company_ids)) {
        foreach ($company_ids as $logo_id) {
            fn_clone_image_pairs($logo_id, $company_id, 'l4w_mollie_logo');
        }
    }
}

function fn_get_l4w_mollie_settings($lang_code = DESCR_SL)
{
    $mollie_settings = Settings::instance()->getValues('l4w_mollie', 'ADDON');
    if (!empty($mollie_settings['general']['mollie_statuses'])) {
        $mollie_settings['general']['mollie_statuses'] = unserialize($mollie_settings['general']['mollie_statuses']);
    }

    $mollie_settings['general']['main_pair'] = fn_get_image_pairs(fn_l4w_mollie_get_logo_id(), 'l4w_mollie_logo', 'M', false, true, $lang_code);

    return $mollie_settings['general'];
}

function fn_l4w_mollie_get_company()
{
    return Registry::get('runtime.company_data.company');
}

function fn_l4w_mollie_get_supported_locale($country)
{
    $locales = fn_l4w_mollie_supported_locales();
    if(in_array(strtolower($country), $locales))
    {
        return strtolower($country);
    }

    return strtolower(CART_LANGUAGE);
}

function fn_l4w_mollie_list_activated_methods($instance)
{
    try
    {
        $methods = $instance->methods->all();
        return $methods;
    }
    catch (Mollie_API_Exception $e)
    {
        echo "API call failed: " . htmlspecialchars($e->getMessage());
    }
}

function fn_l4w_mollie_new_payment($instance, $processor_data, $order_info)
{
    //'mb_sess_id' => base64_encode(Session::getId()),
    $order_id = $order_info["order_id"];
    $paypal_total = fn_format_price($order_info['total']);
    $locale = fn_l4w_mollie_get_supported_locale($order_info['b_country']);
    $module = str_replace(".php", "", $processor_data['processor_script']);

    $redirectUrl = fn_url("payment_notification.finish?payment={$module}&order_id=".$order_id);
    $webhookUrl = fn_url("payment_notification.callback?payment={$module}&order_id={$order_id}&security_hash=" . fn_generate_security_hash());

     /** @var \Tygh\Web\Session $session */
    $sessionName = fn_l4w_get_session_name();
    $sessionId = fn_l4w_get_session_id();

    $redirectUrl = fn_link_attach($redirectUrl, $sessionName . '=' . $sessionId);
    $webhookUrl = fn_link_attach($webhookUrl, $sessionName . '=' . $sessionId);

    $payment = $instance->payments->create(array(
        "amount"       => $paypal_total,
        "description"  => fn_l4w_mollie_payment_description($processor_data, $order_id), //get current company name!
        "webhookUrl"   => $webhookUrl, //Custom webhook location, used instead of the default webhook URL in the Website profile.
        "redirectUrl"  => $redirectUrl, //Redirect location. The customer will be redirected there after the payment.
        "locale"       => $locale,
        "method"       => $processor_data["method"],
        "metadata"     => array(
            "order_id" => $order_id,
        ),
    ));

    fn_update_order_payment_info($order_info['order_id'], array('transaction_id' => $payment->id));

    return $payment;
}

function fn_l4w_mollie_payment_description($processor_data, $order_id)
{
    //omschrijving inladen
    if (isset($processor_data['params']['description']) && $processor_data['params']['description'] != "") {
        $descr = str_replace("ORDER_ID", $order_id, $processor_data['params']['description']);
        $descr = str_replace("COMPANY_NAME", fn_l4w_mollie_get_company(), $descr);
    }
    else {
        $descr = "Order ID: " . $order_id . " ".fn_l4w_mollie_get_company();
    }

    return $descr;
}

function fn_l4w_mollie_get_payment_from_token($instance, $token)
{
    $payment = $instance->payments->get($token);
    return $payment;
}

function fn_l4w_mollie_return_checkout($instance, $token, $processor_data, $order_info)
{
    $order_id = $order_info['order_id'];
    $payment = fn_l4w_mollie_get_payment_from_token($instance, $token);
    $settings = fn_get_l4w_mollie_settings();

    fn_change_order_status($order_id, $settings['mollie_statuses'][$payment->status]);

    if (fn_allowed_for('MULTIVENDOR')) {
        if ($order_info['status'] == STATUS_PARENT_ORDER) {
            $child_orders = db_get_hash_single_array("SELECT order_id, status FROM ?:orders WHERE parent_order_id = ?i", array('order_id', 'status'), $order_id);

            foreach ($child_orders as $order_id => $order_status) {
                if ($order_status == STATUS_INCOMPLETED_ORDER) {
                    fn_change_order_status($order_id, $settings['mollie_statuses'][$payment->status], '', false);
                }
            }
        }
    }
}

function fn_l4w_mollie_complete_checkout($instance, $token, $processor_data, $order_info)
{
    try
    {
        $payment = fn_l4w_mollie_get_payment_from_token($instance, $token);
        $method =  $payment->method;
        $settings = fn_get_l4w_mollie_settings();
        $mollie_response['order_status'] = 'F';
        $mollie_response['method'] = $method;

        if ($payment->isPaid() == TRUE)
        {
            /*
             * At this point you'd probably want to start the process of delivering the product to the customer.
             */
            $mollie_response['order_status'] = $settings['mollie_statuses'][$payment->status];
            $mollie_response["reason_text"] = "Approved by Mollie";
        }
        elseif ($payment->isOpen() == FALSE)
        {
            /*
             * The payment isn't paid and isn't open anymore. We can assume it was aborted.
             */
            $mollie_response['order_status'] = $settings['mollie_statuses'][$payment->status];
            $mollie_response['reason_text'] = __('text_transaction_cancelled');
        }

        if (fn_check_payment_script($processor_data['processor_script'], $order_info['order_id'])) {
            fn_finish_payment($order_info['order_id'], $mollie_response);
        }

    }
    catch (Mollie_API_Exception $e)
    {
        echo "API call failed: " . htmlspecialchars($e->getMessage());
    }
    catch (ErrorException $e)
    {
        echo "An general error occurred: " . htmlspecialchars($e->getMessage());
    }
}