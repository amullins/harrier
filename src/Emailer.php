<?php

class Emailer {
    private $sendgrid = null;

    public function Emailer($username, $password) {
        $this->sendgrid = new SendGrid($username, $password);
    }

    public function create() {
        return new SendGrid\Mail();
    }

    public function send($to, $from, $subject, $html) {
        $mail = $this->create();

        $mail->
            addTo($to)->
            setFrom($from)->
            setSubject($subject)->
            setHtml($html);

        return $this->sendgrid->web->send($mail);
    }
}