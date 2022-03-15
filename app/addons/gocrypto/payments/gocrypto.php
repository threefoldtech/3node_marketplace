<?php
/**
 * @var array $processor_data
 * @var array $order_info
 * @var string $mode
 */

defined('BOOTSTRAP') or die('Access denied');

// Return from ecommerce website
if (defined('PAYMENT_NOTIFICATION')) {
    $order_id = isset($_REQUEST['order_id'])
        ? $_REQUEST['order_id']
        : null;

    if (!fn_check_payment_script('gocrypto.php', $order_id)) {
        die('Access denied');
    }

    if ($mode == 'return') {
        if (Tygh::$app['session']['gocrypto_token'] != $_REQUEST['token']) die('Invalid token');
        fn_change_order_status($order_id, 'P', STATUS_INCOMPLETED_ORDER);
    } elseif ($mode == 'cancel') {
        $order_info = fn_get_order_info($order_id);
        $pp_response['order_status'] = 'I';
        $pp_response['reason_text'] = "Canceled";
        fn_finish_payment($order_id, $pp_response);
    }
    fn_order_placement_routines('route', $order_id);
} else {
    /** @var int $order_id */

    $currency = CART_SECONDARY_CURRENCY;

    $client_id = $processor_data['processor_params']['client_id'];
    $client_secret = $processor_data['processor_params']['client_secret'];


    if ($processor_data['processor_params']['mode'] === 'test') {
        $endpoint = 'https://ecommerce.staging.gocrypto.com/api';
    } else {
        $endpoint = 'https://ecommerce.gocrypto.com/api';
    }

    $total = $order_info['total'];

    $pool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $pool.= "abcdefghijklmnopqrstuvwxyz";
    $pool.= "0123456789";

    $tokenreturset = substr(str_shuffle(str_repeat($pool, 5)), 0, 7);
    Tygh::$app['session']['gocrypto_token'] = $tokenreturset;
    $return_url = fn_url("payment_notification.return?payment=gocrypto&token=". $tokenreturset ."&order_id={$order_id}", AREA, 'current');
    $cancel_url = fn_url("payment_notification.cancel?payment=gocrypto&order_id={$order_id}", AREA, 'current');
    $notify_url = fn_url("payment_notification.gocrypto_ipn", AREA, 'current');

    fn_pp_save_mode($order_info);

    $base_url = "";
    $protocol = "http";
    if(isset($_SERVER['HTTPS'])){
           $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
       }
      $base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_URL, $endpoint . '/auth');
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'X-ELI-Client-ID: ' . trim($client_id),
        'X-ELI-Client-Secret: ' . trim($client_secret),
        'Site-Host: ' . $base_url,
        'Content-Type: application/json',
                                                 
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    $resulthwe = curl_exec($curl);
    var_dump($resulthwe);
    if (!$resulthwe) {
        die("Connection Failure");
    }
    curl_close($curl);

    $responseData = (array)json_decode($resulthwe, true);
    var_dump($responseData);
    if ($responseData['status'] == 1 && !empty($responseData['data']['access_token'])) {
        $apiContext = $responseData['data']['access_token'];
    } else {
        die("Authetication error");
    }

    $company_id = fn_get_default_company_id();
    $data = [
        'shop_name' => fn_get_company_name($company_id),
        'amount' => [
            'total' => $total,
            'currency' => $currency,
        ],
        'items' => [],
        'return_url' => $return_url,
        'cancel_url' => $cancel_url
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_URL, $endpoint . "/charges");
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'X-ELI-Access-Token: ' . trim($apiContext),
        'Content-Type: application/json',
        'X-ELI-Locale: ' . CART_LANGUAGE
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $resulthwe = curl_exec($curl);
    if (!$resulthwe) {
        die("Connection Failure");
    }
    curl_close($curl);

    $decondeset = json_decode($resulthwe, true);

    if (!empty($decondeset['data'])) {
        $rediecturl = $decondeset['data']['redirect_url'];
        echo "<script>";
        echo "location.replace('" . $rediecturl . "');";
        echo "</script>";
    } else {
        echo $decondeset['message'];
    }
}
