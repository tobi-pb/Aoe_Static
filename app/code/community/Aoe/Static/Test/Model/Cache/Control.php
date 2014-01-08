<?php

class Aoe_Static_Test_Model_Cache_Control extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var null|ReflectionClass
     */
    protected $_reflectionInstance = null;

    /**
     * @param $name string
     * @return ReflectionProperty
     */
    protected function _getProtectProperty($name)
    {
        if (is_null($this->_reflectionInstance)) {
            $this->_reflectionInstance = new ReflectionClass('Aoe_Static_Model_Cache_Control');
        }
        $property = $this->_reflectionInstance->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    public function test_enabledDisabled()
    {
        $cacheControl = Mage::getSingleton('aoestatic/cache_control');
        $enabled = $this->_getProtectProperty('_enabled');

        $cacheControl->enable();
        $this->assertTrue($enabled->getValue($cacheControl));
        $cacheControl->disable();
        $this->assertFalse($enabled->getValue($cacheControl));
    }

    public function test_addMaxAge()
    {
        $cacheControl = Mage::getSingleton('aoestatic/cache_control');
        $maxage = $this->_getProtectProperty('_maxAge');

        // should be 0 at the beginning
        $this->assertEquals(0, $maxage->getValue($cacheControl));

        $cacheControl->addMaxAge(100);
        $this->assertEquals(100, $maxage->getValue($cacheControl));
        $cacheControl->addMaxAge(50);
        $this->assertEquals(50, $maxage->getValue($cacheControl));
        $cacheControl->addMaxAge(51);
        $this->assertEquals(50, $maxage->getValue($cacheControl));
        $cacheControl->addMaxAge(array(10, 0, 30, 500, 10000));
        $this->assertEquals(10, $maxage->getValue($cacheControl));
    }

    public function test_addTag()
    {
        $cacheControl = Mage::getSingleton('aoestatic/cache_control');
        $tags = $this->_getProtectProperty('_tags');
        $storeId = Mage::app()->getStore()->getId();

        $this->assertEmpty($tags->getValue($cacheControl));
        $cacheControl->addTag('test');
        $this->assertEquals(array('test-' . $storeId => 1), $tags->getValue($cacheControl));
        $cacheControl->addTag('test');
        $cacheControl->addTag('test');
        $this->assertEquals(array('test-' . $storeId => 3), $tags->getValue($cacheControl));
        $cacheControl->addTag('test2');
        $this->assertEquals(array('test-' . $storeId => 3, 'test2-' . $storeId => 1), $tags->getValue($cacheControl));
    }

    public function test_applyCacheHeaders()
    {
        $cacheControl = Mage::getSingleton('aoestatic/cache_control');
        $maxage = $this->_getProtectProperty('_maxAge');
        $tags = $this->_getProtectProperty('_tags');
        $storeId = Mage::app()->getStore()->getId();
        $response = Mage::app()->getResponse();

        $maxage->setValue($cacheControl, 0);
        $tags->setValue($cacheControl, array());

        $testMaxAge = 100;
        $testTags = array('a', 'b');
        $header1 = array(
            'name'      => 'Aoestatic',
            'value'     => 'cache',
            'replace'   => true,
        );
        $header2 = array(
            'name'      => 'X-Invalidated-By',
            'value'     => $cacheControl::DELIMITER . implode('-' . $storeId . $cacheControl::DELIMITER, $testTags) . '-' . $storeId . $cacheControl::DELIMITER,
            'replace'   => true,
        );
        $header3 = array(
            'name'      => 'Cache-Control',
            'value'     => 'max-age=' . (int) $testMaxAge,
            'replace'   => true,
        );
        $header4 = array(
            'name'      => 'X-Magento-Lifetime',
            'value'     => (int) $testMaxAge,
            'replace'   => true,
        );

        $cacheControl->disable();
        $this->assertInstanceOf('Aoe_Static_Model_Cache_Control', $cacheControl->applyCacheHeaders());
        $this->assertNotContains($header1, $response->getHeaders());

        $cacheControl->enable();
        $cacheControl->applyCacheHeaders();
        $this->assertNotContains($header1, $response->getHeaders());

        $cacheControl->addMaxAge($testMaxAge);
        $cacheControl->addTag($testTags);
        $cacheControl->applyCacheHeaders();

        $this->assertContains($header1, $response->getHeaders());
        $this->assertContains($header2, $response->getHeaders());
        $this->assertContains($header3, $response->getHeaders());
        $this->assertContains($header4, $response->getHeaders());
    }

    public function test_collectTags()
    {
        $cacheControl = Mage::getSingleton('aoestatic/cache_control');
        $tags = $this->_getProtectProperty('_tags');
        $storeId = Mage::app()->getStore()->getId();

        $tags->setValue($cacheControl, array());

        //$this->assertInstanceOf('Aoe_Static_Model_Cache_Control', $cacheControl->collectTags());
    }
}
