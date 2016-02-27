<?php

namespace Thunderhawk\API\Component;
use Thunderhawk\API\Translate\AdapterDb;
use Phalcon\Db;
class OLD_Translator extends AdapterDb{
	protected $_defaultLanguage ;
	protected $_messages = array();
	protected $_useDefault = false ;
	
	public function __construct($defaultLanguage = null){
		if($this->languageExists($defaultLanguage)){
			$this->setDefaultLanguage($defaultLanguage);
			$this->switchLanguage($defaultLanguage);
		}
	}
	public function useDefaultValues($enable){
		$this->_useDefault = (bool)$enable;
	}
	public function languageExists($languageCode){
		$result = $this->db->fetchOne('SELECT COUNT(*) AS count 
				FROM languages WHERE locale = :languageCode',Db::FETCH_ASSOC,array(
						'languageCode' => $languageCode
				));
		return !empty($result['count']);
	}
	public function setDefaultLanguage($languageCode){
		try{
			$this->db->begin();
			//reset the default
			$ret = $this->db->update('languages',array('is_default'),array(0),'is_default = 1');
			// set the default languages
			$this->db->update('languages',array('is_default'),array(1),array(
					'conditions' => 'locale = ?',
					'bind' => array($languageCode)
			));
			$this->db->commit();
		}catch(\PDOException $e){
			$this->db->rollback();
			$this->_messages[] = $e->getMessage();
		}
	}
	public function getDefaultLanguage(){
		$result = $this->db->fetchOne('SELECT * FROM languages WHERE is_default = 1',Db::FETCH_ASSOC);
		if($result){
			$this->_defaultLanguage = $result ;
		}
		return $this->_defaultLanguage['locale'] ;
	}
	
	public function getActiveLanguage(){
		if(is_array($this->_activeLanguage)){
			return $this->_activeLanguage['locale'];
		}
		return null ;
	}
	public function getMessages(){
		return $this->_messages ;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Translate\AdapterDb::switchLanguage()
	 */
	public function switchLanguage($languageCode) {
		$result = $this->db->fetchOne('SELECT * FROM languages WHERE locale = :languageCode',
				Db::FETCH_ASSOC,
				array('languageCode' => $languageCode));
		if($result){
			$this->_activeLanguage = $result ;
			return true;
		}
		return false ;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Translate\Adapter::query()
	 */
	public function query($index, $placeholders = null) {
		$stm = 'SELECT * FROM translations WHERE 
				key_name = :index AND 
				languages_id = :id' ;
		$bind = array('index' => $index,'id' => $this->_activeLanguage['id']);
		if($this->_useDefault){
			$stm .= ' UNION SELECT * FROM translations WHERE 
					key_name = :index AND 
					languages_id = :default' ;
			$bind['default'] = $this->_defaultLanguage['id'] ;
		}
		$result = $this->db->fetchOne($stm,Db::FETCH_ASSOC,$bind);
		if($result){
			if(is_array($placeholders)){
				foreach ($placeholders as $k => $v){
					$result['value'] = str_replace("%$k%", $v, $result['value']);
				}
			}
			return $result['value'] ;
		}
		return null ;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Translate\Adapter::exists()
	 */
	public function exists($index) {
		$result = $this->db->fetchOne('SELECT COUNT(*) AS count FROM translations 
				WHERE key_name = :index AND languages_id = :id',
				Db::FETCH_ASSOC,
				array('index' => $index,'id' => $this->_activeLanguage['id']));
		return !empty($result['count']);
	}
	
	public function add($translateKey,$value){
		return $this->insert($translateKey, $value);
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Translate\AdapterDb::insert()
	 */
	public function insert($translateKey, $value) {
		if(is_array($this->_activeLanguage)){
			try{
				$this->db->begin();
				$data = array('key_name' => $translateKey , 'value' => $value , 'languages_id' => $this->_activeLanguage['id']);
				$this->db->insert('translations',array_values($data),array_keys($data));
				$this->db->commit();
			}catch(\PDOException $e){
				$this->db->rollback();
				$this->_messages[] = $e->getMessage();
			}
		}
		return false ;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Translate\AdapterDb::update()
	 */
	public function update($translateKey, $value) {
		if(is_array($this->_activeLanguage)){
			try{
				$this->db->begin();
				$this->db->update('translations',array('value'),array($value),array(
						'conditions' => 'key_name = ? AND languages_id = ?',
						'bind' => array($translateKey,$this->_activeLanguage['id'])
				));
				$this->db->commit();
			}catch(\PDOException $e){
				$this->db->rollback();
				$this->_messages[] = $e->getMessage();
			}
		}
		return false ;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Translate\AdapterDb::delete()
	 */
	public function delete($translateKey) {
		if(is_array($this->_activeLanguage)){
			try{
				$this->db->begin();
				$this->db->delete('translations',
						'key_name = :index AND languages_id = :id',
						array('index' => $translateKey,'id' => $this->_activeLanguage['id']));
				$this->db->commit();
			}catch(\PDOException $e){
				$this->db->rollback();
				$this->_messages[] = $e->getMessage();
			}
		}
		return false;
	}
}