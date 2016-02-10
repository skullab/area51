<?php

namespace Thunderhawk\API\Engine;

class Listener {
	public function boot($event,$engine){}
	public function beforeSendResponse($event,$engine){}
	public function beforeHandleRequest($event,$engine){}
	public function beforeStartModule($event,$engine){}
	public function moduleNotFound($event,$engine){}
	public function afterStartModule($event,$engine){}
	public function afterHandleRequest($event,$engine){}
	public function viewRender($event,$engine){}
}