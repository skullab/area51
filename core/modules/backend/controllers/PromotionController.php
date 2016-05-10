<?php

namespace Thunderhawk\Modules\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Customer\CustomersDestinations;
use Thunderhawk\API\Mvc\Model\Customer\CustomersGroups;
use Thunderhawk\API\Mvc\Model\Customer\AgentCustomer;

class PromotionController extends Controller {
	protected function onInitialize() {
		$this->view->setTemplateAfter ( 'index' );
		$this->assetsPackage ( 'jquery-wizard' );
		$this->assetsPackage ( 'form-validation' );
		$this->assetsPackage ( 'select2' );
		$this->assetsPackage ( 'date-picker' );
		$this->assetsPackage ( 'sweet-alert' );
		$this->assetsPackage ( 'bootstrap-treeview' );
		$this->assetsPackage ( 'multi-select' );
		$this->assetsPackage ( 'bootstrap-markdown' );
	}
	
	public function getCustomersTreeAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				if ($this->token->check ( 'token' )) {
					$identity = $this->auth->getIdentity ();
					if (! $identity)
						return $this->sendAjax ( array (
								'error' => 1 
						) );
					$relazioni = AgentCustomer::findByAgentId ( $identity ['id'] );
					$response = array (
							'error' => 0,
							'data' => array () 
					);
					$data = array ();
					
					foreach ( $relazioni as $relazione ) {
						$pdv = $relazione->address->destination;
						$customer = $relazione->address->destination->customer;
						$gruppo = $relazione->address->destination->customer->gruppo;
						$data [$gruppo->id] ['text'] = $gruppo->nome;
						if (! is_array ( $data [$gruppo->id] ['nodes'] )) {
							$data [$gruppo->id] ['nodes'] = array ();
						}
						$data [$gruppo->id] ['nodes'] [$customer->id] ['text'] = $customer->nome;
						if (! is_array ( $data [$gruppo->id] ['nodes'] [$customer->id] ['nodes'] )) {
							$data [$gruppo->id] ['nodes'] [$customer->id] ['nodes'] = array ();
						}
						$data [$gruppo->id] ['nodes'] [$customer->id] ['nodes'] [$pdv->id] ['text'] = $pdv->nome;
						$data [$gruppo->id] ['nodes'] [$customer->id] ['nodes'] [$pdv->id] ['pdvId'] = $pdv->id;
					}
					foreach ( $data as $d ) {
						$response ['data'] [] = $d;
					}
					return $this->sendAjax ( $response );
				}
			}
		}
	}
	public function addAction() {
		$this->assets->renderInlineJs ( 'js/controllers/promotionAdd.js' );
	}
}