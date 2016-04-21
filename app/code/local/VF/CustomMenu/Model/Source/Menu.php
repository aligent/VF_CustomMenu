<?php
class VF_CustomMenu_Model_Source_Menu extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    protected $_options = null;

    public function getAllOptions() {
        if (is_null($this->_options)) {
            $this->_options = array(array(
                'label' => '',
                'value' => ''
            ));

            $oMenus = Mage::getModel('menu/menu')->getCollection();
            foreach ($oMenus as $oMenu) {
                $this->_options[] = array(
                    'label' => $oMenu->getLabel(),
                    'value' => $oMenu->getId(),
                );
            }
        }

        return $this->_options;
    }

    public function getOptionText($value) {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if (is_array($value)) {
                if (in_array($option['value'], $value)) {
                    return $option['label'];
                }
            } else {
                if ($option['value'] == $value) {
                    return $option['label'];
                }
            }

        }
        return false;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }


    public function getOptionArray() {
        $aOptions = array();
        foreach ($this->getAllOptions() as $aOption) {
            $aOptions[$aOption['value']] = $aOption['label'];
        }
        return $aOptions;
    }
}