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
interface ClientInterface {
	/**
	 * Set the base uri to perform the request.
	 * @param string $uri : the base uri to use like a prefix.
	 */
	public function setBaseUri($uri);
	/**
	 * Return the base uri.
	 * @return string : the base uri
	 */
	public function getBaseUri();
	/**
	 * Set the default options for all the requests.
	 * @param array $options : the options to send.
	 */
	public function setDefaultOptions(array $options);
	/**
	 * Return the default options.
	 * @return array : the default options.
	 */
	public function getDefaultOptions();
	/**
	 * Send the request to server, passing url and an optional data.
	 * @param string $url : the url of the request.
	 * @param string $method : a rest method to use [GET,POST,PUT,DELETE]
	 * @param array $data : the data to send [optional]
	 * @param array $options : the options to send [optional]
	 */
	public function sendRequest($url,$method,array $data,array $options);
	/**
	 * Alias for {@link ClientInterface::sendRequest}
	 * @param string $url : the url of the request.
	 * @param array $data : the data to send [optional]
	 * @param array $options : the options to send [optional]
	 */
	public function sendGET($url,array $data,array $options);
	/**
	 * Send a POST request to server.
	 * @param string $url : the url of the request.
	 * @param array $data : the data to send [optional]
	 * @param array $options : the options to send [optional]
	 */
	public function sendPOST($url,array $data,array $options);
	/**
	 * Send a PUT request to server.
	 * @param string $url : the url of the request.
	 * @param array $data : the data to send [optional]
	 * @param array $options : the options to send [optional]
	 */
	public function sendPUT($url,array $data,array $options);
	/**
	 * Send a DELETE request to server.
	 * @param string $url : the url of the request.
	 * @param array $data : the data to send [optional]
	 * @param array $options : the options to send [optional]
	 */
	public function sendDELETE($url,array $data,array $options);
	/**
	 * Return the last response of the server
	 * @return mixed : Return the server response or false on failure.
	 */
	public function getLastResponse();
	/**
	 * Return the error number of the failed request.
	 */
	public function getErrorNumber();
	/**
	 * Return the error message of the failed request.
	 */
	public function getErrorMessage();
}