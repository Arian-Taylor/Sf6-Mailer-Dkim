<?php
namespace App\Service\Tests;

use App\Service\Utilities\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TestEmailService {

    public function __construct(
        private ParameterBagInterface $params,
        private MailerService $mailerService,
    ){}

    public function testSendEmail(){
        // parameters required
        $destinataire = "johnDoe@gmail.com";
        $object = "Example email";
        $template = "email/test_email.html.twig";

        // paramaters optionnels
        $nom = "John";
        $prenom = "John";

        $result = [
            "status" => 200,
            "message" => ""
        ];

        $params = [
            "destinataire" => $destinataire,
            "nom" => $nom,
            "prenom" => $prenom,
            "object" => $object,
        ];

        $status_email = $this->mailerService->sendEmail($params, $template);

        if($status_email != 1){
            $result["message"] = "Email non envoyé !";
            $result["status"] = 500;
        } else {
            $result["message"] = "Email envoyé avec succes";
            $result["status"] = 200;
        }

        return $result;
    }

    public function testSendEmailWithAttachements(){
        // parameters required
        $destinataire = "johnDoe@gmail.com";
        $object = "Example email with attachments";
        $template = "email/test_email.html.twig";

        // paramaters optionnels
        $nom = "John";
        $prenom = "John";

        // attachements paramaters
        $project_dir = $this->params->get("kernel.project_dir");
        $attach_doc = $project_dir."/public/uploads/document.pdf";
        $attach_img = $project_dir."/public/uploads/image.png";
        /**
         * @var [] $attachments
         */
        $attachments = [
            "Exemple document.pdf" => $attach_doc,
            "Exemple image.png" => $attach_img,
        ];

        $result = [
            "status" => 200,
            "message" => ""
        ];

        $params = [
            "destinataire" => $destinataire,
            "nom" => $nom,
            "prenom" => $prenom,
            "object" => $object,
            "attachments" => $attachments,
        ];

        $status_email = $this->mailerService->sendEmail($params, $template);

        if($status_email != 1){
            $result["message"] = "Email non envoyé !";
            $result["status"] = 500;
        } else {
            $result["message"] = "Email envoyé avec succes";
            $result["status"] = 200;
        }

        return $result;
    }
}
