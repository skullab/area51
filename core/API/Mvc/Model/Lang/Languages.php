<?php

namespace Thunderhawk\API\Mvc\Model\Lang;
use Thunderhawk\API\Mvc\Model;
class Languages extends Model{
	public $id;
	public $locale;
	public $is_default;
	protected function onInitialize(){
		$this->belongsTo('id',__NAMESPACE__.'\Translations','languages_id',array(
				'alias' => 'translation',
				'reusable' => true
		));
	}
}