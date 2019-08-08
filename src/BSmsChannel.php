<?php

namespace Yna\BSms;

use Illuminate\Notifications\Notification;
use Yna\BSms\BSmsApi;
use Yna\PortToSms\Exceptions\CouldNotSendNotification;

class BSmsChannel
{
    /** @var BSmsApi */
    protected $api;

    public function __construct(BSmsApi $api)
    {
        $this->api = $api;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     *
     * @throws  \Yna\PortToSms\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('bSms');

        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toBSms($notifiable);

        if (is_string($message)) {
            $message = new BSmsMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    protected function sendMessage($recipient, BSmsMessage $message)
    {
        if (mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'to' => $recipient,
            'body' => $message->content
        ];

        if (! empty($message->from)) {
            $params['Sender'] = $message->from;
        }

        $this->api->send($recipient, $message->content, $message->from);
    }
}
