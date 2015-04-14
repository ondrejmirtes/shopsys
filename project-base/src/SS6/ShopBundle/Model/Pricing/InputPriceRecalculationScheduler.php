<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class InputPriceRecalculationScheduler {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceRecalculator
	 */
	private $inputPriceRecalculator;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var boolean
	 */
	private $recalculateInputPricesWithoutVat;

	/**
	 * @var boolean
	 */
	private $recalculateInputPricesWithVat;

	public function __construct(InputPriceRecalculator $inputPriceRecalculator, Setting $setting) {
		$this->inputPriceRecalculator = $inputPriceRecalculator;
		$this->setting = $setting;
	}

	public function scheduleSetInputPricesWithoutVat() {
		$this->recalculateInputPricesWithoutVat = true;
	}

	public function scheduleSetInputPricesWithVat() {
		$this->recalculateInputPricesWithVat = true;
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if ($this->recalculateInputPricesWithoutVat) {
			$this->inputPriceRecalculator->recalculateToInputPricesWithoutVat();
			$this->setting->set(
				PricingSetting::INPUT_PRICE_TYPE,
				PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
				SettingValue::DOMAIN_ID_COMMON
			);
		} elseif ($this->recalculateInputPricesWithVat) {
			$this->inputPriceRecalculator->recalculateToInputPricesWithVat();
			$this->setting->set(
				PricingSetting::INPUT_PRICE_TYPE,
				PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
				SettingValue::DOMAIN_ID_COMMON
			);
		}
	}

}