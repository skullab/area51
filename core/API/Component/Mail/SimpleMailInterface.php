<?php

namespace Thunderhawk\API\Component\Mail;

interface SimpleMailInterface {
	public function send();
	public function sendRaw($to,$subject,$body,$headers);
	public function getFailedRecipients();
	public function setFrom($from);
	public function setTo($to);
	public function setSubject($subject);
	public function setBody($body,$type);
	public function setAttachment($file);
	public function setCc($cc);
}