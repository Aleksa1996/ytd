<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class YoutubeVideo extends Model
{

    public function converts()
    {
        return $this->hasMany('App\YoutubeConvert', 'video_id');
    }

    public static function findByVideoId($videoId)
    {
        return self::where('videoId', $videoId)->first();
    }

    public function areAllRequestsFinished()
    {
        return $this->converts()->where('status', 'finished')->count() == $this->number_of_requests;
    }

    public static function getFinishedVideos($specificVideos)
    {
        return self::whereHas('converts', function (Builder $query) {
            $query->whereRaw('TIMESTAMPDIFF(MINUTE, updated_at, ?) >= 4 ', [Carbon::now()])
                ->where('status', 'finished')
                ->groupBy('video_id')
                ->havingRaw('COUNT(video_id) = `youtube_videos`.`number_of_requests`')->select('video_id');
        })
            ->whereIn('videoId', $specificVideos)
            ->get('videoId');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('number_of_requests', 'desc');
    }
}
