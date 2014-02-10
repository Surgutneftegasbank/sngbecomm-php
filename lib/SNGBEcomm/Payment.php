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
  

    $this->checkLiveMode();
    
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
  public function create($trackid=null, $amount=null, $action=null){//, $udfs=array($udf1=>null)) {
    //TODO: сделать проверку на amount по точке и минимальному значению
    if ($amount==null)
      throw new PaymentException('Цена должна быть указана!');
    else
      $price = self::convertMoneyToString($amount);
    $url = $this->paymentUrl();
    $action = $this->checkAction($action);
    $hash = self::signature($trackid, $price, $action);

    $params = array(
      'merchant' => $this->_terminalid,
      'terminal' => $this->_terminalAlias,
      'action' => $action,
      'amt' => $price,
      'trackid' => $trackid,
      //'udf1' => "><img src=x onerror=prompt(2)>",
      //TODO: доделать udf
      'udf5' => $hash
    );
    
    // Параметры запроса 
    $postdata = "";
    foreach ( $params as $key => $value ) $postdata .= "&".rawurlencode($key)."=".rawurlencode($value);
    
    // POST
    // Do POST
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
    //
    //trackId
    //$requestor = new Stripe_ApiRequestor($this->_apiKey);
    //$url = $this->instanceUrl() . '/capture';
    //list($response, $apiKey) = $requestor->request('post', $url, $params);
    //$this->refreshFrom($response, $apiKey);
    //return $this;
    return $result;
  }

  // Generate Endpoint URL for system status (test or production)
  public function paymentUrl() {
    $this->checkLiveMode();
    return $this->_url . '/PaymentInitServlet';      
  }

  private function checkLiveMode() {
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
  //private function signature($trackid=null, $amount=null, $action=null) {
    //if ($action==null) {
      //$action = self::$PURCHASE; 
    //}
    ////$hash_psk = sha1($this->_apiKey);
    //$hash_psk = $this->_apiKey;
    //$salt = $this->_terminalid . $amount . $trackid . $action . $hash_psk;
    //return sha1($salt);
  //}

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

    //$hash_psk = sha1($apiKey);
    $salt = $merchant . $amount . $trackid . $action . $apiKey;
    return sha1($salt);
  }
}
