<?php
/**
 * Created by PhpStorm.
 * User: msoliman
 * Date: 7/3/18
 * Time: 4:05 PM
 */

namespace App\Lib\RealTime\Pusher;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;


class PusherHandler
{
    public static function send($data, $channels, $event = 'notify')
    {
        $options = array(
            'cluster' => config('services.pusher.cluster'),
            'encrypted' => true
        );


        try{
            $pusher = new Pusher(
                config('services.pusher.auth_key'),
                config('services.pusher.secret'),
                config('services.pusher.app_id'),
                $options
            );

            $pusher->trigger(
                $channels,
                $event,
                $data
            );
        }catch (\Exception $e){
           Log::info($e->getMessage());
        }

    }
}