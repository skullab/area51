<?php

namespace Thunderhawk\API\Mvc\Model\Locale;
use Thunderhawk\API\Mvc\Model;
class LocaleCities extends Model{
	public $id;
	public $name;
	public $postal_code;
	public $lat;
	public $lng;
	public $locale_provinces_id;
	protected function onInitialize(){
		$this->belongsTo('locale_provinces_id',__NAMESPACE__.'\LocaleProvinces','id',array(
				'alias' => 'province',
				'reusable' => true
		));
	}
}