<?php

namespace Thunderhawk\API\Mvc\Model\Products;

use Thunderhawk\API\Mvc\Model;
use Thunderhawk\API\Di\Service\Manager as ServiceManager;

class Product extends Model {
	public $id_product;
	public $id_supplier;
	public $id_manufacturer;
	public $id_category_default;
	public $id_shop_default;
	public $id_tax_rules_group;
	public $on_sale;
	public $online_only;
	public $ean_13;
	public $upc;
	public $ecotax;
	public $quantity;
	public $minimal_quantity;
	public $price;
	public $wholesale_price;
	public $unity;
	public $unit_price_ratio;
	public $additional_shipping_cost;
	public $reference;
	public $supplier_reference;
	public $location;
	public $width;
	public $height;
	public $depth;
	public $weight;
	public $out_of_stock;
	public $quantity_discount;
	public $customizable;
	public $uploadable_files;
	public $text_fields;
	public $active;
	public $redirect_type;
	public $id_product_redirected;
	public $available_for_order;
	public $available_date;
	public $condition;
	public $show_price;
	public $indexed;
	public $visibility;
	public $cache_is_pack;
	public $cache_has_attachments;
	public $is_virtual;
	public $cache_default_attribute;
	public $date_add;
	public $date_upd;
	public $advanced_stock_management;
	public $buyOnAmazonLink;
	public $buyOnEbayLink;
	protected function onInitialize() {
		//$this->switchToRemoteConnection ();
		$this->hasMany ( 'id_product', __NAMESPACE__ . '\PriceList', 'product_id', array (
				'alias' => 'prices',
				'reusable' => true 
		) );
		$this->hasMany ( 'id_product', __NAMESPACE__ . '\ProductLang', 'id_product', array (
				'alias' => 'lang',
				'reusable' => true 
		) );
	}
	public function getName($id_lang) {
		try {
			$id_lang = (int)$id_lang;
			$lang = ProductLang::findFirst("id_lang = $id_lang AND id_product = $this->id_product");
			if($lang){
				return utf8_encode($lang->name);
			}
			return 'no name' ;
			
			/*$db = $this->getDI()->get(ServiceManager::REMOTE_DB);
			$result = $db->fetchOne("SELECT * FROM ps_product_lang WHERE id_lang = $id_lang AND id_product = $this->id_product");
			return utf8_encode($result['name']);*/
		}catch (\Exception $e){
			return $e->getMessage();
		}
		return '';
	}
}