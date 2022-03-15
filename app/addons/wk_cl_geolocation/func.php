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
use Tygh\Navigation\LastView;
use Tygh\Embedded;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_wk_cl_geolocation_data($geolocation_data) {
    $country_code = $geolocation_data['country_code'];
    $is_exist_country = db_get_field('SELECT country_code FROM ?:wk_cl_geolocation WHERE country_code = ?s',$country_code);
    if(empty($is_exist_country)) {
        db_query('INSERT INTO ?:wk_cl_geolocation ?e',$geolocation_data);
    } else {
        db_query('UPDATE ?:wk_cl_geolocation SET ?u WHERE country_code = ?s',$geolocation_data,$country_code);
    }
}

function fn_wk_get_cl_geolocations($params, $items_per_page = 0, $lang_code = CART_LANGUAGE) {
    $params = LastView::instance()->update('wk_cl_geolocation', $params);

    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    
    $params = array_merge($default_params, $params);
    
    $condition = $join = $group =  $limit = NULL;   
      
    $sortings = array (
        'country_code' => '?:wk_cl_geolocation.country_code',
        'lang_code'=>'?:wk_cl_geolocation.lang_code',
        'currency_code'=>'?:wk_cl_geolocation.currency_code',
        'commision'=>'?:wk_cl_geolocation.commision',
        'commision_type'=>'?:wk_cl_geolocation.commision_type',
    );    
      
    if (isset($params['country_code'])&& !empty($params['country_code'])) {
        $condition .= db_quote(" AND country_code = ?s", $params['country_code']);
    } 
    if (isset($params['lang_code'])&& !empty($params['lang_code'])) {
        $condition .= db_quote(" AND lang_code = ?s", $params['lang_code']);
    } 
	if (isset($params['currency_code'])&& !empty($params['currency_code'])) {
	    $condition .= db_quote(" AND currency_code = ?s", $params['currency_code']);
	} 
    
    $sorting =db_sort($params, $sortings, 'country_code', 'asc');
    
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(DISTINCT(?:wk_cl_geolocation.country_code)) FROM ?:wk_cl_geolocation WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'],$params['total_items']);
    } 

    $geo_data = db_get_array("SELECT * FROM ?:wk_cl_geolocation WHERE 1 $condition $sorting $limit",$lang_code);

    return array($geo_data, $params);
}

function fn_delete_wk_cl_geolocation($country_code) {
    db_query("DELETE FROM ?:wk_cl_geolocation WHERE country_code = ?s",$country_code);
}

function fn_get_cl_location_data($country_code) {
    $location_data = db_get_row('SELECT * FROM ?:wk_cl_geolocation WHERE country_code = ?s',$country_code);
    return $location_data;
}


function fn_wk_cl_get_location_get_country_based_on_ip() {
	$ip = fn_get_ip();
	$geo_co_data = array();
	$geo_data = file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip['host']);
	// $geo_data = file_get_contents('http://www.geoplugin.net/php.gp?ip=');
	
    return $geo_data;
}

function fn_wk_cl_get_location_info_by_country($country_code) {
    return db_get_row("SELECT lang_code,currency_code FROM ?:wk_cl_geolocation WHERE country_code = ?s AND status = ?s",$country_code,'A');
}

function fn_get_wl_cl_geolocation_simple_list($avail_only = true, $lang_code = CART_LANGUAGE)
{
    $avail_cond = ($avail_only == true)  ? "WHERE a.status = 'A'" : '';

    return db_get_hash_array("SELECT * FROM ?:wk_cl_geolocation as a LEFT JOIN ?:country_descriptions as b ON b.code = a.country_code AND b.lang_code = ?s $avail_cond ORDER BY b.country", 'country_code',$lang_code);
}

function get_location(){
    $manager = Tygh::$app['location'];
    return  $manager->getLocation()->toArray();
}
function fn_wk_cl_geolocation_init_currency_pre(&$params,$area){ 
    if($area == 'C'){ 
        if(isset($_REQUEST['location-change']) && $_REQUEST['location-change'] != ''){
            $_SESSION['wk_geolocation']['set'] = false;
        }
        if(Registry::get('runtime.controller') != 'xmlsitemap') {
            if(!(isset($_SESSION['wk_geolocation']['set']) && $_SESSION['wk_geolocation']['set'])) {
                $_SESSION['wk_geolocation']['currency_code'] = '';
                $geo_co_data = fn_wk_cl_get_location_get_country_based_on_ip();
				$geo_co_data = unserialize($geo_co_data);
                if(isset($geo_co_data) && isset($geo_co_data['geoplugin_countryCode']) && $geo_co_data['geoplugin_countryCode'] != '' && !isset($_REQUEST['location-change'])) {
                    if(isset($geo_co_data['geoplugin_countryCode'])) {
                        $_SESSION['wk_geolocation']['currency_code'] =  $geo_co_data['geoplugin_countryCode'];
                        setcookie('hw_popup_info', $_SESSION['wk_geolocation']['currency_code'], time() + (86400 * 30), "/");
                        $get_country_location_data = fn_wk_cl_get_location_info_by_country($geo_co_data['geoplugin_countryCode']);
                        if(empty($get_country_location_data)) {
                            $default_location_data = Registry::get('addons.wk_cl_geolocation');
                            $get_country_location_data = array(
                                'lang_code' => $default_location_data['languge'],
                                'currency_code' => $default_location_data['currency']
                            );
                        }
                    } else {
                        $default_location_data = Registry::get('addons.wk_cl_geolocation');
                        $get_country_location_data = array(
                            'lang_code' => $default_location_data['languge'],
                            'currency_code' => $default_location_data['currency']
                        );  
                    }
                }else{
                    if(isset($_REQUEST['location-change'])) {
                        $_SESSION['wk_geolocation']['currency_code'] =  $_REQUEST['location-change'];
                        $get_country_location_data = fn_wk_cl_get_location_info_by_country($_REQUEST['location-change']);
                        if(empty($get_country_location_data)) {
                            $default_location_data = Registry::get('addons.wk_cl_geolocation');
                            $get_country_location_data = array(
                                'lang_code' => $default_location_data['languge'],
                                'currency_code' => $default_location_data['currency']
                            );
                        }
                    } else {
                        $default_location_data = Registry::get('addons.wk_cl_geolocation');
                        $get_country_location_data = array(
                            'lang_code' => $default_location_data['languge'],
                            'currency_code' => $default_location_data['currency']
                        );  
                    }

                }
                if(isset($_REQUEST['sl'])) {
                    unset($_REQUEST['sl']);
                }
                if(isset($_REQUEST['currency'])) {
                    unset($_REQUEST['currency']);
                }
                if(!empty($get_country_location_data['currency_code'])){
                    $params['currency'] = $get_country_location_data['currency_code'];
					$_REQUEST['sl'] = $get_country_location_data['lang_code'];
                }
                $_SESSION['wk_geolocation']['set'] = true;
            }
        }

    }
}
function fn_wk_cl_geolocation_get_products(&$params, &$fields, &$sortings, &$condition, &$join)
{
    if ($params['area'] == 'C') {
        if (!empty($_SESSION['wk_geolocation']['currency_code'])) {
            $country_code = "|".$_SESSION['wk_geolocation']['currency_code']."|";
            $condition .= db_quote(' AND product_location LIKE ?l', '%' . $country_code . '%');
        }
    }
}