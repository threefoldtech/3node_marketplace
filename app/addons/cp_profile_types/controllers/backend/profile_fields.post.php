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

if ($mode == 'manage') {
    $sections = Registry::get('navigation.dynamic.sections');
    $cp_custom_types = fn_cp_get_simple_profile_types('', CART_LANGUAGE, false);
    foreach ($cp_custom_types as $type_code => $t_name) {
        if (!empty($sections[$type_code])) {
            $sections[$type_code]['title'] = $t_name;
        }
    }
    Registry::set('navigation.dynamic.sections', $sections);

    if (!empty($_REQUEST['profile_type'])) {
        $profile_type = fn_cp_get_profile_type_by_code($_REQUEST['profile_type']);
        if (!empty($profile_type['user_type']) && $profile_type['user_type'] == 'V') {
            $default_type = fn_cp_get_default_profile_type($profile_type['user_type']);
            $profile_fields = Tygh::$app['view']->getTemplateVars('profile_fields');
            foreach ($profile_fields as &$fields) {
                foreach ($fields as &$field) {
                    $field['profile_type'] = $default_type;
                }
            }
            Tygh::$app['view']->assign('profile_fields', $profile_fields);
        }
    }
}

if ($mode == 'update' || $mode == 'add') {
    $field = Tygh::$app['view']->getTemplateVars('field');
    if (!empty($field['profile_type'])) {
        $profile_type = fn_cp_get_profile_type_by_code($field['profile_type']);
        if (!empty($profile_type['user_type']) && $profile_type['user_type'] == 'V') {
            $default_type = fn_cp_get_default_profile_type($profile_type['user_type']);
            $field['profile_type'] = $default_type;
            Tygh::$app['view']->assign('field', $field);
        }
    }
}
