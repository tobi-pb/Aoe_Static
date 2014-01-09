<?php

class Aoe_Static_Test_Model_Cache_Adapter_Blackhole extends EcomDev_PHPUnit_Test_Case
{
    public function test_simple()
    {
        $adapter = Mage::getModel('aoestatic/cache_adapter_blackhole');
        $this->assertEmpty($adapter->purgeAll());
        $this->assertEmpty($adapter->purge(array('1', '2')));
        $this->assertEmpty($adapter->purgeTags(array('1', '2')));
        $this->assertNull($adapter->setConfig(array()));
    }
}
