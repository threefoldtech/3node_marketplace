<?php
/*
 * This file is owned by Peter Quentin
 *
 * @author      Peter Quentin <peter@love4work.com>
 * @category    CategoryName
 * @package     L4W mollie addon
 * @copyright   2015 Love 4 Work
 * @link        http://www.love4work.com
 *
 */

use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update' && $_REQUEST['addon'] == 'l4w_mollie' && (!empty($_REQUEST['mollie_settings']) || !empty($_REQUEST['l4w_mollie_logo_image_data']))) {
        $mollie_settings = isset($_REQUEST['mollie_settings']) ? $_REQUEST['mollie_settings'] : array();
        fn_update_l4w_mollie_settings($mollie_settings);
    }
}

if ($mode == 'update') {
    if ($_REQUEST['addon'] == 'l4w_mollie') {
        Registry::get('view')->assign('mollie_settings', fn_get_l4w_mollie_settings());
    }
}
