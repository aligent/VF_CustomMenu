<?php

class VF_CustomMenu_Model_Cron
{

    public function generateRewrites()
    {

        $storeCollection = Mage::getModel('core/store')->getCollection();

        foreach ($storeCollection as $store) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store->getId());

            $itemCollection = Mage::getModel('menu/menu')->getCollection()
                ->addFieldToFilter('type', VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::ATTRIBUTE);

            $itemCollection->load();
            foreach ($itemCollection as $item) {
                $stores = explode(',', $item->getStoreId());

                // check if item is in this store or the default store
                if (array_search(0, $stores) !== false || array_search($store->getId(), $stores)) {
                    if ($item->getSourceAttribute()) {
                        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                        $attribute = Mage::getModel('eav/config')
                            ->getAttribute('catalog_product', $item->getSourceAttribute());

                        /** @var $indexAttribute Mage_CatalogIndex_Model_Attribute */
                        $indexAttribute = Mage::getSingleton('catalogindex/attribute');
                        /** @var $rootCategory Mage_Catalog_Model_Category */
                        $rootCategory = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($item->getDefaultCategoryId());

                        //TODO re-enable to filter attributes that are in the parent category
                        //$entityFilter = $rootCategory->getProductCollection()->getSelect()->distinct();
                        //$activeOptions = array_keys($indexAttribute->getCount($attribute, $entityFilter));
                        if ($attribute->usesSource()) {
                            $allOptions = $attribute->getSource()->getAllOptions(false);
                            foreach ($allOptions as $_option) {
                                //if (in_array($_option['value'], $activeOptions)) {

                                $this->buildRewriteForAttribute($attribute, $_option, $store->getId(), $rootCategory);

                                //}
                            }
                        }
                    }
                }
            }
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }
    }


    public function buildRewriteForAttribute($attribute, $_option, $storeId, $rootCategory = null)
    {

        $urlSuffix = Mage::helper('catalog/category')->getCategoryUrlSuffix($storeId);
        $requestPath = '';
        if (!is_null($rootCategory)) {
            // TODO this only gets the key at the default scope
            $requestPath = $rootCategory->getRequestPath() . '/';
        }
        $helper = Mage::getModel('catalog/category');
        $requestPath .= $helper->formatUrlKey($attribute->getFrontendLabel()) . '/' . $helper->formatUrlKey($_option['label']);
        // TODO url suffix is store scoped, must get it at each store
        if (!empty($urlSuffix)) {
            $requestPath .= '.' . $urlSuffix;
        }

        // TODO check if rewrite still pointing to the same target, if not, update.
        $existingRewrite = $this->getAttributeRewrite($requestPath, $storeId);


        $params = array();
        $params[$attribute->getAttributeCode()] = $_option['value'];
        $params['landing'] = 1;
        $url = parse_url($rootCategory->getUrl());
        // TODO get original URL instead
        $targetPath = ltrim($url['path'], '/') . '?' . http_build_query($params);

        $params = array(
            $attribute->getAttributeCode() => $_option['value'],
            'landing' => 1
        );
        if(is_null($rootCategory)) {
            $catId = Mage::app()->getStore()->getRootCategoryId();
        } else {
            $catId = $rootCategory->getId();
        }
        $targetPath = 'catalog/category/view/id/' . $catId . '?' . http_build_query($params);


        if ($existingRewrite === false) {
            $this->createRewrite($storeId, $requestPath, $targetPath);
        } else {
            // TODO check if rewrite needs to be updated
            $redirects = Mage::getModel('enterprise_urlrewrite/redirect')->getCollection()
                ->addFieldToFilter('identifier', $requestPath)
                ->addFieldToFilter('store_id', $storeId);
            foreach ($redirects as $redirect) {
                $redirect->setTargetPath($targetPath);
                $redirect->save();
            }


        }


    }

    protected function createRewrite($id, $requestPath, $targetPath)
    {
        try {
            $redirect = Mage::getModel('enterprise_urlrewrite/redirect');
            $redirect
                ->setStoreId($id)
                ->setIdentifier($requestPath)
                ->setTargetPath($targetPath)
                ->setOptions('')
                ->setDescription('Attribute landing rewrite');

            file_put_contents('/tmp/rewrites.log', var_export($redirect->getData(), true), FILE_APPEND);
            $redirect->save();
        } catch (Exception $e) {
            // TODO
        }
    }

    public function getAttributeRewrite($requestPath, $storeId = null)
    {
        $resource = Mage::getResourceModel('core/resource');
        $read = $resource->getReadConnection();
        $select = $read->select()
            ->from(array('url_rewrite' => $resource->getTable('enterprise_urlrewrite/url_rewrite')), array('store_id', 'request_path', 'target_path'))
            ->where('url_rewrite.request_path = ?', $requestPath)
            ->where('url_rewrite.store_id = ?', $storeId)
            ->where('url_rewrite.is_system = ?', 0);

        if (!is_null($storeId)) {
            $select = $select->where('url_rewrite.store_id = ?', $storeId);
        }

        $sel = (string) $select;
        // There should only be one rewrite
        return $read->fetchRow($select);

    }

}