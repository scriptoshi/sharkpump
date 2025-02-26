<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Launchpad extends JsonResource
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
            'user_id' => $this->user_id,
            'factory_id' => $this->factory_id,
            'contract' => $this->contract,
            'token' => $this->token,
            'promoted' => $this->promoted ?? false,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'description' => $this->description,
            'chainId' => (int) $this->chainId,
            'twitter' => $this->twitter,
            'discord' => $this->discord,
            'telegram' => $this->telegram,
            'website' => $this->website,
            'livestreamId' => $this->livestreamId,
            'status' => $this->status,
            'logo' => $this->logo,
            'featured' => $this->featured,
            'kingofthehill' => $this->kingofthehill,
            'active' => $this->active,
            'volume24h' => $this->volume24h ?? '0.00',
            'age' => $this->created_at->diffForHumans(),
            'trades_count' => $this->trades_count,
            'makers' => $this->makers ?? 0,
            'holders_count' => $this->holders_count,
            // to be pulled
            'percentage' => 0,
            'marketCap' => 0,
            'isFinalized' => false,
            'isOwner' => $this->user_id === $request->user()?->id,
            'createdAgo' => $this->created_at->diffForHumans(),
            'age' => str($this->created_at->diffForHumans())
                ->replace('ago', '')
                ->replace('  ', '')
                ->replace(
                    ['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'],
                    ['y', 'm', 'w', 'd', 'h', 'm', 's']
                ),
            'latest_price' => $this->latest_price,
            't5m' => $this->price_5m ?? 0,
            't1h' => $this->price_1h ?? 0,
            't6h' => $this->price_6h ?? 0,
            't24h' => $this->price_24h ?? 0,
            'factory' => new Factory($this->whenLoaded('factory')),
            'user' => new ViewUser($this->whenLoaded('user')),
            'trades' => Trade::collection($this->whenLoaded('trades')),
            'msgs' => Msg::collection($this->whenLoaded('msgs')),
            'uploads' => Upload::collection($this->whenLoaded('uploads')),

        ];
    }
}
