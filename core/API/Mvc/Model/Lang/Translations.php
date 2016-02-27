<?php

namespace Thunderhawk\API\Mvc\Model\Lang;
use Thunderhawk\API\Mvc\Model;
class Translations extends Model{
	public $id;
	public $comment;
	public $msgid;
	public $msgstr;
	public $languages_id;
	protected function onInitialize(){
		$this->hasMany('languages_id',__NAMESPACE__.'\Languages','id',array(
				'alias' => 'language',
				'reusable' => true
		));
	}
}