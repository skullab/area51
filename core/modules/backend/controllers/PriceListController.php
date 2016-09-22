<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Listini\Listini;
use Thunderhawk\API\Mvc\Model\Listini\ListiniVersioni;
use Thunderhawk\API\Mvc\Model\Products\Product;
use Thunderhawk\API\Mvc\Model\Listini\InfoProdotti;
use Thunderhawk\API\Mvc\Model\Listini\PrezziProdotti;
use Thunderhawk\API\Mvc\Model;
class PriceListController extends Controller{
	
	protected function onInitialize(){
		$this->view->setTemplateAfter('index');
		$this->assetsPackage('toastr');
	}
	public function newAction(){
		$this->assetsPackage('spinner');
		$this->loadInlineActionJs();
		if($this->request->isPost()){
			if($this->token->check()){
				$name = $this->request->getPost('listName','string');
				$clone = $this->request->getPost('listClone','int');
				$empty = $this->request->getPost('listEmpty') == 'on' ? true : false ;
				
				$listino = new Listini();
				$listino->nome = $name ;
				$listino->attivo = 1 ;
				
				
				try{
					if($listino->save() == false){
						foreach ($listino->getMessages() as $message){
							$this->flash->error($message);
						}
					}else{
						if($clone > 0){
							$response = $this->clonePriceList($clone, $listino->id);
							if($response == false){
								$this->flash->error(_('Impossibile clonare il listino'));
							}else{
								$this->flash->success($response);
							}
						}else{
							// FIRST VERSION
							$versione = new ListiniVersioni();
							$versione->pr_listini_id = $listino->id ;
							$versione->setAsFirstVersion();
							
							if($versione->save() == false){
								$this->flash->error(_('Impossibile creare la prima versione'));
							}else{
								if(!$empty){
									$this->fillPriceList($listino->id);
								}
								$this->flash->success(_('Il listino e stato creato correttamente'));
							}
						}
					}
				}catch (\Exception $e){
					$this->flash->error($e->getMessage());	
				}
			}
		}
	}
	protected function clonePriceList($clone_id,$list_id){
		$clone = Listini::findFirst($clone_id);
		
		if($clone){
			$n_v = 0 ;
			$n_p = 0 ;
			$n_i = 0 ;
			
			$clone_versioni = ListiniVersioni::find(array(
					'pr_listini_id = ?0',
					'bind' => array($clone_id)
			));
			foreach ($clone_versioni as $clone_versione){
				
				$parameters = $clone_versione->toArray();
				unset($parameters['id']);
				unset($parameters['pr_listini_id']);
				$nuova_versione = Model::cloneResult(new ListiniVersioni(),$parameters);
				$nuova_versione->pr_listini_id = $list_id ;
				
				if($nuova_versione->save()){
					$n_v++ ;
					$clone_info = InfoProdotti::find(array(
							'pr_listini_versioni_id = ?0',
							'bind' => array($clone_versione->id)
					));
					foreach ($clone_info as $ci){
						$parameters = $ci->toArray();
						unset($parameters['id']);
						unset($parameters['pr_listini_versioni_id']);
						$nuovo_info = Model::cloneResult(new InfoProdotti(),$parameters);
						$nuovo_info->pr_listini_versioni_id = $nuova_versione->id ;
						if($nuovo_info->save()){
							$n_i++;
							$clone_prezzo = PrezziProdotti::findFirst(array(
									'pr_info_prodotti_id = ?0',
									'bind' => array($ci->id)
							));
							if($clone_prezzo){
								$parameters = $clone_prezzo->toArray();
								unset($parameters['id']);
								unset($parameters['pr_listini_versioni_id']);
								unset($parameters['pr_info_prodotti_id']);
								$nuovo_prezzo = Model::cloneResult(new PrezziProdotti(),$parameters);
								$nuovo_prezzo->pr_listini_versioni_id = $nuova_versione->id ;
								$nuovo_prezzo->pr_info_prodotti_id = $nuovo_info->id ;
								if($nuovo_prezzo->save()){
									$n_p++;
								}
							}
						}
					}
					
				}
			}
			$format = _('Listino clonato correttamente').
					'<br>'._('Versioni clonate : %s').
					'<br>'._('Prodotti clonati : %s').
					'<br>'._('Prezzi clonati : %s') ;
			
			return sprintf($format,$n_v,$n_i,$n_p);
		}
		
		return false ;
	}
	protected function fillPriceList($list_id){
		$listino = Listini::findFirst($list_id);
		if($listino){
			$versione = $listino->getLastVersionModel();
			$prodotti = Product::find();
			foreach ($prodotti as $prodotto){
				$info = new InfoProdotti();
				$info->ps_product_id = $prodotto->id_product ;
				$info->pr_listini_versioni_id = $versione->id ;
				if($info->save()){
					$prezzi = new PrezziProdotti();
					$prezzi->pr_info_prodotti_id = $info->id ;
					$prezzi->pr_listini_versioni_id = $versione->id ;
					$prezzi->save();
				}
			}
		}
		
	}
	public function allAction(){}
	
