<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiTool extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'api_id' => $this->api_id,
            'version' => $this->version,
            'method' => $this->method,
            'add_user_to_request' => $this->add_user_to_request,
            'path' => $this->path,
            'query_params' => $this->query_params,
            'tool_config' => $this->tool_config,
            'output_transformer' => $this->output_transformer,
            'strict' => $this->strict,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'api' => new Api($this->whenLoaded('api')),
            'user' => new User($this->whenLoaded('user')),
            'bots' => Bot::collection($this->whenLoaded('bots')),
            'commands' => Command::collection($this->whenLoaded('commands')),
        ];
    }
}
