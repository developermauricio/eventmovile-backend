<?php

namespace App\Mail\Event;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRegister extends Mailable
{
    use Queueable, SerializesModels;
    private $name;
    private $last_name;
    private $event;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $last_name, $event)
    {
        $this->name = $name;
        $this->lastName = $last_name;
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(config('app.name').'-'. 'REGISTRO EXITOSO' )
            ->markdown('email.event.new-register-event')
            ->with('name',$this->name)
            ->with('last_name',$this->lastName)
            ->with('event',$this->event);
    }
}
