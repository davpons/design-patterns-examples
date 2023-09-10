<?php

class YouTubeDownloaderFacade
{
    private YouTubeApiSubsystem $youtube;
    private FFMpegApiSubsystem $ffmpeg;

    public function __construct(string $youtubeApiKey)
    {
        $this->youtube = new YouTubeApiSubsystem($youtubeApiKey);
        $this->ffmpeg = new FFMpegApiSubsystem();
    }

    public function downloadVideo(string $url): void
    {
        echo "YouTubeSubsystem...<br>";
        echo "Fetching video metadata from youtube...<br>";
        // $title = $this->youtube->fetchVideo($url)->getTitle();
        echo "Saving video file to a temporary file...<br>";
        // $this->youtube->saveAs($url, "video.mpg");

        echo "<br>FFMpegSubsystem...<br>";
        echo "Processing source video...<br>";
        // $video = $this->ffmpeg->open('video.mpg');
        echo "Normalizing and resizing the video to smaller dimensions...<br>";
        // $video
        //     ->filters()
        //     ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        //     ->synchronize();
        echo "Capturing preview image...<br>";
        // $video
        //     ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        //     ->save($title . 'frame.jpg');
        echo "Saving video in target formats...<br>";
        // $video
        //     ->save(new FFMpeg\Format\Video\X264(), $title . '.mp4')
        //     ->save(new FFMpeg\Format\Video\WMV(), $title . '.wmv')
        //     ->save(new FFMpeg\Format\Video\WebM(), $title . '.webm');
        echo "Done!";
    }
}

class YouTubeApiSubsystem
{
    private string $youtubeApiKey;

    public function __construct(string $youtubeApiKey)
    {
        $this->youtubeApiKey = $youtubeApiKey;
    }

    public function fetchVideo(): string
    {
        //
    }

    public function saveAs(string $path): void
    {
        //
    }
}

class FFMpegApiSubsystem
{
    public static function create(): self
    {
        //
    }

    public function open(string $video): void
    {
        //
    }
}

class Client
{
    public function videoProcess()
    {
        $videoDownloader = new YouTubeDownloaderFacade('apikey');
        $videoDownloader->downloadVideo('https://youtube.com/videopath');
    }
}

(new Client())->videoProcess();
