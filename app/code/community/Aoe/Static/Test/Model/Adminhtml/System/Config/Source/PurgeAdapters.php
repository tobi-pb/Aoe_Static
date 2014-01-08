<?php

class Aoe_Static_Test_Model_Adminhtml_System_Config_Source_PurgeAdapters extends EcomDev_PHPUnit_Test_Case
{
    public function test_toOptionArray()
    {
        $model = Mage::getModel('aoestatic/adminhtml_system_config_source_purgeAdapters');
        $this->assertNotEmpty($model->toOptionArray());
        $this->assertContains(array(
            'value'=> '',
            'label'=> Mage::helper('aoestatic')->__('(Purging disabled)')
        ), $model->toOptionArray());
    }
}
