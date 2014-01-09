<?php

class Aoe_Static_Test_Model_Config extends EcomDev_PHPUnit_Test_Case
{
    public function test_construct()
    {
        // cache disabled
        Mage::getModel('aoestatic/config');
        $this->assertFalse(Mage::app()->loadCache(Aoe_Static_Model_Config::CACHE_ID));

        // cache generation
        $cacheOptions = Mage::app()->useCache();
        $cacheOptions['config'] = 1;
        Mage::app()->setCacheOptions($cacheOptions);
        Mage::getModel('aoestatic/config');
        $this->assertNotNull(Mage::app()->loadCache(Aoe_Static_Model_Config::CACHE_ID));

        // cache unchanged
        $oldCache = Mage::app()->loadCache(Aoe_Static_Model_Config::CACHE_ID);
        Mage::getModel('aoestatic/config');
        $this->assertEquals($oldCache, Mage::app()->loadCache(Aoe_Static_Model_Config::CACHE_ID));
    }

    public function test_getAdapters()
    {
        $config = Mage::getModel('aoestatic/config');

        $this->assertNotEmpty($config->getAdapters());

        $config->setNode('aoe_static_purging', null);
        $this->assertEmpty($config->getAdapters());
    }
}
