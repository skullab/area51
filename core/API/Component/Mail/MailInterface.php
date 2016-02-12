<?php

namespace Thunderhawk\API\Component\Mail;

interface MailInterface {
	public function setHost($host);
	public function setPort($port);
	public function setEncryption($encryption);
	public function setUsername($username);
	public function setPassword($password);
	public function setTransport(\Swift_SmtpTransport $transport);
	public function setMailer(\Swift_Mailer $mailer);
	public function setMessage(\Swift_Message $message);
	public function send();
	public function sendRaw($from,$to,$subject,$body,$cc,$attachment);
	public function setOptions(array $settings);
	public function getFailedRecipients();
	public function setFrom($from);
	public function setTo($to);
	public function setSubject($subject);
	public function setBody($body,$type);
	public function setAttachment($attachment,$filename,$contentType,$rawData);
	public function setCc($cc);
}