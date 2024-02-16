<?php
namespace App\Email;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailBuilder
{
    public const DSN = 'smtp://800542c71826e3:57cf71441210a7@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';

    public const HOST = 'http://localhost:8000';

    public const CONTACT = 'contact@maison.fr';

    public const NO_REPLY = 'no-reply@maison.fr';

    public const NAME = 'maison.fr';

    private Mailer $mailer;

    protected Email $email;

    private Environment $twig;

    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $transport = Transport::fromDsn(self::DSN);
        $this->mailer = new Mailer($transport);
        $this->email = new Email();
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    public function send():void 
    {
        $this->mailer->send($this->email);
    }

    public function from(string $address, ?string $name = ''):self 
    {
        $this->email->from(new Address($address, $name));

        return $this;
    }

    public function to(string $address, ?string $name = ''):self 
    {
        $this->email->to(new Address($address, $name));

        return $this;
    }

    public function subject(string $subject):self 
    {
        $this->email->subject($subject);

        return $this;
    }
    public function text(string $text):self 
    {
        $this->email->text($text);

        return $this;
    }
    public function html(string $view, ?array $params = []):self 
    {
        $this->email->html($this->twig->render($view, $params));

        return $this;
    }
}



