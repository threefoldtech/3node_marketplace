<?php
/*
 * Powered by Love 4 Work
 * @author Peter Quentin
 * @website http://www.love4work.com
 */

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    require DIR_ROOT.'/app/payments/init_payment.php';
}

if (defined('PAYMENT_NOTIFICATION')) {

    $order_id = (!empty($_REQUEST['order_id'])) ? $_REQUEST['order_id'] : 0;
    $order_info = fn_get_order_info($order_id);
    if (empty($processor_data)) {
        $processor_data = fn_get_processor_data($order_info['payment_id']);
    }

    $token = $order_info['payment_info']['transaction_id'];

    if ($mode == 'callback') {

        if (empty($order_info)) {
            throw new Exception(sprintf("Missing order by specified id (order_id=%s)", $order_id));
        }

        try {
            $mollie = fn_l4w_mollie_instance($processor_data['processor_params']);
            fn_l4w_mollie_complete_checkout($mollie, $token, $processor_data, $order_info);
            exit("OK");
        } catch (Exception $e) {
            exit(sprintf("ERROR: %s", $e->getMessage()));
        }
    } elseif ($mode == 'cancel') {

        $order_info = fn_get_order_info($_REQUEST['order_id']);

        if ($order_info['status'] == 'O' || $order_info['status'] == 'I') {
            $pp_response['order_status'] = 'I';
            $pp_response["reason_text"] = __('text_transaction_cancelled');
            fn_finish_payment($order_info['order_id'], $pp_response);
        }

        fn_order_placement_routines('route', $order_id, false);

    } elseif ($mode == 'finish') {
        if (fn_check_payment_script($processor_data['processor_script'], $order_info['order_id'])) {

            try {
                $mollie = fn_l4w_mollie_instance($processor_data['processor_params']);
                fn_l4w_mollie_return_checkout($mollie, $token, $processor_data, $order_info);
            } catch (Exception $e) {
                exit(sprintf("ERROR: %s", $e->getMessage()));
            }

        }

        fn_order_placement_routines('route', $order_id, false);
        die();
    }
} else {
    $mode = (!empty($mode)) ? $mode : (!empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '');
    $mollie = fn_l4w_mollie_instance($processor_data['processor_params']);

    if($mode == 'place_order' || $mode == 'repay')
    {
        $processor_data['method'] = $paymethod;
        $payment = fn_l4w_mollie_new_payment($mollie, $processor_data, $order_info);

        if($payment)
        {
            fn_create_payment_form($payment->getPaymentUrl(), array(), 'Mollie Server', true, $method = 'get');
        } else {
            exit;
        }
    }

}