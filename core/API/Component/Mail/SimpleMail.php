<?php

namespace Thunderhawk\API\Component\Mail;
use Phalcon\Mvc\User\Component;

class SimpleMail extends Component implements SimpleMailInterface{
	
	protected $_to;
	protected $_from;
	protected $_replyTo;
	protected $_subject;
	protected $_message;
	protected $_headers;
	protected $_failed;
	protected $_cc;
	protected $_attachment;
	protected $_smtp ;
	protected $_contentType;
	protected $_mime = '1.0';
	
	public function __construct(array $smtp_config = array()){
		$this->_smtp = $smtp_config ;
		if(!empty($this->_smtp)){
			if(isset($this->_smtp['from'])){
				$this->setFrom($this->_smtp['from']);
			}
		}
	}
	protected function prepareAttachment(){}
	
	protected function prepareHeaders(){
		$this->_headers = '' ;
		if($this->_mime){
			$this->_headers .= 'MIME-Version: '.$this->_mime . "\r\n" ;
		}
		if($this->_contentType){
			$this->_headers .= 'Content-type: '.$this->_contentType . "\r\n" ;
		}
		
		$this->_headers .= 'To: '.$this->preparerRecipients(). "\r\n";
		$this->_headers .= 'From: '.$this->_from . "\r\n" ;
		if($this->_replyTo){
			$this->_headers .= 'Reply-To: '.$this->_replyTo. "\r\n" ;
		}
		if(!empty($this->_cc)){
			$this->_headers .= 'Cc: '.$this->prepareCc(). "\r\n" ;
		}
	}
	protected function prepareSender(){}
	protected function preparerRecipients(){
		return implode(', ', $this->_to);
	}
	protected function prepareCc(){
		return implode(', ',$this->_cc);
	}
	
	public function setMimeVersion($mime){
		$this->_mime = (string)$mime;
	}
	public function setContentType($type){
		$this->_contentType = (string)$type;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::send()
	 */
	public function send() {
		$this->prepareHeaders();
		$this->prepareAttachment();
		$to = implode(', ',$this->_to);
		$this->_send($to, $this->_subject, $this->_message,$this->_headers);
	}

	protected function _send($to,$subject,$body,$headers = null){
		$response = mail($to, $subject, $body, $headers);
		$this->_failed = !$response ;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::sendRaw()
	 */
	public function sendRaw($to, $subject, $body, $headers = null) {
		$this->_send($to, $subject, $body,$headers);
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::getFailedRecipients()
	 */
	public function getFailedRecipients() {
		if($this->_failed)return $this->_to ;
		return array();
	}
	
	public function setReplyTo($reply){
		$this->_replyTo = str_replace(array("\r", "\n"), '', $reply); // to prevent email injection
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::setFrom()
	 */
	public function setFrom($from) {
		$this->_from = str_replace(array("\r", "\n"), '', $from); // to prevent email injection
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::setTo()
	 */
	public function setTo($to) {
		if(!is_array($to)){
			$to = array(str_replace(array("\r", "\n"), '', $to));
		}
		$this->_to = $to ;
	}
	public function addTo($to){
		$this->_to[] = (string)$to;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::setSubject()
	 */
	public function setSubject($subject) {
		$this->_subject = (string)$subject;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::setBody()
	 */
	public function setBody($body, $type = 'text/html; charset=iso-8859-1') {
		$this->_message = (string)$body;
		$this->setContentType($type);
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::setAttachment()
	 */
	public function setAttachment($file) {
		$this->_attachment = $file ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Mail\SimpleMailInterface::setCc()
	 */
	public function setCc($cc) {
		if(!is_array($cc)){
			$cc = array(str_replace(array("\r", "\n"), '', $cc));
		}
		$this->_cc = $cc ;
	}
	public function addCc($cc){
		$this->_cc = (string)$cc;
	}
}