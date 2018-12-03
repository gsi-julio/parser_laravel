<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNumberMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mediodia_centena;
    protected $mediodia_fijo;
    protected $noche_centena;
    protected $noche_fijo;
    protected $fecha;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mediodia_centena, $mediodia_fijo, $noche_centena, $noche_fijo, $fecha)
    {
        $this->mediodia_centena = $mediodia_centena;
        $this->mediodia_fijo = $mediodia_fijo;
        $this->noche_centena = $noche_centena;
        $this->noche_fijo = $noche_fijo;
        $this->fecha = $fecha;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.send_number', [
            'mediodia_centena' => $this->mediodia_centena,
            'mediodia_fijo' => $this->mediodia_fijo,
            'noche_centena' => $this->noche_centena,
            'noche_fijo' => $this->noche_fijo,
            'fecha' => $this->fecha,
        ]);
    }
}
