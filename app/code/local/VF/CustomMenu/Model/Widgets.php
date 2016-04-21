<?php

class VF_CustomMenu_Model_Widgets {

    protected $_aOptions;
    protected $aTypes = array('vf_custommenu/menu');

    public function addType($sTypeAlias) {
        if (array_search($sTypeAlias, $this->aTypes) === false) {
            $this->aTypes[] = $sTypeAlias;
        }
    }

    public function removeType($sTypeAlias) {
        if ($index = array_search($sTypeAlias, $this->aTypes) === false)  return;
        unset($this->aTypes[$index]);
    }

    public function toOptionArray() {

        if (!$this->_aOptions) {
            $this->_aOptions = array();

            /** @var $collection Mage_Widget_Model_Resource_Widget_Instance_Collection */
            $cCollection = Mage::getModel('widget/widget_instance')->getCollection();

            $aColumns = array();
            $aValues = array();

            Mage::dispatchEvent('vf_custommenu_widget_source_before_types', array('model' => $this));

            foreach ($this->aTypes as $sType) {
                $aColumns[] = 'instance_type';
                $aValues[] = array('eq' => trim($sType));
            }

            $cCollection->addFieldToFilter(
                $aColumns,
                $aValues
            );

            $cCollection->getSelect()
                ->order('instance_type')
                ->order('sort_order');

            foreach ($cCollection as $oWidget) {
                $this->_aOptions[] = array(
                    'value' => $oWidget->instanceId,
                    'label' => $oWidget->title
                );
            }

        }

        return $this->_aOptions;
    }

}
