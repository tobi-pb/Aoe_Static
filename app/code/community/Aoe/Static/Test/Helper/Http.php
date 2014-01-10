<?php

class Aoe_Static_Test_Helper_Http extends EcomDev_PHPUnit_Test_Case
{
    public function test_helper()
    {
        $helper = Mage::helper('aoestatic/http');

        $helper->reset();

        $this->assertEmpty($helper->getRequests());
        $this->assertEmpty($helper->getGlobalOptions());

        $helper->setGlobalOptions(array('o1' => 'v1'));

        $this->assertCount(1, $helper->getGlobalOptions());

        $helper->setGlobalOption('o2', 'v2');

        $this->assertCount(2, $helper->getGlobalOptions());

        $helper->addRequests(array('a', 'b'));

        $this->assertCount(1, $helper->getRequests());

        $helper->reset();

        $this->assertEmpty($helper->getRequests());
        $this->assertEmpty($helper->getGlobalOptions());
    }
}
