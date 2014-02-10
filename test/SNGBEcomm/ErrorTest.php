<?php

class ErrorTest extends UnitTestCase {

  public function testErrorCreate()
  {
    authorizeFromEnv();

    $errormessage = new SNGBEcomm_Error('CGW0000039');
    $this->assertEqual($errormessage->error, 'CGW0000039');

    $errormessage = new SNGBEcomm_Error('CGW0000039', 'NOT APROVED', '03');
    $this->assertEqual($errormessage->result, 'NOT APROVED');
  }
  
  public function testErrorVerification()
  {
    authorizeFromEnv();

    $errormessage = new SNGBEcomm_Error('CGW0000500', 'NOT APROVED', '03');
    $this->assertEqual($errormessage->isError('1', '1.0', '1'), "Оплата не удалась! Обратитесь в службу поддержки сайта.");

    $errormessage = new SNGBEcomm_Error('CGW000029', 'NOT APROVED', '03');
    $this->assertEqual($errormessage->isError('1', '1.0', '1'), 'Card Number Invalid');
    $this->assertEqual($errormessage->isError(), 'Card Number Invalid');
  }
}
