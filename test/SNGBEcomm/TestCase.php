<?php

class SNGBEcommTestCase extends UnitTestCase
{  
  public function testSimple()
  {    
    $this->assertEqual(5, 3+2);
    $this->assertFalse(file_exists(dirname(__FILE__) . '/../temp/test.log'));
  }

}
