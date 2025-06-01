<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'email' => config('app.demo') ? "** hidden in demo **" : $this->email,
            'address' => $this->address,
            'email_verified_at' => $this->email_verified_at,
            'verified' => !!$this->email_verified_at,
            'canVerify' => !$this->email_verified_at,
            'joined' => $this->created_at->toDateTimeString(),
            'joinedAgo' => $this->created_at->diffForHumans(),
            'active' => $this->active,
            'banned' => $this->banned,
            //.git/
            'launchpads_count' => $this->launchpads_count,
            'trades_count' => $this->trades_count,
            'msgs_count' => $this->msgs_count,
            'holders_count' => $this->holders_count,
            // profile
            'profile_photo_path' => $this->profile_photo_path,
            'profile_photo_url' => $this->profile_photo_url,
            'is_admin' => $this->isAdmin(),
            'nfts' => Nft::collection($this->whenLoaded('nfts')),
        ];
    }
}
