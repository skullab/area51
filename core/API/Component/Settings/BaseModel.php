<?php

namespace Thunderhawk\API\Component\Settings;
use Phalcon\Mvc\Model;
class BaseModel extends Model implements \ArrayAccess{
	
	protected $data ;
	protected $_dataAccess = array();
	
	public function offsetExists($offset) {
		return isset ( $this->_dataAccess [$offset] );
	}
	public function offsetGet($offset) {
		if ($this->offsetExists ( $offset ))
			return $this->_dataAccess [$offset];
			return null;
	}
	public function offsetSet($offset, $value) {
		$this->_dataAccess [$offset] = $value;
	}
	public function offsetUnset($offset) {
		unset ( $this->_dataAccess [$offset] );
	}
	public function afterFetch() {
		if ($this->data != null) {
			$this->_dataAccess = unserialize ( $this->data );
		}
	}
	protected function prepareData() {
		$this->data = serialize ( $this->_dataAccess );
	}
	public function save($data = null, $whiteList = null) {
		$this->prepareData ();
		return parent::save ( $data, $whiteList );
	}
	public function __get($property) {
		if ($this->offsetExists ( $property ))
			return $this->_dataAccess [$property];
			return parent::__get ( $property );
	}
	public function __set($property, $value) {
		if (is_object ( $property ) || is_array ( $property ))
			return parent::__set ( $property, $value );
			$this->_dataAccess [$property] = $value;
	}
	public function __isset($property) {
		return isset ( $this->_dataAccess [$property] );
	}
}