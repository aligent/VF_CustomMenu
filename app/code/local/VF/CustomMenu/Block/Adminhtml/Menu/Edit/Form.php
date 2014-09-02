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
 * Menu edit form
 *
 * @category   VF
 * @package    VF_CustomMenu
 * @subpackage Block
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 * @author     Jonathan Day <jonathan@aligent.com.au>
 */
class VF_CustomMenu_Block_Adminhtml_Menu_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @return Mage_Adminhtml_Block_Widget_Form|void
     */
    protected function _prepareForm()
    {
        //add form
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id', null))),
            'method' => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        //add fieldset
        $fieldSet = $form->addFieldset(
            'custom_menu_form',
            array('legend' => $this->__('Menu Item'))
        );



        $field =$fieldSet->addField('store_id', 'multiselect', array(
            'name'      => 'store_id[]',
            'label'     => Mage::helper('cms')->__('Store View'),
            'title'     => Mage::helper('cms')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);



        $fieldSet->addField('label', 'text', array(
            'label'     => $this->__('Label'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'label'
        ));

        $fieldSet->addField('type', 'select', array(
            'label'     => $this->__('Type'),
            'class'     => 'required-entry',
            'required'  => 'true',
            'name'      => 'type',
            'options'   => VF_CustomMenu_Model_Resource_Menu_Attribute_Source_Type::getValues()
        ));

        $fieldSet->addField('url', 'text', array(
            'label'     => $this->__('Url'),
            'name'      => 'url',
            'note'      => $this->__(
                'Url without base url. To display "http://www.domain.com/test-page.html", enter "test-page.html". Leave blank to use default item URL'
            )
        ));

        $fieldSet->addField('title', 'text', array(
            'label'     => $this->__('Title'),
            'name'      => 'title'
        ));

        $fieldSet->addField('position', 'text', array(
            'label'     => $this->__('Position'),
            'name'      => 'position',
            'note'      => $this->__('Default 0')
        ));

        $fieldSet->addField('source_attribute', 'select', array(
            'label'     => $this->__('Source Attribute'),
            'name'      => 'source_attribute',
            'note'      => $this->__('If you select attribute, '
            . 'you will see dropdown with its values for layered navigation'),
            'values'    => Mage::getModel('menu/attribute')->getSourceAttributes()
        ));

        $fieldSet->addField('attribute_as_level_3', 'select', array(
            'label'     => $this->__('Display Attribute Values as 3rd Level'),
            'name'      => 'attribute_as_level_3',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note'      => $this->__(
                    'Enable if you want attribute values to display as level 3 instead of 2 with custom level 2'
                )
        ));

        $fieldSet->addField('attribute_level_2_name', 'text', array(
            'label'     => $this->__('Attribute Level 2 Label'),
            'name'      => 'attribute_level_2_name'
        ));

        $fieldSet->addField('attribute_level_2_url', 'text', array(
            'label'     => $this->__('Attribute Level 2 Url'),
            'name'      => 'attribute_level_2_url'
        ));


        /** @var $categories Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addAttributeToSelect('name');
        $values = array(array('label' => '', 'value' => ''));
        foreach ($categories as $_category) {
            $catId = $_category->getId();
            $values[] = array('value' => $catId, 'label' => $_category->getName() . " ($catId)");
        }

        $fieldSet->addField('default_category', 'select', array(
            'label'     => $this->__('Category'),
            'name'      => 'default_category',
            'note'      => $this->__('Custom default category'),
            'values'    => $values
        ));

        /** @var $cCmsPages Mage_Cms_Model_Resource_Page_Collection */
        $cCmsPages = Mage::getModel('cms/page')->getCollection();
        $values = array(array('label' => '', 'value' => ''));
        foreach ($cCmsPages as $oPage) {
            $iPageId = $oPage->getId();
            $values[] = array('value' => $iPageId, 'label' => $oPage->getTitle() . " ($iPageId)");
        }

        $fieldSet->addField('cms_page_id', 'select', array(
            'label'     => $this->__('CMS Page'),
            'name'      => 'cms_page_id',
            'values'    => $values
        ));

        $data = Mage::registry('current_menu');
        $showChildren = false;
        if ($data) {
            $showChildren = $data->getShowChildren();
        }

        $fieldSet->addField('show_children', 'checkbox', array(
            'label'     => $this->__('Show Children'),
            'name'      => 'show_children',
            'checked'   => $showChildren
        ));

        $fieldSet->addField('is_current_exclusive', 'select', array(
            'label'     => $this->__('Is Current Exclusive'),
            'name'      => 'is_current_exclusive',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note'      => $this->__(
                'For this item to have the \'current\' classname applied on a PDP, that product must be assigned ONLY to this category or its children'
            )
        ));

        $fieldSet->addField('disable_upper_links', 'select', array(
            'label'     => $this->__('Disable Upper Links'),
            'name'      => 'disable_upper_links',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note'      => $this->__(
                    'Enable if you want only menu items at the deepest level to be links'
                )
        ));

        $fieldSet->addField('static_block', 'select', array(
            'label'     => $this->__('Static Block'),
            'name'      => 'static_block',
            'values'    => Mage::getSingleton('catalog/category_attribute_source_page')->getAllOptions(),
            'note'      => $this->__(
                'Display content from the selected static block on this navigation item'
            )
        ));

        $fieldSet->addField('widgets', 'multiselect', array(
            'label'     => $this->__('Widgets'),
            'name'      => 'widgets[]',
            'values'    => Mage::getSingleton('menu/widgets')->toOptionArray(),
            'note'      => $this->__(
                    'Display content from the selected widgets on this navigation item'
                )
        ));

        if ($data) {
            $form->setValues($data->getData());
        }

        parent::_prepareForm();
    }
}
