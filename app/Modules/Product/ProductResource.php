<?php

namespace App\Modules\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Category\CategoryResource;
use App\Modules\Auth\AuthResource;
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'image' => $this->image,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'creator' => new AuthResource($this->whenLoaded('creator')),
            'creatorCount' => $this->whenCounted('creator'),
            'updaterCount' => new AuthResource($this->whenCounted('updater'))
        ];
    }
}
