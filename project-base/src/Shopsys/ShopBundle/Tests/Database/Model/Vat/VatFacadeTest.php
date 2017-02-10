<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Vat;

use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PaymentDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\TransportDataFixture;
use Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Transport\TransportEditDataFactory;
use Shopsys\ShopBundle\Model\Transport\TransportEditFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class VatFacadeTest extends DatabaseTestCase {

	public function testDeleteByIdAndReplace() {
		$em = $this->getEntityManager();
		$vatFacade = $this->getContainer()->get(VatFacade::class);
		/* @var $vatFacade \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade */
		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \Shopsys\ShopBundle\Model\Transport\TransportEditFacade */
		$transportEditDataFactory = $this->getContainer()->get(TransportEditDataFactory::class);
		/* @var $transportEditDataFactory \Shopsys\ShopBundle\Model\Transport\TransportEditDataFactory */
		$paymentEditDataFactory = $this->getContainer()->get(PaymentEditDataFactory::class);
		/* @var $paymentEditDataFactory \Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade */

		$vatToDelete = $vatFacade->create(new VatData('name', 10));
		$vatToReplaceWith = $this->getReference(VatDataFixture::VAT_HIGH);
		/* @var $vatToReplaceWith \Shopsys\ShopBundle\Model\Pricing\Vat\Vat */
		$transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		/* @var $transport \Shopsys\ShopBundle\Model\Transport\Transport */
		$transportEditData = $transportEditDataFactory->createFromTransport($transport);
		$payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		/* @var $payment \Shopsys\ShopBundle\Model\Payment\Payment */
		$paymentEditData = $paymentEditDataFactory->createFromPayment($payment);

		$transportEditData->transportData->vat = $vatToDelete;
		$transportEditFacade->edit($transport, $transportEditData);

		$paymentEditData->paymentData->vat = $vatToDelete;
		$paymentEditFacade->edit($payment, $paymentEditData);

		$vatFacade->deleteById($vatToDelete, $vatToReplaceWith);

		$em->refresh($transport);
		$em->refresh($payment);

		$this->assertEquals($vatToReplaceWith, $transport->getVat());
		$this->assertEquals($vatToReplaceWith, $payment->getVat());
	}
}