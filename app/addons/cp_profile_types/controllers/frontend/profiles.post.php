<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'add' || $mode == 'update') {
    $params = array(
        'user_type' => !empty($auth['user_type']) ? $auth['user_type'] : 'C'
    );
    if ($params['user_type'] == 'C') {
        $user_data = ($mode == 'update') ? Tygh::$app['view']->getTemplateVars('user_data') : array();
        if (!empty($_REQUEST['cp_profile_type']) && fn_cp_pt_check_allow_change($user_data, 'profile')) {
            $params['profile_type'] = $_REQUEST['cp_profile_type'];
        } elseif (!empty($user_data['cp_profile_type'])) {
            $params['profile_type'] = $user_data['cp_profile_type'];
        }
        $profile_fields = fn_get_profile_fields('C', array(), CART_LANGUAGE, $params);
        Tygh::$app['view']->assign('profile_fields', $profile_fields);
        Tygh::$app['view']->assign('ship_to_another', fn_check_shipping_billing($user_data, $profile_fields));
        Tygh::$app['view']->assign('cp_profile_types', fn_cp_get_simple_profile_types($params['user_type']));
    }
}
