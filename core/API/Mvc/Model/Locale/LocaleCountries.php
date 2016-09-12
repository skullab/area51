<?php
namespace Thunderhawk\API\Mvc\Model\Locale ;
use Thunderhawk\API\Mvc\Model;
class LocaleCountries extends Model{
	public $id;
	public $name;
	public $iso_alpha_2;
	public $iso_alpha_3;
	public $numeric_code;
	public $postal_code_format;
	protected function onInitialize(){
		
	}
}