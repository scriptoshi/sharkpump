<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Nft extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'chainId' => (int) $this->chainId,
            'contract' => $this->contract,
            'abi' => $this->abi,
            'metadata' => $this->metadata,
            'active' => $this->active,
            'busy' => false,
            'type' => $this->type,
            'image' => $this->image,

        ];
    }
}
