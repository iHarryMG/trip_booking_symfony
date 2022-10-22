<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

use Psr\Log\LoggerInterface;
use App\Util\Util;
use App\Service\ControllerService;
use Doctrine\DBAL\Connection;

class OrderController extends AbstractController
{
    private $logger;
    private $util;
    private $requestStack;
    private $service;
    private $connection;
    
    public function __construct(LoggerInterface $logger, RequestStack $requestStack, Connection $connection)
    {
        $this->logger = $logger;
        $this->util = new Util();
        $this->requestStack = $requestStack;
        $this->service = new ControllerService($logger);
        $this->connection = $connection;
    }
    
    /**
     * @Route("/order/list", name="order_list")
     */
    public function orderList()
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'order_list()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $session = $this->requestStack->getSession();
        if($session->get('user_session')){
            if($this->service->is_prod == true){ 
                $userID = $session->get('user_session')['user_id'];
                $accessToken = $session->get('user_session')['access_token'];
            }else{
                $userID = 'GAS8FjZSle5snF37BSu2uA==';
                $accessToken = 'NzBiYTgyNjc0MmU3Zjk1NDgzYTMxMGU5NDU1OGE1N2Q0NGZjMDBhNjg3NGQ3ODM4NmYwMjFkOTBmZDk0Zjg2MQ';
            }
            $this->logger->info("USER SESSON:: USER ACCOUNT ID=".$userID.", ACCESS TOKEN=".$accessToken);
            
            //            User table-н ID утга авах
            $user = $this->service->getUserId($this->connection, $request, $userID);
            $orderList = $this->service->getOrderListByUserId($this->connection, Request::createFromGlobals(), $user['user_id'][0]['id']);
            $this->logger->info("ORDER LIST RESULT:: ".var_export($orderList, true));
            
            if ($orderList) {
                $result = array(
                    'orderList' => $orderList,
                );
            }else{
                $result = array(
                    'orderList' => null,
                );
            }

            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));

        }else{
            $result = array(
                'orderList' => null,
            );
        }
        
        return $this->render('order/order_list.html.twig', $result);
    }
    
    /**
     * @Route("/order/detail/{order_id}", name="order_detail")
     */
    public function orderDetail($order_id)
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'order_detail()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $this->connection->fetchAllAssociative('
            SELECT ot.id as order_trip_id, trip.is_special, ot.invoice_num, ot.order_status, ot.updated_at, ot.total_amount, 
                    u.last_name, u.first_name, country.country_name, city.city_name, hotel.hotel_name, hotel.hotel_star, 
                    ot.adult_count, ot.children_count, ot.children_age, ot.car_price_id,
                    flight.departure_datetime, flight.arrival_datetime, flight.night_count, flight.night_count_plus, flight.direction, trip.insurance
                    FROM order_trip as ot
                    INNER JOIN user as u ON u.id=ot.user_id 
                    INNER JOIN trip_package as trip ON trip.id=ot.trip_id
                    INNER JOIN flight_info as flight ON flight.id=trip.flight_info_id 
                    INNER JOIN hotel_list as hotel ON hotel.id=trip.hotel_list_id 
                    INNER JOIN country_name as country ON country.id=flight.country_id 
                    INNER JOIN city_name as city ON city.id=flight.city_id 
                    WHERE ot.id='.$order_id
                );
        $this->logger->info("ORDER DETAIL RESULT:: ".var_export($queryResult, true));

        $roomResult = $this->connection->fetchAllAssociative('
            SELECT room_name, room_qty 
                FROM order_room
                WHERE order_id='.$order_id
                );
        
        $this->logger->info("ORDER DETAIL RESULT:: ".var_export($roomResult , true));
        
        if($queryResult[0]['car_price_id'] != null && $queryResult[0]['car_price_id'] != ''){
            $carPriceDetail = $this->service->getCarPriceForCreateInvoice($this->connection, Request::createFromGlobals(), $queryResult[0]['car_price_id']);
            $this->logger->info("CAR PRICE INFO RESULT:: ".var_export($carPriceDetail , true));
        }else{
            $carPriceDetail = null;
        }
        
        if ($queryResult) {
            $result = array(
                'orderDetail' => $queryResult,
                'rooms' => $roomResult,
                'car_info' => $carPriceDetail,
            );
        }else{
            $result = array(
                'orderDetail' => null,
                'rooms' => null,
                'car_info' => null,
            );
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $this->render('order/order_detail.html.twig', $result);
    }
    
}
