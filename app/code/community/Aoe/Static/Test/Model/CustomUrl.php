<?php

class Aoe_Static_Test_Model_CustomUrl extends EcomDev_PHPUnit_Test_Case
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
    public function test_model()
    {
        $customurl = Mage::getModel('aoestatic/customUrl')->load(1);

        $this->assertEquals(1, $customurl->getId());
        $this->assertEquals(0, $customurl->getStoreId());
        $this->assertEquals('test', $customurl->getRequestPath());
        $this->assertEquals(100, $customurl->getMaxAge());

        $this->assertCount(0, $customurl->getCollection()->addStoreFilter(1, false));
        $this->assertCount(3, $customurl->getCollection()->addStoreFilter(1));
        $this->assertCount(3, $customurl->getCollection()->addStoreFilter(0, false));
        $this->assertCount(3, $customurl->getCollection()->addStoreFilter(0));
    }

    /**
     * @loadFixture general.yaml
     */
    public function test_loadByRequestPath()
    {
        $customurl = Mage::getModel('aoestatic/customUrl');

        $this->assertEquals(0, $customurl->getId());

        $customurl->loadByRequestPath('test2');
        $this->assertEquals(2, $customurl->getId());
    }

    /**
     * @loadFixture general.yaml
     */
    public function test_deleteCustomUrls()
    {
        $customurl = Mage::getModel('aoestatic/customUrl');

        $this->assertCount(3, $customurl->getCollection());
        $customurl->deleteCustomUrls(array(1, 3));
        $this->assertCount(1, $customurl->getCollection());
    }

    /**
     * @loadFixture general.yaml
     */
    public function test__afterSave()
    {
        $customurl = Mage::getModel('aoestatic/customUrl');

        $this->assertCount(3, $customurl->getCollection());
        Mage::unregister('_aoestatic_testadapter_purge');
        $this->assertNull(Mage::registry('_aoestatic_testadapter_purge'));
        $this->assertEquals(0, $customurl->getId());
        $customurl->addData(array(
            'store_id'      => 0,
            'request_path'  => 'test4',
            'max_age'       => 400,
        ));
        $customurl->save();
        $this->assertEquals(1, Mage::registry('_aoestatic_testadapter_purge'));
        $this->assertCount(4, $customurl->getCollection());
        $this->assertGreaterThan(3, $customurl->getId());
        $this->assertEquals('test4', $customurl->getRequestPath());
    }
}
