<?php
/******************************************************************
# Geolocation ----- Geolocation                                   *
# ----------------------------------------------------------------*
# copyright Copyright (C) 2010 webkul.com. All Rights Reserved.   *
# @license - http://www.gnu.org/licenses/gpl.html GNU/GPL         *
# Websites: http://webkul.com                                     *
*******************************************************************
*/

use Tygh\Registry;
use Tygh\Languages\Languages;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($mode == 'update') {
        fn_wk_cl_geolocation_data($_REQUEST['geolocation_data']);
    }

    if($mode == 'delete') {
        fn_delete_wk_cl_geolocation($_REQUEST['country_code']);
        fn_set_notification('N',__("notify"),__('data_deleted_successfully'));
    }

    if($mode == 'm_delete') {
        foreach($_REQUEST['location_country'] as $country_code => $country) {
            fn_delete_wk_cl_geolocation($country_code);
        }
        fn_set_notification('N',__("notify"),__('data_deleted_successfully'));
    }

    if($mode == 'm_update') {
        foreach($_REQUEST['location_country'] as $country_code => $data) {
            db_query("UPDATE ?:wk_cl_geolocation SET ?u WHERE country_code = ?s",$data,$country_code);
        }
    }

    return array(CONTROLLER_STATUS_OK, 'wk_cl_geolocation.manage');
}

if($mode == 'manage') {
    $languages_list = Languages::getAll();
    $currencies_list = fn_get_currencies_list(array(), 'C', CART_LANGUAGE);
    $countries_list = fn_get_simple_countries();

    list($location_list,$search) = fn_wk_get_cl_geolocations($_REQUEST,Registry::get('settings.Appearance.admin_elements_per_page'));

    Registry::get('view')->assign('location_list',$location_list);
    Registry::get('view')->assign('search',$search);
    Registry::get('view')->assign('languages_list',$languages_list);
    Registry::get('view')->assign('currencies_list',$currencies_list);
    Registry::get('view')->assign('countries_list',$countries_list);

} elseif($mode == 'update') {
    $languages_list = Languages::getAll();
    $currencies_list = fn_get_currencies_list(array(), 'C', CART_LANGUAGE);
    $countries_list = fn_get_simple_countries();
    $country_code = $_REQUEST['country_code'];
    $location_data = fn_get_cl_location_data($country_code);

    Registry::get('view')->assign('location_data',$location_data);
    Registry::get('view')->assign('languages_list',$languages_list);
    Registry::get('view')->assign('currencies_list',$currencies_list);
    Registry::get('view')->assign('countries_list',$countries_list); 
} 