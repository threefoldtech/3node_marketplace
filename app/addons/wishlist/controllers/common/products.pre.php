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

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var string $mode
 * @var array $auth
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'options') {
        if (!empty($_REQUEST['product_data']) && !empty($_REQUEST['appearance']['wishlist'])) {
            $wishlist = Tygh::$app['session']['wishlist'];
            $product_data = $_REQUEST['product_data'];

            foreach ($product_data as $id => $product) {
                if (isset($wishlist['products'][$id])) {
                    $wishlist['products'][$id] = array_merge($wishlist['products'][$id], $product);
                }
            }

            $products = !empty($wishlist['products']) ? $wishlist['products'] : [];

            if (!empty($products)) {
                foreach ($products as $k => $v) {
                    $extra = $v['extra'];
                    $products[$k] = fn_get_product_data($v['product_id'], $auth);
                    if (empty($products[$k])) {
                        unset($products[$k], $wishlist['products'][$k]);
                        continue;
                    }
                    $products[$k]['extra'] = empty($products[$k]['extra']) ? [] : $products[$k]['extra'];
                    $products[$k]['extra'] = array_merge($products[$k]['extra'], $extra);

                    if (isset($products[$k]['extra']['product_options'])) {
                        $products[$k]['selected_options'] = !empty($product_data[$k]['product_options']) ? $product_data[$k]['product_options'] : $products[$k]['extra']['product_options'];
                    }
                }
            }

            if (!empty($_REQUEST['changed_option'])) {
                $option_id = reset($_REQUEST['changed_option']);
                $key = key($_REQUEST['changed_option']);

                if (isset($products[$key])) {
                    $products[$key]['changed_option'] = $option_id;
                }
            }

            fn_gather_additional_products_data($products, [
                'get_icon'      => true,
                'get_detailed'  => true,
                'get_options'   => true,
                'get_discounts' => true
            ]);

            Tygh::$app['view']->assign('products', $products);
            Tygh::$app['view']->assign('wishlist', $wishlist);

            Tygh::$app['view']->display('addons/wishlist/views/wishlist/view.tpl');
            exit;
        }
    }
}
