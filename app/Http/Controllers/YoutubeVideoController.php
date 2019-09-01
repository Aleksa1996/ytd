<?php

namespace App\Http\Controllers;

use App\YoutubeVideo;
use App\YoutubeConvert;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Jobs\ProcessYoutubeVideo;

use App\Classes\YoutubeVideoUtils;
use Illuminate\Support\Facades\Log;
use App\Exceptions\GeneralException;
use App\Http\Resources\YoutubeVideo as YoutubeVideoResource;

class YoutubeVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = YoutubeVideo::popular()
            ->limit(4)
            ->get();

        return YoutubeVideoResource::collection($videos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate request
        $data = $request->validate([
            'video_id' => 'required|string', // Youtube video ID
            'video_format' => [Rule::in([ // The MIME type of the video. e.g. video/mp4, video/webm, etc.
                'application/vnd.apple.mpegurl',
                'application/x-mpegurl', 'video/3gpp', 'video/mp4', 'video/mpeg',
                'video/ogg', 'video/quicktime', 'video/webm', 'video/x-m4v',
                'video/ms-asf', 'video/x-ms-wmv', 'video/x-msvideo'
            ])],
            'fd' => 'required|numeric'
        ]);

        // if video format is not selected then video/mp4 is by default
        $data['video_format'] = $data['video_format'] ?? 'video/mp4';

        try {

            // try to find existing video
            $youtubeVideo = YoutubeVideo::findByVideoId($data['video_id']);

            if (empty($youtubeVideo)) {
                // get video info by video id
                $youtubeVideoInfo = YoutubeVideoUtils::getVideoInfo($data['video_id']);

                // do some checks
                if (
                    empty($youtubeVideoInfo)
                    || empty($youtubeVideoInfo['player_response'])
                    || empty($youtubeVideoInfo['url_encoded_fmt_stream_map'])
                ) {
                    throw new \Exception('Failed to get video info!');
                }

                // get player response in json format and decode it
                $playerResponse = @json_decode($youtubeVideoInfo['player_response'], true);

                // check if we corretcly decoded player response
                if ($playerResponse === null && json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Failed to get video info!');
                }

                // save video info in db
                $youtubeVideo = new YoutubeVideo();
                $youtubeVideo->videoId = $playerResponse['videoDetails']['videoId'];
                $youtubeVideo->title = $playerResponse['videoDetails']['title'];
                $youtubeVideo->lengthSeconds = $playerResponse['videoDetails']['lengthSeconds'];
                $youtubeVideo->thumbnail = collect($playerResponse['videoDetails']['thumbnail']['thumbnails'])->pop()['url'];
                $youtubeVideo->streams = $youtubeVideoInfo['url_encoded_fmt_stream_map'];
                $youtubeVideo->number_of_requests = 0;
            }

            // set new requested count
            $youtubeVideo->number_of_requests++;

            // save it
            if (!$youtubeVideo->save()) {
                throw new \Exception('Failed to save video in database!');
            }

            // create and save new convert request
            $youtubeConvert  = new YoutubeConvert();
            $youtubeConvert->ip = $request->ip();
            $youtubeConvert->status = 'converting';
            $youtubeConvert->for_fd = $data['fd'];
            $youtubeVideo->converts()->save($youtubeConvert);

            // queue youtube video processing in queue job
            ProcessYoutubeVideo::dispatch($youtubeConvert);

            return response()->json(new YoutubeVideoResource($youtubeVideo), 200);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            throw new GeneralException('Failed to get video info!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\YoutubeVideo  $youtubeVideo
     * @return \Illuminate\Http\Response
     */
    public function show(YoutubeVideo $youtubeVideo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\YoutubeVideo  $youtubeVideo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, YoutubeVideo $youtubeVideo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\YoutubeVideo  $youtubeVideo
     * @return \Illuminate\Http\Response
     */
    public function destroy(YoutubeVideo $youtubeVideo)
    {
        //
    }
}
