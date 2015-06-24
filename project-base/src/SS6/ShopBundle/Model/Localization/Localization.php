<?php

namespace SS6\ShopBundle\Model\Localization;

use SS6\ShopBundle\Model\Domain\Domain;

class Localization {

	const DEFAULT_COLLATION = 'en_US';

	/**
	 * @var string[locale]
	 */
	private $languageNamesByLocale = [
		'cs' => 'Čeština',
		'de' => 'Deutsch',
		'en' => 'English',
		'hu' => 'Magyar',
		'pl' => 'Polski',
		'sk' => 'Slovenčina',
	];

	/**
	 * @var string[locale]
	 */
	private $collationsByLocale = [
		'cs' => 'cs_CZ',
		'de' => 'de_DE',
		'en' => 'en_US',
		'hu' => 'hu_HU',
		'pl' => 'pl_PL',
		'sk' => 'sk_SK',
	];

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var array
	 */
	private $allLocales;

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->domain->getLocale();
	}

	/**
	 * @return string
	 */
	public function getDefaultLocale() {
		return $this->domain->getDomainConfigById(1)->getLocale();
	}

	/**
	 * @return array
	 */
	public function getAllLocales() {
		if ($this->allLocales === null) {
			$this->allLocales = [];
			foreach ($this->domain->getAll() as $domainConfig) {
				$this->allLocales[$domainConfig->getLocale()] = $domainConfig->getLocale();
			}
		}

		return $this->allLocales;
	}

	/**
	 * @param string $locale
	 * @return string
	 */
	public function getLanguageName($locale) {
		if (!array_key_exists($locale, $this->languageNamesByLocale)) {
			throw new \SS6\ShopBundle\Model\Localization\Exception\InvalidLocaleException(
				sprintf('Locale "%s" is not valid', $locale)
			);
		}

		return $this->languageNamesByLocale[$locale];
	}

	/**
	 * @param string $locale
	 * @return string
	 */
	public function getCollationByLocale($locale) {
		if (array_key_exists($locale, $this->collationsByLocale)) {
			return $this->collationsByLocale[$locale];
		} else {
			return self::DEFAULT_COLLATION;
		}
	}

}
