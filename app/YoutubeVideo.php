<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{

    public static function findByVideoId($videoId)
    {
        return self::where('videoId', $videoId)->first();
    }
}
