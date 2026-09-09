<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function query(): Builder;

    public function find(int $id): Customer;

    public function findByConsumerId(string $consumerId): ?Customer;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): bool;

    public function search(string $term, int $perPage = 25): LengthAwarePaginator;

    public function activeForRoute(int $routeId): Collection;
}
