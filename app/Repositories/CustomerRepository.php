<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(protected Customer $model)
    {
    }

    public function query(): Builder
    {
        return $this->model->newQuery()->with(['village', 'area', 'route', 'category']);
    }

    public function find(int $id): Customer
    {
        return $this->query()->findOrFail($id);
    }

    public function findByConsumerId(string $consumerId): ?Customer
    {
        return $this->model->where('consumer_id', $consumerId)->first();
    }

    public function create(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh();
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    public function search(string $term, int $perPage = 25): LengthAwarePaginator
    {
        return $this->query()->search($term)->latest()->paginate($perPage);
    }

    public function activeForRoute(int $routeId): Collection
    {
        return $this->model->active()->where('route_id', $routeId)->orderBy('name')->get();
    }
}
