<?php

/**
 * Enterprise Attribute Url model
 *
 * @category    Enterprise
 * @package     Enterprise_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class VF_CustomMenu_Model_Attribute_Enterprise_Url extends VF_CustomMenu_Model_Attribute_Url
{
    /**
     * Retrieve request path
     * @param $attribute
     * @param $parentCat
     * @param $attVal
     * @param $storeId
     * @param null $urlSuffix
     * @return bool|string
     */
    protected function _getRequestPath($attribute, $parentCat, $attVal, $storeId, $urlSuffix = null)
    {
        $requestPath = '';
        if(!is_null($parentCat)) {
            $requestPath = $parentCat->getRequestPath() . '/';
        }
        $helper = Mage::getModel('catalog/category');
        $requestPath .= $helper->formatUrlKey($attribute->getFrontendLabel()) . '/' . $helper->formatUrlKey($attVal['label']);
        if(!empty($urlSuffix)) {
            $requestPath .= '.' . $urlSuffix;
        }
        $rewrites = $this->getRewritesForPath($requestPath, $storeId);
        if(count($rewrites) == 0) {
            return false;
        }
        return $rewrites[0];
    }

    protected function getRewritesForPath($requestPath, $storeId) {

        $resource = Mage::getResourceModel('core/resource');
        $read = $resource->getReadConnection();
        $select = $read->select()->distinct()
            ->from(array('url_rewrite' => $resource->getTable('enterprise_urlrewrite/url_rewrite')), array('request_path'))
            ->where('url_rewrite.request_path = ?', $requestPath)
            ->where('url_rewrite.is_system = ?', 0)
            ->where('url_rewrite.store_id = ?', $storeId);

        return $read->fetchCol($select);
    }


}
