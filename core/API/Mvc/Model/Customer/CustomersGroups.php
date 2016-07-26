<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
use Thunderhawk\API\Engine;
class CustomersGroups extends Model{
	public $id;
	public $codice_gruppo;
	public $nome;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\Customers','customers_groups_id',array(
				'alias' => 'fatturatario',
				'reusable' => true
		));
	}
	public static function getProgressiveCode(){
		$engine = Engine::getInstance();
		$dbname = $engine->getDbName();
		$result = $engine->db->fetchOne("SELECT `AUTO_INCREMENT`
				FROM  INFORMATION_SCHEMA.TABLES
				WHERE TABLE_SCHEMA = '$dbname'
				AND   TABLE_NAME   = 'customers_groups'"
				);
		return str_pad($result['AUTO_INCREMENT'],4,0,STR_PAD_LEFT) ;
	}
}