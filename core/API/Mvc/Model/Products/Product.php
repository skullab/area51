<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class Product extends Model{
	public $id;
	public $name;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\PriceList','product_id',array(
				'alias' => 'prices',
				'reusable' => true
		));
	}
}