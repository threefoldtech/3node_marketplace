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
use Tygh\Enum\ProfileTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//
// Hooks
//

function fn_cp_profile_types_update_user_pre($user_id, &$user_data, $auth, $ship_to_another, $notify_user)
{
    $user_data['firstname'] = isset($user_data['firstname']) ? $user_data['firstname'] : '';
    $user_data['lastname'] = isset($user_data['lastname']) ? $user_data['lastname'] : '';
    if (!empty($user_id)) {
        $current_profile_type = fn_cp_get_user_profile_type($user_id);
        if (!empty($user_data['cp_profile_type']) && $current_profile_type != $user_data['cp_profile_type']) {
            $user_data['cp_old_profile_type'] = $current_profile_type;
        }
    }

    // Automatic user creation
    if (empty($user_id) && !empty(Tygh::$app['session']['cart']['cp_profile_type'])
        && isset(Tygh::$app['session']['cp_acc_skip_creation'])
        && Tygh::$app['session']['cp_acc_skip_creation'] === false
    ) {
        $user_data['cp_profile_type'] = Tygh::$app['session']['cart']['cp_profile_type'];
    }
}

function fn_cp_profile_types_update_profile($action, $user_data, $current_user_data)
{
    if (empty($user_data['user_id']) || empty($user_data['user_type'])) {
        return;
    }
    $cp_profile_type = !empty($user_data['cp_profile_type']) ? $user_data['cp_profile_type'] : '';
    if (fn_allowed_for('MULTIVENDOR') && Registry::get('addons.vendor_privileges.status') == 'A'
        && $user_data['user_type'] == 'V' && !empty($user_data['company_id'])
    ) {
        // Сreating a company admin
        $cp_profile_type = fn_cp_get_company_profile_type($user_data['company_id']);
    }
    if (empty($cp_profile_type)) {
        return;
    }
    // Disable old profile type groups
    if (isset($user_data['cp_old_profile_type'])) {
        $old_type = fn_cp_get_profile_type_by_code($user_data['cp_old_profile_type']);
        if (!empty($old_type['usergroup_ids'])) {
            foreach ($old_type['usergroup_ids'] as $usergroup_id) {
                fn_change_usergroup_status('F', $user_data['user_id'], $usergroup_id);
            }
        }
    }
    // Assign new profile type groups
    if (isset($user_data['cp_old_profile_type']) || $action == 'add') {
        $notify = array(); //array('C' => true);
        $type = fn_cp_get_profile_type_by_code($cp_profile_type);
        if (!empty($type['usergroup_ids'])) {
            $ug_status = Registry::get('addons.cp_profile_types.ug_status');
            foreach ($type['usergroup_ids'] as $usergroup_id) {
                fn_change_usergroup_status($ug_status, $user_data['user_id'], $usergroup_id, $notify);
            }
        }
    }
}

function fn_cp_profile_types_get_users($params, $fields, $sortings, &$condition, $join, $auth)
{
    if (!empty($params['cp_profile_type'])) {
        $default_type = fn_cp_get_default_profile_type('C');
        if ($params['cp_profile_type'] == $default_type) {
            $condition['cp_profile_type'] = db_quote(
                ' AND (?:users.cp_profile_type = ?s OR ?:users.cp_profile_type = "")', $params['cp_profile_type']
            );
        } else {
            $condition['cp_profile_type'] = db_quote(' AND ?:users.cp_profile_type = ?s', $params['cp_profile_type']);
        }
    }
}

function fn_cp_profile_types_get_companies($params, $fields, $sortings, &$condition, $join, $auth, $lang_code, $group)
{
    if (fn_allowed_for('MULTIVENDOR') && !empty($params['cp_profile_type'])) {
        $default_type = fn_cp_get_default_profile_type('V');
        if ($params['cp_profile_type'] == $default_type) {
            $condition .= db_quote(
                ' AND (?:companies.cp_profile_type = ?s OR ?:companies.cp_profile_type = "")', $params['cp_profile_type']
            );
        } else {
            $condition .= db_quote(' AND ?:companies.cp_profile_type = ?s', $params['cp_profile_type']);
        }
    }
}

function fn_cp_profile_types_create_order(&$order)
{
    if (empty($order['cp_profile_type']) || empty(Tygh::$app['session']['cart']['user_data'])) {
        return;
    }
    $profile_data = Tygh::$app['session']['cart']['user_data'];
    $extra_data = fn_cp_pt_get_profile_filtered_data($order['cp_profile_type'], $profile_data, $order);
    if (!empty($extra_data)) {
        $order = array_merge($order, $extra_data);
    }
}

