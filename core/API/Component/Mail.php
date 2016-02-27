<?php

namespace Thunderhawk\API\Component;

use Thunderhawk\API\Component\Mail\MailInterface;
use Phalcon\Mvc\User\Component;
use Swift_SmtpTransport as Smtp;
use Swift_Mailer as Mailer;
use Swift_Message as Message;
use Swift_Attachment as Attachment;

class Mail extends Component implements MailInterface {
	protected $_from;
	protected $_to;
	protected $_subject;
	protected $_body;
	protected $_cc;
	protected $_attachment;
	//
	protected $_transport;
	protected $_mailer;
	protected $_message;
	protected $_failedRecipients;
	protected $_settings = array ();
	//
	public function __construct(array $settings = array(), $default = true) {
		$this->_settings = $settings;
		if ($default)
			$this->initialize ();
	}
	public function setOptions(array $settings) {
		$this->_settings = array_merge ( $this->_settings, $settings );
	}
	public function applyOptions() {
		foreach ( $this->_settings as $option => $value ) {
			switch ($option) {
				case 'host' :
					$this->setHost ( $value );
					break;
				case 'port' :
					$this->setPort ( $value );
					break;
				case 'encryption' :
					$this->setEncryption ( $value );
					break;
				case 'username' :
					$this->setUsername ( $value );
					break;
				case 'password' :
					$this->setPassword ( $value );
					break;
				case 'from' :
					$this->setFrom($value);
					break;
				case 'to' :
					$this->setTo($value);
					break;
				case 'subject':
					$this->setSubject($value);
					break;
			}
		}
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setHost()
	 */
	public function initialize() {
		if ($this->_transport == null) {
			$this->_transport = Smtp::newInstance ();
		}
		if ($this->_mailer == null) {
			$this->_mailer = Mailer::newInstance ( $this->_transport );
		}
		if ($this->_message == null) {
			$this->_message = Message::newInstance ();
		}
		$this->applyOptions ();
	}
	public function setHost($host) {
		if ($this->_transport != null)
			$this->_transport->setHost ( $host );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setPort()
	 */
	public function setPort($port) {
		if ($this->_transport != null)
			$this->_transport->setPort ( $port );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setEncryption()
	 */
	public function setEncryption($encryption) {
		if ($this->_transport != null)
			$this->_transport->setEncryption ( $encryption );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setUsername()
	 */
	public function setUsername($username) {
		if ($this->_transport != null)
			$this->_transport->setUsername ( $username );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setPassword()
	 */
	public function setPassword($password) {
		if ($this->_transport != null)
			$this->_transport->setPassword ( $password );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setTransport()
	 */
	public function setTransport(Swift_SmtpTransport $transport) {
		$this->_transport = $transport;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setMailer()
	 */
	public function setMailer(Swift_Mailer $mailer) {
		$this->_mailer = $mailer;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::setMessage()
	 */
	public function setMessage(Swift_Message $message) {
		$this->_message = $message;
	}
	public function setFrom($from) {
		if (! is_array ( $from ))
			$from = [ 
					$from 
			];
		if ($this->_message != null)
			$this->_message->setFrom ( $from );
	}
	public function setTo($to) {
		if (! is_array ( $to ))
			$to = [ 
					$to 
			];
		if ($this->_message != null)
			$this->_message->setTo ( $to );
	}
	public function setSubject($subject) {
		if ($this->_message != null)
			$this->_message->setSubject ( $subject );
	}
	public function setBody($body, $type = 'text/html') {
		if ($this->_message != null)
			$this->_message->setBody ( $body, $type );
	}
	public function setAttachment($attachment, $filename = null, $contentType = null, $rawData = false) {
		if ($attachment instanceof Attachment) {
			$this->_attachment = $attachment;
		} else {
			if (! $rawData) {
				$this->_attachment = Attachment::fromPath ( $attachment );
			} else {
				$this->_attachment = Attachment::newInstance ();
				$this->_attachment->setBody ( $attachment );
			}
			if ($contentType != null)
				$this->_attachment->setContentType ( $contentType );
			if ($filename != null)
				$this->_attachment->setFilename ( $filename );
		}
		if ($this->_message != null)
			$this->_message->attach ( $this->_attachment );
	}
	public function setCc($cc) {
		if (! is_array ( $cc ))
			$cc = [ 
					$cc 
			];
		if ($this->_message != null)
			$this->_message->setCc ( $cc );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::send()
	 */
	public function send() {
		if ($this->_message == null)
			throw new Mail\Exception ( 'The message is empty' );
		return $this->_mailer->send ( $this->_message, $this->_failedRecipients );
	}
	public function getFailedRecipients() {
		return $this->_failedRecipients;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \API\Component\Mail\MailInterface::sendRaw()
	 */
	public function sendRaw($from, $to, $subject, $body, $cc = null, $attachment = null) {
		if (! is_array ( $from )) {
			$from = [ 
					$from 
			];
		}
		if (! is_array ( $to )) {
			$to = [ 
					$to 
			];
		}
		$message = new Message ();
		$message->setSubject ( $subject );
		$message->setFrom ( $from );
		$message->setTo ( $to );
		$message->setBody ( $body, 'text/html' );
		if ($cc != null)
			$message->setCc ( $cc );
		if ($attachment instanceof Attachment)
			$message->attach ( $attachment );
		
		return $this->_mailer->send ( $message, $this->_failedRecipients );
	}
}