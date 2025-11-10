<?php

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserQuery
{
    private Builder $query;

    private array $allowedSorts = [
        'name',
        'email',
        'created_at',
    ];

    public function __construct()
    {
        $this->query = User::query()
            ->select('users.*')
            ->withCount('orders')
            ->active();
    }

    public function applySearch(?string $search): self
    {
        if ($search) {
            $this->query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $this;
    }

    public function applySort(?string $sortBy, ?string $sortDirection = 'asc'): self
    {
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, $this->allowedSorts)) {
            $this->query->orderBy($sortBy, $sortDirection);
        } else {
            // Default sort
            $this->query->latest('created_at');
        }

        return $this;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }

    public function get(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query->get();
    }

    public static function fromRequest(Request $request): self
    {
        return (new static)
            ->applySearch($request->query('search'))
            ->applySort(
                $request->query('sort_by'),
                $request->query('sort_direction', 'asc')
            );
    }
}
