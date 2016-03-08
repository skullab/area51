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
interface ServerInterface {
	/**
	 * Handle the request and call the controller and action to resolve it.
	 * @param string $url : Passing $url to handle [optional]
	 * @return mixed : The Controller->Action response or an Exception
	 */
	public function handle($url);
	/**
	 * Send the response to client
	 */
	public function sendResponse();
	/**
	 * Return the controller name to handle
	 * @return string
	 */
	public function getController();
	/**
	 * Return the action name of the controller to handle
	 * @return string
	 */
	public function getAction();
	/**
	 * Return the parameters passing by request
	 * @return array
	 */
	public function getParams();
	/**
	 * Return the options passing by rest client
	 * @return array
	 */
	public function getOptions();
	/**
	 * Return the method used to perform request
	 */
	public function getRequestMethod();
	/**
	 * Add an url pattern to resolve the request.
	 * The $handler parameter used to perform the request, 
	 * calling the controller and the action, passing the parameters.
	 * @param string $pattern : the url to resolve
	 * @param array $handler : the configuration handler to perform the request
	 */
	public function addPattern($pattern,array $handler);
	/**
	 * Compile the pattern and return a regular expression to match.
	 * @param string $pattern : the url to resolve
	 */
	public function compilePattern($pattern);
	/**
	 * Set an optional controller suffix
	 * @param string $suffix : the controller suffix
	 */
	public function setControllerSuffix($suffix);
	/**
	 * Set an optional action suffix
	 * @param string $suffix : the action suffix
	 */
	public function setActionSuffix($suffix);
	/**
	 * Return the controller suffix
	 */
	public function getControllerSuffix();
	/**
	 * Return the action suffix
	 */
	public function getActionSuffix();
}