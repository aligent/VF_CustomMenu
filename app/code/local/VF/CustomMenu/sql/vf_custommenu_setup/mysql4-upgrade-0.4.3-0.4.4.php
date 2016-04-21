<?php
/**
 * VF extension for Magento
 *
 * Add type column to menu table
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
 * @author     Jonathan Day <jonathan@aligent.com.au>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
        $installer->getTable('menu/menu'),
        'attribute_as_level_3',
        'tinyint(1) not null default 0'
    );

$installer->getConnection()->addColumn(
        $installer->getTable('menu/menu'),
        'attribute_level_2_name',
        'varchar(255) null'
    );

$installer->getConnection()->addColumn(
        $installer->getTable('menu/menu'),
        'attribute_level_2_url',
        'varchar(255) null'
    );

$installer->endSetup();
