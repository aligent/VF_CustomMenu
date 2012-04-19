<?php 
/**
 * Osme extension for Magento
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
 * the Osme CustomMenu module to newer versions in the future.
 * If you wish to customize the Osme CustomMenu module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Osme
 * @package    Osme_CustomMenu
 * @copyright  Copyright (C) 2012 Osme
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Menu edit container
 *
 * @category   Osme
 * @package    Osme_CustomMenu
 * @subpackage Block
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Osme_CustomMenu_Block_Adminhtml_Menu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'menu';
        $this->_controller = 'adminhtml_menu';
    }

    /**
     * get container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_menu')) {
            return $this->__('Edit Menu Item "%s"', $this->escapeHtml(Mage::registry('current_menu')->getLabel()));
        } else {
            return $this->__('New Menu Item');
        }
    }
}
