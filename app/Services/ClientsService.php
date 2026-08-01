<?php

namespace App\Services;

use App\Repositories\ClientsRepository;

class ClientsService
{
    protected ClientsRepository $repository;
    public function __construct(ClientsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createClient(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getClients(int $limit,  int $companyId, string $search = '')
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedClients($limit, $companyId, $search);
        }
        return $this->repository->getClients($limit, $companyId);
    }
    //---------------
    public function getAllClients(int $companyId)
    {
        return $this->repository->getAllClients($companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkClientExist($id, $companyId);
    }
    //---------------
    public function getClientById(int $id, int $companyId)
    {
        return $this->repository->findClient($id, $companyId);
    }
    //---------------
    public function updateClient(int $id, array $data, int $companyId   ): bool
    {
        $client = $this->repository->findClient($id, $companyId);
        if (!$client) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteClient(int $id, int $companyId): bool
    {
        $client = $this->repository->findClient($id, $companyId);
        if (!$client) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function getClientStatistics(int $companyId): object
    {
        return $this->repository->getClientStatistics($companyId);
    }
}
