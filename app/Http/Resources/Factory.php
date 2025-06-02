<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Factory extends JsonResource
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
            'version' => $this->version,
            'nft_type' => $this->nft_type,
            'chainId' => $this->chainId,
            'foundry' => $this->foundry,
            'contract' => $this->contract,
            'lock' => $this->lock,
            'lock_abi' => $this->lock_abi,
            'factory_abi' => $this->factory_abi,
            'launchpads_count' => $this->launchpads_count,
            'abi' => $this->abi,
            'busy' => false,
            'active' => $this->active,
            'launchpads' => Launchpad::collection($this->whenLoaded('launchpads')),
        ];
    }
}
