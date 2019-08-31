<?php

namespace App\Jobs;

use App\YoutubeVideo;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Classes\WebsocketClient;
use App\Classes\YoutubeVideoUtils;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessYoutubeVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $youtubeVideo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(YoutubeVideo $youtubeVideo)
    {
        $this->youtubeVideo = $youtubeVideo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // start timer
        $start = \microtime(true);

        // create directory for video
        Storage::makeDirectory('/public/converts/' . $this->youtubeVideo->videoId);

        // echo log (time used to create directory)
        echo '[CREATING_DIR]' . (microtime(true) - $start) . "\n";

        // break video stream map string into pieces
        $videoStreams = explode(',', $this->youtubeVideo->streams);
        $that = $this;

        // if stream of video exists
        if (!isset($videoStreams[0])) {
            throw new \Exception('There are no stream links for video!');
        }
        $videoStream  = $videoStreams[0];

        // parse the stream
        parse_str($videoStream, $parsedStream);

        // create corutine
        go(function () use ($that, $parsedStream) {
            // init websocket connection
            $websocketClient = new WebsocketClient('swoole', 1215);

            // inform user that we are starting process
            $websocketClient->push('VIDEO_PROCESSING_PROGRESS_B', [
                'progress_type' => 'preparation',
                'progress' => -1,
                'for_fd' => $that->youtubeVideo->for_fd
            ]);

            // get signature if video is protected with cipher
            $signature = '';
            if (isset($parsedStream['s'])) {
                $playerScript = YoutubeVideoUtils::getPlayerScriptByVideoId($that->youtubeVideo->videoId);
                $decipher = YoutubeVideoUtils::extractDecipher($playerScript['content']);
                $signature = YoutubeVideoUtils::generateSignature($decipher['decipherPatterns'], $decipher['deciphers'], $parsedStream['s']);
                $signature = ($parsedStream['sp'] ?? 'sig') . '=' . $signature;
            }

            // generate download and local path
            $downloadUrl = $parsedStream['url'] . '&asv=3&el=detailpage&hl=en_US&' . $signature;
            $fileName = $this->youtubeVideo->id . '-' . Str::slug($this->youtubeVideo->title);
            $videoPath = storage_path('app/public/converts/') . $that->youtubeVideo->videoId . '/' . $fileName . '.mp4';
            $mp3Path = storage_path('app/public/converts/') . $that->youtubeVideo->videoId . '/' . $fileName . '.mp3';

            // download video
            YoutubeVideoUtils::makeDownloadVideoRequest($downloadUrl, $videoPath, function ($percentage) use ($websocketClient, $that) {
                // inform user that we are starting to download video
                $websocketClient->push('VIDEO_PROCESSING_PROGRESS_B', [
                    'progress_type' => 'video_download',
                    'progress' => $percentage,
                    'for_fd' => $that->youtubeVideo->for_fd
                ]);
            });

            // convert video to mp3
            YoutubeVideoUtils::convertVideoToMp3($videoPath, $mp3Path, function ($percentage) use ($websocketClient, $that) {
                // inform user that we are converting to download video
                $websocketClient->push('VIDEO_PROCESSING_PROGRESS_B', [
                    'progress_type' => 'video_convert',
                    'progress' => $percentage,
                    'for_fd' => $that->youtubeVideo->for_fd
                ]);
            });

            // push message to backend that we finished with converting video
            $websocketClient->push('VIDEO_PROCESSING_PROGRESS_B', [
                'progress_type' => 'video_finished',
                'link' => asset('/static/' . $this->youtubeVideo->videoId . '/' . $fileName . '.mp3'),
                'file' =>  $fileName . '.mp3',
                'for_fd' => $that->youtubeVideo->for_fd
            ]);

            // update status of video
            $that->youtubeVideo->status = 'finished';
            $that->youtubeVideo->save();
        });

        \Swoole\Event::wait();
    }
}
