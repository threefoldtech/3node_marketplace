<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Enum\NotificationSeverity;
use Tygh\Registry;
use Tygh\Navigation\LastView;
use Tygh\Languages\Languages;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update') {
        if (!empty($_REQUEST['rule_data']) && !empty($_REQUEST['rule_data']['name']) && !empty($_REQUEST['rule_data']['rule_params'])) {
            if (Registry::get('addons.seo.single_url') == 'Y') {
                $lang_codes = [Registry::get('settings.Appearance.frontend_default_language')];
            } else {
                $lang_codes = array_keys(Languages::getAll());
            }
            foreach ($lang_codes as $lang_code) {
                $created_seo_name = fn_create_seo_name(0, 's', $_REQUEST['rule_data']['name'], 0, $_REQUEST['rule_data']['rule_params'], '', $lang_code);
            }
        }
    }

    if ($mode == 'm_update') {
        if (!empty($_REQUEST['seo_data'])) {
            foreach ($_REQUEST['seo_data'] as $k => $v) {
                if (!empty($v['name'])) {
                    $created_seo_name = fn_create_seo_name(0, 's', $v['name'], 0, $v['rule_params'], '', fn_get_corrected_seo_lang_code(DESCR_SL));
                }
            }
        }
    }

    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['dispatches'])) {
            foreach ($_REQUEST['dispatches'] as $v) {
                fn_delete_seo_name(0, 's', $v);
            }
        }
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['rule_params'])) {
            fn_delete_seo_name(0, 's', $_REQUEST['rule_params']);
        }
    }

    return array(CONTROLLER_STATUS_OK, "seo_rules.manage");
}

if ($mode == 'manage') {

    list($seo_data, $search) = fn_get_seo_rules($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

    Tygh::$app['view']->assign('seo_data', $seo_data);
    Tygh::$app['view']->assign('search', $search);
}

function fn_get_seo_rules($params = array(), $items_per_page = 0, $lang_code = DESCR_SL)
{
    $condition = fn_get_seo_company_condition('?:seo_names.company_id');

    $lang_code = fn_get_corrected_seo_lang_code($lang_code);

    $global_total = db_get_fields("SELECT dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' ?p GROUP BY dispatch", $condition);
    $local_total = db_get_fields("SELECT dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' AND lang_code = ?s ?p", $lang_code, $condition);
    if ($diff = array_diff($global_total, $local_total)) {
        foreach ($diff as $disp) {
            fn_create_seo_name(0, 's', str_replace('.', '-', $disp), 0, $disp, '', DESCR_SL);
        }
    }

    // Init filter
    $params = LastView::instance()->update('seo_rules', $params);

    // Set default values to input params
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    if (isset($params['name']) && fn_string_not_empty($params['name'])) {
        $condition .= db_quote(" AND name LIKE ?l", "%".trim($params['name'])."%");
    }

    if (isset($params['rule_params']) && fn_string_not_empty($params['rule_params'])) {
        $condition .= db_quote(" AND dispatch LIKE ?l", "%".trim($params['rule_params'])."%");
    }

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:seo_names WHERE object_id = '0' AND type = 's' AND lang_code = ?s ?p", $lang_code, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $seo_data = db_get_array("SELECT name, dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' AND lang_code = ?s ?p ORDER BY dispatch $limit", $lang_code, $condition);

    return array($seo_data, $params);
}
