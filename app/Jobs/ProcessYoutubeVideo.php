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
    protected $info;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(YoutubeVideo $youtubeVideo, $info)
    {
        $this->youtubeVideo = $youtubeVideo;
        $this->info = $info;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $streams = explode(',', $this->info['url_encoded_fmt_stream_map']);

        // return response()->json($info, 200);
        // return response()->json(json_decode($info['player_response']), 200);

        // strimova ima vise
        // videti da se zabrane ogromni videi

        // CfihYWRWRTQ // vevo - ciphered
        // omjnFuAd_sw // mudja - not ciphered

        $urls = [];
        foreach ($streams as $stream) {
            // decode the stream
            parse_str($stream, $parsedStream);
            // return response()->json($p, 200);

            // get signature if video is protected with cipher
            $signature = '';
            if (isset($parsedStream['s'])) {
                $playerScript = YoutubeVideoUtils::getPlayerScriptByVideoId($this->youtubeVideo->videoId);
                $decipher = YoutubeVideoUtils::extractDecipher($playerScript['content']);
                $signature = YoutubeVideoUtils::generateSignature($decipher['decipherPatterns'], $decipher['deciphers'], $parsedStream['s']);
                $signature = $parsedStream['sp'] . '=' . $signature;
            }

            // store generated url
            $urls[] = $parsedStream['url'] . '&asv=3&el=detailpage&hl=en_US&' . $signature;
        }

        return YoutubeVideoUtils::makeDownloadVideoRequest($urls[0], $this->youtubeVideo->videoId);
    }
}
