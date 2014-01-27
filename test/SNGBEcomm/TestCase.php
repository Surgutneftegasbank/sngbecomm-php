<?php

class SNGBEcommTestCase extends UnitTestCase
{  
  public function testSimple()
  {    
    $this->assertEqual(5, 3+2);
    $this->assertFalse(file_exists(dirname(__FILE__) . '/../temp/test.log'));

  }

  public function testAPIInitial()
  {
    $key = 'sdfajfhfk234d8e8fca2dc0f8';
    SNGBEcomm::setApiKey($key);
    $this->assertEqual($key, SNGBEcomm::getApiKey());
  }

}
