<?php

namespace App\Jobs;

use App\YoutubeVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteConvertedData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // converts storage path
        $convertsPath = 'public/converts';
        // get all directories on this path
        $allConvertFolderPaths = Storage::directories($convertsPath);

        // if there are no directories quit the job
        if (empty($allConvertFolderPaths)) {
            return;
        }

        // get the collection of folders, but only their names
        $allConvertFolderNames = collect($allConvertFolderPaths)->map(function ($path) {
            return basename($path);
        });

        // query db to get all videos that are converted 4 mins ago or more
        $convertsToDelete = YoutubeVideo::getFinishedVideos($allConvertFolderNames->toArray());

        // remove directories
        $convertsToDelete->each(function ($video) use ($convertsPath) {
            Storage::deleteDirectory($convertsPath . '/' . $video->videoId);
        });
    }
}
