<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class VideoDecode implements ShouldQueue
{
    use Queueable;

    protected $name;
    protected $video;

    /**
     * Create a new job instance.
     */
    public function __construct($name, $video)
    {
        $this->name = $name;
        $this->video = $video;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        set_time_limit(0); // Set an unlimited execution time limit 
        try {

            Artisan::call('video:process', [
                'name' => $this->name,
                'video' => $this->video,
            ]);

            $output = Artisan::output();

            if (strpos($output, 'Video processing completed successfully.') === false) {
                throw new Exception("Video processing failed. Output: $output");
            }
        } catch (Exception $e) {
            throw new Exception("Video processing failed: " . $e->getMessage());
        }
    }
}
