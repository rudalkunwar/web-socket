<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DecodeMovie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:process {name} {video}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $video = $this->argument('video');

        // Construct the full path to the video file
        $videoPath = public_path('videos' . DIRECTORY_SEPARATOR . 'movies') . DIRECTORY_SEPARATOR . $video;

        // Ensure the video file exists
        if (!file_exists($videoPath)) {
            $this->error("File does not exist: $videoPath");
            return 1;
        }

        // Output directory for HLS files
        $outputDir = public_path('movies') . DIRECTORY_SEPARATOR . $name . '_hls';

        // Create the output directory if it doesn't exist
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true); // Ensure permissions are set correctly
        }
        // Define qualities and resolutions as needed
        $qualities = [
            [
                'resolution' => '426x240',
                'bitrate' => '400k',
                'filename_prefix' => '240p'
            ],
            [
                'resolution' => '854x480',
                'bitrate' => '800k',
                'filename_prefix' => '480p'
            ],
            [
                'resolution' => '1280x720',
                'bitrate' => '1500k',
                'filename_prefix' => '720p'
            ],
            [
                'resolution' => '1920x1080',
                'bitrate' => '3000k',
                'filename_prefix' => '1080p'
            ],
        ];

        // Process each quality version
        foreach ($qualities as $quality) {
            $resolution = $quality['resolution'];
            $bitrate = $quality['bitrate'];
            $filenamePrefix = $quality['filename_prefix'];

            // Output path for each quality version
            $outputPath = $outputDir . DIRECTORY_SEPARATOR . "$filenamePrefix.m3u8";

            // FFmpeg command to generate HLS playlist and segments
            $command = "ffmpeg -i \"$videoPath\" -vf scale=$resolution -b:v $bitrate -bufsize 1835k -maxrate $bitrate -hls_time 10 -hls_list_size 0 -hls_segment_filename \"$outputDir/$filenamePrefix" . "_%03d.ts\" \"$outputPath\"";

            // Execute FFmpeg command
            exec($command, $output, $returnCode);

            // Check if FFmpeg command executed successfully
            if ($returnCode !== 0 || !file_exists($outputPath)) {
                $this->error("FFmpeg Error: Failed to process video $video");
                return 1;
            }
        }

        // Create a master playlist that references the different quality playlists
        $masterPlaylist = "#EXTM3U\n";
        foreach ($qualities as $quality) {
            $resolution = $quality['resolution'];
            $filenamePrefix = $quality['filename_prefix'];
            $masterPlaylist .= "#EXT-X-STREAM-INF:BANDWIDTH=" . intval($quality['bitrate']) * 1000 . ",RESOLUTION=$resolution\n";
            $masterPlaylist .= "$filenamePrefix.m3u8\n";
        }

        // Save master playlist
        file_put_contents($outputDir . DIRECTORY_SEPARATOR . "master.m3u8", $masterPlaylist);

        // Output success message
        $this->info("Video processing completed successfully.");

        return 0;
    }
}
