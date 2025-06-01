<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ViewUser extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'joined' => $this->created_at->toDateTimeString(),
            'active' => $this->active,
            'banned' => $this->banned,
            //.git/
            'launchpads_count' => $this->launchpads_count,
            'trades_count' => $this->trades_count,
            'msgs_count' => $this->msgs_count,
            'holders_count' => $this->holders_count,
            // profile
            'profile_photo_url' => $this->profile_photo_url,
            'nfts' => Nft::collection($this->whenLoaded('nfts')),
        ];
    }
}
