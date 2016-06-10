<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class PriceLists extends Model{
	public $id;
	public $name;
	public $created_at;
	public $update_at;
	public $has_revisions;
	protected function onInitialize(){
		//1-n
		$this->hasMany('id',__NAMESPACE__.'\ProductPrices','price_lists_id',array(
				'alias' => 'prices',
				'resusable' => true
		));
		//1-n
		$this->hasMany('id','Thunderhawk\API\Mvc\Model\Customer\CustomersPriceLists','price_lists_id',array(
				'alias' => 'relation',
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