function fn_cp_profile_types_update_order(&$order, $order_id)
{
    if (empty($order['cp_profile_type']) || empty(Tygh::$app['session']['cart']['user_data'])) {
        return;
    }
    $profile_data = Tygh::$app['session']['cart']['user_data'];
    $extra_data = fn_cp_pt_get_profile_filtered_data($order['cp_profile_type'], $profile_data, $order);
    if (!empty($extra_data)) {
        $order = array_merge($order, $extra_data);
    }
}

function fn_cp_profile_types_update_company($company_data, $company_id, $lang_code, $action)
{
    $cp_old_profile_type = !empty($company_data['cp_old_profile_type'])
        ? $company_data['cp_old_profile_type']
        : fn_cp_get_default_profile_type('V');
    
    if ($action == 'update' && !empty($company_data['cp_profile_type'])
        && $company_data['cp_profile_type'] != $cp_old_profile_type
    ) {
        $cp_profile_type = $company_data['cp_profile_type'];
        $company_users = db_get_hash_array(
            'SELECT user_id, cp_profile_type, user_type, is_root FROM ?:users'
            . ' WHERE user_type = ?s AND company_id = ?i', 'user_id', 'V', $company_id
        );
        $useless_auth = $notify = array();
        foreach ($company_users as $user_id => $user_data) {
            $user_data['cp_profile_type'] = $cp_profile_type;
            fn_cp_profile_types_update_user_pre($user_id, $user_data, $useless_auth, false, array());
            db_query('UPDATE ?:users SET cp_profile_type = ?s WHERE user_id = ?i', $cp_profile_type, $user_id);
            fn_cp_profile_types_update_profile('update', $user_data, array());
        }
    }
}

//
// Common functions
//

function fn_cp_pt_get_profile_filtered_data($profile_type, $profile_data, $current_data)
{
    $profile_fields = db_get_fields(
        'SELECT field_name FROM ?:profile_fields'
        . ' WHERE profile_type = ?s AND checkout_show = ?s AND is_default = ?s',
        $profile_type, 'Y', 'Y'
    );
    $profile_data = array_filter($profile_data, function($field) use ($profile_fields) {
        return in_array($field, $profile_fields) ? true : false;
    }, ARRAY_FILTER_USE_KEY);
    $current_data = array_filter($current_data);
    $extra_data = array_diff_key($profile_data, $current_data);
    if (!empty($extra_data)) {
        $extra_data = array_map(function ($value) {
            return is_null($value) ? '' : $value;
        }, $extra_data);
    }
    return $extra_data;
}

function fn_cp_pt_check_allow_change($user_data, $location = 'profiles')
{
    $allow_change = true;
    $user_data = !empty($user_data) ? $user_data : array();
    if ($location == 'profiles') {
        $type_change = Registry::get('addons.cp_profile_types.type_change');
        if (!empty($user_data['user_id'])
            && ($user_data['user_type'] != 'C'
                || $type_change == 'O' && !empty($user_data['cp_profile_type'])
                || $type_change == 'D')
        ) {
            $allow_change = false;
        }
    }
    return $allow_change;
}

function fn_cp_update_profile_type($type_id, $data, $lang_code = DESCR_SL)
{
    /**
     * Update profile type pre hook
     *
     * @param array  $data
     * @param int    $type_id
     * @param string $lang_code
     */
    fn_set_hook('cp_update_profile_type_pre', $data, $type_id, $lang_code);

    $data['extra'] = !empty($data['extra']) && is_array($data['extra']) ? serialize($data['extra']) : '';
    $data['usergroup_ids'] = !empty($data['usergroup_ids']) ? implode(',', $data['usergroup_ids']) : '';

    if (empty($type_id)) {
        $data['code'] = fn_cp_get_new_profile_type_code();
        $type_id = db_query('INSERT INTO ?:cp_profile_types ?e', $data);

        $data['type_id'] = $type_id;
        foreach (fn_get_translation_languages() as $data['lang_code'] => $_v) {
            db_query('REPLACE INTO ?:cp_profile_type_descriptions ?e', $data);
        }

        if (!empty($type_id)) {
            $user_type = !empty($data['user_type']) ? $data['user_type'] : 'C';
            fn_cp_generate_profile_type_fields($data['code'], $user_type);
        }

    } else {
        db_query('UPDATE ?:cp_profile_types SET ?u WHERE type_id = ?i', $data, $type_id);
        db_query(
            'UPDATE ?:cp_profile_type_descriptions SET ?u WHERE type_id = ?i AND lang_code = ?s',
            $data, $type_id, $lang_code
        );
    }

    /**
     * Update profile type post hook
     *
     * @param array  $data
     * @param int    $type_id
     * @param string $lang_code
     */
    fn_set_hook('cp_update_profile_type_post', $data, $type_id, $lang_code);

    return $type_id;
}

