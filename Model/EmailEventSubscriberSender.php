<?php


class EmailEventSubscriberSender
{

    private $to;

    private $subject;

    private $headers;

    private $body;



    public function __construct(string $to)
    {
        $this->to = $to;

    }

    public function sendToAdmin(string $name, string $email ,int $people, string $phone, string $event)
    {
        $this->subject = $name.' se ha apunto al evento '.$event;

        $this->body = '<ul>'.
                '<li> <strong>Nombre: </strong>'.$name.'</li>'.
            '<li> <strong>Evento: </strong>'.$event.'</li>'.
            '<li> <strong>Numero de personas: </strong>'.$people.'</li>'.
            '<li> <strong>Teléfono: </strong>'.$phone.'</li>'.
            '<li> <strong>Email: </strong>'.$email.'</li>'.
            '</ul>';

        $this->send();
    }

    public function sendToSubscriber(string $name,
                                     string $email,
                                     int $people,
                                     string $phone,
                                     string $event,
                                     string $contactMail,
                                     string $adminPhone)
    {
        $this->subject = 'Te has apuntado correctamente al evento '. $event;
        $this->to = $email;

        $this->body = '<ul>'.
            '<li> <strong>Evento: </strong>'.$event.'</li>'.
            '<li> <strong>Nombre de la reserva: </strong>'.$name.'</li>'.
            '<li> <strong>Numero de personas: </strong>'.$people.'</li>'.
            '<li> <strong>Teléfono de contacto: </strong>'.$phone.'</li>'.
            '<li> <strong>Email de contacto: </strong>'.$email.'</li>'.
            '</ul>'.
            '<p></p>'.
            '<p>Si tienes cualquier duda o quieres modificar la reserva llámanos al teléfono: :<strong> '.$adminPhone.'</strong>'.
            ' o envíanos un correo a la dirección <a href="'.$contactMail.'">'.$contactMail.'</a></p>'.
            '<p></p>'.
            '<p>El Equipo de Cafeteria Sahara</p>';

        $this->send();

    }

    private function send()
    {
        wp_mail( $this->to, $this->subject, $this->body, $this->headers );
    }
}