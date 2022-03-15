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
    if ($mode == 'delete') {
        if (!empty($_REQUEST['type_id'])) {
            fn_cp_delete_profile_type($_REQUEST['type_id']);
        }
    }
    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['type_ids'])) {
            foreach ($_REQUEST['type_ids'] as $type_id) {
                fn_cp_delete_profile_type($type_id);
            }
        }
    }
    if ($mode == 'update') {
        $cpv1 = ___cp('dHlwZV9pZA');
        $cpv2 = ___cp('dHlwZV9kYPRh');
        if (!empty($_REQUEST[$cpv2])) {
            $type_id = !empty($_REQUEST[$cpv1]) ? $_REQUEST[$cpv1] : 0;
            $type_id = call_user_func(___cp('Zm5fY3BfdPBkYPRlP3Byb2ZpbGVfdHlwZQ'), $type_id, $_REQUEST[$cpv2]);
            return array(CONTROLLER_STATUS_OK, 'cp_profile_types.update?type_id=' . $type_id);
        }
    }
    if ($mode == 'm_update') {
        if (!empty($_REQUEST['types_data'])) {
            foreach ($_REQUEST['types_data'] as $type_id => $type_data) {
                db_query('UPDATE ?:cp_profile_types SET ?u WHERE type_id = ?i', $type_data, $type_id);
            }
        }
    }
    $suffix = '';
    if (!empty($_REQUEST['user_type'])) {
        $suffix .= '?user_type=' . $_REQUEST['user_type'];
    }
    
    return array(CONTROLLER_STATUS_OK, 'cp_profile_types.manage' . $suffix);
}

if ($mode == 'manage') {
    if (fn_allowed_for('MULTIVENDOR')) {
        $active_section = !empty($_REQUEST['user_type']) ? $_REQUEST['user_type'] : 'C';
        Registry::set('navigation.dynamic.sections', array (
            'C' => array (
                'title' => __('cp_user_profile_types'),
                'href' => 'cp_profile_types.manage?user_type=C',
            ),
            'V' => array (
                'title' => __('cp_vendor_profile_types'),
                'href' => 'cp_profile_types.manage?user_type=V',
            )
        ));
        Registry::set('navigation.dynamic.active_section', $active_section);
    }

    $cpv1 = ___cp('cHJvZmlsZV90ePBlcw');
    list($types, $search) = call_user_func(___cp('Zm5fY3BfZ2V0P3Byb2ZpbGVfdHlwZPM'), $_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

    Tygh::$app['view']->assign($cpv1, $types);
    Tygh::$app['view']->assign('search', $search);

} elseif ($mode == 'update' || $mode == 'add') {
    $tabs = array(
        'general' => array(
            'title' => __('general'),
            'js' => true
        ),
        'addons' => array(
            'title' => __('addons'),
            'js' => true
        )
    );
    Registry::set('navigation.tabs', $tabs);

    if (!empty($_REQUEST['type_id'])) {
        $type = fn_cp_get_profile_type_data($_REQUEST['type_id']);
        Tygh::$app['view']->assign('type', $type);

        if (!empty($type['user_type']) && in_array($type['user_type'], array('C', 'V'))) {
            $ug_params = array(
                'type' => $type['user_type'],
                'status' => array('A', 'H')
            );
            Tygh::$app['view']->assign('usergroups', fn_get_usergroups($ug_params), CART_LANGUAGE);

            $default_usergroups = array();
            if ($type['user_type'] == 'V' && Registry::get('addons.vendor_privileges.status') == 'A') {
                $default_usergroup_id = Registry::get('addons.vendor_privileges.default_vendor_usesrgroup');
                if (!empty($default_usergroup_id)) {
                    $default_usergroups[] = $default_usergroup_id;
                }
            }
            Tygh::$app['view']->assign('default_usergroups', $default_usergroups);
        }
    }
} 
