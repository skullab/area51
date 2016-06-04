<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class CategoryLang extends Model{
	public $id_category;
	public $id_shop;
	public $id_lang;
	public $name;
	public $subtitle;
	public $description;
	public $link_rewrite;
	public $meta_title;
	public $meta_keywords;
	public $meta_description;
	
	protected function onInitialize(){
		// n-1
		$this->belongsTo('id_category',__NAMESPACE__.'\Category','id_category',array(
				'alias' => 'category',
				'reusable' => true
		));
		$this->switchToRemoteConnection();
	}
}