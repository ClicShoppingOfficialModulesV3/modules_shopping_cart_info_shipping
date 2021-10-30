<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ms_shopping_cart_info_shipping {
    public string $code;
    public $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_shopping_cart_info_shipping_title');
      $this->description = CLICSHOPPING::getDef('module_shopping_cart_info_shipping_description');

      if (\defined('MODULE_SHOPPING_CART_INFO_SHIPPING_STATUS')) {
        $this->sort_order = MODULE_SHOPPING_CART_INFO_SHIPPING_SORT_ORDER;
        $this->enabled = (MODULE_SHOPPING_CART_INFO_SHIPPING_STATUS == 'True');
      }
     }

    public function execute() {
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      if (isset($_GET['Cart']) && $CLICSHOPPING_ShoppingCart->getCountContents() > 0) {
        $content_width = (int)MODULE_SHOPPING_CART_INFO_SHIPPING_CONTENT_WIDTH;
        $position = MODULE_SHOPPING_CART_INFO_SHIPPING_POSITION;

        if (MODULE_SHOPPING_CART_INFO_SHIPPING_INTERNATIONAL == 'True') {
          $shipping_international = ClicShopping::getDef('module_shopping_cart_info_shipping_international');
        }

        if (!empty(MODULE_SHOPPING_CART_INFO_SHIPPING_FREE_SHIPPING)) {
          $free_shipping = ClicShopping::getDef('module_shopping_cart_info_free_shipping', ['free_shipping_amount' => $CLICSHOPPING_Currencies->displayPrice(MODULE_SHOPPING_CART_INFO_SHIPPING_FREE_SHIPPING, null)]);
        }

        $shopping_cart_information_customers = '  <!-- start ms_shopping_cart_out_of_message -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/shopping_cart_info_shipping'));

        $shopping_cart_information_customers .= ob_get_clean();

        $shopping_cart_information_customers .= '<!-- end ms_shopping_cart_out_of_message -->' . "\n";

        $CLICSHOPPING_Template->addBlock($shopping_cart_information_customers, $this->group);
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULE_SHOPPING_CART_INFO_SHIPPING_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_SHIPPING_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you use free shipping ?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_SHIPPING_FREE_SHIPPING',
          'configuration_value' => '',
          'configuration_description' => 'Please indicate the amount (without currency) when the free shipping is applied',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you sent the product at international ?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_SHIPPING_INTERNATIONAL',
          'configuration_value' => 'True',
          'configuration_description' => 'Is you sent the product out of your country select true',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_SHIPPING_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do you want to display the module?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_SHIPPING_POSITION',
          'configuration_value' => 'float-none',
          'configuration_description' => 'Displays the module to the left or to the right ',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-end\', \'float-start\', \'float-none\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_SHIPPING_SORT_ORDER',
          'configuration_value' => '5',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }
    
    public function keys() {
      return array(
        'MODULE_SHOPPING_CART_INFO_SHIPPING_STATUS',
        'MODULE_SHOPPING_CART_INFO_SHIPPING_FREE_SHIPPING',
        'MODULE_SHOPPING_CART_INFO_SHIPPING_INTERNATIONAL',
        'MODULE_SHOPPING_CART_INFO_SHIPPING_CONTENT_WIDTH',
        'MODULE_SHOPPING_CART_INFO_SHIPPING_POSITION',
        'MODULE_SHOPPING_CART_INFO_SHIPPING_SORT_ORDER'
      );
    }
  }