function fn_cp_get_profile_type_by_code($code, $lang_code = DESCR_SL)
{
    $type_id = db_get_field('SELECT type_id FROM ?:cp_profile_types WHERE code = ?s', $code);
    return !empty($type_id) ? fn_cp_get_profile_type_data($type_id, $lang_code) : array();
}

function fn_cp_get_profile_type_data($type_id, $lang_code = DESCR_SL)
{
    $type = db_get_row(
        'SELECT types.*, content.* FROM ?:cp_profile_types as types'
        . ' LEFT JOIN ?:cp_profile_type_descriptions as content ON content.type_id = types.type_id'
        . ' WHERE types.type_id = ?i AND content.lang_code = ?s', $type_id, $lang_code
    );
    $type['extra'] = !empty($type['extra']) ? unserialize($type['extra']) : array();
    $type['usergroup_ids'] = !empty($type['usergroup_ids']) ? explode(',', $type['usergroup_ids']) : array();

    /**
     * Post processing of profile type data
     *
     * @param int    $type_id
     * @param string $lang_code
     * @param array  $data
     */
    fn_set_hook('cp_get_profile_type_data_post', $type_id, $lang_code, $data);

    return $type;
}

function fn_cp_delete_profile_type($type_id)
{
    $type_code = db_get_field('SELECT code FROM ?:cp_profile_types WHERE type_id = ?i', $type_id);
    
    db_query('DELETE FROM ?:cp_profile_types WHERE type_id = ?i', $type_id);
    db_query('DELETE FROM ?:cp_profile_type_descriptions WHERE type_id = ?i', $type_id);

    /**
     * Process profile type delete
     *
     * @param int    $type_id
     * @param string $type_code
     */
    fn_set_hook('cp_delete_profile_type', $type_id, $type_code);
    
    if (!empty($type_code)) {
        // Delete profile fields
        fn_cp_delete_profile_type_fields($type_code);
        // Reset linked objects
        db_query('UPDATE ?:users SET cp_profile_type = "" WHERE cp_profile_type = ?s', $type_code);
        db_query('UPDATE ?:orders SET cp_profile_type = "" WHERE cp_profile_type = ?s', $type_code);
        if (fn_allowed_for('MULTIVENDOR')) {
            db_query('UPDATE ?:companies SET cp_profile_type = "" WHERE cp_profile_type = ?s', $type_code);
        }
    }
}


function fn_cp_get_profile_types($params, $items_per_page = 0, $lang_code = DESCR_SL)
{
    /**
     * Changes params for selecting profile types
     *
     * @param array   $param
     * @param int     $items_per_page
     * @param string  $lang_code
     */
    fn_set_hook('cp_get_profile_types_pre', $params, $items_per_page, $lang_code);

    $default_params = array(
        'items_per_page' => $items_per_page,
        'page' => 1
    );
    $params = array_merge($default_params, $params);

    $fields = array (
        '?:cp_profile_types.*',
        '?:cp_profile_type_descriptions.*'
    );

    $join = $condition = $sortings = $group = $limit = '';

    $join .= db_quote(
        ' LEFT JOIN ?:cp_profile_type_descriptions ON ?:cp_profile_type_descriptions.type_id = ?:cp_profile_types.type_id'
        . ' AND lang_code = ?s', $lang_code
    );
    
    if (!empty($params['name'])) {
        $condition .= db_quote(' AND ?:cp_profile_type_descriptions.name LIKE ?l', '%' . $params['name'] . '%');
    }

    if (!empty($params['status'])) {
        $condition .= db_quote(' AND ?:cp_profile_types.status = ?s', $params['status']);
    }
    
    if (!empty($params['user_type'])) {
        $condition .= db_quote(' AND ?:cp_profile_types.user_type = ?s', $params['user_type']);
    }

    $sortings = array(
        'position' => '?:cp_profile_types.position',
        'status' => '?:cp_profile_types.status',
        'user_type' => '?:cp_profile_types.user_type',
        'name' => '?:cp_profile_type_descriptions.name',
    );

    /**
     * Change SQL parameters before profile types selection
     *
     * @param array  $params
     * @param array  $fields
     * @param string $join
     * @param string $condition
     * @param array  $sortings
     * @param string $lang_code
     */
    fn_set_hook('cp_get_profile_types', $params, $fields, $join, $condition, $sortings, $lang_code);
    
    $sorting = db_sort($params, $sortings, 'position', 'asc');

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field(
            'SELECT COUNT(DISTINCT(?:cp_profile_types.type_id)) FROM ?:cp_profile_types ?p WHERE 1 ?p',
            $join, $condition
        );
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $types = db_get_hash_array(
        'SELECT ?p FROM ?:cp_profile_types ?p WHERE 1 ?p ?p ?p', 'type_id',
        implode(', ', $fields), $join, $condition, $sorting, $limit
    );

    if (AREA == 'A') {
        foreach ($types as &$type) {
            if ($type['user_type'] == 'V') {
                $type['count'] = fn_cp_get_profile_type_vendors_count($type['code']);
            } else {
                $type['count'] = fn_cp_get_profile_type_users_count($type['code']);
            }
        }
    }

    /**
     * Changes selected ptofile types
     *
     * @param array  $types
     * @param array  $params
     * @param string $lang_code
     */
    fn_set_hook('cp_get_profile_types_post', $types, $params, $lang_code);

    return array($types, $params);
}

