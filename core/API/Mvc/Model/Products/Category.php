<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class Category extends Model{
	public $id_category ;
	public $id_parent ;
	public $id_shop_default;
	public $level_depth;
	public $nleft;
	public $nright;
	public $active ;
	public $date_add;
	public $date_upd;
	public $position;
	public $is_root_category ;
	
	protected function onInitialize(){
		// 1-n
		$this->hasMany('id_category',__NAMESPACE__.'\CategoryLang','id_category',array(
				'alias' => 'lang',
				'reusable' => true
		));
		$this->switchToRemoteConnection();
	}
	
}