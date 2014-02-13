<?php

class PaymentTest extends UnitTestCase {

  public function testPaymentCreate()
  {
    authorizeFromEnv();
    $payment = new SNGBEcomm_Payment('1', '64fdfab72758601fbff4dd0ef54fa6e6d96338f5', '7000', '7000-alias');
    $url = $payment->create(2, 1000);
    $this->assertTrue(strpos($url, "https://ecm.sngb.ru:443/ECommerce/hppaction?formAction") == 0);
  }

  public function testUrl() {
    authorizeFromEnv();
    $payment = new SNGBEcomm_Payment();
    $url = $payment->paymentUrl();
    $this->assertEqual("https://ecm.sngb.ru/ECommerce/PaymentInitServlet", $url);
    SNGBEcomm::setLiveMode(true);
    $url = $payment->paymentUrl();
    $this->assertEqual("https://ecm.sngb.ru/Gateway/PaymentInitServlet", $url);
    SNGBEcomm::setLiveMode(false);
  }

  public function testSetterGetter() {
    authorizeFromEnv();

    $apikey = '123456';
    SNGBEcomm::setApiKey($apikey);
    $this->assertEqual(SNGBEcomm::getApiKey(), $apikey);

    $merchant = '7000';
    SNGBEcomm::setMerchant($merchant);
    $this->assertEqual(SNGBEcomm::getMerchant(), $merchant);

    $terminalalias = '7000-alias';
    SNGBEcomm::setTerminalAlias($terminalalias);
    $this->assertEqual(SNGBEcomm::getTerminalAlias(), $terminalalias);

  }

  public function testPaymentWithoutArgument() {
    //authorizeFromEnv();

    SNGBEcomm::setApiKey('64fdfab72758601fbff4dd0ef54fa6e6d96338f5');
    SNGBEcomm::setMerchant('7000');
    SNGBEcomm::setTerminalAlias('7000-alias');

    $payment = new SNGBEcomm_Payment();
    $this->assertEqual($payment->_apiKey, SNGBEcomm::getApiKey());
    $url = $payment->create(2, 1000);
    $this->assertTrue(strpos($url, "https://ecm.sngb.ru:443/ECommerce/hppaction?formAction") == 0);
  }


  public function testMoneyConverting() {
    $payment = new SNGBEcomm_Payment();
    $this->assertEqual($payment->convertMoneyToString(100), '1.00');
    $this->assertEqual($payment->convertMoneyToString(10), '0.10');
    $this->assertEqual($payment->convertMoneyToString(4202), '42.02');
    $this->assertEqual($payment->convertMoneyToString(0), '0');
    $this->assertEqual($payment->convertMoneyToString(2451235234576), '24512352345.76');

    $this->assertEqual($payment->convertStringToMoney('24512352345.76'), 2451235234576);
    $this->assertEqual($payment->convertStringToMoney('42.02'), 4202);
    $this->assertEqual($payment->convertStringToMoney('1.00'), 100);
    $this->assertEqual($payment->convertStringToMoney('0.10'), 10);
    $this->assertEqual($payment->convertStringToMoney('0'), 0);
  }
}
