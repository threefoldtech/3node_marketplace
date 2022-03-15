<?php

use Tygh\Registry;
use Tygh\Languages\Languages;

function fn_settings_variants_addons_wk_cl_geolocation_languge()
{
    $language_all = Languages::getAll();
    $list = array();

    foreach($language_all as $lang_code => $lang_values) {
        if($lang_values['status'] == 'A')
            $list[$lang_code] = $lang_values['name'];
     }
     
    return $list;
}

function fn_settings_variants_addons_wk_cl_geolocation_currency()
{
    $currencies = fn_get_currencies_list(array(), 'C', CART_LANGUAGE);
    $list = array();

    foreach($currencies as $currency_code => $currency) {
            $list[$currency_code] = $currency['description'];
     }

    return $list;
}