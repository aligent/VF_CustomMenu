<?php

/**
 * Attribute Url model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class VF_CustomMenu_Model_Attribute_Url extends Varien_Object
{

    /**
     * URL instance
     *
     * @var Mage_Core_Model_Url
     */
    protected  $_url;

    /**
     * Factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Initialize Url model
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
        $this->_store = !empty($args['store']) ? $args['store'] : Mage::app()->getStore();
    }

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (null === $this->_url) {
            $this->_url = Mage::getModel('core/url');
        }
        return $this->_url;
    }


    /**
     * @param $attribute
     * @param $attVal
     * @param $parentCat
     * @return string
     */
    public function getAttributeUrl($attribute, $attVal, $parentCat)
    {
        $params = array();
        return $this->getUrl($attribute, $attVal, $parentCat, $params);
    }

    /**
     * Retrieve Product Url path (with category if exists)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Category $category
     *
     * @return string
     */
    public function getUrlPath($product, $category=null)
    {
        $path = $product->getData('url_path');

        if (is_null($category)) {
            /** @todo get default category */
            return $path;
        } elseif (!$category instanceof Mage_Catalog_Model_Category) {
            Mage::throwException('Invalid category object supplied');
        }

        return Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath())
        . '/' . $path;
    }

    /**
     * Retrieve URL using UrlDataObject
     *
     * @param $attribute
     * @param $attVal
     * @param $parentCat
     * @param array $params
     * @return string
     */
    public function getUrl($attribute, $attVal, $parentCat, $params = array())
    {

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = Mage::app()->getStore()->getId();
        }

        // TODO Create a model to represent an attribute landing page.  This call will never return anything
        $requestPath = $attribute->getData('request_path');
        if (empty($requestPath)) {
            $urlSuffix = Mage::helper('catalog/category')->getCategoryUrlSuffix($storeId);
            $requestPath = $this->_getRequestPath($attribute, $parentCat, $attVal, $storeId, $urlSuffix);
        }

        $this->getUrlInstance()->setStore($storeId);

        $params[$attribute->getAttributeCode()] = $attVal['value'];
        $params['landing'] = 1;

        $requestPath = $this->_getAttributeUrl($parentCat, $storeId, $requestPath, $params);

        return $requestPath;

    }

    protected function _getAttributeUrl($parentCat, $storeId, $requestPath = null, $params = array()) {

        if (!empty($requestPath)) {
            return $this->getUrlInstance()->getDirectUrl($requestPath);
        }
        // If the request can't be handled by a URL rewrite, return the default path
        return $parentCat->getUrl() . '?' . http_build_query($params);
    }

    /**
     * Returns checked store_id value
     *
     * @param int|null $id
     * @return int
     */
    protected function _getStoreId($id = null)
    {
        return Mage::app()->getStore($id)->getId();
    }

    /**
     * Retrieve request path
     *
     * @param $attribute
     * @param $parentCat
     * @param $attVal
     * @param $storeId
     * @return bool|string
     */
    protected function _getRequestPath($attribute, $parentCat, $attVal, $storeId)
    {
        // TODO Implement for community edition
        return false;
    }
}
