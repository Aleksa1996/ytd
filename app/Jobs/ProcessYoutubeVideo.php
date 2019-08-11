<?php

namespace App\Jobs;

use App\YoutubeVideo;
use Illuminate\Bus\Queueable;
use App\Classes\YoutubeVideoUtils;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Classes\WebsocketClient;

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
        // CfihYWRWRTQ // john newman - ciphered
        // omjnFuAd_sw // mudja - not ciphered
        // 6-g0jxauBJg // VEVO - MULTIPLE STREAMS

        // start timer
        $start = \microtime(true);

        // create directory for video
        Storage::makeDirectory('/public/youtube_videos/' . $this->youtubeVideo->videoId);

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

        go(function () use ($that, $parsedStream) {
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
            $saveToPath = storage_path('app/public/youtube_videos/') . $that->youtubeVideo->videoId . '/' . $this->youtubeVideo->id . '_video.mp4';

            // init websocket connection
            $websocketClient = new WebsocketClient('swoole', 1215);

            // download video
            YoutubeVideoUtils::makeDownloadVideoRequest($downloadUrl, $saveToPath, function ($percentage) use ($websocketClient) {
                $websocketClient->push('example', ['downloaded' => $percentage]);
            });
            // convert video to mp3
            YoutubeVideoUtils::convertVideoToMp3($saveToPath);
        });

        \Swoole\Event::wait();
    }
}
