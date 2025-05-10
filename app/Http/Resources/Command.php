<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Command extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'command' => $this->command,
            'system_prompt_override' => $this->system_prompt_override,
            'is_active' => $this->is_active,
            'credits_per_message' => $this->credits_per_message,
            'priority' => $this->priority,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bot' => new Bot($this->whenLoaded('bot')),
            'tools' => ApiTool::collection($this->whenLoaded('tools')),
        ];
    }
}
