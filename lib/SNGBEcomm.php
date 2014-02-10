<?php

// Tested on PHP 5.3

if (!function_exists('curl_init')) {
    throw new Exception('SNGBEcomm needs the CURL PHP extension.');
}
//if (!function_exists('json_decode')) {
    //throw new Exception('Stripe needs the JSON PHP extension.');
//}
//if (!function_exists('mb_detect_encoding')) {
    //throw new Exception('Stripe needs the Multibyte String PHP extension.');
//}

// SNGBEcomm
require(dirname(__FILE__) . '/SNGBEcomm/SNGBEcomm.php');

require(dirname(__FILE__) . '/SNGBEcomm/Payment.php');
require(dirname(__FILE__) . '/SNGBEcomm/Error.php');
// Utilities
//require(dirname(__FILE__) . '/SNGBEcomm/Util.php');
//require(dirname(__FILE__) . '/SNGBEcomm/Util/Set.php');

// Errors
//require(dirname(__FILE__) . '/SNGBEcomm/Error.php');
