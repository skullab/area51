<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class PriceListsRevisions extends Model{
	public $id;
	public $name;
	public $created_at;
	public $update_at;
	public $price_lists_id;
	
	protected function onInitialize(){
		//n-1
		$this->belongsTo('price_lists_id',__NAMESPACE__.'\PriceLists','id',array(
				'alias' => 'price_list',
				'reusable' => true
		));
	}
	public function beforeValidationOnCreate(){
		$this->created_at = date('Y-m-d H:i:s');
	}
	public function beforeValidationOnUpdate(){
		$this->update_at = date('Y-m-d H:i:s');
	}
}