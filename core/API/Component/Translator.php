<?php

namespace Thunderhawk\API\Component;

use Phalcon\Mvc\User\Component;
use Thunderhawk\API\Component\Translator\TranslatorInterface;
use Thunderhawk\API\Mvc\Model\Lang\Languages;
use Thunderhawk\API\Mvc\Model\Lang\Translations;
use Phalcon\Db;
use Thunderhawk\API\Component\Translator\Parser;

class Translator extends Component implements TranslatorInterface {
	protected $_localePath;
	protected $_domains = array ();
	protected $_activeDomain ;
	protected $_locale;
	
	public function setEnv($var) {
		putenv ( $var );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::setLocalePath()
	 */
	public function setLocalePath($path) {
		$this->_localePath = $path;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::getLocalePath()
	 */
	public function getLocalePath() {
		return $this->_localePath;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::getDefaultLocale()
	 */
	public function getDefaultLocale() {
		return \Locale::getDefault ();
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::setDomainName()
	 */
	public function setDomain($domain) {
		if(in_array($domain, $this->_domains)){
			$this->_activeDomain = $domain ;
			textdomain($this->_activeDomain);
		}
	}
	public function getActiveDomain() {
		return $this->_activeDomain;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::bindDomain()
	 */
	public function bindDomains($domains, $encoding = null, $path = null) {
		if (! is_array ( $domains )) {
			$domains = [ 
					$domains 
			];
		}
		$this->_domains = array_merge ( $this->_domains, $domains );
		$path = $path ? $path : $this->_localePath;
		foreach ( $domains as $domain ) {
			bindtextdomain ( $domain, $path );
			if ($encoding) {
				$this->bindCodeset ( $domain, $encoding );
			}
		}
	}
	public function bindCodeset($domain, $encoding) {
		bind_textdomain_codeset ( $domain, $encoding );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::setLocale()
	 */
	public function setLocale($locale) {
		$locale = \Locale::canonicalize ( $locale );
		if($this->localeExists($locale)){
			$this->setEnv("LANG=$locale");
			$this->_locale = setlocale ( LC_ALL, $locale );
			return $this->_locale ;
		}
		return false ;
	}
	public function getLocale() {
		return $this->_locale ? $this->_locale : $this->getDefaultLocale();
	}
	public function getLang(){
		return \Locale::getPrimaryLanguage($this->getLocale());
	}
	public function setDefaultLocale($locale) {
		$locale = \Locale::canonicalize ( $locale );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::createLocale()
	 */
	public function createLocale($locale) {
		$locale = \Locale::canonicalize ( $locale );
		$instance = new Languages ();
		$instance->locale = $locale;
		try {
			$instance->save ();
			return mkdir ( $this->getLocalePath () . "/$locale/LC_MESSAGES/", 0777, true );
		} catch ( \Exception $e ) {
			return false;
		}
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::importPOFile()
	 */
	public function importPOFile($filename, $locale) {
		$locale = \Locale::canonicalize ( $locale );
		$language = Languages::findFirstByLocale ( $locale );
		if ($language != false) {
			$path = $this->getLocalePath () . "/$locale/LC_MESSAGES/$filename";
			$handle = file ( $path );
			if ($handle === false)
				return false;
			$comment = null;
			foreach ( $handle as $line ) {
				if (substr ( $line, 0, 1 ) == '#') {
					$comment = trim ( str_replace ( '#', '', $line ) );
				}
				if (substr ( $line, 0, 5 ) == 'msgid') {
					$msgid = trim ( substr ( trim ( substr ( $line, 5 ) ), 1, - 1 ) );
				}
				if (substr ( $line, 0, 6 ) == 'msgstr') {
					$msgstr = trim ( substr ( trim ( substr ( $line, 6 ) ), 1, - 1 ) );
					$translations = new Translations ();
					$translations->comment = $comment;
					$translations->msgid = $msgid;
					$translations->msgstr = $msgstr;
					$translations->languages_id = $language->id;
					try {
						$translations->save ();
					} catch ( \Exception $e ) {
					}
				}
			}
		}
		return true;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::createMOFIle()
	 */
	public function createMOFIle($filename, $locale) {
		$locale = \Locale::canonicalize ( $locale );
		if ($this->localeExists ( $locale )) {
			$input = $this->getLocalePath () . "/$locale/LC_MESSAGES/$filename";
			$parser = new Parser ();
			return $parser->phpmo_convert ( $input );
		}
	}
	public function createPOFile($filename, $locale) {
		$locale = \Locale::canonicalize ( $locale );
		$language = Languages::findFirstByLocale ( $locale );
		if ($language) {
			$path = $this->getLocalePath () . "/$locale/LC_MESSAGES/$filename";
			$result = $this->db->fetchAll ( 'SELECT * FROM translations WHERE languages_id = :language_id', Db::FETCH_ASSOC, array (
					'language_id' => $language->id 
			) );
			$po = fopen ( $path, 'w' );
			if ($po === false)
				return false;
			foreach ( $result as $translations ) {
				if ($translations ['comment'] != null) {
					fwrite ( $po, '# ' . $translations ['comment'] . PHP_EOL );
				}
				fwrite ( $po, 'msgid "' . $translations ['msgid'] . '"' . PHP_EOL );
				fwrite ( $po, 'msgstr "' . $translations ['msgstr'] . '"' . PHP_EOL );
			}
			return fclose ( $po );
		}
	}
	public function poFileToArray($filename, $locale) {
		$locale = \Locale::canonicalize ( $locale );
		$path = $this->getLocalePath () . "/$locale/LC_MESSAGES/$filename";
		$translations = array ();
		$po = file ( $path );
		$current = null;
		if ($po === false)
			return false;
		foreach ( $po as $line ) {
			if (substr ( $line, 0, 5 ) == 'msgid') {
				$current = trim ( substr ( trim ( substr ( $line, 5 ) ), 1, - 1 ) );
			}
			if (substr ( $line, 0, 6 ) == 'msgstr') {
				$translations [$current] = trim ( substr ( trim ( substr ( $line, 6 ) ), 1, - 1 ) );
			}
		}
		return $translations;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Component\Translator\TranslatorInterface::localeExists()
	 */
	public function localeExists($locale) {
		$locale = \Locale::canonicalize ( $locale );
		return (Languages::findFirstByLocale ( $locale ) !== false);
	}
}