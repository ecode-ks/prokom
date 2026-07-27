<?php

namespace App\Services;

use App\Repositories\ContractsRepository;
use Illuminate\Support\Facades\Storage;

class ContractsService
{
    protected ContractsRepository $repository;

    public function __construct(ContractsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createContract(array $data): bool
    {
        if (!empty($data['docs'])) {
            $data['docs'] = $this->storeDocs($data['docs']);
        }

        return $this->repository->create($data);
    }
    //---------------
    public function getContracts(int $limit, string $search, string $status, int $companyId)
    {
        $limit = (int) $limit ?: 10;
        $this->repository->updateContractsStatus($companyId);

        if (!empty($search) || !empty($status)) {
            return $this->repository->getSearchedContracts($limit, $companyId, $search, $status);
        }

        return $this->repository->getContracts($limit, $companyId);
    }
    //---------------
    public function getContractStatistics(string $search, int $companyId): object
    {
        $this->repository->updateContractsStatus($companyId);

        return $this->repository->getContractStatistics($companyId, $search);
    }
    //---------------
    public function getContractDetails(int $id, int $companyId)
    {
        return $this->repository->getContractDetails($id, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkContractExist($id, $companyId);
    }
    //---------------
    public function getContractById(int $id, int $companyId)
    {
        return $this->repository->findContract($id, $companyId);
    }
    //---------------
    public function updateContract(int $id, array $data, int $companyId): bool
    {
        $contract = $this->repository->findContract($id, $companyId);
        if (!$contract) {
            return false;
        }

        if (!empty($data['docs'])) {
            if (!empty($contract->docs)) {
                $oldFiles = json_decode($contract->docs, true);

                if (is_array($oldFiles)) {
                    Storage::disk('public')->delete($oldFiles);
                }
            }

            $data['docs'] = $this->storeDocs($data['docs']);
        } else {
            unset($data['docs']);
        }

        $data['status'] = 'Active';

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteContract(int $id, int $companyId): bool
    {
        $contract = $this->repository->findContract($id, $companyId);
        if (!$contract) {
            return false;
        }

        if (!empty($contract->docs)) {
            $files = json_decode($contract->docs, true);

            if (is_array($files)) {
                Storage::disk('public')->delete($files);
            }
        }

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    private function storeDocs(array $docs): string
    {
        $paths = [];

        foreach ($docs as $doc) {
            $fileName = pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME)
                . '_' . time() . '_' . uniqid()
                . '.' . $doc->getClientOriginalExtension();

            $paths[] = $doc->storeAs('contracts', $fileName, 'public');
        }

        return json_encode($paths);
    }
}
