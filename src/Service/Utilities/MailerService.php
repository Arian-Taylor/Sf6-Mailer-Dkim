<?php
namespace App\Service\Utilities;

use App\Service\Utilities\ArrayService;
use App\Service\Utilities\DKIMService;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Twig\Environment ;

class MailerService {

    public function __construct(
        private Environment $twig,
        private DKIMService $DKIMService,
        private ParameterBagInterface $params
    ){}

    /**
     * Send email
     * @param array $params associative array ex: ["lastname" => "value", "phone" => "value"];
     * @param string $template path template email ex:'back/emails/fournisseur.html.twig';
     * 
     * @return int|NotFoundHttpException (status 2: not sended, status 1: sended)
     */
    public function sendEmail(
        array $params, 
        ?string $template = null
    ): int | NotFoundHttpException
    {
        $status = 2;
        $destinataire = ArrayService::getValOfKey($params, "destinataire");
        $attachments = ArrayService::getValOfKey($params, "attachments");
        $is_valid_email = $this->checkEmail($destinataire);

        if($is_valid_email) {
            $MAILER_DSN = $this->params->get('MAILER_DSN');
            $MAILER_FROM = $this->params->get('MAILER_FROM');
            $MAILER_NAME = $this->params->get('MAILER_NAME');
            $object = ArrayService::getValOfKey($params, "object");

            if(!$template) {
                $template = ArrayService::getValOfKey($params, "template");
                if(!$template) {
                    throw new NotFoundHttpException('Template email non trouvé');
                }
            } 

            $messageListener = new MessageListener(null, new BodyRenderer($this->twig));
            $eventDispatcher = new EventDispatcher();
            $eventDispatcher->addSubscriber($messageListener);
            $mailTransport = Transport::fromDsn($MAILER_DSN, $eventDispatcher);
            $mailer = new Mailer($mailTransport, null, $eventDispatcher);
            $email = (new TemplatedEmail())
                ->from(new Address($MAILER_FROM, $MAILER_NAME))
                ->to(new Address($destinataire))
                ->subject($object ? $object :  'Object')
                ->htmlTemplate($template)
                ->context([
                    'params' => $params,
                ]);

            if($attachments && gettype($attachments) == "array"){
                foreach ($attachments as $key => $attachment) {
                    $email->attachFromPath($attachment, $key);
                }
            }

            try {
                $email = $this->DKIMService->sign($email);
                $mailer->send($email);
                $status = 1;
            } catch (TransportExceptionInterface $e) {
                $status = 2;
            }
        }

        return $status;
    }

    /**
     * Check an email
     * 
     * @param string $email
     * 
     * @return bool
     */
    public function checkEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          return false;
        }
        return true;
    }
}
