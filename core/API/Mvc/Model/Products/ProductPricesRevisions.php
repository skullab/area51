<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class ProductPricesRevisions extends Model{
	public $id;
	public $product_prices_id;
	public $price_lists_revisions_id;
	public $price_list;
	public $price_retail;
	public $price_ecommerce;
	public $update_at;
	
	protected function onInitialize(){
		//n-1
		$this->belongsTo('product_prices_id',__NAMESPACE__.'\ProductPrices','id',array(
				'alias' => 'product_prices',
				'reusable' => true
		));
		//n-1
		$this->belongsTo('price_lists_revisions_id',__NAMESPACE__.'\PriceListsRevisions','id',array(
				'alias' => 'revision',
				'reusable' => true
		));
	}
	public function beforeValidationOnCreate(){
		$this->update_at = date('Y-m-d H:i:s');
	}
	public function beforeValidationOnUpdate(){
		$this->update_at = date('Y-m-d H:i:s');
		$this->revision->update_at = date('Y-m-d H:i:s');
		$this->revision->save();
	}
}