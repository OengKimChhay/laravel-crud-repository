<?php

namespace App\Modules\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Auth\AuthResource;
use App\Modules\Product\ProductResource;
class CategoryResource extends JsonResource
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
            'creator' => new AuthResource($this->whenLoaded('creator')),
            'products' => new ProductResource($this->whenLoaded('products')),
            'productCount' => $this->whenCounted('products')
        ];
    }
}
