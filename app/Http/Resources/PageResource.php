<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return[
        'title'=>$this->title,
        'title_fr'=>$this->title_fr,
        'title_ar'=>$this->title_ar,
        'sub_title'=>$this->sub_title,
        // 'slug'=>$this->slug,
        'content'=>$this->content,
        'content_fr'=>$this->content_fr,
        'content_ar'=>$this->content_ar,
        // 'language'=>$this->language,
       ];
    }
}
