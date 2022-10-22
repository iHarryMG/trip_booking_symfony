<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookController extends AbstractController
{

    private $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/webhook/{invoice_number}/{total_amount}', name: 'app_webhook')]
    public function index($invoice_number, $total_amount)
    {
        $dateTime = new \DateTime();
        $now = $dateTime->format('Y-m-d H:i:s');
        $this->logger->info("############ DATE TIME ############ ".$now);

        $dummyResponse = 
            [
                "eventType"=>"invoice.paid",
                "data"=> [
                    "invoiceNumber"=> $invoice_number, //request->get("invoice_number"),
                    "description"=>"",
                    "status"=>1,
                    "amount"=> $total_amount, //$request->get("total_amount"),
                    "trackingData"=>"",
                    "createdAt"=>$now,
                    "expireDate"=>$now,
                    "paidDate"=>$now
                ],
                "signature"=>"aarnGcFn/2LOTfjlDMHZdBiiYjbSkZ5X8f0Tt/Ip3EWqrZVvex9V7F2zyd0AzK7c+YNkygd1qgRRmg7b9tRYEHGJtMycvRT79rj6u53tuwbV29CpHA/1iFVQIYrokDq1Alt7QXE8vesylvQRkdqbzfIuIzulvuRa0nQbdbml48YrlpDKS0BLJXI1XKmWiiKZxcaAJSM4GCyqotFLRzjVprV4VWWirKbF6bvnLfYsjRQ3wetfUAFI6BxGEcFLiFt4t7VXpbNnY8jInaQBrj56yUQAfL2eHVwRNsexxcAn2Ca/VyZiAh22Bg6Z2fKGQoqaq/nl6K8CpA6krDUlQb/MiUR/DyzakE7pFqKo742r/WUOh/eX+HNxourz0h8TdxikIAATJf7DWo5VXzbWu4qJSo5ww0TiLpMFniptBg0KkJeomfreyTCi3S+zTi4mitFqhrMOETvQjc2sC8bH8EurVDLu7io+1UmRM2TfIFlTARD2PsG5BfQ5XHqbww4fHol+rt9U3l8ZUFyvBWQQJKW4Vr5jFYPI0BQuqVnlnH4vKztkORfsnV6nQPA21y5dWAcgKex4h1LuS5GbFZHjkbOJZlaIGxf3hbxOEhKpXgKNqUSzz63L8PAsMnFDGOOs4MKnPQeK/Kfe/ioGBTKfkpanrvf5p+KcPX1w4pSg1dU1kMs="
            ];
        
        $this->logger->info("############ WEBHOOK REDIRECT TEST ############ ");
        $this->logger->info("WEBHOOK DATA:: ".var_export($dummyResponse, true));
        $this->logger->info("############################################### ");
        
        return $this->redirectToRoute('lendmn_pay', $dummyResponse);

    }
}
