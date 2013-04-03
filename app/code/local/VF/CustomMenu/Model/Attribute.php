<?php
/**
 * VF extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the VF CustomMenu module to newer versions in the future.
 * If you wish to customize the VF CustomMenu module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   VF
 * @package    VF_CustomMenu
 * @copyright  Copyright (C) 2012 Vladimir Fishchenko (http://fishchenko.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source Attribute model
 *
 * @category   VF
 * @package    VF_CustomMenu
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class VF_CustomMenu_Model_Attribute
{
    /**
     * get ln attributes
     *
     * @return array
     */
    public function getSourceAttributes()
    {
        $values = array(array('label' => '', 'value' => ''));
        $cAttributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->addFieldToFilter('additional_table.is_filterable', array('gt' => 0));
        foreach($cAttributes as $oAttribute){
            /* @var $oAttribute Mage_Catalog_Model_Entity_Attribute */
            $values[$oAttribute->getAttributeCode()] = array(
                'label' => $oAttribute->getStoreLabel(),
                'value' => $oAttribute->getAttributeCode(),
            );
        }
        ksort($values);
        return array_values($values);
    }

    /**
     * get as options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();
        foreach ($this->getSourceAttributes() as $_attribute) {
            $options[$_attribute['value']] = $_attribute['label'];
        }
        unset($options['']);
        return $options;
    }
}
