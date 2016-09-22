<?php

namespace Thunderhawk\Modules\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\LeadTime\GeoLeadTime;

class LeadTimeController extends Controller {
	protected function onInitialize() {
		$this->view->setTemplateAfter ( 'index' );
	}
	public function showAction() {
	}
	public function addGeoAction() {
		$this->assets->addJs ( 'https://maps.googleapis.com/maps/api/js?key=' . $this->config->app->google->api->key . '&libraries=drawing', false );
		$this->loadInlineActionJs ();
		
		//var_dump($this->request->getPost());
		
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				
				$label = $this->request->getPost ( 'label', 'string' );
				$lead_time = $this->request->getPost ( 'lead_time', 'int' );
				$lat = $this->request->getPost ( 'lat', 'string' );
				$lng = $this->request->getPost ( 'lng', 'string' );
				$radius = $this->request->getPost ( 'radius', 'float' );
				
				$geo = new GeoLeadTime ();
				$geo->label = $label;
				$geo->lead_time = $lead_time;
				$geo->lat = $lat;
				$geo->lng = $lng;
				$geo->radius = $radius;
				
				try {
					if ($geo->save () == false) {
						foreach ( $geo->getMessages () as $message ) {
							$this->flash->error ( $message );
						}
					} else {
						$message = sprintf(_('Nuova Area Geografica aggiunta').' :<br>'
								._('Etichetta').' : %s<br>'
								._('Lead Time').' : %s<br>'
								._('Latitudine').' : %s<br>'
								._('Longitudine').' : %s<br>'
								._('Raggio').' : %s<br>',$label,$lead_time,$lat,$lng,$radius);
						$this->flash->success ( $message );
					}
				} catch ( \Exception $e ) {
					$this->flash->error ( $e->getMessage () );
				}
			}
		}
	}
}