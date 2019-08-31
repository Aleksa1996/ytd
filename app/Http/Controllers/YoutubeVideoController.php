<?php

namespace App\Http\Controllers;

use App\YoutubeVideo;
use App\Jobs\ProcessYoutubeVideo;
use App\Classes\YoutubeVideoUtils;
use App\Exceptions\GeneralException;
use App\Http\Resources\YoutubeVideo as YoutubeVideoResource;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class YoutubeVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            // get video info by video id
            $youtubeVideoInfo = YoutubeVideoUtils::getVideoInfo($data['video_id']);

            if (
                empty($youtubeVideoInfo)
                || empty($youtubeVideoInfo['player_response'])
                || empty($youtubeVideoInfo['url_encoded_fmt_stream_map'])
            ) {
                throw new \Exception('Failed to get video info!');
            }

            // get player response in json format and decode it
            $playerResponse = json_decode($youtubeVideoInfo['player_response'], true);

            // check if we corretcly decoded player response
            if ($playerResponse === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to get video info!');
            }

            // try to find existing one
            $youtubeVideo = YoutubeVideo::findByVideoId($playerResponse['videoDetails']['videoId']);

            // save video info in db
            if (empty($youtubeVideo)) {
                $youtubeVideo = new YoutubeVideo();
                $youtubeVideo->videoId = $playerResponse['videoDetails']['videoId'];
                $youtubeVideo->title = $playerResponse['videoDetails']['title'];
                $youtubeVideo->lengthSeconds = $playerResponse['videoDetails']['lengthSeconds'];
                $youtubeVideo->thumbnail = collect($playerResponse['videoDetails']['thumbnail']['thumbnails'])->pop()['url'];
                $youtubeVideo->streams = $youtubeVideoInfo['url_encoded_fmt_stream_map'];
                $youtubeVideo->for_fd = $data['fd'];
                $youtubeVideo->number_of_requests = 1;
            } else {
                $youtubeVideo->number_of_requests++;
            }

            $youtubeVideo->status = 'converting';

            if (!$youtubeVideo->save()) {
                throw new \Exception('Failed to save video in database!');
            }

            // queue youtube video processing in queue job
            ProcessYoutubeVideo::dispatch($youtubeVideo);

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
