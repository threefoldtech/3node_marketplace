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

use Tygh\Enum\Addons\location\locationObjectTypes;
use Tygh\Enum\Addons\location\locationTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/** @var array $auth */
$auth = Tygh::$app['session']['auth'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update') {
        if (!empty($_REQUEST['product_id']) && fn_location_check_update_permission($_REQUEST['posts'], $auth)) {
            fn_update_product_location($_REQUEST);        }
    }
    return;
}
if ($mode == 'update' && !empty($_REQUEST['product_id'])) {
    $countries_list = fn_get_simple_countries(true, CART_LANGUAGE);
    $locations = fn_get_product_location($_REQUEST['product_id']); 
    $selected_countries = array();
    $else_countries = array();
    if(!empty($locations)){
        $locations = explode('|',$locations);
        foreach($countries_list as $key => $country){
            if(in_array($key,$locations)){
                $selected_countries[$key] =  $country;
            }else{
                $else_countries[$key] =  $country;
            }
        }
    }else{
		$else_countries = $countries_list;
	}

    Registry::get('view')->assign('selected_countries',$selected_countries);
    Registry::get('view')->assign('all_countries',$else_countries);
}
