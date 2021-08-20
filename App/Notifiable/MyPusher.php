<?php

namespace App\Notifiable;

use Pusher\Pusher;

class MyPusher
{
    private static $pusher;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$pusher == null) {
            $options = array(
                'cluster' => 'eu',
                'useTLS' => true
            );
            self::$pusher = new Pusher(
                PUSHER_KEY,
                PUSHER_SECRET,
                PUSHER_ID,
                $options
            );
        }
        return self::$pusher;
    }

    public static function send($message)
    {
        $response = self::$pusher->trigger('my-channel', 'my-event', $message, ['info' => 'subscription_count']);
    }
}
