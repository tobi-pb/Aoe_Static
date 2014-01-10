<?php

class Aoe_Static_Test_Model_Cache_Marker extends EcomDev_PHPUnit_Test_Case
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
            $this->_reflectionInstance = new ReflectionClass('Aoe_Static_Model_Cache_Marker');
        }
        $property = $this->_reflectionInstance->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    public function test_addMarkerValues()
    {
        $cacheMarker = Mage::getModel('aoestatic/cache_marker');
        $markersValues = $this->_getProtectProperty('_markersValues');

        $this->assertEmpty($markersValues->getValue($cacheMarker));
        $cacheMarker->addMarkerValues('test');
        $this->assertEquals(array('test'), $markersValues->getValue($cacheMarker));
        $cacheMarker->addMarkerValues(array('test2', 'test'));
        $this->assertEquals(array('test', 'test2', 'test'), $markersValues->getValue($cacheMarker));
    }

    public function test_replaceMarkers()
    {
        $cacheMarker = Mage::getModel('aoestatic/cache_marker');

        $cacheMarker->addMarkerValues('test');
        $cacheMarker->addMarkerValues(array('testmarker' => 'testvalue'));

        $this->assertEquals('aaa-test-bbb', $cacheMarker->replaceMarkers('aaa-test-bbb'));
        $this->assertEquals('aaa-###testvalue###-bbb', $cacheMarker->replaceMarkers('aaa-###testmarker###-bbb'));
    }

    public function test_getMarkerValue()
    {
        $cacheMarker = Mage::getModel('aoestatic/cache_marker');

        $cacheMarker->addMarkerValues(array('test' => 'testvalue'));
        $this->assertEquals('testvalue', $cacheMarker->getMarkerValue('test'));
    }

    public function test_executeCallback()
    {
        // I'm abusing the api-model to have something testable. Ugly as hell
        $callbackMock = $this->getModelMock('aoestatic/api', array('testcallback'));
        $callbackMock->expects($this->any())->method('testcallback')->will($this->returnValue('testresult'));

        $this->replaceByMock('model', 'aoestatic/api', $callbackMock);

        $this->assertEquals('testresult', Mage::getModel('aoestatic/cache_marker')->executeCallback('aoestatic/api::testcallback'));
    }
}
