<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Image params
        $og_image_params = [
            'path'  => 'product',
            'image' => $this->avatar
        ];
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category_id,
            'avatar' => $this->getImageUrl($og_image_params),
        ];
    }

    /**
     * Get Image Url.
     *
     * @param array $param
     * @return string|null
     */
    public function getImageUrl($param)
    {
        $imagePath  = public_path() . '/storage/' . strtolower($param['path']) . '/' . $param['image'];
        $imageUrl   = URL::to('/') . '/storage/' . strtolower($param['path']) . '/' . $param['image'];

        // Check file exists in uploaded folder
        if (file_exists($imagePath)) {
            return $imageUrl;
        }
        return null;
    }
}
