<?php
namespace App\Service\Utilities;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Message;
use Twig\Environment;

class DKIMService {

    public function __construct(
        private Environment $twig,
        private ParameterBagInterface $params
    ){}

    public function getPrivateKeyFilePath(): string
    {
        $SYS_DKIM_FILE_NAME = $this->params->get('SYS_DKIM_FILE_NAME');
        $SYS_DKIM_PATH_ABSOLUTE = $this->params->get('SYS_DKIM_PATH_ABSOLUTE');
        $private_file = "";

        if($SYS_DKIM_PATH_ABSOLUTE === "on"){
            $private_file = "$SYS_DKIM_FILE_NAME";
        }
        else{
            $project_dir = $this->params->get("kernel.project_dir");
            $private_file = $project_dir."/private/dkim/$SYS_DKIM_FILE_NAME";
        }
        $private_file = str_replace("\\", "/", $private_file);
        return $private_file;
    }

    public function sign(TemplatedEmail $email): TemplatedEmail|Message
    {
        $SYS_DKIM_SIGNEE_ACTIVE = $this->params->get('SYS_DKIM_SIGNEE_ACTIVE');
        $SYS_DKIM_SIGNEE_DOMAIN = $this->params->get('SYS_DKIM_SIGNEE_DOMAIN');
        $SYS_DKIM_SIGNEE_SELECTOR = $this->params->get('SYS_DKIM_SIGNEE_SELECTOR');
        
        if($SYS_DKIM_SIGNEE_ACTIVE !== "on"){
            return $email;
        }

        $private_file = $this->getPrivateKeyFilePath();
        $private_key = file_get_contents($private_file);

        $signer = new DkimSigner(
            $private_key,
            $SYS_DKIM_SIGNEE_DOMAIN,
            $SYS_DKIM_SIGNEE_SELECTOR
        );

        $renderer = new BodyRenderer($this->twig, $email->getContext());
        $renderer->render($email);

        $email = new Message($email->getPreparedHeaders(), $email->getBody());
        $email = $signer->sign($email);

        return $email;
    }
    
}
