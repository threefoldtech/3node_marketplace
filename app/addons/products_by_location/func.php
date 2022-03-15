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

use Tygh\Embedded;
use Tygh\Enum\YesNo;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
function fn_location_check_update_permission($posts, $auth)
{
    return $auth['user_type'] === 'A';
}
function fn_update_product_location($post){
    if (!empty($post) && is_array($post)) {
        $data = "|".implode('|', $post['product_location']['countries'])."|";
        $p_id = $post['product_id'];
        $countries_list = fn_get_simple_countries();
        db_query("UPDATE ?:products SET product_location = ?s WHERE product_id = ?i", $data, $p_id);
    }
}
function fn_get_product_location($project_id){
    return db_get_field("SELECT product_location FROM ?:products WHERE product_id = (?i)", $project_id);
}