<?php

namespace Thunderhawk\Modules\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Customer\CustomersGroups;
use Thunderhawk\API\Mvc\Model\Customer\Customers;
use Thunderhawk\API\Mvc\Model\Customer\CustomersState;
use Thunderhawk\API\Mvc\Model\User\Users;
use Thunderhawk\API\Mvc\Model\Customer\CustomersDetails;
use Thunderhawk\API\Mvc\Model\Customer\CustomersAddress;
use Thunderhawk\API\Mvc\Model\Customer\PaymentModes;

class OrderEntryController extends Controller {
	protected function onInitialize() {
		$this->view->setTemplateAfter ( 'index' );
	}
	//SOURCES
	public function sourcePaymentModesAction(){
		
			if ($this->request->isAjax ()) {
				$payment = PaymentModes::find();
				$source = array();
				$i = 0 ;
				foreach ($payment as $pay){
					$source[$i] = array();
					$source[$i]['value'] = $pay->id ;
					$source[$i]['text'] = $pay->mode ;
					$i++;
				}
				return $this->sendAjax($source);
			}
		
	}
	public function sourceBookkeepersAction(){
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$bookkeepers = Users::findByRole('bookkeeper');
				$source = array();
				$i = 0 ;
				foreach ($bookkeepers as $bookkeeper){
					$source[$i] = array();
					$source [$i] ['value'] = $bookkeeper->id ;
					$source [$i] ['text'] = $bookkeeper->details->name.' '.$bookkeeper->details->surname ;
					$i++ ;
				}
				return $this->sendAjax ( $source );
				
			}
		}
	}
	public function sourceGroupsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$groups = CustomersGroups::find ()->toArray ();
				$source = array ();
				for($i = 0; $i < count ( $groups ); $i ++) {
					$source [$i] = array ();
					$source [$i] ['value'] = $groups [$i] ['id'];
					$source [$i] ['text'] = $groups [$i] ['nome'];
				}
				return $this->sendAjax ( $source );
			}
		}
	}
	public function sourceStatesAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$states = CustomersState::find ()->toArray ();
				$source = array ();
				for($i = 0; $i < count ( $states ); $i ++) {
					$source [$i] = array ();
					$source [$i] ['value'] = $states [$i] ['id'];
					$source [$i] ['text'] = $states [$i] ['stato'];
				}
				return $this->sendAjax ( $source );
			}
		}
	}
	public function sourceYesNo(){
		if ($this->request->isAjax ()) {
			$source = array (
					array (
							'value' => 1,
							'text' => _ ( 'SI' )
					),
					array (
							'value' => 0,
							'text' => _ ( 'NO' )
					)
			);
			return $this->sendAjax ( $source );
		}
	}
	public function sourceEpalAction() {
		return $this->sourceYesNo();
	}
	public function sourceMonorefAction() {
		return $this->sourceYesNo();
	}
	//----------------------------------------------
	// GRUPPI
	public function updateGroupAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				$field = $data ['columnName'];
				$group = CustomersGroups::findFirstById ( $id );
				$response = array ();
				if ($group) {
					$group->{$field} = $newValue;
					if($field == 'codice_gruppo'){
						$customers = Customers::findByCustomersGroupsId($id);
						foreach ($customers as $customer){
							$customer->codice_fatturatario = $newValue . substr($customer->codice_fatturatario, -4);
							$customer->save();
						}
					}
				}
				try {
					if ($group->save () !== false) {
						$response ['error'] = 0;
						$response ['message'] = _ ( 'Gruppo aggiornato con successo' );
					} else {
						$response ['error'] = 1;
						foreach ( $group->getMessages () as $message ) {
							$response ['message'] .= $message . '<br>';
						}
					}
				} catch ( \Exception $e ) {
					$response ['error'] = 2;
					$response ['message'] = $e->getMessage ();
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function dropGroupsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->checkReusable ( 'token' )) {
					$dropList = $this->request->getPost ( 'dropList' );
					$response = array (
							'error' => 0 
					);
					$messages = '';
					$check = true;
					foreach ( $dropList as $list ) {
						$group = CustomersGroups::findFirstById ( $list ['id'] );
						if ($group) {
							$check &= $group->delete ();
							if (! $check) {
								foreach ( $group->getMessages () as $message ) {
									$messages .= $message . '<br>';
								}
							}
						}
					}
					if (! $check) {
						$response ['error'] = 1;
						$response ['message'] = $messages;
					}
				}
				
				return $this->sendAjax ( $response );
			}
		}
	}
	public function addGroupAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$name = $this->request->getPost ( 'groupName', 'string' );
				$group = new CustomersGroups ();
				$group->codice_gruppo = CustomersGroups::getProgressiveCode ();
				$group->nome = $name;
				try {
					if ($group->save () !== false) {
						$this->flash->success ( _ ( 'Il gruppo e stato aggiunto correttamente' ) );
					} else {
						foreach ( $group->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'groups' );
	}
	public function getGroupsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$table = CustomersGroups::find ()->toArray ();
					return $this->sendAjax ( array (
							'data' => $table 
					) );
				}
			}
		}
	}
	public function groupsAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryGroup.js' );
	}
	// FATTURATARI
	public function customersAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'select2' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assetsPackage ( 'spinner' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryCustomer.js' );
	}
	public function getCustomersAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$customers = Customers::find ()->toArray ();
					$response = array ();
					foreach ( $customers as $customer ) {
						$bookkeper = Users::findFirstById ( $customer ['bookkeeper_id'] );
						$customer ['gruppo'] = CustomersGroups::findFirstById ( $customer ['customers_groups_id'] )->nome;
						$customer ['contabile'] = $bookkeper->details->name . ' ' . $bookkeper->details->surname;
						$customer ['stato'] = CustomersState::findFirstById ( $customer ['customers_state_id'] )->stato;
						$response [] = $customer;
					}
					return $this->sendAjax ( array (
							"data" => $response 
					) );
				}
			}
		}
	}
	public function addCustomerAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$nome = $this->request->getPost ( 'customerName', 'string' );
				$gruppo = $this->request->getPost ( 'customerGroup', 'int' );
				$contabile = $this->request->getPost ( 'customerBookkeeper', 'int' );
				$stato = $this->request->getPost ( 'customerState', 'int' );
				$customer = new Customers ();
				$customer->codice_fatturatario = Customers::getProgressiveCode($gruppo);
				$customer->nome = $nome;
				$customer->customers_groups_id = $gruppo;
				$customer->bookkeeper_id = $contabile ;
				$customer->customers_state_id = $stato;
				try {
					if ($customer->save () !== false) {
						$this->flash->success ( _ ( 'The customer has been added' ) );
					} else {
						foreach ( $customer->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'customers' );
	}
	public function dropCustomersAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->checkReusable ( 'token' )) {
					$dropList = $this->request->getPost ( 'dropList' );
					$response = array (
							'error' => 0
					);
					$messages = '';
					$check = true;
					foreach ( $dropList as $list ) {
						$customer = Customers::findFirstById ( $list ['id'] );
						if ($customer) {
							$check &= $customer->delete ();
							if (! $check) {
								foreach ( $customer->getMessages () as $message ) {
									$messages .= $message . '<br>';
								}
							}
						}
					}
					if (! $check) {
						$response ['error'] = 1;
						$response ['message'] = $messages;
					}
					$response ['data'] = $dropList;
					return $this->sendAjax ( $response );
				}
			}
		}
	}
	public function updateCustomerAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				$field = $data ['columnName'];
				$customer = Customers::findFirstById ( $id );
				$response = array ();
	
				if ($customer) {
					if ($field == 'gruppo') {
						$field = 'customers_groups_id';
						$group = CustomersGroups::findFirstById($newValue);
						if($group){
							$customer->codice_fatturatario = $group->codice_gruppo.substr($customer->codice_fatturatario, -4);
						}
					}
					if ($field == 'stato') {
						$field = 'customers_state_id';
					}
					if ($field == 'contabile') {
						$field = 'bookkeeper_id';
					}
					$customer->{$field} = $newValue;
				}
				try {
					if ($customer->save () !== false) {
						$response ['error'] = 0;
						$response ['message'] = _ ( 'The customer has been updated' );
					} else {
						$response ['error'] = 1;
						foreach ( $customer->getMessages () as $message ) {
							$response ['message'] .= $message . '<br>';
						}
					}
				} catch ( \Exception $e ) {
					$response ['error'] = 2;
					$response ['message'] = $e->getMessage ();
				}
				if ($field == 'customers_groups_id') {
					$response ['newValue'] = $customer->gruppo->nome;
				} elseif ($field == 'customers_state_id') {
					$response ['newValue'] = $customer->stato->stato;
				} elseif ($field == 'bookkeeper_id'){
					$response['newValue'] = $customer->contabile->details->name.' '.$customer->contabile->details->surname ;
				}else {
					$response ['newValue'] = $customer->{$field};
				}
	
				return $this->sendAjax ( $response );
			}
		}
	}
	public function getCustomerDetailsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
	
				$id = $this->request->getPost ( 'id' );
				$customer = Customers::findFirstById ( $id );
				if ($customer) {
					$response = array (
							'error' => 0,
							'name' => $customer->nome,
							'details' => $customer->dettagli,
							'payment_mode' => $customer->dettagli->pagamento->mode,
							'address' => $customer->indirizzo,
							'id' => $id
					);
				} else {
					$response = array (
							'error' => 1,
							'message' => _ ( 'No Customer found' )
					);
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function updateCustomerDetailsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$field = $this->request->getPost ( 'name' );
				$value = $this->request->getPost ( 'value' );
				$id = $this->request->getPost ( 'pk' );
				$customer = Customers::findFirstById ( $id );
				if ($customer) {
					if (! $customer->dettagli) {
						$customer->dettagli = new CustomersDetails();
					}
					if (! $customer->indirizzo) {
						$customer->indirizzo = new CustomersAddress();
					}
					$customer->dettagli->{$field} = $value;
					$customer->indirizzo->{$field} = $value;
					if ($customer->save () !== false) {
						$response = array (
								'error' => 0,
								'message' => _ ( 'Customer Details updated' )
						);
					} else {
						$response = array (
								'error' => 1
						);
						foreach ( $customer->getMessages () as $message ) {
							$response ['message'] .= $message . '<br>';
						}
					}
				} else {
					$response = array (
							'error' => 2,
							'message' => _ ( 'No Customer Found' )
					);
				}
				return $this->sendAjax ( $response );
			}
		}
	}
}