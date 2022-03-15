<?php
/**
 *
 * Thank you for your purchase! You are the best!
 *
 * @copyright    (C) 2018 Hungryweb
 * @website      https://www.hungryweb.net/
 * @support      support@hungryweb.net
 * @license      https://www.hungryweb.net/license-agreement.html
 *
 * ---------------------------------------------------------------------------------
 * This is a commercial software, only users who have purchased a valid license
 * and accepts the terms of the License Agreement can install and use this program.
 * ---------------------------------------------------------------------------------
 *
 */

if ( !defined('BOOTSTRAP') ) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Http;

#Hungryweb
function fn_hw_popup_info_install(){ fn_hw_dublin_action('popup_info','install'); }
function fn_hw_popup_info_uninstall(){ fn_hw_dublin_action('popup_info','uninstall'); }
if (!function_exists('fn_hw_dublin_action')){
    function fn_hw_dublin_action($addon,$a){
        $request = array('addon'=>$addon,'host'=>Registry::get('config.http_host'),'path'=>Registry::get('config.http_path'),'version'=>PRODUCT_VERSION,'edition'=>PRODUCT_EDITION,'lang'=>strtoupper(CART_LANGUAGE),'a'=>$a,'love'=>'dublin');
        Http::post('https://api.hungryweb.net/ws/addons', $request);
    }
}
if (!function_exists('fn_hw_dublin_license_info')){
	function fn_hw_dublin_license_info(){
		return '<div class="control-group setting-wide"><label class="control-label">&nbsp;</label><div class="controls"><span><a href="https://www.hungryweb.net/generate-license.html" target="_blank">'.__('hw_license_generator').'</a></span></div></div>';
	}
}