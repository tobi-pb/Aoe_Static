<?php

class Aoe_Static_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    public function setup()
    {
        // mock testing adapter
        Mage::unregister('_singleton/aoestatic/test_model_cache_adapter_testing');
        Mage::register('_singleton/aoestatic/test_model_cache_adapter_testing', new Aoe_Static_Test_Model_Cache_Adapter_Testing());
    }

    protected function setCache($status)
    {
        $cacheOptions = Mage::app()->useCache();
        $cacheOptions['aoestatic'] = $status;
        Mage::app()->setCacheOptions($cacheOptions);
    }

    /**
     * reset and return helper
     *
     * @return Aoe_Static_Helper_Data
     */
    protected function getHelper()
    {
        Mage::unregister('_helper/aoestatic');
        $helper = Mage::helper('aoestatic');
        $helper->getConfig()->setNode('aoe_static_purging/adapters/testing/model', 'aoestatic/test_model_cache_adapter_testing');
        $helper->getConfig()->setNode('aoe_static_purging/adapters/testing/config', '');
        return $helper;
    }

    /**
     * test getConfig call
     */
    public function test_getConfig()
    {
        $helper = $this->getHelper();
        $this->assertInstanceOf('Aoe_Static_Model_Config', $helper->getConfig());
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function test_unknownPurgeAdapter()
    {
        Mage::app()->getStore()->setConfig('dev/aoestatic/purgeadapter', 'aaa');
        $this->setCache(1);
        $this->getHelper()->purgeAll();
    }

    /**
     * @doNotIndexAll
     * @loadFixture general.yaml
     */
    public function test_purgeAllAndEnabledCache()
    {
        $helper = $this->getHelper();

        $this->setCache(0);
        // test disabled cache
        $this->assertEmpty($helper->purgeAll());
        $this->setCache(1);
        //test enabled cache
        $this->assertNotEmpty($helper->purgeAll());

        $this->assertContains('purged all', $helper->purgeAll());
    }

    public function test_purge()
    {
        $helper = $this->getHelper();

        $this->setCache(0);
        $this->assertEmpty($helper->purge(array('a', 'b'), false));
        $this->setCache(1);
        $this->assertNotEmpty($helper->purge(array('http://url1', 'http://url2'), false));
    }

    public function test_purgeTags()
    {
        $helper = $this->getHelper();

        $this->setCache(0);
        $this->assertEmpty($helper->purgeTags(array('tag1', 'tag2')));
        $this->assertEmpty($helper->purgeTags(array('tag1', 'tag2'), 1));

        $this->setCache(1);
        $this->assertNotEmpty($helper->purgeTags('tag1'));
        $this->assertNotEmpty($helper->purgeTags('tag1', 1));
        $this->assertNotEmpty($helper->purgeTags(array('tag1', 'tag2')));
        $this->assertNotEmpty($helper->purgeTags(array('tag1', 'tag2'), 1));
    }

    public function test_trimExplode()
    {
        $helper = $this->getHelper();

        $testString = ' test1 , test2 ,test3, , aaa';

        $this->assertContains('test2', $helper->trimExplode(',', $testString));
        $this->assertContains('aaa', $helper->trimExplode(',', $testString));

        $this->assertEquals(array(trim($testString)), $helper->trimExplode(':', $testString));

        $this->assertCount(5, $helper->trimExplode(',', $testString));
        $this->assertCount(4, $helper->trimExplode(',', $testString, true));
    }
}
