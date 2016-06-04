<?php

namespace Thunderhawk\Modules\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Italia\ItaliaRegioni;
use Thunderhawk\API\Mvc\Model\Italia\ItaliaProvince;
use Thunderhawk\API\Mvc\Model\Italia\ItaliaComuni;

class CountriesController extends Controller {
	protected function onInitialize() {
		$this->view->setTemplateAfter ( 'index' );
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
	}
	public function regionAction(){
		$country = $this->dispatcher->getParam('country');
		$this->view->country = _($country) ;
		$method = $country.'Region';
		if(method_exists($this, $method)){
			return $this->{$method}() ;
		}else{
			return $this->show404Action();
		}
	}
	public function provinceAction(){
		$country = $this->dispatcher->getParam('country');
		$this->view->country = _($country) ;
		$method = $country.'Province';
		if(method_exists($this, $method)){
			return $this->{$method}() ;
		}else{
			return $this->show404Action();
		}
	}
	public function cityAction(){
		$country = $this->dispatcher->getParam('country');
		$this->view->country = _($country) ;
		$method = $country.'City';
		if(method_exists($this, $method)){
			return $this->{$method}() ;
		}else{
			return $this->show404Action();
		}
	}
	public function sourceTextItalyRegionAction(){
		//if($this->request->isPost()){
			if($this->request->isAjax()){
				$regioni = ItaliaRegioni::find();
				$response = array();
				foreach ($regioni as $regione){
					$source = array('value'=>$regione->nome,'text'=>$regione->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		//}
	}
	public function sourceItalyRegionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$regioni = ItaliaRegioni::find();
				$response = array();
				foreach ($regioni as $regione){
					$source = array('value'=>$regione->id,'text'=>$regione->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	public function getItalyRegionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$regioni = ItaliaRegioni::find()->toArray();
					return $this->sendAjax(array('data'=>$regioni));
				}
			}
		}
	}
	public function dropItalyRegionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$dropList = $this->request->getPost ( 'dropList' );
					$response = array (
							'error' => 0
					);
					$messages = '';
					$check = true;
					foreach ( $dropList as $list ) {
						$obj = ItaliaRegioni::findFirstById ( $list ['id'] );
						if ($obj) {
							$check &= $obj->delete ();
							if (! $check) {
								foreach ( $obj->getMessages () as $message ) {
									$messages .= $message . '<br>';
								}
							}
						}
					}
					if (! $check) {
						$response ['error'] = 1;
						$response ['message'] = $messages;
					}
					
					return $this->sendAjax ( $response );
				}
			}
		}
	}
	public function updateItalyRegionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$data = $this->request->getPost();
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				$field = $data ['columnName'];
				$regione = ItaliaRegioni::findFirstById($id);
				$response = array('error'=>100,'message'=>_('Nessun record trovato'));
				if($regione){
					$regione->{$field} = $newValue ;
					try{
						if($regione->save() !== false){
							$response['error'] = 0 ;
							$response['message'] = _('La Regione e stata aggiornata');
						}else{
							$response['error'] = 1 ;
							foreach ($regione->getMessages() as $message){
								$response['message'] .= $message.'<br>' ;
							}
						}
					}catch(\Exception $e){
						$response['error'] = 2 ;
						$response['message'] = $e->getMessage();
					}
				}
				return $this->sendAjax($response);
			}
		}
	}
	public function addItalyRegionAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$nome = $this->request->getPost('nome','string');
				$istat = $this->request->getPost('istat');
				$regione = new ItaliaRegioni();
				$regione->nome = $nome ;
				$regione->codice_istat = $istat ;
				try{
					if($regione->save() !== false){
						$this->flash->success(_('La Regione e stata aggiunta'));
					}else{
						foreach ($regione->getMessages() as $message){
							$this->flash->error($message);
						}
					}
				}catch(\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
		return $this->forward('countries','region',array('country'=>'italy'));
	}
	public function italyRegion(){
		$this->assets->renderInlineJs('js/controllers/countriesItalyRegion.js');
	}
	public function sourceTextItalyProvinceAction(){
		//if($this->request->isPost()){
		if($this->request->isAjax()){
			$province = ItaliaProvince::find();
			$response = array();
			foreach ($province as $provincia){
				$source = array('value'=>$provincia->nome,'text'=>$provincia->nome);
				$response[] = $source ;
			}
			return $this->sendAjax($response);
		}
		//}
	}
	public function sourceItalyProvinceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$province = ItaliaProvince::find();
				$response = array();
				foreach ($province as $provincia){
					$source = array('value'=>$provincia->id,'text'=>$provincia->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	
	public function sourceTextItalyProvinceByRegionAction(){
	if($this->request->isPost()){
			if($this->request->isAjax()){
				$name = $this->request->getPost('name','string');
				if(trim($name) == '' )return $this->sourceTextItalyProvinceAction();
				$regione = ItaliaRegioni::findFirst(array(
						'nome = ?0',
						'bind' => array($name)
				));
				if(!$regione)return $this->sourceItalyProvinceAction();
				
				$province = ItaliaProvince::find("italia_regioni_id = $regione->id");
				$response = array();
				foreach ($province as $provincia){
					$source = array('value'=>$provincia->nome,'text'=>$provincia->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	
	public function sourceItalyProvinceByRegionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$name = $this->request->getPost('name','string');
				if(trim($name) == '' )return $this->sourceItalyProvinceAction();
				$regione = ItaliaRegioni::findFirst(array(
						'nome = ?0',
						'bind' => array($name)
				));
				if(!$regione)return $this->sourceItalyProvinceAction();
				
				$province = ItaliaProvince::find("italia_regioni_id = $regione->id");
				$response = array();
				foreach ($province as $provincia){
					$source = array('value'=>$provincia->id,'text'=>$provincia->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	
	public function sourceItalyProvinceByIdAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$id = $this->request->getPost('id','int');
				if($id == 0)return $this->sourceItalyProvinceAction();
				$province = ItaliaProvince::find("italia_regioni_id = $id");
				$response = array();
				foreach ($province as $provincia){
					$source = array('value'=>$provincia->id,'text'=>$provincia->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	
	public function getItalyProvinceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$province = ItaliaProvince::find();
					$response = array();
					foreach ($province as $provincia){
						$r = array();
						$r['id'] = $provincia->id;
						$r['regione'] = $provincia->regione->nome ;
						$r['nome'] = $provincia->nome ;
						$r['sigla'] = $provincia->sigla ;
						$r['codice_istat'] = $provincia->codice_istat ;
						$response[] = $r ;
					}
					return $this->sendAjax(array('data'=>$response));
				}
			}
		}
	}
	public function addItalyProvinceAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$regione = $this->request->getPost('regione','int');
				$nome = $this->request->getPost('nome','string');
				$sigla = $this->request->getPost('sigla','string');
				$istat = $this->request->getPost('istat');
				
				$provincia = new ItaliaProvince();
				$provincia->italia_regioni_id = $regione ;
				$provincia->nome = $nome ;
				$provincia->sigla = $sigla;
				$provincia->codice_istat = $istat ;
				
				try{
					if($provincia->save() !== false){
						$this->flash->success(_('La Provincia e stata aggiunta'));
					}else{
						foreach ($provincia->getMessages() as $message){
							$this->flash->error($message);
						}
					}
				}catch(\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
		return $this->forward('countries','province',array('country'=>'italy'));
	}
	public function updateItalyProvinceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$data = $this->request->getPost();
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				switch ($data['columnName']){
					case 'regione':
						$field = 'italia_regioni_id';
						break;
					default:
						$field = $data ['columnName'];
				}
				
				$provincia = ItaliaProvince::findFirstById($id);
				$response = array('error'=>100,'message'=>_('Nessun record trovato'));
				if($provincia){
					$provincia->{$field} = $newValue ;
					try{
						if($provincia->save() !== false){
							$response['error'] = 0 ;
							$response['message'] = _('La Provincia e stata aggiornata');
						}else{
							$response['error'] = 1 ;
							foreach ($provincia->getMessages() as $message){
								$response['message'] .= $message.'<br>' ;
							}
						}
					}catch(\Exception $e){
						$response['error'] = 2 ;
						$response['message'] = $e->getMessage();
					}
				}
				return $this->sendAjax($response);
			}
		}
	}
	public function dropItalyProvinceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$dropList = $this->request->getPost ( 'dropList' );
					$response = array (
							'error' => 0
					);
					$messages = '';
					$check = true;
					foreach ( $dropList as $list ) {
						$obj = ItaliaProvince::findFirstById ( $list ['id'] );
						if ($obj) {
							$check &= $obj->delete ();
							if (! $check) {
								foreach ( $obj->getMessages () as $message ) {
									$messages .= $message . '<br>';
								}
							}
						}
					}
					if (! $check) {
						$response ['error'] = 1;
						$response ['message'] = $messages;
					}
						
					return $this->sendAjax ( $response );
				}
			}
		}
	}
	public function italyProvince(){
		$this->assets->renderInlineJs('js/controllers/countriesItalyProvince.js');
	}
	
	public function sourceTextItalyCityAction(){
		//if($this->request->isPost()){
		if($this->request->isAjax()){
			$comuni = ItaliaComuni::find();
			$response = array();
			foreach ($comuni as $comune){
				$source = array('value'=>$comune->nome,'text'=>$comune->nome);
				$response[] = $source ;
			}
			return $this->sendAjax($response);
		}
		//}
	}
	public function sourceItalyCityAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$comuni = ItaliaComuni::find();
				$response = array();
				foreach ($comuni as $comune){
					$source = array('value'=>$comune->id,'text'=>$comune->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	
	public function sourceTextItalyCityByProvinceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$name = $this->request->getPost('name','string');
				if(trim($name) == '')return $this->sourceItalyCityAction();
				$provincia = ItaliaProvince::findFirst(array(
						'nome = ?0',
						'bind'=>array($name)
				));
				if(!$provincia)return $this->sourceItalyCityAction();
				$comuni = ItaliaComuni::find("italia_province_id = $provincia->id");
				$response = array();
				foreach ($comuni as $comune){
					$source = array('value'=>$comune->nome,'text'=>$comune->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	
	public function sourceItalyCityByProvinceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$name = $this->request->getPost('name','string');
				if(trim($name) == '')return $this->sourceItalyCityAction();
				$provincia = ItaliaProvince::findFirst(array(
						'nome = ?0',
						'bind'=>array($name)
				));
				if(!$provincia)return $this->sourceItalyCityAction();
				$comuni = ItaliaComuni::find("italia_province_id = $provincia->id");
				$response = array();
				foreach ($comuni as $comune){
					$source = array('value'=>$comune->id,'text'=>$comune->nome);
					$response[] = $source ;
				}
				return $this->sendAjax($response);
			}
		}
	}
	public function getItalyCityCapByIdAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$id = $this->request->getPost('id','int');
				$comune = ItaliaComuni::findFirstById($id);
				if($comune){
					return $this->sendAjax($comune->cap);
				}
				return $this->sendAjax();
			}
		}
	}
	public function getItalyCityCapByNameAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$name = $this->request->getPost('name','string');
				$comune = ItaliaComuni::findFirstByNome($name);
				if($comune){
					return $this->sendAjax($comune->cap);
				}
				return $this->sendAjax();
			}
		}
	}
	public function getItalyCityAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$comuni = ItaliaComuni::find();
					$response = array();
					foreach ($comuni as $comune){
						$r = array();
						$r['id'] = $comune->id;
						$r['regione'] = $comune->provincia->regione->nome ;
						$r['provincia'] = $comune->provincia->nome;
						$r['nome'] = $comune->nome ;
						$r['cap'] = $comune->cap ;
						$r['prefisso_telefonico'] = $comune->prefisso_telefonico;
						$r['codice_istat'] = $comune->codice_istat ;
						$r['codice_catastale'] = $comune->codice_catastale;
						$r['lat'] = $comune->lat ;
						$r['lng'] = $comune->lng ;
						$response[] = $r ;
					}
					return $this->sendAjax(array('data'=>$response));
				}
			}
		}
	}
	public function addItalyCityAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				
				$provincia = $this->request->getPost('provincia','int');
				$nome = $this->request->getPost('nome','string');
				$cap = $this->request->getPost('cap','string');
				$prefisso = $this->request->getPost('prefisso','string');
				$istat = $this->request->getPost('istat');
				$catasto = $this->request->getPost('catasto','string');
				$lat = $this->request->getPost('latitudine','float');
				$lng = $this->request->getPost('longitudine','float');
				
				$comune = new ItaliaComuni();
				$comune->italia_province_id = $provincia ;
				$comune->nome = $nome ;
				$comune->cap = $cap ;
				$comune->prefisso_telefonico = $prefisso ;
				$comune->codice_istat = $istat ;
				$comune->codice_catastale = $catasto ;
				$comune->lat = $lat ;
				$comune->lng = $lng ;
		
				try{
					if($comune->save() !== false){
						$this->flash->success(_('Il Comune e stata aggiunto'));
					}else{
						foreach ($comune->getMessages() as $message){
							$this->flash->error($message);
						}
					}
				}catch(\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
		return $this->forward('countries','city',array('country'=>'italy'));
	}
	public function updateItalyCityAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$data = $this->request->getPost();
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				
				switch ($data['columnName']){
					case 'provincia':
						$field = 'italia_province_id';
						break;
					default:
						$field = $data ['columnName'];
				}
		
				$comune = ItaliaComuni::findFirstById($id);
				
				$response = array('error'=>100,'message'=>_('Nessun record trovato'));
				if($comune){
					$comune->{$field} = $newValue ;
					try{
						if($comune->save() !== false){
							$response['error'] = 0 ;
							$response['message'] = _('Il Comune e stata aggiornato');
						}else{
							$response['error'] = 1 ;
							foreach ($comune->getMessages() as $message){
								$response['message'] .= $message.'<br>' ;
							}
						}
					}catch(\Exception $e){
						$response['error'] = 2 ;
						$response['message'] = $e->getMessage();
					}
				}
				return $this->sendAjax($response);
			}
		}
	}
	public function dropItalyCityAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$dropList = $this->request->getPost ( 'dropList' );
					$response = array (
							'error' => 0
					);
					$messages = '';
					$check = true;
					foreach ( $dropList as $list ) {
						$obj = ItaliaComuni::findFirstById ( $list ['id'] );
						if ($obj) {
							$check &= $obj->delete ();
							if (! $check) {
								foreach ( $obj->getMessages () as $message ) {
									$messages .= $message . '<br>';
								}
							}
						}
					}
					if (! $check) {
						$response ['error'] = 1;
						$response ['message'] = $messages;
					}
		
					return $this->sendAjax ( $response );
				}
			}
		}
	}
	public function italyCity(){
		$this->assets->renderInlineJs('js/controllers/countriesItalyCity.js');
	}
}