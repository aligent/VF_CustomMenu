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
 * Custom menu block
 *
 * @category   VF
 * @package    VF_CustomMenu
 * @subpackage Block
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class VF_CustomMenu_Block_Navigation extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 86400,
            'cache_tags' => array(
                Mage_Catalog_Model_Category::CACHE_TAG,
                Mage_Core_Model_Store_Group::CACHE_TAG,
                VF_CustomMenu_Model_Menu::CACHE_TAG
            ),
        ));
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $aKeys = array(
            'CATALOG_NAVIGATION',
            Mage::app()->getStore()->getId(),
        );
        //if this block is being viewed on a category page, add the ID of that category to the cache key
        if(Mage::registry('current_category')){
            $aKeys[] = Mage::registry('current_category')->getId();
        }

	return $aKeys;
    }

    /**
     * get menu items
     *
     * @return VF_CustomMenu_Model_Resource_Menu_Collection
     */
    public function getMenuItems()
    {
        $collection = Mage::getModel('menu/menu')->getCollection()
            ->addStoreFilter()
            ->setOrder('position', 'asc');
        return $collection;
    }

    /**
     * get item url
     *
     * @param VF_CustomMenu_Model_Menu $item
     * @return string
     */
    public function getItemUrl(VF_CustomMenu_Model_Menu $item)
    {
        $url = ltrim($item->getUrl());
        switch ($item->getType()) {
            case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::LINK_INTERNAL:
                return Mage::getBaseUrl() . $url;
            case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::LINK_EXTERNAL:
                return $url;
            case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::CATEGORY:
                return $item->getCategory()->getUrl();
            case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::ATTRIBUTE:
                return 'javascript:;';
            default:
                return $url;
        }
    }

    /**
     * get dynamic block html for current item
     *
     * @param VF_CustomMenu_Model_Menu $item
     * @param null $itemNumber
     * @return mixed
     */
    public function getDynamicBlock(VF_CustomMenu_Model_Menu $item, $itemNumber = null)
    {
        if (!$item->hasData('dynamic_block')) {
            $block = '';
            switch ($item->getType()) {
                case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::ATTRIBUTE:
                    $items = $this->_getAttributeValueItems($item);
                    $block = $this->_getDynamicBlockList($items, $itemNumber);
                    break;
                case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::CATEGORY:
                    if ($item->getShowChildren() && !$item->getData('dynamic_block')) {
                        $items = $this->_getCategoryItems($item);
                        $block = $this->_getDynamicBlockList($items, $itemNumber);
                    }
                    break;
            }
            $item->setData('dynamic_block', $block);
        }
        return $item->getData('dynamic_block');
    }

    /**
     * get category children array with 'label' and 'href'
     *
     * @param VF_CustomMenu_Model_Menu $item
     * @return array
     */
    protected function _getCategoryItems(VF_CustomMenu_Model_Menu $item)
    {
        $items = array();
        /** @var $oParentCategory Mage_Catalog_Model_Category */
        $oParentCategory = Mage::getModel('catalog/category')->load($item->getDefaultCategory());
        $iCurrentCategoryId = false;
        $bIsCurrent = false;
        if(Mage::registry('current_category')){
            $iCurrentCategoryId = Mage::registry('current_category')->getId();
        }
        if ($oParentCategory->getId()) {
            if($oParentCategory->getId() == $iCurrentCategoryId){
                $item->setData('current',true);
            }
            $categories = $oParentCategory->getCategories($oParentCategory->getId(),true,false,true,true);
            $items = array();
            $level = $oParentCategory->getLevel() + 1;
            foreach ($categories as $oChildCategory) {
                /** @var $oChildCategory Mage_Catalog_Model_Category */
                if ($oChildCategory->getLevel() == $level) {
                    $bIsCurrent = false;
                    if($oChildCategory->getId() === $iCurrentCategoryId){
                        $bIsCurrent = true;
                        $item->setData('current',true);
                    }
                    $items[] = array(
                        'label' => $oChildCategory->getName(),
                        'href' => $oChildCategory->getUrl(),
                        'current' => $bIsCurrent,
                    );
                }
            }
        }
        return $items;
    }

    /**
     * get attribute values array with 'label' and 'href'
     *
     * @param VF_CustomMenu_Model_Menu $item
     * @return array
     */
    protected function _getAttributeValueItems(VF_CustomMenu_Model_Menu $item)
    {
        $items = array();
        if ($item->getSourceAttribute()) {
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $item->getSourceAttribute());

            /** @var $indexAttribute Mage_CatalogIndex_Model_Attribute */
            $indexAttribute = Mage::getSingleton('catalogindex/attribute');
            /** @var $rootCategory Mage_Catalog_Model_Category */
            $rootCategory = Mage::getModel('catalog/category')->load($item->getDefaultCategoryId());
            $entityFilter = $rootCategory->getProductCollection()->getSelect()->distinct();
            $activeOptions = array_keys($indexAttribute->getCount($attribute, $entityFilter));
            if ($attribute->usesSource()) {
                $allOptions = $attribute->getSource()->getAllOptions(false);
                foreach ($allOptions as $_option) {
                    if (in_array($_option['value'], $activeOptions)) {

                        $route = 'catalog/category/view';
                        $params = array(
                            'id' => $rootCategory->getId(),
                            '_query' => array($attribute->getAttributeCode() => $_option['value']),
                            '_use_rewrite' => true,
                        );

                        $result = new Varien_Object();
                        Mage::dispatchEvent(
                            'custom_menu_popup_update_item_url',
                            array('route' => $route, 'params' => $params, 'result' => $result)
                        );
                        if ($result->getUrl()) {
                            $href = $result->getUrl();
                        } else {
                            $href = $rootCategory->getUrl() . '?' . http_build_query($params['_query']);
                        }
                        $_option['href'] = $href;

                        $items[] = $_option;
                    }
                }
            }
        }
        return $items;
    }

    /**
     * render a list for add to menu popup
     *
     * @param $items array items to show with 'label' and 'href'
     * @param $itemNumber int it is added to 'nav' class
     * @return string
     */
    protected function _getDynamicBlockList($items, $itemNumber)
    {
        $block = '';
        if (!empty($items)) {
            $block .= "<ul class='level0'>\n";
            $odd = false;
            $index = 0;
            $count = count($items);
            foreach ($items as $_item) {
                ++$index;
                $class = ($odd) ? 'odd' : 'even';
                if ($itemNumber) {
                    $class .= ' nav-' . $itemNumber . '-' . $index;
                }
                if ($index == 1) {
                    $class .= ' first';
                } elseif ($index == $count) {
                    $class .= ' last';
                }
                if(isset($_item['current']) && $_item['current'] == true){
                    $class .= ' current';
                }
                $odd ^= 1;
                $class = " class=\"level1 $class\"";

                $block .= "<li{$class}><a href=\"{$_item['href']}\">"
                    . "<span>{$this->escapeHtml($_item['label'])}</span></a></li>";
            }
            $block .= "</ul>\n";
        }
        return $block;
    }

    public function isCurrent(VF_CustomMenu_Model_Menu $item, $itemNumber = null){
        switch ($item->getType()) {
            case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::CATEGORY:
                $this->getDynamicBlock($item,$itemNumber);
                if($item->getCurrent() == true){
                    return true;
                }
                break;
            case VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::LINK_INTERNAL:
                $vCurrentUrl = Mage::helper('core/url')->getCurrentUrl();
                $bIsCurrent = (strcmp($vCurrentUrl,$this->getItemUrl($item))===0);
                return $bIsCurrent;
                break;
            default:
                return false;
                break;
            //TODO: implement for Attribute
        }
    }

}
