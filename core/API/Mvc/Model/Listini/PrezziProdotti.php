<?php

namespace Thunderhawk\API\Mvc\Model\Listini;
use Thunderhawk\API\Mvc\Model;
class PrezziProdotti extends Model{
	public $id;
	public $prezzo_listino;
	public $prezzo_retail;
	public $prezzo_ecommerce;
	public $update_time;
	public $pr_listini_versioni_id;
	public $pr_info_prodotti_id;
	protected function onInitialize(){
		$this->setAsVendorModel();
		$this->belongsTo('pr_listini_versioni_id',__NAMESPACE__.'\ListiniVersioni','id',array(
				'alias' => 'listino',
				'reusable' => true
		));
		$this->belongsTo('pr_info_prodotti_id',__NAMESPACE__.'\InfoProdotti','id',array(
				'alias' => 'info',
				'reusable' => true
		));
	}
	public function beforeValidationOnCreate(){
		$this->update_time = date("Y-m-d H:i:s");
	}
	public function beforeValidationOnUpdate(){
		$this->update_time = date("Y-m-d H:i:s");
	}
}