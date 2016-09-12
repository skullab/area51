<?php

namespace Thunderhawk\API\Mvc\Model\Locale;
use Thunderhawk\API\Mvc\Model;
class LocaleProvinces extends Model{
	public $id;
	public $name;
	public $iso_code_2;
	public $lead_time;
	public $locale_regions_id;
	protected function onInitialize(){
		$this->belongsTo('locale_regions_id',__NAMESPACE__.'\LocaleRegions','id',array(
				'alias' => 'region',
				'reusable' => true
		));
	}
	
}