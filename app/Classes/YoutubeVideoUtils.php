<?php

namespace App\Classes;

use \Swoole\Coroutine\Http\Client as SwooleHttpClient;

class YoutubeVideoUtils
{
    private static $videoInfoUrl = 'https://www.youtube.com/get_video_info?asv=3&el=detailpage&hl=en_US&video_id=';

    public static function getVideoInfo($videoId)
    {
        $videoInfo = self::makeGetRequest(self::$videoInfoUrl . $videoId);

        if (empty($videoInfo)) {
            return false;
        }

        parse_str($videoInfo, $parsedVideoInfo);
        return $parsedVideoInfo;
    }

    public static function extractDecipher($decipherScript)
    {
        $decipherPatterns = explode('.split("")', $decipherScript);
        unset($decipherPatterns[0]);
        foreach ($decipherPatterns as $value) {

            // Make sure it's inside a function and also have join
            $value = explode('.join("")', explode('}', $value)[0]);
            if (count($value) === 2) {
                $value = explode(';', $value[0]);

                // Remove first and last index
                array_pop($value);
                unset($value[0]);

                $decipherPatterns = implode(';', $value);

                break;
            }
        }

        preg_match_all('/(?<=;).*?(?=\[|\.)/', $decipherPatterns, $deciphers);
        if ($deciphers && count($deciphers[0]) >= 2) {
            $deciphers = $deciphers[0][0];
        } else {
            throw new \Exception('Failed to get deciphers function');

            return false;
        }

        $deciphersObjectVar = $deciphers;
        $decipher = explode($deciphers . '={', $decipherScript)[1];
        $decipher = str_replace(["\n", "\r"], '', $decipher);
        $decipher = explode('}};', $decipher)[0];
        $decipher = explode('},', $decipher);

        // Convert deciphers to object
        $deciphers = [];

        foreach ($decipher as &$function) {
            $deciphers[explode(':function', $function)[0]] = explode('){', $function)[1];
        }

        // Convert pattern to array
        $decipherPatterns = str_replace($deciphersObjectVar . '.', '', $decipherPatterns);
        $decipherPatterns = str_replace($deciphersObjectVar . '[', '', $decipherPatterns);
        $decipherPatterns = str_replace(['](a,', '(a,'], '->(', $decipherPatterns);
        $decipherPatterns = explode(';', $decipherPatterns);

        return [
            'decipherPatterns' => $decipherPatterns,
            'deciphers' => $deciphers,
        ];
    }

    public static function generateSignature($patterns, $deciphers, $signature)
    {
        // Execute every $patterns with $deciphers dictionary
        $processSignature = str_split($signature);
        for ($i = 0; $i < count($patterns); $i++) {
            // This is the deciphers dictionary, and should be updated if there are different pattern
            // as PHP can't execute javascript

            //Separate commands
            $executes = explode('->', $patterns[$i]);

            // This is parameter b value for 'function(a,b){}'
            $number = intval(str_replace(['(', ')'], '', $executes[1]));
            // Parameter a = $processSignature

            $execute = $deciphers[$executes[0]];

            //Find matched command dictionary
            switch ($execute) {
                case 'a.reverse()':
                    $processSignature = array_reverse($processSignature);
                    break;
                case 'var c=a[0];a[0]=a[b%a.length];a[b]=c':
                    $c = $processSignature[0];
                    $processSignature[0] = $processSignature[$number % count($processSignature)];
                    $processSignature[$number] = $c;
                    break;
                case 'var c=a[0];a[0]=a[b%a.length];a[b%a.length]=c':
                    $c = $processSignature[0];
                    $processSignature[0] = $processSignature[$number % count($processSignature)];
                    $processSignature[$number % count($processSignature)] = $c;
                    break;
                case 'a.splice(0,b)':
                    $processSignature = array_slice($processSignature, $number);
                    break;
                default:
                    // die("\n==== Decipher dictionary was not found ====");

                    break;
            }
        }

        return implode('', $processSignature);
    }

    public static function getPlayerScriptByVideoId($videoId)
    {
        // fetch video page
        $data = self::makeGetRequest('https://www.youtube.com/watch?v=' . $videoId);

        // find player script
        $data = explode('/yts/jsbin/player', $data)[1];
        $data = explode('"', $data)[0];
        $playerURL = 'https://www.youtube.com/yts/jsbin/player' . $data;

        // find player id
        try {
            $playerId = explode('-', explode('/', $data)[0]);
            $playerId = $playerId[count($playerId) - 1];
        } catch (\Exception $e) {
            throw new \Exception(sprintf(
                'Failed to retrieve player script for video id: %s',
                $videoId
            ));

            return false;
        }

        // fetch whole player script
        $playerScript = self::makeGetRequest($playerURL);

        return [
            'id' => $playerId,
            'url' => $playerURL,
            'content' => $playerScript
        ];
    }

    public static function makeGetRequest($url)
    {
        $parsedUrl = parse_url($url);

        $cli = new SwooleHttpClient($parsedUrl['host'], 443, true);
        $cli->setHeaders([
            'Host' => $parsedUrl['host']
        ]);

        $success = $cli->get('/' . ($parsedUrl['path'] ?? '') . '?' . ($parsedUrl['query'] ?? ''));

        if ($success) {
            return $cli->body;
        }

        return false;
    }

    public static function makeDownloadVideoRequest($downloadUrl, $saveToPath, $progressCallback = null)
    {
        // start to measure
        $start = microtime(true);

        // log message
        echo '[DOWNLOAD_START]' . ($start) . "\n";

        // open descriptor for file where we save video
        $fd = fopen($saveToPath, 'w+');
        // init curl
        $ch = curl_init($downloadUrl);
        // set request timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        // assign response to file
        curl_setopt($ch, CURLOPT_FILE, $fd);
        // follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // calculate download proggress
        $previousPercent = 0;
        if (is_callable($progressCallback)) {
            // required for progress to work
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);

            // bind callback to curl
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $downloadSize, $downloaded, $uploadSize, $uploaded) use (&$previousPercent, $progressCallback) {
                if ($downloaded > 0) {
                    $percent = (int) (($downloaded / $downloadSize) * 100);
                    if ($percent > 1 && $previousPercent < $percent && $percent % 5 == 0) {
                        $progressCallback($percent, $downloadSize, $downloaded, $uploadSize, $uploaded);
                    }
                    $previousPercent = $percent;
                }
            });
        }

        // execute and close curl
        curl_exec($ch);
        curl_close($ch);

        // close file descriptor for writing
        fclose($fd);

        // log time when we complete download
        echo '[DOWNLOAD_END]' . (microtime(true) - $start) . "\n";

        return true;
    }

    public static function convertVideoToMp3($videoPath)
    {
        // escape video path
        $escapedVideoPath = escapeshellarg($videoPath);

        // create process
        $process = new \Symfony\Component\Process\Process("ffmpeg -i {$escapedVideoPath} -f mp3 -ab 192000 -vn {$escapedVideoPath}.mp3");

        // start process
        $process->start();

        // get output from ffmpeg
        foreach ($process as $type => $data) {
            echo $data . "\n";
        }
    }
}
