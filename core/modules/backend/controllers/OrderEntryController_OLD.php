<?php

namespace Thunderhawk\Modules\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Customer\CustomersGroups;
use Thunderhawk\API\Mvc\Model\Customer\Customers;
use Thunderhawk\API\Mvc\Model\Customer\CustomersState;
use Thunderhawk\API\Mvc\Model\Customer\CustomersDetails;
use Thunderhawk\API\Mvc\Model\Customer\CustomersAddress;
use Thunderhawk\API\Mvc\Model\Customer\CustomersDestintions;
use Thunderhawk\API\Mvc\Model\Customer\CustomersDestinations;
use Thunderhawk\API\Mvc\Model\Customer\CustomersDestinationsAddress;
use Thunderhawk\API\Mvc\Model\Customer\AgentManager;
use Thunderhawk\API\Mvc\Model\User\Users;
use Thunderhawk\API\Mvc\Model\Customer\CustomersBook;
use Thunderhawk\API\Mvc\Model\Customer\CustomersPriceLists;
use Thunderhawk\API\Mvc\Model\Products\PriceLists;
use Thunderhawk\API\Mvc\Model\Customer\AgentCustomer;
use Thunderhawk\API\Mvc\Model\Italia\ItaliaComuni;

class OrderEntryController_OLD extends Controller {
	protected function onInitialize() {
		$this->view->setTemplateAfter ( 'index' );
	}
	public function getRegistryAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$relations = AgentCustomer::find ();
					
					$response = array ();
					$response_t = array ();
					$listini = array ();
					$contabili = array ();
					
					foreach ( $relations as $relation ) {
						$registry = array ();
						$registry ['id'] = $relation->id;
						$registry ['agente'] = $relation->agent->details->name . ' ' . $relation->agent->details->surname;
						$manager = AgentManager::findFirstByAgentId ( $relation->agent->id );
						if ($manager) {
							$registry ['responsabile'] = $manager->manager->details->name . ' ' . $manager->manager->details->surname;
						} else {
							$registry ['responsabile'] = _ ( 'Nessuno' );
						}
						$registry ['indirizzo'] = $relation->address->indirizzo;
						$registry ['pdv'] = $relation->address->destination->nome;
						$registry ['fatturatario'] = $relation->address->destination->customer->nome;
						$registry ['gruppo'] = $relation->address->destination->customer->gruppo->nome;
						$listini [$relation->id] = CustomersPriceLists::findByCustomersDestinationsId ( $relation->address->destination->id );
						$contabili [$relation->id] = CustomersBook::findByCustomersId ( $relation->address->destination->customer->id );
						$registry ['stato'] = $relation->address->destination->customer->stato->stato;
						$response_t [] = $registry;
					}
					
					foreach ( $response_t as $reg ) {
						$clone = $reg;
						foreach ( $listini [$reg ['id']] as $listino ) {
							$clone ['listino'] = $listino->listino->name;
							foreach ( $contabili [$reg ['id']] as $contabile ) {
								$clone ['contabilita'] = $contabile->bookkeeper->details->name . ' ' . $contabile->bookkeeper->details->surname;
								$response [] = $clone;
							}
						}
					}
					
