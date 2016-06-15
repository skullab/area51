<?php
namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Products\PriceLists;
use Thunderhawk\API\Mvc\Model\Products\ProductPrices;
use Thunderhawk\API\Mvc\View\Helper\Model as HelperModel ;
use Thunderhawk\API\Mvc\Model\Products\Product;
use Thunderhawk\API\Mvc\Model\Products\ProductLang;
use Thunderhawk\API\Mvc\Model\Products\PriceListsRevisions;
use Thunderhawk\API\Mvc\Model\Products\ProductPricesRevisions;
class ProductsController extends Controller{
	protected function onInitialize(){
		$this->view->setTemplateAfter('index');
	}
	public function updateProductsPriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$data = $this->request->getPost('data');
				
				$newValue = $this->request->getPost('value');
				$idRev = $this->request->getPost('idRevision');
				switch ($this->request->getPost('columnName')){
					
					case 'prezzo_di_listino';
						$field = 'price_list';
						break;
					case 'prezzo_di_vendita':
						$field = 'price_retail';
						break;
					case 'prezzo_ecommerce':
							$field = 'price_ecommerce';
							break;
				}
				if($idRev != 0){
					$productPrice = ProductPricesRevisions::findFirstByProductPricesId($data['id']);
					if(!$productPrice){
						$parent = ProductPrices::findFirstById($data['id']);
						if($parent){
							$productPrice = new ProductPricesRevisions();
							$productPrice->product_prices_id = $parent->id ;
							$productPrice->price_lists_revisions_id = $idRev ;
							$productPrice->price_list = $parent->price_list ;
							$productPrice->price_retail = $parent->price_retail;
							$productPrice->price_ecommerce = $parent->price_ecommerce;
						}
					}
				}else{
					$productPrice = ProductPrices::findFirstById($data['id']);
				}
				$payload = array();
				
