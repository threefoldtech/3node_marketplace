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

$menu = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cp_profile_types.manage?user_type=C',
    'position' => 850
);

if (fn_allowed_for('MULTIVENDOR')) {
    $menu['subitems'] = array(
        'cp_user_profile_types' => array(
            'href' => 'cp_profile_types.manage?user_type=C',
            'position' => 100
        ),
        'cp_vendor_profile_types' => array(
            'href' => 'cp_profile_types.manage?user_type=V',
            'position' => 200
        )
    );
}

if (isset($schema['central']['cart_power_addons'])) {
    $schema['central']['cart_power_addons']['items']['cp_profile_types'] = $menu;
} else {
    $schema['central']['customers']['items']['cp_profile_types'] = $menu;
}

return $schema;
