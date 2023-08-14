<?php

namespace App\Controller\Tests;

use App\Service\Tests\TestEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(condition: "'dev' === '%kernel.environment%'")]
class TestEmailController extends AbstractController {

    #[Route('/test/send/email', name: 'test_send_email')]
    public function testSendEmailAction(Request $request, TestEmailService $testEmailService){
    	$result = $testEmailService->testSendEmail();
        return new Response($result["message"]);
    }

    #[Route('/test/send/email/with/attachements', name: 'test_send_email_with_attachements')]
    public function testSendEmailWithAttachementsAction(Request $request, TestEmailService $testEmailService){
        $result = $testEmailService->testSendEmailWithAttachements();
        return new Response($result["message"]);
    }
}