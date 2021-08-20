<?php

namespace App\Notifiable;

class Mailable
{
    protected $view;
    protected $data;
    private $mail;
    private $to, $from, $name, $fromName, $subject, $message;

    public function __construct($view, $data)
    {
        $this->view = $view;
        $this->data = $data;
        $this->mail = new Mail();
    }

    private function make()
    {
        extract($this->data);
        ob_start();
        // include template
        include(__DIR__ . "/../../email/" . $this->view . ".php");
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function build()
    {
        $body = $this->make();
        $this->to = $this->data['to'];
        $this->name = $this->data['name'];
        $this->fromName = $this->data['fromName'];
        $this->from = $this->data['from'];
        $this->subject = $this->data['subject'];
        $this->message = $body;
        return $this;
    }

    public function send()
    {
        $this->mail->sendMail([$this->to => $this->name], $this->subject, $this->message, $this->fromName, $this->from);
        if ($this->mail->sent)
            return TRUE;
        return FALSE;
    }
}
