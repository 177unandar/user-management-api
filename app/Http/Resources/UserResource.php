<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'active' => $this->active,
            'created_at' => $this->created_at->toIso8601String(),
            'orders_count' => $this->whenCounted('orders', $this->orders_count),
            'can_edit' => $this->canBeEditedBy($request->user()),
        ];
    }

    protected function canBeEditedBy($user): bool
    {
        if (! $user) {
            return false;
        }

        // User can always edit themselves
        if ($this->id === $user->id) {
            return true;
        }

        // Admin can edit any user
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can only edit regular users
        if ($user->isManager()) {
            return $this->isRegularUser();
        }

        return false;
    }
}
