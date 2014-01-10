<?php

class Aoe_Static_Test_Model_Cache_Adapter_Varnish extends EcomDev_PHPUnit_Test_Case
{
    public function setup()
    {
        $httpHelperMock = $this->getHelperMock('aoestatic/http', array('execute'));
        $httpHelperMock->expects($this->any())->method('execute')->will($this->returnValue(array('test-return-value')));
        $this->replaceByMock('helper', 'aoestatic/http', $httpHelperMock);

        Mage::app()->getStore()->setConfig('dev/aoestatic/servers', "10.0.0.1\n10.0.0.2");
    }

    public function test_purge()
    {
        $helper = Mage::helper('aoestatic/http');
        $adapter = Mage::getModel('aoestatic/cache_adapter_varnish');
        $helper->reset();

        $this->assertContains('test-return-value', $adapter->purge(array('test')));
        $this->assertCount(2, $helper->getRequests());  // 2 test-server, 1 url = 2 * 1 = 2 ;-)
        $globalOptions = $helper->getGlobalOptions();
        $this->assertCount(4, $globalOptions);
        $this->assertEquals('PURGE', $globalOptions[CURLOPT_CUSTOMREQUEST]);

        $helper->reset();
        $this->assertContains('test-return-value', $adapter->purge(array('test1', 'test2')));
        $requests = $helper->getRequests();
        $this->assertCount(4, $requests);
        $this->assertContains(array(CURLOPT_URL => 'http://10.0.0.1/test1'), $requests);
        $this->assertContains(array(CURLOPT_URL => 'http://10.0.0.2/test1'), $requests);
        $this->assertContains(array(CURLOPT_URL => 'http://10.0.0.1/test2'), $requests);
        $this->assertContains(array(CURLOPT_URL => 'http://10.0.0.2/test2'), $requests);
    }

    public function test_purgeAll()
    {
        $helper = Mage::helper('aoestatic/http');
        $adapter = Mage::getModel('aoestatic/cache_adapter_varnish');
        $helper->reset();

        $this->assertContains('test-return-value', $adapter->purgeAll());
        $requests = $helper->getRequests();
        $this->assertCount(2, $requests);
        $this->assertContains(array(CURLOPT_URL => 'http://10.0.0.1/.*'), $requests);
        $this->assertContains(array(CURLOPT_URL => 'http://10.0.0.2/.*'), $requests);
    }

    public function test_purgeTags()
    {
        $helper = Mage::helper('aoestatic/http');
        $adapter = Mage::getModel('aoestatic/cache_adapter_varnish');
        $helper->reset();

        $this->assertContains('test-return-value', $adapter->purgeTags(array('test')));
        $this->assertCount(2, $helper->getRequests());
        $globalOptions = $helper->getGlobalOptions();
        $this->assertCount(4, $globalOptions);
        $this->assertEquals('BAN', $globalOptions[CURLOPT_CUSTOMREQUEST]);

        $expectedRequests = array(
            array(
                CURLOPT_URL         => 'http://10.0.0.1',
                CURLOPT_HTTPHEADER  => array('X-Invalidates: ' . Aoe_Static_Model_Cache_Control::DELIMITER . 'test'),
            ),
            array(
                CURLOPT_URL         => 'http://10.0.0.2',
                CURLOPT_HTTPHEADER  => array('X-Invalidates: ' . Aoe_Static_Model_Cache_Control::DELIMITER . 'test'),
            ),
        );
        foreach ($expectedRequests as $er) {
            $this->assertContains($er, $helper->getRequests());
        }
    }

    public function test_setConfig()
    {
        $adapter = Mage::getModel('aoestatic/cache_adapter_varnish');
        $adapter->setConfig(array());
        // nothing to do here...
    }
}
