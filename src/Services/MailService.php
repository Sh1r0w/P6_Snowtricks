<?php 

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/* The MailService class is responsible for sending emails using a MailerInterface implementation. */
class MailService
{
    private $mailer;

    /**
     * The function is a constructor that takes a MailerInterface object as a parameter and assigns it
     * to the  property of the class.
     * 
     * @param MailerInterface mailer The mailer parameter is an instance of a class that implements the
     * MailerInterface. This allows the constructor to receive any object that conforms to the
     * MailerInterface, providing flexibility and allowing for dependency injection.
     */
    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    /**
     * The function sends an email using a template and specified parameters.
     * 
     * @param string from The email address from which the email will be sent.
     * @param string to The "to" parameter is the email address of the recipient to whom the email will
     * be sent.
     * @param string subject The subject of the email that will be sent.
     * @param string template The "template" parameter is the name of the email template file that will
     * be used to generate the HTML content of the email. It is expected to be a Twig template file
     * located in the "emails" directory.
     * @param array context The context parameter is an array that contains the variables and values
     * that will be used in the email template. These variables can be used to personalize the email
     * content or provide dynamic data.
     */
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {

        $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->htmlTemplate("emails/$template.html.twig")
        ->context($context);

        $this->mailer->send($email);
    }
}