	public function updateListNameAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$id = $this->request->getPost('pk','int');
					$nuovo_nome = $this->request->getPost('value','string');
					$listino = Listini::findFirst($id);
					$payload = array('error' => 0 , 'message' => '');
					if($listino){
						$listino->nome = $nuovo_nome ;
						try{
							if($listino->save() == false){
								$payload['error'] = 1 ;
								foreach ($listino->getMessages() as $message){
									$payload['message'] .= $message.'<br>' ;
								}
							}else{
								$payload['message'] = _('Nuovo nome salvato');
							}
						}catch (\Exception $e){
							$payload['error'] = 2 ;
							$payload['message'] = $e->getMessage();
						}
						return $this->sendAjax($payload);
					}
				}
			}
		}
	}
	public function updateListActiveAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$id = $this->request->getPost('id','int');
					$state = $this->request->getPost('state','string') == 'true' ? 1 : 0 ;
					$listino = Listini::findFirst($id);
					$payload = array('error' => 0 , 'message' => '');
					if($listino){
						$listino->attivo = $state ;
						try{
							if($listino->save() == false){
								$payload['error'] = 1 ;
								foreach ($listino->getMessages() as $message){
									$payload['message'] .= $message.'<br>' ;
								}
							}else{
								$payload['message'] = _('Nuovo stato salvato') ;
							}
						}catch (\Exception $e){
							$payload['error'] = 2 ;
							$payload['message'] = $e->getMessage();
						}
					}
					return $this->sendAjax($payload);
				}
			}
		}
	}
	public function updateListVersionActiveAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$id = $this->request->getPost('id','int');
					$state = $this->request->getPost('state','string') == 'true' ? 1 : 0 ;
					$versione = ListiniVersioni::findFirst($id);
					$payload = array('error' => 0 , 'message' => '');
					if($versione){
						$versione->attivo = $state ;
						try{
							if($versione->save() == false){
								$payload['error'] = 1 ;
								foreach ($versione->getMessages() as $message){
									$payload['message'] .= $message.'<br>' ;
								}
							}else{
								$payload['message'] = _('Nuovo stato salvato') ;
							}
						}catch (\Exception $e){
							$payload['error'] = 2 ;
							$payload['message'] = $e->getMessage();
						}
					}
					return $this->sendAjax($payload);
				}
			}
		}
	}
	public function getListVersionsAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$id = $this->request->getPost('id','int');
					$listino = Listini::findFirst($id);
					if($listino){
						$versioni = ListiniVersioni::find(array(
								'pr_listini_id = ?0',
								'bind' => array($listino->id)
						));
						return $this->sendAjax(array('data'=>$versioni->toArray()));
					}
				}
			}
		}
	}
	public function updateListVersionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					$data = $this->request->getPost('data');
					$id = (int)$data['id'] ;
					$value = $this->request->getPost('value','string');
					$columnName = $this->request->getPost('columnName','string');
					switch ($columnName){
						case _('versione'):
							$columnName = 'versione_estesa' ;
							break;
						case _('attivo'):
							$columnName = 'attivo' ;
							break;
						
					}
					$versione =ListiniVersioni::findFirst($id);
					$payload = array('error' => 0 , 'message' => '');
					if($versione){
						$versione->$columnName = $value ;
						try{
							if($versione->save() == false){
								$payload['error'] = 1 ;
								foreach ($versione->getMessages() as $message){
									$payload['message'] .= $message.'<br>' ;
								}
							}else{
								$payload['message'] = _('Modifiche salvate');
							}
						}catch (\Exception $e){
							$payload['error'] = 2 ;
							$payload['message'] = $e->getMessage();
						}
					}
					return $this->sendAjax($payload);
				}
			}
		}
	}
	public function singleAction($listino_id){
		
		$listino = Listini::findFirst($listino_id);
		$this->view->listino = $listino ;
		
		$this->assetsPackage('bootstrap-switch');
		$this->assetsPackage('x-editable');
		$this->assetsPackage('data-table');
		$this->assetsPackage('data-editor');
		
		$this->loadInlineActionJs(array('listino' => $listino));
		
	}
	public function versionAction($listino_id,$versione_id){
		
	}
	
	public function showAction($listino_id = null,$versione_id = null){
		if($listino_id == null){
			// mostra elenco
			$this->view->pick('price_list/all');
		}else{
			if($versione_id != null){
				// mostra versione
			}else{
				// mostra solo listino
				return $this->forward('price_list','single',array('listino_id' => $listino_id));
			}
		}
	}
}