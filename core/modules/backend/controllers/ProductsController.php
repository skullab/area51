<?php
namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Products\PriceLists;
use Thunderhawk\API\Mvc\Model\Products\ProductPrices;
use Thunderhawk\API\Mvc\View\Helper\Model as HelperModel ;
use Thunderhawk\API\Mvc\Model\Products\Product;
class ProductsController extends Controller{
	protected function onInitialize(){
		$this->view->setTemplateAfter('index');
	}
	public function productPriceListAction(){
		$this->assetsPackage('toastr');
		$this->assetsPackage('sweet-alert');
		$this->assetsPackage('data-table');
		$this->assetsPackage('data-editor');
		$this->assetsPackage('form-validation');
		$this->assets->renderInlineJs('js/controllers/productsPriceList.js',true,array(
				'model' => new HelperModel()
		));
	}
	public function sourceProductsAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$records = Product::find()->toArray();
				$source = array();
				foreach ($records as $record){
					$r = array('value'=>$record['id'],'text'=>$record['name']);
					$source[] = $r ;
				}
				return $this->sendAjax($source);
			}
		}
	}
	public function sourcePriceListsAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$records = PriceLists::find()->toArray();
				$source = array();
				foreach ($records as $record){
					$r = array('value'=>$record['name'],'text'=>$record['name']);
					$source[] = $r ;
				}
				return $this->sendAjax($source);
			}
		}
	}
	public function updateProductsPriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$field = $this->request->getPost('columnName');
				$newValue = $this->request->getPost('value');
				$data = $this->request->getPost('data');
				$productPrice = ProductPrices::findFirstById($data['id']);
				$payload = array('error'=>1);
				if($productPrice){
					if($field == 'list_name'){
						$field = 'price_lists_id';
						$priceList = PriceLists::findFirstByName($newValue);
						if($priceList){
							$newValue = $priceList->id ;
						}
					}
					
					$productPrice->{$field} = $newValue;
					
					try{
						if($productPrice->save() === false){
							
							foreach ($productPrice->getMessages() as $message){
								$payload['message'] .= $message.'<br>' ;
							}
							
						}else{
							$payload['error'] = 0 ;
							$payload['message'] = _('Data updated');
						}
					}catch (\Exception $e){
						$payload['message'] = $e->getMessage();
					}
				}
				$payload['extra'] = $newValue ;
				return $this->sendAjax($payload);
			}
		}
	}
	public function getProductsPriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$records = ProductPrices::find();
					$table = array();
					$i = 0 ;
					foreach ($records as $record){
						
						$table[$i] = $record->toArray() ;
						$table[$i]['name'] = $record->product->name ;
						$table[$i]['list'] = $record->list->name ;
						
						$i++;
					}
					$data = array('data'=>$table);
					return $this->sendAjax($data);
				}
			}
		}
	}
	public function priceListsAction(){
		$this->assetsPackage('toastr');
		$this->assetsPackage('sweet-alert');
		$this->assetsPackage('data-table');
		$this->assetsPackage('data-editor');
		$this->assetsPackage('date-picker');
		$this->assetsPackage('form-validation');
		$this->assets->renderInlineJs('js/controllers/priceList.js');
	}
	public function getPriceListsAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$records = PriceLists::find()->toArray();
					$data = array('data'=>$records);
					return $this->sendAjax($data);
				}
			}
		}
	}
	public function addPriceListAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$name = $this->request->getPost('listName');
				$start_time = $this->request->getPost('start');
				$end_time = $this->request->getPost('end');
				$priceList = new PriceLists();
				$priceList->name = $name ;
				$priceList->start_time = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $start_time))) ;
				$priceList->end_time = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $end_time)));
				try{
					if($priceList->save() === false){
						foreach ($priceList->getMessages() as $message){
							$this->flash->error($message);
						}
					}else{
						$this->flash->success(_('The Price List is successfully created'));
					}
				}catch (\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
		return $this->forward('products','priceLists');
	}
	public function addProductPriceAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$product_id = $this->request->getPost('product');
				$price_lists_id = $this->request->getPost('list');
				$price_list = $this->request->getPost('priceList');
				$price_retail = $this->request->getPost('priceRetail');
				$productPrice = new ProductPrices();
				$productPrice->product_id = $product_id ;
				$productPrice->price_lists_id = $price_lists_id;
				$productPrice->price_list = $price_list;
				$productPrice->price_retail = $price_retail;
				try{
					if($productPrice->save() === false){
						foreach ($productPrice->getMessages() as $message){
							$this->flash->error($message);
						}
					}else{
						$this->flash->success(_('The Product Price is successfully created'));
					}
				}catch (\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
		return $this->forward('products','productPriceList');
	}
	public function dropPriceListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$dropList = $this->request->getPost('dropList');
					$payload = array('error'=>0);
					$check = true ;
					foreach ($dropList as $list){
						$priceList = PriceLists::findFirstById($list['id']);
						if($priceList){
							$check &= $priceList->delete();
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
	public function updatePriceListAction(){
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
	}
}