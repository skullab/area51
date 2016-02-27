<?php

namespace Thunderhawk\API\Component\Translator;

interface TranslatorInterface {
	public function setEnv($var);
	public function setLocalePath($path);
	public function getLocalePath();
	public function getDefaultLocale();
	public function setDomain($domain);
	public function getActiveDomain();
	public function bindDomains($domains,$encoding,$path);
	public function bindCodeset($domain,$encoding);
	public function setDefaultLocale($locale);
	public function setLocale($locale);
	public function getLocale();
	public function createLocale($locale);
	public function importPOFile($filename,$locale);
	public function createMOFIle($filename,$locale);
	public function createPOFile($filename,$locale);
	public function localeExists($locale);
	public function poFileToArray($filename,$locale);
}