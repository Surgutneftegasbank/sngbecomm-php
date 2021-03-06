<?php

//echo "Running the SNGBEcomm PHP bindings test suite.\n".
     //"If you're trying to use the SNGBEcomm PHP bindings you'll probably want ".
     //"to require('lib/SNGBEcomm.php'); instead of this file\n";

function authorizeFromEnv()
{
  $apiKey = getenv('SNGB_API_KEY');
  if (!$apiKey)
    $apiKey = "";
  SNGBEcomm::setApiKey($apiKey);
}

$ok = @include_once(dirname(__FILE__).'/../vendor/simpletest/simpletest/autorun.php');
if (!$ok) {
  echo "MISSING DEPENDENCY: The SNGBEcomm API test cases depend on SimpleTest. ".
       "Download it at <http://www.simpletest.org/>, and either install it ".
       "in your PHP include_path or put it in the test/ directory.\n";
  exit(1);
}

// Throw an exception on any error
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

set_error_handler('exception_error_handler');
error_reporting(E_ALL | E_STRICT);

require_once(dirname(__FILE__) . '/../lib/SNGBEcomm.php');

require_once(dirname(__FILE__) . '/SNGBEcomm/TestCase.php');

require_once(dirname(__FILE__) . '/SNGBEcomm/PaymentTest.php');

require_once(dirname(__FILE__) . '/SNGBEcomm/ErrorTest.php');
//
//require_once(dirname(__FILE__) . '/Stripe/ApiRequestorTest.php');
//require_once(dirname(__FILE__) . '/Stripe/Error.php');
//require_once(dirname(__FILE__) . '/Stripe/UtilTest.php');
