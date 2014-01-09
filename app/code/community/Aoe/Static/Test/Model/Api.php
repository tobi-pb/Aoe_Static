<?php

class Aoe_Static_Test_Model_Api extends EcomDev_PHPUnit_Test_Case
{
    public function setup()
    {
        // setup helper stuff
        $cacheOptions = Mage::app()->useCache();
        $cacheOptions['aoestatic'] = 1;
        Mage::app()->setCacheOptions($cacheOptions);
        $helper = Mage::helper('aoestatic');
        $helper->getConfig()->setNode('aoe_static_purging/adapters/testing/model', 'aoestatic/test_model_cache_adapter_testing');
        $helper->getConfig()->setNode('aoe_static_purging/adapters/testing/config', '');
        Mage::unregister('_singleton/aoestatic/test_model_cache_adapter_testing');
        Mage::register('_singleton/aoestatic/test_model_cache_adapter_testing', new Aoe_Static_Test_Model_Cache_Adapter_Testing());
    }

    /**
     * @loadFixture general.yaml
     */
    public function test_purgeAll()
    {
        $apiModel = Mage::getModel('aoestatic/api');

        $this->assertNull(Mage::registry('_aoestatic_testadapter_purgeall'));
        $apiModel->purgeAll();
        $this->assertEquals(1, Mage::registry('_aoestatic_testadapter_purgeall'));
    }

    /**
     * @loadFixture general.yaml
     */
    public function test_purge()
    {
        $apiModel = Mage::getModel('aoestatic/api');

        $this->assertNull(Mage::registry('_aoestatic_testadapter_purge'));
        $apiModel->purge(array(1, 2, 3));
        $this->assertEquals(3, Mage::registry('_aoestatic_testadapter_purge'));
    }

    /**
     * @loadFixture general.yaml
     */
    public function test_purgeTags()
    {
        $apiModel = Mage::getModel('aoestatic/api');

        $this->assertNull(Mage::registry('_aoestatic_testadapter_purgetags'));
        $apiModel->purgeTags(array(4, 5, 6, 7));
        $this->assertEquals(4, Mage::registry('_aoestatic_testadapter_purgetags'));
    }
}
