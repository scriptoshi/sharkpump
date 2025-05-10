<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class Api extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'url' => $this->url,
            'website' => $this->website,
            'content_type' => $this->content_type,
            'auth_type' => $this->auth_type,
            'auth_username' => $this->auth_username,
            'auth_password' => $this->auth_password,
            'auth_token' => $this->auth_token,
            'auth_query_key' => $this->auth_query_key,
            'auth_query_value' => $this->auth_query_value,
            'active' => $this->active,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tools' => ApiTool::collection($this->whenLoaded('tools')),
        ];
    }
}
