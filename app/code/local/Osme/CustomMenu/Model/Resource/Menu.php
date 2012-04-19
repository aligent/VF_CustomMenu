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
 * Menu resource model
 *
 * @category   Osme
 * @package    Osme_CustomMenu
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Osme_CustomMenu_Model_Resource_Menu extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Init
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('menu/menu', 'item_id');
    }
}
