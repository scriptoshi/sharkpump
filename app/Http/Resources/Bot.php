<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class Bot extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'description' => $this->description,
            'logo' => $this->logo,
            'bot_token' => $this->bot_token,
            'bot_provider' => $this->bot_provider,
            'ai_model' => $this->ai_model,
            'api_key' => $this->api_key,
            'system_prompt' => $this->system_prompt,
            'is_active' => $this->is_active,
            'is_cloneable' => $this->is_cloneable,
            'settings' => $this->settings,
            'last_active_at' => $this->last_active_at,
            'ai_temperature' => $this->ai_temperature,
            'ai_max_tokens' => $this->ai_max_tokens,
            'ai_store' => $this->ai_store,
            'credits_per_message' => (int) $this->credits_per_message ?? 0,
            'credits_per_star' => (int) $this->credits_per_star ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'commands' => Command::collection($this->whenLoaded('commands')),
            'tools' => ApiTool::collection($this->whenLoaded('tools')),
        ];
    }
}