function fn_cp_generate_profile_type_fields($type_code, $user_type = 'C')
{
    $fields_exists = db_get_field('SELECT COUNT(field_id) FROM ?:profile_fields WHERE profile_type = ?s', $type_code);
    if (!empty($fields_exists)) {
        return;
    }
    $default_type = ($user_type == 'V') ? ProfileTypes::CODE_SELLER : ProfileTypes::CODE_USER;
    $default_fields = db_get_hash_array(
        'SELECT * FROM ?:profile_fields WHERE profile_type = ?s', 'field_id', $default_type
    );
    foreach ($default_fields as $def_field_id => $field) {
        $add_match = $update_old_field = false;
        if ($field['section'] == 'B' || $field['section'] == 'S') {
            $matching_ids[$def_field_id]['old_field_id'] = $field['field_id'];
            //$matching_ids[$def_field_id]['matching_id'] = $field['matching_id'];
            //$matching_ids[$def_field_id]['section'] = $field['section'];
            $add_match = true;
            foreach ($matching_ids as $matching) {
                if (empty($matching['new_field_id'])) {
                    continue;
                }
                if ($matching['old_field_id'] == $field['matching_id']) {
                    $field['matching_id'] = $matching['new_field_id'];
                    $update_old_field = true;
                }
            }
        }
        unset($field['field_id']);
        $field['profile_type'] = $type_code;
        $field_id = db_query('INSERT INTO ?:profile_fields ?e', $field);
        
        if ($add_match) {
            $matching_ids[$def_field_id]['new_field_id'] = $field_id;
        }
        if ($update_old_field) {
            db_query('UPDATE ?:profile_fields SET matching_id = ?i WHERE field_id = ?i', $field_id, $field['matching_id']);
        }

        $fields_descr = db_get_array(
            'SELECT * FROM ?:profile_field_descriptions WHERE object_type = ?s AND object_id = ?i',
            'F', $def_field_id
        );
        foreach ($fields_descr as $descr) {
            $descr['object_id'] = $field_id;
            db_query('REPLACE INTO ?:profile_field_descriptions ?e', $descr);
        }
    }
}

function fn_cp_delete_profile_type_fields($type_code)
{
    $standart_codes = fn_cp_get_standart_type_codes();
    if (in_array($type_code, $standart_codes)) {
        return;
    }
    $field_ids = db_get_fields('SELECT field_id FROM ?:profile_fields WHERE profile_type = ?s', $type_code);
    foreach ($field_ids as $field_id) {
        db_query('DELETE FROM ?:profile_fields WHERE field_id = ?i', $field_id);
        db_query(
            'DELETE FROM ?:profile_field_descriptions WHERE object_type = ?s AND object_id = ?i',
            'F', $field_id
        );
    }
}

