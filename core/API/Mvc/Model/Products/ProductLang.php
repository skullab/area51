<?php

namespace Thunderhawk\API\Mvc\Model\Products;
use Thunderhawk\API\Mvc\Model;
class ProductLang extends Model{
	public $id_product;	//int(10)	No
	public $id_shop;	//int(11)	No 	1
	public $id_lang;	//int(10)	No
	public $description;	//text	Sì 	NULL
	public $description_short; //text	Sì 	NULL
	public $link_rewrite; //	varchar(128)	No
	public $meta_description; //	varchar(255)	Sì 	NULL
	public $meta_keywords; //	varchar(255)	Sì 	NULL
	public $meta_title; //	varchar(128)	Sì 	NULL
	public $name; //	varchar(128)	No
	public $subtitle; //	varchar(200)	No
	public $ingredients; //	text	No
	public $faqUser1; //	varchar(200)	No
	public $faqQuestion1; //	text	No
	public $faqAnswer1; //	text	No
	public $faqUser2; //	varchar(200)	No
	public $faqQuestion2; //	text	No
	public $faqAnswer2; //	text	No
	public $faqUser3; //	varchar(200)	No
	public $faqQuestion3; //	text	No
	public $faqAnswer3; //	text	No
	public $available_now; //	varchar(255)	Sì 	NULL
	public $available_later; //	varchar(255)	Sì 	NULL
	
	protected function onInitialize(){
		//$this->switchToRemoteConnection();
		$this->belongsTo('id_product',__NAMESPACE__.'\Product','id_product',array(
				'alias' => 'product',
				'reusable' => true
		));
	}
}