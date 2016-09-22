<?php

namespace Thunderhawk\API\Mvc\Model\LeadTime;
use Thunderhawk\API\Mvc\Model;
class GeoLeadTime extends Model{
	public $id;
	public $label;
	public $lead_time;
	public $lat;
	public $lng;
	public $radius;
	protected function onInitialize(){
		$this->setAsVendorModel();
	}
}