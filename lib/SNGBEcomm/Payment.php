<?php

class PaymentException extends Exception { }

class SNGBEcomm_Payment
{
  //Types of transaction
  static $PURCHASE = 1;
  static $CREDIT = 2;
  static $AUTHORIZATION = 4;
  static $CAPTURE = 5;
  static $VOIDCREDIT = 6;
  static $VOIDCAPTURE = 7;
  static $VOIDAUTHORIZATION = 9;

  public function __construct($action=null, $apiKey=null, $terminalid=null, $terminalAlias=null)
  {
    if ($action==null)
      $this->_action = self::$PURCHASE;

    if ($apiKey==null)
      $apiKey=SNGBEcomm::getApiKey();

    if ($terminalid==null)
      $terminalid=SNGBEcomm::getMerchant();

    if ($terminalAlias==null)
      $terminalAlias=SNGBEcomm::getTerminalAlias();
  

    $this->getLiveMode();
    
    $this->_apiKey = $apiKey;
    $this->_terminalid = $terminalid;
    $this->_terminalAlias = $terminalAlias;
  }

  public static function convertMoneyToString($money) {
    $string = $money / 100 + '';
    return $string; 
  }

  public static function convertStringToMoney($string) {
    $money = $string * 100;
    return $money + ''; 
  }

  // amount in minimal currency unit
  // amount в минимальных значениях валюты (копейках или центах)
  public function create($trackid=null, $amount=null, $additional_fields=null, $action=null) {//, $udfs=array($udf1=>null)) {
    //TODO: сделать проверку на amount по точке и минимальному значению
    if ($amount==null)
      throw new PaymentException('Цена должна быть указана!');
    else
      $price = self::convertMoneyToString($amount);
    $action = $this->checkAction($action);
    $hash = self::signature($trackid, $price, $action);

    if ($additional_fields==null) {
      $additional_fields = array(
        'udf1' => null,
        'udf2' => null,
        'udf3' => null,
        'udf4' => null
      ); 
    }

    $params = array(
      'merchant' => $this->_terminalid,
      'terminal' => $this->_terminalAlias,
      'action' => $action,
      'amt' => $price,
      'trackid' => $trackid,
      'udf1' => array_key_exists('udf1', $additional_fields)?$additional_fields["udf1"]: null,
      'udf2' => array_key_exists('udf2', $additional_fields)?$additional_fields["udf2"]: null,
      'udf3' => array_key_exists('udf3', $additional_fields)?$additional_fields["udf3"]: null,
      'udf4' => array_key_exists('udf4', $additional_fields)?$additional_fields["udf4"]: null,
      'udf5' => $hash
    );
    
    $url = $this->paymentUrl();
    $result = $this->curlpost($params, $url);
    return $result;
  }

  public function manage($amount=null, $trackid=null, $tranid=null, $paymentid=null, $action=null) {
    //TODO: сделать проверку на amount по точке и минимальному значению
    if ($amount==null)
      throw new PaymentException('Цена должна быть указана!');
    else
      $price = self::convertMoneyToString($amount);

    // Signature has another realiazation for manage transaction
    $salt = $this->_terminalid . $price . $trackid . SNGBEcomm::getApiKey();         
    $hash = sha1($salt);

    $params = array(
      'merchant' => $this->_terminalid,
      'terminal' => $this->_terminalAlias,
      'action' => $action ,
      'amt' => $price ,
      'paymentid' => $paymentid ,
      'trackid' => $trackid ,            
      'tranid' => $tranid ,
      'udf5' => $hash 
    );
    $url = $this->manageTranUrl();
    $result = $this->curlpost($params, $url);
    return $result;
  }

  private function curlpost($params, $url) {
    $postdata = "";
    foreach ( $params as $key => $value ) 
      $postdata .= "&".rawurlencode($key)."=".rawurlencode($value);
    
    // POST
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url );
    curl_setopt ($ch, CURLOPT_POST, 1 );
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata );
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec ($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    //if ($curl_errno > 0) {
        //echo "cURL Error ($curl_errno): $curl_error";
    //} else {
        //echo "Data received ";
    //}
    return $result;
  }
  
  // Generate Endpoint URL for system status (test or production)
  public function paymentUrl() {
    $this->getLiveMode();
    return $this->_url . '/PaymentInitServlet';      
  }

  public function manageTranUrl() {
    $this->getLiveMode();
    return $this->_url . '/PaymentTranServlet';      
  }

  private function getLiveMode() {
    if (SNGBEcomm::getLiveMode()) {
      $this->_url = SNGBEcomm::$productionApiBase;
    }
    else {
      $this->_url = SNGBEcomm::$testApiBase;
    }
  }

  private function checkAction($action=null) {
    //TODO: Check intrance action value in acceptable set of actions;
    if($action != null) {
      $this->_action = $action;
    }
    if($action == null || $action == '') {
      $this->_action = self::$PURCHASE;
      $action = $this->_action;
    }
    return $action;
  }

  // Hash signature
  public static function signature($trackid=null, $amount=null, $action=null, $merchant=null, $apiKey=null) {
    if ($action==null) {
      $action = self::$PURCHASE; 
    }
    if ($trackid==null or $amount==null) {
      return 0;
    }
    if ($apiKey==null)
      $apiKey=SNGBEcomm::getApiKey();

    if ($merchant==null)
      $merchant=SNGBEcomm::getMerchant();

    if ($apiKey==null or $merchant==null)
      return 0;

    $salt = $merchant . $amount . $trackid . $action . $apiKey;
    return sha1($salt);
  }
}
