<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class YoutubeVideo extends JsonResource
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
            'title' => $this->title,
            'videoId' => $this->videoId,
            'lengthSeconds' => $this->lengthSeconds,
            'thumbnail' => $this->thumbnail,
            'requested' => $this->number_of_requests,
            'lastRequest' => $this->updated_at->diffForHumans(),
            'status' => $this->areAllRequestsFinished() ? 'finished' : 'converting'
        ];
    }
}
