<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Product\Flag\Flag;
use SS6\ShopBundle\Model\Product\ProductRepository;

class FlagFilterChoiceRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(
		ProductRepository $productRepository
	) {
		$this->productRepository = $productRepository;
	}

	/**
	 * @param type $domainId
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public function getFlagFilterChoicesInCategory($domainId, Category $category) {
		$productsQueryBuilder = $this->productRepository->getVisibleByDomainIdAndCategoryQueryBuilder($domainId, $category);

		return $this->getFlagsByProductsQueryBuilder($productsQueryBuilder);
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param string|null $searchText
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public function getFlagFilterChoicesForSearch($domainId, $locale, $searchText) {
		$productsQueryBuilder = $this->productRepository
			->getVisibleByDomainIdAndSearchTextQueryBuilder($domainId, $locale, $searchText);

		return $this->getFlagsByProductsQueryBuilder($productsQueryBuilder);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	private function getFlagsByProductsQueryBuilder(QueryBuilder $productsQueryBuilder) {
		$clonnedProductsQueryBuilder = clone $productsQueryBuilder;

		$clonnedProductsQueryBuilder
			->select('1')
			->join('p.flags', 'pf', Join::WITH, 'pf.id = f.id');

		$flagsQueryBuilder = $productsQueryBuilder->getEntityManager()->createQueryBuilder();
		$flagsQueryBuilder
			->select('f')
			->from(Flag::class, 'f')
			->andWhere($flagsQueryBuilder->expr()->exists($clonnedProductsQueryBuilder));

		foreach ($clonnedProductsQueryBuilder->getParameters() as $parameter) {
			$flagsQueryBuilder->setParameter($parameter->getName(), $parameter->getValue());
		}

		return $flagsQueryBuilder->getQuery()->execute();
	}

}