<?php

namespace App\Services;

use App\Repositories\OffersRepository;

class OffersService
{
    protected int $companyId;
	protected OffersRepository $repository;
	public function __construct(OffersRepository $repository)
	{
		$this->repository = $repository;
        $this->companyId = session('company_id');
	}
	//---------------
	public function createOffer(array $data): bool
	{
		return $this->repository->create($data);
	}
	//---------------
	public function getOffers(int $limit, string $search = '')
	{
		$limit = (int) $limit ?: 10;
		if (!empty($search)) {
			return $this->repository->getSearchedOffers($limit, $search, $this->companyId);
		}
		return $this->repository->getOffers($limit, $this->companyId);
	}
	//---------------
	public function offerNumberExists(string $offerNumber): bool
	{
		return $this->repository->checkOfferNumberExists($offerNumber, $this->companyId);
	}
	//---------------
	public function getOfferRowsByNumber(string $offerNumber)
	{
		return $this->repository->getOfferRowsByNumber($offerNumber, $this->companyId);
	}
	//---------------
	public function getOfferRowById(int $id)
	{
		return $this->repository->getOfferRowById($id, $this->companyId);
	}
	//---------------
	public function updateOfferByNumber(string $offerNumber, array $data): bool
	{
		return $this->repository->updateOfferByNumber($offerNumber, $data, $this->companyId);
	}
	//---------------
	public function deleteOfferRowById(int $id): bool
	{
		return $this->repository->deleteOfferRowById($id, $this->companyId);
	}
	//---------------
	public function deleteOfferByNumber(string $offerNumber): bool
	{
		return $this->repository->deleteOfferByNumber($offerNumber, $this->companyId);
	}
}
