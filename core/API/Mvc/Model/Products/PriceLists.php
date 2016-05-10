<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class PriceLists extends Model{
	public $id;
	public $name;
	public $created_at;
	public $start_time;
	public $end_time;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\ProductPrices','price_lists_id',array(
				'alias' => 'prices',
				'resusable' => true
		));
		$this->hasMany('id','Thunderhawk\API\Mvc\Model\Customer\CustomersPriceLists','price_lists_id',array(
				'alias' => 'relation',
				'reusable' => true
		));
	}
	public function beforeSave(){
		$this->created_at = date('Y-m-d H:i:s');
	}
}