<?php
namespace Sigurd\SpecialBarCode\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
    * Category setup factory
    *
    * @var CategorySetupFactory
    */
    private $categorySetupFactory;
    /**
    * Init
    *
    * @param CategorySetupFactory $categorySetupFactory
    */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
         $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $categorySetup->addAttribute(
             Product::ENTITY,
             'special_bar_code',
             [
                 'type' => 'varchar',
                 'label' => 'Special bar code',
                 'input' => 'text',
                 'required' => false,
                 'sort_order' => 100,
                 'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                 'group' => 'General',
             ]
         );
         $installer->endSetup();
    }
}
