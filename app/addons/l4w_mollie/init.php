<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'update_payment_pre',
    'summary_get_payment_method'
);