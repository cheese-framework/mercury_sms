<?php

namespace App\Notifiable;

use App\Database\Database;
use Exception;
use Twilio\Rest\Client;

class Notifiable extends Mailable
{

    private \Twilio\Rest\Client $twilio;
    private Mail $mail;
    public array $via = [];
    private $sent = false;
    protected $id;

    public function __construct(array $via, $id = 0)
    {
        $this->twilio = new Client(TWILIO_KEY, TWILIO_SECRET);
        $this->mail = new Mail();
        $this->via = $via;
        $this->id = $id;
    }

    protected function getNotificationLinkTwilio()
    {
        $db = Database::getInstance();
        $db->query("SELECT phone FROM parents WHERE id=?");
        $db->bind(1, $this->id);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            return $record->phone;
        } else {
            return "";
        }
    }

    protected function getNotificationLinkMail()
    {
        $db = Database::getInstance();
        $db->query("SELECT name,email FROM parents WHERE id=?");
        $db->bind(1, $this->id);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            return [$record->email => $record->name];
        } else {
            return "";
        }
    }

    /**
     * @throws Exception
     */
    public function notify($message, $subject = null, $fromName = null, $from = null)
    {
        if ($this->via != NULL) {
            if (in_array("twilio", $this->via)) {
                if ($this->getNotificationLinkTwilio() != "") {
                    $this->sendNotificationViaTwilio($this->getNotificationLinkTwilio(), $message);
                }
            }

            if (in_array("mail", $this->via)) {
                if ($this->getNotificationLinkMail() != NULL) {
                    $this->mail->sendMail($this->getNotificationLinkMail(), $subject, $message, $fromName, $from);
                    $this->sent = $this->mail->sent;
                    if (!$this->sent) {
                        $this->mail->returnError();
                    }
                }
            }

            if (in_array('', $this->via)) {
                throw new \Exception("No notifier set up");
            }
        } else {
            throw new \Exception("No notifier set up");
        }
    }



    public function sendNotificationViaTwilio($to, $message)
    {
        try {
            $this->twilio->notify->services(TWILIO_NOTIFY_SID)->notifications->create([
                "toBinding" => '{"binding_type":"sms","address":"' . $to . '"}',
                'body' => $message
            ]);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
