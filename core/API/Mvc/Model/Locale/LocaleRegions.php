<?php

namespace Thunderhawk\API\Mvc\Model\Locale;
use Thunderhawk\API\Mvc\Model;
class LocaleRegions extends Model{
	public $id;
	public $name;
	public $iso_code_2;
	public $locale_countries_id;
	protected function onInitialize(){
		$this->belongsTo('locale_countries_id',__NAMESPACE__.'\LocaleCountries','id',array(
			'alias' => 'country',
			'reusable' => true
		));
	}
}