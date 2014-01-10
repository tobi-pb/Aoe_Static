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

    public function test_getActionConfiguration()
    {
        $config = Mage::getModel('aoestatic/config');

        $config->setNode('aoe_static/testaction1/disabled', true);
        $config->setNode('aoe_static/testaction2', 'test-config');
        $config->setNode('aoe_static/testaction3/use', 'testaction2');

        $this->assertFalse($config->getActionConfiguration('testaction0'));
        $this->assertFalse($config->getActionConfiguration('testaction1'));
        $this->assertEquals('test-config', $config->getActionConfiguration('testaction2'));
        $this->assertEquals('test-config', $config->getActionConfiguration('testaction3'));
    }

    public function test_getMarkersCallbackConfiguration()
    {
        $config = Mage::getModel('aoestatic/config');

        $config->setNode('aoe_static/default/markers', 'aaa');

        $this->assertEquals('aaa', $config->getMarkersCallbackConfiguration());
    }

    public function test_getMarkerCallback()
    {
        $config = Mage::getModel('aoestatic/config');

        $config->setNode('aoe_static/default/markers/m1/valueCallback', 'test-callback');

        $this->assertEquals('', $config->getMarkerCallback('aaa'));
        $this->assertEquals('', $config->getMarkerCallback('###aaa'));

        $this->assertEquals('test-callback', $config->getMarkerCallback('m1'));
        $this->assertEquals('test-callback', $config->getMarkerCallback('#m#1###'));
    }
}
