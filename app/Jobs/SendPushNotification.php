<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $title;
    public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $title,$body)
    {
        $this->to=$to;
        $this->title=$title;
        $this->body=$body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response=Http::acceptJson()
        ->withHeaders([
            'Authorization'=>'key='.config('fcm.token'),
            'Content-Type'=>'application/json'
        ])
        ->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'to' => $this->to,
                'notification' => [
                    'title'=>$this->title,
                    'body'=>$this->body,
                    //'image'=> asset('storage/images/rawLogo.png'),
                    'sound' => 'default', 
                ],
            ]
        );
        return $response;
    }
}
