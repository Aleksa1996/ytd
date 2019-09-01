<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubeConvert extends Model
{

    public function video()
    {
        return $this->belongsTo('App\YoutubeVideo', 'video_id');
    }
}
