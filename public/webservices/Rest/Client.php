<?php
/**
 * Copyright (c) 2016 ivan.maruca@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Skullab\Rest;
require 'ClientInterface.php';
class Client implements ClientInterface{
	//
	const METHOD_GET 	= 'GET';
	const METHOD_POST 	= 'POST';
	const METHOD_PUT 	= 'PUT';
	const METHOD_DELETE = 'DELETE';
	//
 	protected $_baseUri;
	protected $_options = array();
	protected $_response ;
	protected $_lastErrNo;
	protected $_lastErrMsg;	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::setBaseUri()
	 */
	public function setBaseUri($uri) {
		$this->_baseUri = $uri ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::getBaseUri()
	 */
	public function getBaseUri() {
		return $this->_baseUri;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::setDefaultOptions()
	 */
	public function setDefaultOptions(array $options) {
		$this->_options = $options;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::getDefaultOptions()
	 */
	public function getDefaultOptions() {
		return $this->_options ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::sendRequest()
	 */
	public function sendRequest($url, $method, array $data = null , array $options = null) {
		$ch = curl_init();
		switch ($method){
			case self::METHOD_POST:
				curl_setopt($ch, CURLOPT_POST, true);
				if($data){
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				}
				break;
			case self::METHOD_PUT:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST,self::METHOD_PUT);
				if($data){
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				}
				break;
			case self::METHOD_DELETE:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST,self::METHOD_DELETE);
				if($data){
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				}
				break;
			default:
		}
		$options = $options != null ? '?options='.json_encode($options) : '' ;
		curl_setopt($ch, CURLOPT_URL, $this->_baseUri.$url.$options);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$this->_response = curl_exec($ch);
		if($this->_response === false){
			$this->_lastErrNo = curl_errno($ch);
			$this->_lastErrMsg = curl_error($ch);
		}
		curl_close($ch);
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::sendGET()
	 */
	public function sendGET($url, array $data = null, array $options = null) {
		$this->sendRequest($url, self::METHOD_GET,$data,$options);
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::sendPOST()
	 */
	public function sendPOST($url, array $data, array $options) {
		// TODO: Auto-generated method stub

	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::sendPUT()
	 */
	public function sendPUT($url, array $data, array $options) {
		// TODO: Auto-generated method stub

	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::sendDELETE()
	 */
	public function sendDELETE($url, array $data, array $options) {
		// TODO: Auto-generated method stub

	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::getLastResponse()
	 */
	public function getLastResponse() {
		return $this->_response ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::getErrorNumber()
	 */
	public function getErrorNumber() {
		return $this->_lastErrNo;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ClientInterface::getErrorMessage()
	 */
	public function getErrorMessage() {
		return $this->_lastErrMsg;
	}

}