function fn_cp_get_simple_profile_types($user_type = '', $lang_code = DESCR_SL, $add_default = true)
{
    $condition = '';
    if (!empty($user_type)) {
        $condition .= db_quote(' AND types.user_type = ?s', $user_type);
    } elseif (AREA != 'A') {
        $condition .= db_quote(' AND 0');
    }

    $types = db_get_hash_single_array(
        'SELECT types.code, descr.name FROM ?:cp_profile_types as types'
        . ' LEFT JOIN ?:cp_profile_type_descriptions as descr ON descr.type_id = types.type_id AND lang_code = ?s'
        . ' WHERE types.status = ?s ?p ORDER BY position ASC', array('code', 'name'), $lang_code, 'A', $condition
    );
    if ($add_default) {
        $default_types = array();
        if (empty($user_type) || $user_type != 'V') {
            $default_types[ProfileTypes::CODE_USER] = __('cp_profile_common_user');
        }
        if (fn_allowed_for('MULTIVENDOR')
            && (empty($user_type) || $user_type == 'V')
        ) {
            $default_types[ProfileTypes::CODE_SELLER] = __('cp_profile_common_vendor');
        }
        $types = array_merge($default_types, $types);
    }
    return $types;
}

function fn_cp_get_user_profile_type($user_id)
{
    return db_get_field('SELECT cp_profile_type FROM ?:users WHERE user_id = ?i', $user_id);
}

function fn_cp_get_company_profile_type($company_id)
{
    if (!fn_allowed_for('MULTIVENDOR')) {
        return false;
    }
    return db_get_field('SELECT cp_profile_type FROM ?:companies WHERE company_id = ?i', $company_id);
}

function fn_cp_get_default_profile_type($user_type)
{
    $profile_type = '';
    if ($user_type == 'C') {
        $profile_type = ProfileTypes::CODE_USER;
    } elseif (fn_allowed_for('MULTIVENDOR') && $user_type == 'V') {
        $profile_type = ProfileTypes::CODE_SELLER;
    }
    return $profile_type;
}

function fn_cp_get_profile_type_vendors_count($type_code)
{
    return db_get_field('SELECT COUNT(company_id) FROM ?:companies WHERE cp_profile_type = ?s', $type_code);
}

function fn_cp_get_profile_type_users_count($type_code)
{
    return db_get_field('SELECT COUNT(user_id) FROM ?:users WHERE cp_profile_type = ?s', $type_code);
}

function fn_cp_get_new_profile_type_code()
{
    $standart_codes = fn_cp_get_standart_type_codes();
    $existing_codes = db_get_fields('SELECT code FROM ?:cp_profile_types GROUP BY code');
    $existing_codes = array_merge($existing_codes, $standart_codes);

    $codes = array_diff(range('A', 'Z'), $existing_codes);
    $new_code = $codes[array_rand($codes)]; // OR reset($codes);

    return $new_code;
}

function fn_cp_get_standart_type_codes()
{
    $standart_codes = array();
    if (class_exists('Tygh\Enum\ProfileTypes')) { // get standart codes from ProfileTypes
        $ref_class = new ReflectionClass('Tygh\Enum\ProfileTypes');
        $consts = $ref_class->getConstants();
        foreach ($consts as $c_name => $c_code) {
            if (strpos($c_name, 'CODE_') === 0) {
                $standart_codes[] = $c_code;
            }
        }
    }
    return $standart_codes;
}


function fn_cp_get_template_order_user_data($order)
{
    if (empty($order) || empty($order['cp_profile_type'])) {
        return array();
    }
    $user = array_intersect_key($order, array_flip(array('email', 'firstname', 'lastname', 'phone')));
    $group_fields = fn_get_profile_fields('I', array(), $order['lang_code'], array('profile_type' => $order['cp_profile_type']));
    
    $sections = array('C', 'B', 'S');
    $profile_extra_fields = array();
    foreach ($sections as $section) {
        $user[strtolower($section) . '_fields'] = array();

        if (isset($group_fields[$section])) {
            foreach ($group_fields[$section] as $field) {
                $value = fn_get_profile_field_value($order, $field);

                if (!empty($field['field_name'])) {
                    $f_name = $field['field_name'];
                    if (in_array($field['field_type'], array('A', 'O'))) {
                        $user[$f_name . '_descr'] = $value;
                        $user[$f_name] = isset($order[$f_name]) ? $order[$f_name] : $value;
                    } else {
                        $user[$f_name] = $value;
                    }
                    $profile_extra_fields[$f_name][] = $field['description'];
                } else {
                    $user[strtolower($section) . '_fields'][$field['field_id']] = array(
                        'name' => $field['description'],
                        'value' => $value
                    );
                }
            }
        }
    }
    
    return $user;
}