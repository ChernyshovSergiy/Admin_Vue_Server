<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Language extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'flag_country' => $this->flag_country,
            'code' => $this->code,
            'iso' => $this->iso,
            'file' => $this->file,
            'name' => $this->name,
        ];
    }
}