					$response_t = null;
					return $this->sendAjax ( array (
							'data' => $response 
					) );
				}
			}
		}
	}
	public function registryAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryRegistry.js' );
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
				}
				try {
					if ($group->save () !== false) {
						$response ['error'] = 0;
						$response ['message'] = _ ( 'The group has been updated' );
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
				$code = $this->request->getPost ( 'groupCode', 'string' );
				$name = $this->request->getPost ( 'groupName', 'string' );
				$group = new CustomersGroups ();
				$group->codice = $code;
				$group->nome = $name;
				try {
					if ($group->save () !== false) {
						$this->flash->success ( _ ( 'The group has been successfully added' ) );
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
	public function groupsAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryGroup.js' );
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
	public function sourceEpalAction() {
		// if($this->request->isPost()){
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
		// }
	}
	public function sourceMonorefAction() {
		// if($this->request->isPost()){
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
		// }
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
					}
					if ($field == 'stato') {
						$field = 'customers_state_id';
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
				} else {
					$response ['newValue'] = $customer->{$field};
				}
				
				return $this->sendAjax ( $response );
			}
		}
	}
	public function getCustomersAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$customers = Customers::find ()->toArray ();
					$response = array ();
					foreach ( $customers as $customer ) {
						$customer ['gruppo'] = CustomersGroups::findFirstById ( $customer ['customers_groups_id'] )->nome;
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
	public function addCustomerAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$codice = $this->request->getPost ( 'customerCode' );
				$nome = $this->request->getPost ( 'customerName' );
				$gruppo = $this->request->getPost ( 'customerGroup' );
				$stato = $this->request->getPost ( 'customerState' );
				$customer = new Customers ();
				$customer->codice = $codice;
				$customer->nome = $nome;
				$customer->customers_groups_id = $gruppo;
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
	public function updateCustomerDetailsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$field = $this->request->getPost ( 'name' );
				$value = $this->request->getPost ( 'value' );
				$id = $this->request->getPost ( 'pk' );
				$customer = Customers::findFirstById ( $id );
				if ($customer) {
					if (! $customer->dettagli) {
						$customer->dettagli = new CustomersDetails ();
					}
					if (! $customer->indirizzo) {
						$customer->indirizzo = new CustomersAddress ();
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
	public function customersAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ('select2');
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assetsPackage ( 'spinner' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryCustomer.js' );
	}
	public function getPdvsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$pdvs = CustomersDestinations::find ()->toArray ();
					$response = array ();
					foreach ( $pdvs as $pdv ) {
						$pdv ['customer'] = Customers::findFirstById ( $pdv ['customers_id'] )->nome;
						$response [] = $pdv;
					}
					return $this->sendAjax ( array (
							"data" => $response 
					) );
				}
			}
		}
	}
	public function addPdvAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$pdvCode = $this->request->getPost ( 'pdvCode' );
				$pdvName = $this->request->getPost ( 'pdvName' );
				$pdvAddressCode = $this->request->getPost ( 'pdvAddressCode' );
				$pdvLabelCode = $this->request->getPost ( 'pdvLabelCode' );
				$pdvLabel = $this->request->getPost ( 'pdvLabel' );
				$pdvCustomer = $this->request->getPost ( 'pdvCustomer' );
				$pdv = new CustomersDestinations ();
				$pdv->codice_destinazione = $pdvCode;
				$pdv->nome = $pdvName;
				$pdv->codice_indirizzo = $pdvAddressCode;
				$pdv->codice_insegna = $pdvLabelCode;
				$pdv->insegna = $pdvLabel;
				$pdv->customers_id = ( int ) $pdvCustomer;
				try {
					if ($pdv->save () !== false) {
						$this->flash->success ( _ ( 'PDV has been added' ) );
					} else {
						foreach ( $pdv->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
				return $this->forward ( 'order_entry', 'pdv' );
			}
		}
	}
	public function sourceCustomersAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$customers = Customers::find ()->toArray ();
				$response = array ();
				foreach ( $customers as $customer ) {
					$source = array (
							'value' => $customer ['id'],
							'text' => $customer ['nome'] 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function updatePdvAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				$field = $data ['columnName'];
				if ($field == 'fatturatario')
					$field = "customers_id";
				$pdv = CustomersDestinations::findFirstById ( $id );
				$response = array (
						'error' => 0 
				);
				if ($pdv) {
					$pdv->{$field} = $newValue;
					try {
						if ($pdv->save () !== false) {
							$response ['message'] = _ ( 'Destination has been updated' );
						} else {
							$response ['error'] = 1;
							foreach ( $pdv->getMessages () as $message ) {
								$response ['message'] .= $message . '<br>';
							}
						}
					} catch ( \Exception $e ) {
						$response ['error'] = 2;
						$response ['message'] = $e->getMessage ();
					}
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function dropPdvsAction() {
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
						$pdv = CustomersDestinations::findFirstById ( $list ['id'] );
						if ($pdv) {
							$check &= $pdv->delete ();
							if (! $check) {
								foreach ( $pdv->getMessages () as $message ) {
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
	public function pdvAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryPdv.js' );
	}
	public function getAddressAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$address = CustomersDestinationsAddress::find ()->toArray ();
					$response = array ();
					foreach ( $address as $ad ) {
						$ad ['pdv'] = CustomersDestinations::findFirstById ( $ad ['customers_destinations_id'] )->nome;
						$response [] = $ad;
					}
					return $this->sendAjax ( array (
							'data' => $response 
					) );
				}
			}
		}
	}
	public function addAddressAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$indirizzo = $this->request->getPost ( 'indirizzo' );
				$cap = $this->request->getPost ( 'cap' );
				$citta = $this->request->getPost ( 'citta' );
				$provincia = $this->request->getPost ( 'provincia' );
				$regione = $this->request->getPost ( 'regione' );
				$nazione = $this->request->getPost ( 'nazione' );
				$telefono = $this->request->getPost ( 'telefono' );
				$note = $this->request->getPost ( 'note' );
				$customers_destinations_id = $this->request->getPost ( 'pdv' );
				
				$address = new CustomersDestinationsAddress ();
				$address->indirizzo = $indirizzo;
				$address->cap = $cap;
				$address->citta = $citta;
				$address->provincia = $provincia;
				$address->regione = $regione;
				$address->nazione = $nazione;
				$address->telefono = $telefono;
				$address->note = $note;
				$address->customers_destinations_id = ( int ) $customers_destinations_id;
				try {
					if ($address->save () !== false) {
						$this->flash->success ( _ ( 'Address has been added' ) );
					} else {
						foreach ( $address->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
					$comune = ItaliaComuni::findFirstByNome($citta);
					if($comune){
						if(trim($comune->cap) == '' || $comune->cap != $cap){
							$comune->cap = $cap ;
							try{
								$comune->save();
							}catch (\Exception $e){}
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'address' );
	}
	public function updateAddressAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ( '' );
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				$field = $data ['columnName'];
				if ($field == 'pdv')
					$field = 'customers_destinations_id';
				$response = array (
						'error' => 0 
				);
				$address = CustomersDestinationsAddress::findFirstById ( $id );
				if ($address) {
					$address->{$field} = $newValue;
					try {
						if ($address->save () !== false) {
							$response ['message'] = _ ( 'Address has been updated' );
						} else {
							$response ['error'] = 1;
							foreach ( $address->getMessages () as $message ) {
								$response ['message'] .= $message . '<br>';
							}
						}
					} catch ( \Exception $e ) {
						$response ['error'] = 2;
						$response ['message'] = $e->getMessage ();
					}
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function dropAddressAction() {
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
						$address = CustomersDestinationsAddress::findFirstById ( $list ['id'] );
						if ($address) {
							$check &= $address->delete ();
							if (! $check) {
								foreach ( $address->getMessages () as $message ) {
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
	public function sourcePdvsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$pdvs = CustomersDestinations::find ()->toArray ();
				$response = array ();
				foreach ( $pdvs as $pdv ) {
					$source = array (
							'value' => $pdv ['id'],
							'text' => $pdv ['nome'] 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function addressAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryAddress.js' );
	}
	public function getAgentAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$relations = AgentCustomer::find ();
					$response = array ();
					foreach ( $relations as $relation ) {
						$r = array ();
						$r ['id'] = $relation->id;
						$r ['gruppo'] = $relation->address->destination->customer->gruppo->nome;
						$r ['fatturatario'] = $relation->address->destination->customer->nome;
						$r ['pdv'] = $relation->address->destination->nome;
						$r ['indirizzo'] = $relation->address->indirizzo;
						$r ['agente'] = $relation->agent->details->name . ' ' . $relation->agent->details->surname;
						$response [] = $r;
					}
					return $this->sendAjax ( array (
							'data' => $response 
					) );
				}
			}
		}
	}
	public function addAgentAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$agente = $this->request->getPost ( 'agente' );
				$indirizzo = $this->request->getPost ( 'indirizzo' );
				$relation = new AgentCustomer ();
				$relation->agent_id = ( int ) $agente;
				$relation->customers_destinations_address_id = ( int ) $indirizzo;
				try {
					if ($relation->save () !== false) {
						$this->flash->success ( _ ( 'Relation has been added' ) );
					} else {
						foreach ( $relation->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'agent' );
	}
	public function dropAgentAction() {
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
						$relation = AgentCustomer::findFirstById ( $list ['id'] );
						if ($relation) {
							$check &= $relation->delete ();
							if (! $check) {
								foreach ( $relation->getMessages () as $message ) {
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
	public function sourceAgentsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$users = Users::find ( "role = 'sales agent'" );
				$response = array ();
				foreach ( $users as $user ) {
					$source = array (
							'value' => $user->id,
							'text' => $user->details->name . ' ' . $user->details->surname 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function sourceManagersAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$users = Users::find ( "role = 'promotions manager'" );
				$response = array ();
				foreach ( $users as $user ) {
					$source = array (
							'value' => $user->id,
							'text' => $user->details->name . ' ' . $user->details->surname 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function sourceAddressAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$address = CustomersDestinationsAddress::find ();
				$response = array ();
				foreach ( $address as $ad ) {
					$source = array (
							'value' => $ad->id,
							'text' => $ad->indirizzo 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function sourceAddressByIdAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$id = $this->request->getPost ( 'id', 'int' );
				if ($id == 0)
					return $this->sourceAddressAction ();
				$address = CustomersDestinationsAddress::find ( array (
						'customers_destinations_id = ?0',
						'bind' => array (
								$id 
						) 
				) );
				$response = array ();
				foreach ( $address as $ad ) {
					$source = array (
							'value' => $ad->id,
							'text' => $ad->indirizzo 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function updateAgentAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				
				switch ($data ['columnName']) {
					case 'agente' :
						$field = 'agent_id';
						break;
					case 'indirizzo' :
						$field = 'customers_destinations_address_id';
						break;
				}
				$relation = AgentCustomer::findFirstById ( $id );
				$response = array ();
				if ($relation) {
					$relation->{$field} = $newValue;
				}
				try {
					if ($relation->save () !== false) {
						$response ['error'] = 0;
						$response ['message'] = _ ( 'The Relation has been updated' );
					} else {
						$response ['error'] = 1;
						foreach ( $relation->getMessages () as $message ) {
							$response ['message'] .= $message . '<br>';
						}
					}
				} catch ( \Exception $e ) {
					$response ['error'] = 2;
					$response ['message'] = $e->getMessage ();
				}
				$response ['column'] = $data ['columnName'];
				return $this->sendAjax ( $response );
			}
		}
	}
	public function agentAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryAgent.js' );
	}
	public function getBookkeeperAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$relations = CustomersBook::find ();
					$response = array ();
					foreach ( $relations as $relation ) {
						$r = array ();
						$r ['id'] = $relation->id;
						$r ['contabilita'] = $relation->bookkeeper->details->name . ' ' . $relation->bookkeeper->details->surname;
						$r ['fatturatario'] = $relation->customer->nome;
						$response [] = $r;
					}
					return $this->sendAjax ( array (
							'data' => $response 
					) );
				}
			}
		}
	}
	public function addBookkeeperAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$contabile = $this->request->getPost ( 'contabile', 'int' );
				$fatturatario = $this->request->getPost ( 'fatturatario', 'int' );
				
				$relation = new CustomersBook ();
				$relation->bookkeeper_id = $contabile;
				$relation->customers_id = $fatturatario;
				
				try {
					if ($relation->save () !== false) {
						$this->flash->success ( _ ( 'Relation has been added' ) );
					} else {
						foreach ( $relation->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'bookkeeper' );
	}
	public function updateBookkeeperAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = ( int ) $data ['value'];
				switch ($data ['columnName']) {
					case 'contabilita' :
						$field = 'bookkeeper_id';
						break;
					case 'fatturatario' :
						$field = 'customers_id';
						break;
				}
				$relation = CustomersBook::findFirstById ( $id );
				$response = array ();
				if ($relation) {
					$relation->{$field} = $newValue;
				}
				try {
					if ($relation->save () !== false) {
						$response ['error'] = 0;
						$response ['message'] = _ ( 'The Relation has been updated' );
					} else {
						$response ['error'] = 1;
						foreach ( $relation->getMessages () as $message ) {
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
	public function sourceBookkeeperAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$bookkeepers = Users::find ( "role = 'bookkeeper'" );
				$response = array ();
				foreach ( $bookkeepers as $bookkeeper ) {
					$source = array (
							'value' => $bookkeeper->id,
							'text' => $bookkeeper->details->name . ' ' . $bookkeeper->details->surname 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function dropBookkeeperAction() {
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
						$relation = CustomersBook::findFirstById ( $list ['id'] );
						if ($relation) {
							$check &= $relation->delete ();
							if (! $check) {
								foreach ( $relation->getMessages () as $message ) {
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
	public function bookkeeperAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryBookkeeper.js' );
	}
	public function getPriceListsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$lists = CustomersPriceLists::find ();
					$response = array ();
					foreach ( $lists as $list ) {
						$r = array ();
						$r ['id'] = $list->id;
						$r ['gruppo'] = $list->pdv->customer->gruppo->nome ;
						$r ['fatturatario'] = $list->pdv->customer->nome ;
						$r ['pdv'] = $list->pdv->nome;
						$r ['listino'] = $list->listino->name;
						$response [] = $r;
					}
					return $this->sendAjax ( array (
							'data' => $response 
					) );
				}
			}
		}
	}
	public function addPriceListsAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$listino = $this->request->getPost ( 'listino', 'int' );
				$pdv = $this->request->getPost ( 'pdv', 'int' );
				$relation = new CustomersPriceLists ();
				$relation->price_lists_id = $listino;
				$relation->customers_destinations_id = $pdv;
				try {
					if ($relation->save () !== false) {
						$this->flash->success ( _ ( 'The Relation has been added' ) );
					} else {
						foreach ( $relation->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'pricelists' );
	}
	public function sourcePriceListsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$listini = PriceLists::find ();
				$response = array ();
				foreach ( $listini as $listino ) {
					$source = array (
							'value' => $listino->id,
							'text' => $listino->name 
					);
					$response [] = $source;
				}
				return $this->sendAjax ( $response );
			}
		}
	}
	public function updatePriceListsAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				switch ($data ['columnName']) {
					case 'listino' :
						$field = 'price_lists_id';
						break;
					case 'pdv' :
						$field = 'customers_destinations_id';
						break;
				}
				$relation = CustomersPriceLists::findFirstById ( $id );
				$response = array ();
				if ($relation) {
					$relation->{$field} = $newValue;
				}
				try {
					if ($relation->save () !== false) {
						$response ['error'] = 0;
						$response ['message'] = _ ( 'The Relation has been updated' );
					} else {
						$response ['error'] = 1;
						foreach ( $relation->getMessages () as $message ) {
							$response ['message'] .= $message . '<br>';
						}
					}
				} catch ( \Exception $e ) {
					$response ['error'] = 2;
					$response ['message'] = $e->getMessage ();
				}
				$response ['column'] = $data ['columnName'];
				return $this->sendAjax ( $response );
			}
		}
	}
	public function dropPriceListsAction() {
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
						$relation = CustomersPriceLists::findFirstById ( $list ['id'] );
						if ($relation) {
							$check &= $relation->delete ();
							if (! $check) {
								foreach ( $relation->getMessages () as $message ) {
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
	public function pricelistsAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryPriceLists.js' );
	}
	public function dropManagerAction() {
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
						$manager = AgentManager::findFirstById ( $list ['id'] );
						if ($manager) {
							$check &= $manager->delete ();
							if (! $check) {
								foreach ( $manager->getMessages () as $message ) {
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
	
	public function updateManagerAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$data = $this->request->getPost ();
				$id = $data ['data'] ['id'];
				$newValue = $data ['value'];
				$response = array (
						'error' => 1,
						'message' => _ ( 'No Relations found' ) 
				);
				switch ($data ['columnName']) {
					case 'agente' :
						$field = 'agent_id';
						break;
					case 'responsabile' :
						$field = 'manager_id';
						break;
				}
				$relation = AgentManager::findFirstById ( $id );
				if ($relation) {
					try {
						$relation->{$field} = ( int ) $newValue;
						if ($relation->save () !== false) {
							$response ['error'] = 0;
							$response ['message'] = _ ( 'Relation has been updated' );
						} else {
							$response ['error'] = 1;
							foreach ( $relation->getMessages () as $message ) {
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
	}
	public function addManagerAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$agente = $this->request->getPost ( 'agente', 'int' );
				$manager = $this->request->getPost ( 'responsabile', 'int' );
				$relation = new AgentManager ();
				$relation->agent_id = $agente;
				$relation->manager_id = $manager;
				try {
					if ($relation->save () !== false) {
						$this->flash->success ( _ ( 'Relation has been added' ) );
					} else {
						foreach ( $relation->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
		return $this->forward ( 'order_entry', 'manager' );
	}
	public function getManagerAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$managers = AgentManager::find ();
					$response = array ();
					foreach ( $managers as $manager ) {
						$r = array ();
						$r ['id'] = $manager->id;
						$r ['agente'] = $manager->agent->details->name . ' ' . $manager->agent->details->surname;
						$r ['responsabile'] = $manager->manager->details->name . ' ' . $manager->manager->details->surname;
						$response [] = $r;
					}
					return $this->sendAjax ( array (
							'data' => $response 
					) );
				}
			}
		}
	}
	public function managerAction() {
		$this->assetsPackage ( 'toastr' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'data-table' );
		$this->assetsPackage ( 'data-editor' );
		$this->assetsPackage ( 'form-validation' );
		$this->assets->renderInlineJs ( 'js/controllers/orderEntryManager.js' );
	}
}