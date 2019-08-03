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
        // 18 sec for first test

        // CfihYWRWRTQ // john newman - ciphered
        // omjnFuAd_sw // mudja - not ciphered
        // 6-g0jxauBJg // VEVO - MULTIPLE STREAMS

        // break video stream map string into pieces
        $videoStreams = explode(',', $this->youtubeVideo->streams);
        $that = $this;

        $start = \microtime(true);
        Storage::makeDirectory('/public/youtube_videos/' . $this->youtubeVideo->videoId);
        echo '[CREATING_DIR]' . (microtime(true) - $start) . "\n";

        foreach ($videoStreams as $videoStreamIndex => $videoStream) {

            go(function () use ($that, $videoStreamIndex, $videoStream) {
                // parse the stream
                parse_str($videoStream, $parsedStream);

                // get signature if video is protected with cipher
                $signature = '';
                if (isset($parsedStream['s'])) {
                    $playerScript = YoutubeVideoUtils::getPlayerScriptByVideoId($that->youtubeVideo->videoId);
                    $decipher = YoutubeVideoUtils::extractDecipher($playerScript['content']);
                    $signature = YoutubeVideoUtils::generateSignature($decipher['decipherPatterns'], $decipher['deciphers'], $parsedStream['s']);
                    $signature = ($parsedStream['sp'] ?? 'sig') . '=' . $signature;
                }

                $downloadUrl = $parsedStream['url'] . '&asv=3&el=detailpage&hl=en_US&' . $signature;
                $saveToPath = storage_path('app/public/youtube_videos/') . $that->youtubeVideo->videoId . '/' . $videoStreamIndex . '_video.mp4';

                YoutubeVideoUtils::makeDownloadVideoRequest($downloadUrl, $saveToPath);
            });
        }

        // wait for all coroutines to finish
        \Swoole\Event::wait();
    }
}
