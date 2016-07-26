<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
use Thunderhawk\API\Engine;
class Customers extends Model{
	public $id;
	public $codice_fatturatario;
	public $nome;
	public $customers_groups_id;
	public $customers_state_id;
	public $bookkeeper_id;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('customers_groups_id',__NAMESPACE__.'\CustomersGroups','id',array(
				'alias' => 'gruppo',
				'reusable' => true
		));
		$this->belongsTo('customers_state_id',__NAMESPACE__.'\CustomersState','id',array(
				'alias' => 'stato',
				'reusable' => true
		));
		$this->belongsTo('bookkeeper_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'contabile',
				'reusable' => true
		));
		// 1-1
		$this->hasOne('id',__NAMESPACE__.'\CustomersAddress','customers_id',array(
				'alias' => 'indirizzo',
				'reusable' => true
		));
		$this->hasOne('id',__NAMESPACE__.'\CustomersDetails','customers_id',array(
				'alias' => 'dettagli',
				'reusable' => true
		));
		//1-n
		$this->hasMany('id',__NAMESPACE__.'\CustomersDestinations','customers_id',array(
				'alias' => 'pdv',
				'reusable' => true
		));
	}
	public static function getProgressiveCode($group_id){
		$max = Customers::maximum(array(
				'column' => 'codice_fatturatario',
				'conditions' => "customers_groups_id = $group_id" 
		));
		$customer_code = str_pad((int)substr($max, -4) + 1 ,4,0,STR_PAD_LEFT);
		$group_code = CustomersGroups::findFirstById($group_id)->codice_gruppo ;
		return $group_code.$customer_code ;
	}
}