				if($productPrice){
					
					$productPrice->{$field} = $newValue;
					
					try{
						if($productPrice->save() === false){
							
							foreach ($productPrice->getMessages() as $message){
								$payload['error'] = 1 ;
								$payload['message'] .= $message.'<br>' ;
							}
							
						}else{
							$payload['error'] = 0 ;
							$payload['message'] = _('Prezzo aggiornato');
						}
					}catch (\Exception $e){
						$payload['error'] = 2 ;
						$payload['message'] = $e->getMessage();
					}
				}else{
					$payload['error'] = 3 ;
					$payload['message'] = _('Record non trovato');
				}
				$payload['extra'] = $this->request->getPost();
				return $this->sendAjax($payload);
			}
		}
	}
	protected function joinProductsPriceRevision($id,$idRev){
		$query = $this->modelsManager->createQuery("SELECT pp.id,ppr.id as revision_id,pp.product_id,pp.price_lists_id,pp.price_list,pp.price_retail,pp.price_ecommerce,ppr.price_lists_revisions_id,ppr.price_list as price_list_revision,ppr.price_retail as price_retail_revision,ppr.price_ecommerce as price_ecommerce_revision
				FROM Thunderhawk\API\Mvc\Model\Products\ProductPrices AS pp 
				LEFT JOIN Thunderhawk\API\Mvc\Model\Products\ProductPricesRevisions AS ppr ON pp.id = ppr.product_prices_id
				AND ppr.price_lists_revisions_id = :idRev: WHERE pp.price_lists_id = :idList:");
		
		
		$prices = $query->execute(array('idRev'=>$idRev,'idList'=>$id));
		$data = array();
		$i = 0 ;
		foreach ($prices as $price){
			$data[$i] = $price->toArray();
			$data[$i]['prodotto'] = Product::findFirstByIdProduct($price->product_id)->getName(1);
			
			if($data[$i]['price_list_revision'] != null){
				$data[$i]['price_list'] = $data[$i]['price_list_revision'] ;
			}
			if($data[$i]['price_retail_revision'] != null){
				$data[$i]['price_retail'] = $data[$i]['price_retail_revision'] ;
			}
			if($data[$i]['price_ecommerce_revision'] != null){
				$data[$i]['price_ecommerce'] = $data[$i]['price_ecommerce_revision'] ;
			}
			$i++ ;
		}
		return $this->sendAjax(array('data'=>$data));
	}
	public function getProductsPriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->checkReusable('token')){
					
					$id =(int)$this->request->getPost('idListino');
					$idRev = (int)$this->request->getPost('idRevisione');
					if($idRev > 0)return $this->joinProductsPriceRevision($id, $idRev);
					
					$records = ProductPrices::find(array(
							'price_lists_id = ?0',
							'bind' => array($id)
					));
					$table = array();
					$i = 0 ;
					foreach ($records as $record){
						
						$table[$i] = $record->toArray() ;
						//$product = ProductLang::findFirst("id_lang = 1 AND id_product = $record->product_id");
						
						$table[$i]['prodotto'] = $record->product->getName(1) ;
						
						$i++;
					}
					$data = array('data'=>$table);
					return $this->sendAjax($data);
				}
			}
		}
	}
	public function priceListsAllAction(){
		
	}
	public function priceListsNewAction(){
		
	}
	public function priceListsAction($idListino,$idRevisione = 0){
		$this->assetsPackage('toastr');
		$this->assetsPackage('sweet-alert');
		$this->assetsPackage('data-table');
		$this->assetsPackage('data-editor');
		$this->assetsPackage('date-picker');
		$this->assetsPackage('form-validation');
		
		var_dump($idListino);
		var_dump($idRevisione);
		$listino = PriceLists::findFirstById($idListino);
		if(!$listino){
			$listino = PriceLists::findFirst();
		}
		$this->view->listino = $listino ;
		
		if($idRevisione != 0){
			$revisione = PriceListsRevisions::findFirstById($idRevisione);
			if($revisione)$this->view->revisione = $revisione ;
		}
		$this->assets->renderInlineJs('js/controllers/productsPriceList.js',true,array(
				'idListino' => $listino->id,
				'idRevisione' => $idRevisione
		));
	}
	protected function clonePriceList($idClone,$idNew){
		$clone = PriceLists::findFirstById($idClone);
		if($clone){
			$new = PriceLists::findFirstById($idNew);
			if($new){
				$prezzi = ProductPrices::findByPriceListsId($idClone);
				foreach ($prezzi as $prezzo){
					$newPrezzo = new ProductPrices();
					$newPrezzo->product_id = $prezzo->product_id ;
					$newPrezzo->price_list = $prezzo->price_list ;
					$newPrezzo->price_retail = $prezzo->price_retail;
					$newPrezzo->price_ecommerce = $prezzo->price_ecommerce;
					$newPrezzo->price_lists_id = $new->id ;
					if($newPrezzo->save() == false){
						foreach ($newPrezzo->getMessages() as $message){
							$this->flash->error($message);
						}
					}
				}
		
			}else{
				$this->flash->error(_('Il listino non esiste'));
			}
		}else{
			$this->flash->error(_('Il listino da clonare non esiste'));
		}
		
		$this->flash->success('Il listino e stato clonato !');
		return $this->forward('products','priceLists',array($idNew));
	}
	public function clonePriceListAction($idClone,$idNew){
		if($this->request->isPost()){
			if($this->token->checkReusable()){
				return $this->clonePriceList($idClone, $idNew);
			}
		}
	}
	public function addPriceListAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$name = (string)$this->request->getPost('listName');
				$empty = $this->request->getPost('listEmpty') == 'on' ? true : false ;
				
				$listino = new PriceLists();
				$listino->name = $name ;
				$check = false ;
				try{
					if($listino->save() == false){
						foreach ($listino->getMessages() as $message){
							$this->flash->error($message);
						}
					}else{
						$check = true ;
						$this->flash->success(_('Nuovo Listino creato'));
					}
				}catch(\Exception $e){
					$this->flash->error($e->getMessage());
				}
				
				$idClone = (int)$this->request->getPost('listClone');
				if($check && $idClone > 0)return $this->clonePriceList($idClone,$listino->id);
				
				if($check && !$empty){
					$prodotti = Product::find();
					foreach ($prodotti as $prodotto){
						$prezzi = new ProductPrices();
						$prezzi->product_id = $prodotto->id_product ;
						$prezzi->price_lists_id = $listino->id ;
						if($prezzi->save() == false){
							foreach ($prezzi->getMessages() as $message){
								$this->flash->error($message);
							}
						}
					}
				}
				
				return $this->forward('products','priceListsAll');
			}
		}
	}
	public function priceListsRenameAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$id = $this->request->getPost('pk');
				$payload = array('error'=>0);
				$listino = PriceLists::findFirstById($id);
				if(!$listino){
					$payload['error'] = 1 ;
					$payload['message'] = _('Il listino non esiste');
				}else{
					$listino->name = (string)$this->request->getPost('value');
					try{
						if($listino->save() == false){
							$payload['error'] = 2 ;
							foreach ($listino->getMessages() as $message){
								$payload['message'] .= $message.'<br>' ;
							}
						}else{
							$payload['message'] = _('Il listino e stato rinominato');
						}
					}catch (\Exception $e){
						$payload['error'] = 3 ;
						$payload['message'] = $e->getMessage();
					}
				}
				return $this->sendAjax($payload);
			}
		}
	}
	public function priceListsRevisionsRenameAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$id = $this->request->getPost('pk');
				$payload = array('error'=>0);
				$listino = PriceListsRevisions::findFirstById($id);
				if(!$listino){
					$payload['error'] = 1 ;
					$payload['message'] = _('La revisione non esiste');
				}else{
					$listino->name = (string)$this->request->getPost('value');
					try{
						if($listino->save() == false){
							$payload['error'] = 2 ;
							foreach ($listino->getMessages() as $message){
								$payload['message'] .= $message.'<br>' ;
							}
						}else{
							$payload['message'] = _('La revisione di listino e stata rinominata');
						}
					}catch (\Exception $e){
						$payload['error'] = 3 ;
						$payload['message'] = $e->getMessage();
					}
				}
				return $this->sendAjax($payload);
			}
		}
	}
	public function addPriceListRevisionAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$id = (int)$this->request->getPost('idListino');
				$name = (string)$this->request->getPost('revisionName');
				$listino = PriceLists::findFirstById($id);
				if($listino){
					$revision = new PriceListsRevisions();
					$revision->price_lists_id = $listino->id ;
					$revision->name = $name ;
					try{
						if($revision->save() == false){
							foreach ($revision->getMessages() as $message){
								$this->flash->error($message);
							}
						}else{
							$listino->has_revisions = 1 ;
							$listino->save();
							$this->flash->success(_('Revisione listino creata'));
						}
					}catch(\Exception $e){
						$this->flash->error($e->getMessage());
					}
				}
				$this->forward('products','priceLists',array($listino->id));
			}
		}
	}
	public function dropPriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$id = $this->request->getPost('idListino');
					$payload = array('error'=>0);
					$check = true ;
					$listino = PriceLists::findFirstById($id);
					if(!$listino){
						$payload['error'] = 1 ;
						$payload['message'] = _('Il listino non esiste');
					}else{
						try{
							if($listino->delete() == false){
								$payload['error'] = 2 ;
								foreach ($listino->getMessages() as $message){
									$payload['message'] .= $message ;
								}
							}
						}catch (\Exception $e){
							$payload['error'] = 3 ;
							$payload['message'] = $e->getMessage() ;
						}
					}
					
					return $this->sendAjax($payload);
				}
			}
		}
	}
	public function dropPriceListRevisionAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$id = $this->request->getPost('idRevisione');
					$payload = array('error'=>0);
					$check = true ;
					$listino = PriceListsRevisions::findFirstById($id);
					if(!$listino){
						$payload['error'] = 1 ;
						$payload['message'] = _('La revisione non esiste');
					}else{
						try{
							$parent = PriceLists::findFirstById($listino->price_lists_id);
							if($listino->delete() == false){
								$payload['error'] = 2 ;
								foreach ($listino->getMessages() as $message){
									$payload['message'] .= $message ;
								}
							}
							$all = PriceListsRevisions::findByPriceListsId($parent->id);
							if(count($all) == 0){
								$parent->has_revisions = 0 ;
								$parent->save();
							}
						}catch (\Exception $e){
							$payload['error'] = 3 ;
							$payload['message'] = $e->getMessage() ;
						}
					}
						
					return $this->sendAjax($payload);
				}
			}
		}
	}
	public function addProductPriceAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$product_id = (int)$this->request->getPost('product');
				$price_lists_id = (int)$this->request->getPost('idListino');
				$price_lists_revisions_id = (int)$this->request->getPost('idRevisione');
				$price_list = (float)$this->request->getPost('productPriceList');
				$price_retail = (float)$this->request->getPost('productPriceRetail');
				$price_ecommerce = (float)$this->request->getPost('productPriceEcommerce');
				
				$listino = PriceLists::findFirstById($price_lists_id);
				if($listino){
					$prodotto = new ProductPrices();
					$prodotto->price_lists_id = $price_lists_id ;
					$prodotto->product_id = $product_id ;
					$prodotto->price_list = $price_list ;
					$prodotto->price_retail = $price_retail ;
					$prodotto->price_ecommerce = $price_ecommerce ;
					$check = false ;
					try{
						if($prodotto->save() === false){
							foreach ($prodotto->getMessages() as $message){
								$this->flash->error($message);
							}
						}else{
							$check = true ;
							$this->flash->success(_('Nuovo prodotto aggiunto al listino'));
						}
					}catch (\Exception $e){
						$this->flash->error($e->getMessage());
					}
					if($check && $listino->has_revisions && $price_lists_revisions_id > 0){
						$rev = new ProductPricesRevisions();
						$rev->product_prices_id = $prodotto->id ;
						$rev->price_lists_revisions_id = $price_lists_revisions_id ;
						$rev->price_list = $price_list ;
						$rev->price_retail = $price_retail ;
						$rev->price_ecommerce = $price_ecommerce ;
						try{
							if($rev->save() === false){
								foreach ($rev->getMessages() as $message){
									$this->flash->error($message);
								}
							}else{
								$this->flash->success(_('Nuovo prodotto aggiunto alla revisione di listino'));
							}
						}catch (\Exception $e){
							$this->flash->error($e->getMessage());
						}
					}
				}
				return $this->forward('products','priceLists',array($price_lists_id,$price_lists_revisions_id));
			}
		}
		
	}
	public function dropProductPriceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$dropList = $this->request->getPost('dropList');
					$payload = array('error'=>0);
					$check = true ;
					foreach ($dropList as $list){
						$productPrice = ProductPrices::findFirstById($list['id']);
						if($productPrice){
							$check &= $productPrice->delete();
						}
					}
					if(!$check){
						$payload['error'] = 1 ;
					}
					return $this->sendAjax($payload);
				}
			}
		}
	}
	/*public function updatePriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$field = $this->request->getPost('columnName');
				$newValue = $this->request->getPost('value');
				$data = $this->request->getPost('data');
				$priceList = PriceLists::findFirstById($data['id']);
				if($priceList){
					$priceList->{$field} = $newValue ;
					try{
						if($priceList->save() !== false){
							$payload = array('error'=>0,'message'=>_('Data updated'));
						}
					}catch (\Exception $e){
						$payload = array('error'=>1,'message'=>$e->getMessage());
					}
				}
				return $this->sendAjax($payload);
			}
		}
	}*/
}