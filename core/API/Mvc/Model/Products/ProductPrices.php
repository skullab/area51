<?php
namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class ProductPrices extends Model{
	public $id;
	public $price_list;
	public $price_retail;
	public $price_ecommerce;
	public $price_lists_id;
	public $product_id;
	public $update_at;
	
	protected function onInitialize(){
		// n-1
		$this->belongsTo('product_id',__NAMESPACE__.'\Product','id_product',array(
				'alias' => 'product',
				'reusable' => true
		));
		//n-1
		$this->belongsTo('price_lists_id',__NAMESPACE__.'\PriceLists','id',array(
				'alias' => 'list',
				'reusable' => true
		));
	}
	public function beforeValidationOnCreate(){
		$this->update_at = date('Y-m-d H:i:s');
	}
	public function beforeValidationOnUpdate(){
		$this->update_at = date('Y-m-d H:i:s');
		$this->list->update_at = date('Y-m-d H:i:s');
		$this->list->save();
	}
}