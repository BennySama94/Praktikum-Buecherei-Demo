<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status,
            'due_date'    => $this->due_date,
            'returned_at' => $this->returned_at,
            'borrowed_at' => $this->created_at,
            'book'        => new BookResource($this->whenLoaded('book')),
            'user'        => new UserResource($this->whenLoaded('user')),
        ];
    }
}
