<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Entity\OrderTrip;
use App\Entity\OrderRoom;

use App\Util\Util;
use App\Util\Bank;

/**
 * Description of ControllerService
 *
 * @author bayarmagnai
 */
class ControllerService {
    
    private $logger;
    private $util;
    public $is_prod;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->util = new Util();
        $this->is_prod = false;
    }
    
    public function sendRequest($headers, $url, $data){
                
        $lendmn = Bank::$LENDMN;  

        if($this->is_prod == true){
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            curl_setopt($curl, CURLOPT_HEADER, 0); 
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, $lendmn['request_uri_lend'].$url);
            curl_setopt($curl, CURLOPT_POST, 0);
            if($data != null){
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            }
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_ENCODING, "UTF-8");
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            curl_setopt($curl, CURLE_HTTP_POST_ERROR, 1);
            curl_setopt($curl, CURLE_HTTP_RETURNED_ERROR, 1);

            curl_setopt($curl, CURLOPT_VERBOSE, true);

            $response = curl_exec($curl);
            curl_close($curl);

            return $response;
        
        }else{
  
            $result = array(
              "code"=> 0,
              "response" => array(
                  "accessToken" => "NzBiYTgyNjc0MmU3Zjk1NDgzYTMxMGU5NDU1OGE1N2Q0NGZjMDBhNjg3NGQ3ODM4NmYwMjFkOTBmZDk0Zjg2MQ",
                  "expiresIn" => 3600,
                  "scope" => "invoices,invoice_edit,walletTransactions,POST:/api/w/invoices,GET:/api/w/invoices,POST:/api/w/pos/invoices,ROLE_USER_PHONE,ROLE_USER_NAME,ROLE_USER_ID"
                  )
            );
            return $result;
        }
    }
    
    public function getLendAccessToken($LENDCODE, $request)
    {
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'lendmn_get_access_token()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));

        $lendmn = Bank::$LENDMN;                
        try{
            if($this->is_prod == true){
                $data = array(
                    'code' => $LENDCODE, 
                    'redirect_uri' => $lendmn['redirect_uri_real'], 
                    'client_id' => $lendmn['client_id'], 
                    'client_secret' => $lendmn['client_secret'], 
                    'grant_type' => $lendmn['grant_type'], 
                );
            }else{
                $data = array(
                    'code' => $LENDCODE, 
                    'redirect_uri' => $lendmn['redirect_uri_local_test'], 
                    'client_id' => $lendmn['client_id'], 
                    'client_secret' => $lendmn['client_secret'], 
                    'grant_type' => $lendmn['grant_type'], 
                );
            }
            
            $this->logger->info("REQUEST DATA:: ".var_export($data, true));

            $headers[] = 'connection: Keep-Alive'; 
            $headers[] = 'content-type: application/x-www-form-urlencoded;'; 
            
            if($this->is_prod == true){
                $response = json_decode($this->sendRequest($headers, '/api/oauth/v2/token', $data), true);
                $this->logger->info("GET LEND ACCESS TOKEN RESULT:: ".var_export($response['response'], true));    

                if ($response && ($response['code'] == 0 )) {
                    return $response['response'];                        
                }
            }else{
                return array(
                    "accessToken" => "NzBiYTgyNjc0MmU3Zjk1NDgzYTMxMGU5NDU1OGE1N2Q0NGZjMDBhNjg3NGQ3ODM4NmYwMjFkOTBmZDk0Zjg2MQ",
                    "expiresIn" => 3600,
                    "scope" => "invoices,invoice_edit,walletTransactions,POST:/api/w/invoices,GET:/api/w/invoices,POST:/api/w/pos/invoices,ROLE_USER_PHONE,ROLE_USER_NAME,ROLE_USER_ID"
                );
            }

        } catch (\Exception $e) {
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN GETTING ACCESS TOKEN FROM LEND: '.$e->getMessage()));
            return null;

        }
        return null;
    }
    
    public function getUserInfo($accessToken, $request)
    {
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'getUserInfo()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));

        try{
            $data = array();
            
            $this->logger->info("REQUEST DATA:: ".var_export($data, true));

            $headers[] = 'connection: Keep-Alive'; 
            $headers[] = 'content-type: application/x-www-form-urlencoded;'; 
            $headers[] = 'x-and-auth-token:'.$accessToken; 
            
            if($this->is_prod == true){
                $response = json_decode($this->sendRequest($headers, '/api/user/info', $data), true);
                $this->logger->info("GET USER INFO RESULT:: ".var_export($response['response'], true));    

                if ($response && ($response['code'] == 0 )) {
                    return $response['response'];                        
                }
            }else{
                return array(
                    'userId' => 'GAS8FjZSle5snF37BSu2uA==',   
                    'firstName' => 'LendMN',   
                    'lastName' => 'Test',   
                    'phoneNumber' => '91113079', 
                );
            }

        } catch (\Exception $e) {
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN GETTING USER INFO FROM LEND: '.$e->getMessage()));
            return null;

        }
        return null;
    }
    
    public function saveAccessToken($lendAccessToken, $userID, $request, $entityManager)
    {
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'saveAccessToken()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        try{
            $now = new \DateTime();
            $token = new AccessToken();
            
            if(!empty($userID)){
                $token->setUserId($userID);
            }
            $token->setAccessToken($lendAccessToken['accessToken']);
            $token->setExpiresIn($lendAccessToken['expiresIn']);
            $token->setScope($lendAccessToken['scope']);
            $token->setCreatedAt($now);
            $token->setUpdatedAt($now);

            $entityManager->persist($token);
            $entityManager->flush();
            
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'SUCCESSFULLY SAVED LEND ACCESS TOKEN. TOKEN ID='.$token->getId()));
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
            return $token->getId();
            
        } catch (\Exception $e) {
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN SAVING TOKEN: '.$e->getMessage()));
            return null;
        }
    }
    
    public function saveUserInfo($userInfo, $request, $entityManager)
    {
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'saveUserInfo()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        try{
            $now = new \DateTime();
            $user = new User();

            if(!empty($userInfo['userId'])){
                $user->setUserAccount($userInfo['userId']);
            }
            if(!empty($userInfo['regNumber'])){
                $user->setRegNumber($userInfo['regNumber']);
            }
            if(!empty($userInfo['firstName'])){
                $user->setFirstName($userInfo['firstName']);
            }
            if(!empty($userInfo['lastName'])){
                $user->setLastName($userInfo['lastName']);
            }
            if(!empty($userInfo['email'])){
                $user->setEmail($userInfo['email']);
            }
            if(!empty($userInfo['phoneNumber'])){
                $user->setPhone($userInfo['phoneNumber']);
            }
            
            $user->setIsActive(1);
//            $user->setPassword($userInfo['']);
            
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);

            $entityManager->persist($user);
            $entityManager->flush();
        
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'SUCCESSFULLY SAVED USER INFO: USER ID='.$user->getId()));
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
            return $user->getId();

        } catch (\Exception $e) {
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN SAVING TOKEN: '.$e->getMessage()));
            return null;
        }
        
    }
    
    public function sendCreateInvoiceRequest($accessToken, $request, $amount)
    {
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'createInvoice()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));

        $lendmn = Bank::$LENDMN;
        try{
            if($this->is_prod == true){
                $data = array(
                    'amount' => $amount, 
                    'duration' => $lendmn['invoice_expires_in'], 
                    'description' => 190505, 
                    'successUri' => $lendmn['redirect_uri_real'].$lendmn['redirect_url'], 
                );
            }else{
                $data = array(
                    'amount' => $amount, 
                    'duration' => $lendmn['invoice_expires_in'], 
                    'description' => 190505, 
                    'successUri' => $lendmn['redirect_uri_local_test'].$lendmn['redirect_url'], 
                );
            }
            $this->logger->info("LEND CREATE INVOICE:: REQUEST DATA=".var_export($data, true));

            $headers[] = 'connection: Keep-Alive'; 
            $headers[] = 'content-type: application/x-www-form-urlencoded;'; 
            $headers[] = 'x-and-auth-token:'.$accessToken; 
            
            if($this->is_prod == true){
                
                $response = json_decode($this->sendRequest($headers, '/api/w/invoices', $data), true);
                $this->logger->info("LEND CREATE INVOICE RESULT:: ".var_export($response, true));    

                if ($response && ($response['code'] == 0 )) {
                    return $response['response'];                        
                }else if($response && ($response['code'] == 401)){

                    $headers[] = 'connection: Keep-Alive'; 
                    $headers[] = 'content-type: application/x-www-form-urlencoded;'; 
                    $headers[] = 'x-and-auth-token:'.$lendmn['ecommer_token']; 

                    $this->logger->info("RE-REQUEST - LEND CREATE INVOICE:: REQUEST HEADER=".var_export($headers, true));
                    $response = json_decode($this->sendRequest($headers, '/api/w/invoices', $data), true);
                    $this->logger->info("LEND CREATE INVOICE RESULT:: ".var_export($response, true));  

                    if ($response && ($response['code'] == 0 )) {
                        return $response['response'];                        
                    }else{
                        return null;
                    }
                }
            }else{
                return array (   
                    'invoiceNumber' => '47d07ce5-79b0-459e-b1a3-0ae5b1b3dbd4',   
                    'amount' => 1200,   
                    'qr_string' => 'https://pay.and.global/i/m/47d07ce5-79b0-459e-b1a3-0ae5b1b3dbd4',   
                    'qr_link' => 'andpay://lend.mn/i/m/47d07ce5-79b0-459e-b1a3-0ae5b1b3dbd4', 
                );
            }
        } catch (\Exception $e) {
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN GETTING USER INFO FROM LEND: '.$e->getMessage()));
            return null;

        }
    }
    
    public function createOrder($request, $entityManager, $data)
    {    
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'createOrder()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        try{
            $now = new \DateTime();
            $order = new OrderTrip();

            if(!empty($data['invoice_num'])){
                $order->setInvoiceNum($data['invoice_num']);
            }
            if(!empty($data['user_id'])){
                $order->setUserId($data['user_id']);
            }
//            if(!empty($data['is_tc_confirmed'])){
//                $order->setIsTcConfirmed($data['is_tc_confirmed']);
//            }
            if(!empty($data['adult_count'])){
                $order->setAdultCount($data['adult_count']);
            }
            if(!empty($data['children_count'])){
                $order->setChildrenCount($data['children_count']);
            }
            if(!empty($data['children_age'])){
//                $order->setChildrenAge(implode(",",$data['children_age']));
                $order->setChildrenAge($data['children_age']);
            }
            if(!empty($data['trip_id'])){
                $order->setTripId($data['trip_id']);
            }
            if(!empty($data['total_amount'])){
                $order->setTotalAmount($data['total_amount']);
            }
            if(!empty($data['order_status'])){
                $order->setOrderStatus($data['order_status']);
            }
            if(!empty($data['ip_address'])){
                $order->setIpAddress($data['ip_address']);
            }
            if(!empty($data['car_price_id'])){
                $order->setCarPriceId($data['car_price_id']);
            }
            
            $order->setCreatedAt($now);
            $order->setUpdatedAt($now);

            $entityManager->persist($order);
            $entityManager->flush();
        
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'SUCCESSFULLY CREATED ORDER: ORDER_ID='.$order->getId()));
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
            return $order->getId();

        } catch (\Exception $e) {
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN CREATING ORDER_TRIP: '.$e->getMessage()));
            return null;
        }
    }
    
    public function addOrderRoom($request, $entityManager, $data)
    {    
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'addOrderRoom()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $now = new \DateTime();
        $resultId = [];
        
        for($i=0; $i < sizeof($data['selected_rooms']); $i++){
            
            try{
                $order = new OrderRoom();

                if(!empty($data['order_id'])){
                    $order->setOrderId($data['order_id']);
                }
                if(!empty($data['selected_rooms'][$i]['room_id'])){
                    $order->setRoomPriceId($data['selected_rooms'][$i]['room_id']);
                }
                if(!empty($data['selected_rooms'][$i]['room_name'])){
                    $order->setRoomName($data['selected_rooms'][$i]['room_name']);
                }
                if(!empty($data['selected_rooms'][$i]['room_qty'])){
                    $order->setRoomQty($data['selected_rooms'][$i]['room_qty']);
                }
                if(!empty($data['selected_rooms'][$i]['room_price_a'])){
                    $order->setRoomPrice($data['selected_rooms'][$i]['room_price_a']);
                }
                if(!empty($data['selected_rooms'][$i]['room_price_bb'])){
                    $order->setRoomPriceBb($data['selected_rooms'][$i]['room_price_bb']);
                }
                if(!empty($data['adult_price'])){
                    $order->setAdultPrice($data['adult_price']);
                }
                if(!empty($data['children_price'])){
                    $order->setChildrenPrice($data['children_price']);
                }
                if(!empty($data['total_price'])){
                    $order->setTotalPrice($data['total_price']);
                }
                if(!empty($data['order_status'])){
                    $order->setOrderStatus($data['order_status']);
                }

                $order->setCreatedAt($now);
                $order->setUpdatedAt($now);

                $entityManager->persist($order);
                $entityManager->flush();

                $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'SUCCESSFULLY ADDED ORDER ROOM: ORDER_ROOM_ID='.$order->getId()));
            
                array_push($resultId, $order->getId());

            } catch (\Exception $e) {
                $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'ERROR OCCURRED WHEN ADDING ORDER_ROOM: '.$e->getMessage()));
            }
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $resultId;
    }
    
    public function updateOrder($request, $entityManager, $data)
    {    
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'updateOrder()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $orderTrip = $entityManager->getRepository(OrderTrip::class)->find($data['order_id']);

        if (!$orderTrip) {
            $this->logger->info('No ORDER TRIP found for order_id='.$data['order_id']);
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
            return null;
        }

        $now = new \DateTime();
        $orderTrip->setInvoiceNum($data['invoice_num']);
        $orderTrip->setOrderStatus($data['order_status']);
        $orderTrip->setUpdatedAt($now);
        $entityManager->flush();
        
        return $orderTrip->getId();
    }
    
    public function updateOrderRoom($request, $entityManager, $data, $order_room_ids)
    {    
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'updateOrderRoom()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $resultId = [];
        
        for($i=0; $i < sizeof($order_room_ids); $i++){
            $orderRoom = $entityManager->getRepository(OrderRoom::class)->find($order_room_ids[$i]);

            if (!$orderRoom) {
                $this->logger->info('No ORDER ROOM found for order_room_id='.$order_room_ids[$i]);
                $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
                break;
            }

            $now = new \DateTime();
            $orderRoom->setPaidPrice($data['paid_amount']);
            $orderRoom->setOrderStatus($data['order_status']);
            $orderRoom->setUpdatedAt($now);
            $entityManager->flush();
            
            array_push($resultId, $orderRoom->getId());
        }
        
        return $resultId;
    }
    
    public function updateOrderByInvoiceNum($request, $entityManager, $data)
    {    
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'updateOrderByInvoiceNum()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
 
        $orderTrips = $entityManager->getRepository(OrderTrip::class)->findBy(array('invoice_num' => $data['invoiceNumber']));

        $this->logger->info("ORDER TRIP:: ".var_export($orderTrips, true));
        
        if (!$orderTrips) {
            $this->logger->info('No ORDER TRIP found for invoiceNumber='.$data['invoiceNumber']);
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
            return null;
        }

        foreach ($orderTrips as $orderTrip) {
            $orderTrip->setOrderStatus('PAID');
            $orderTrip->setUpdatedAt(new \DateTime($data['paidDate']));
            $entityManager->flush();

            $this->logger->info("UPDATE ORDER TRIP RESULT:: ".$orderTrip->getId());
            $order_trip_id = $orderTrip->getId();
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));

        return $order_trip_id;
    }
    
    public function updateOrderRoomByOrderId($request, $entityManager, $data)
    {    
        $this->logger->info('-----------------------------------------------');
        
        $logStep = 0;
        $tagName = 'updateOrderRoomByOrderId()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $now = new \DateTime();
        
        $orderRooms = $entityManager->getRepository(OrderRoom::class)->findBy(array('order_id' => $data['order_id']));

        if (!$orderRooms) {
            $this->logger->info('No ORDER ROOM found for order_id='.$data['order_id']);
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
            return null;
        }

        foreach ($orderRooms as $orderRoom) {
            $orderRoom->setOrderStatus('PAID');
            $orderRoom->setPaidPrice($data['paid_amount']);
            $orderRoom->setUpdatedAt($now);
            $entityManager->flush();

            $this->logger->info("UPDATE ORDER ROOM RESULT:: ".$orderRoom->getId());
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));

        return $data['order_id'];
    }
    
    public function getRoomForCreateInvoice($connection, $request, $room_price_id)
    {
        $this->logger->info('================================================');
        
        $logStep = 0;
        $tagName = 'getRoomForCreateInvoice()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
            SELECT room.id as room_id, room.trip_id, room.price_a, room.price_bb, room.price_discounted, room.price_discounted_bb, trip.is_special
                FROM room_price as room 
                INNER JOIN trip_package as trip 
                ON ( trip.id = room.trip_id )
                WHERE room.id IN ('. implode(",",$room_price_id).')
            ');
 
        $this->logger->info("SELECTED ROOM PRICE RESULT:: ".var_export($queryResult, true));
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));

        if ($queryResult) {
            return $queryResult;
        }else{
            return null;
        }
    }
    
    public function getCarPriceForCreateInvoice($connection, $request, $car_id)
    {
        $this->logger->info('================================================');
        
        $logStep = 0;
        $tagName = 'getCarPriceForCreateInvoice()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
            SELECT car_info_id, car_info.direction as car_direction, way, adult_price, child_price
                FROM car_price
                INNER JOIN car_info 
                ON car_info.id=car_price.car_info_id
                WHERE car_price.id='.$car_id
            );
 
        $this->logger->info("SELECTED CAR PRICE RESULT:: ".var_export($queryResult, true));
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));

        if ($queryResult) {
            return $queryResult;
        }else{
            return null;
        }
    }
    
    public function getFlightForCreateInvoice($connection, $request, $trip_id)
    {
        $this->logger->info('================================================');
        
        $logStep = 0;
        $tagName = 'getFlightForCreateInvoice()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
            SELECT * 
                FROM flight_info as flight 
                INNER JOIN trip_package as trip
                ON (flight.id = trip.flight_info_id)
                WHERE trip.id='.$trip_id
                );
 
        $this->logger->info("SELECTED FLIGHT RESULT:: ".var_export($queryResult, true));

        if ($queryResult) {
            $result = array(
                'flight' => $queryResult,
            );
        }else{
            $result = array(
                'flight' => null,
            );
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $result;
    }
    
    public function getTotalAmount($adult_count, $children_count, $children_ages, $select_rooms)
    {
        $allocatedRooms = [];
        $ageList = explode(',', $children_ages);
        
        
//        Өрөөнүүдийг нэгтгэж бэлтгэх
        for ($i = 0; $i < sizeof($select_rooms); $i++) { // Нийт сонгосон өрөөний төрлийн тоогоор эргэнэ // Standart, Superior etc..
            
            for ($j = 0; $j < intval($select_rooms[$i]['room_qty']); $j++) { // Тухайн төрөлд хэдэн өрөө сонгосон тоогоор // Standart=2, Superior=1 etc..

                array_push($allocatedRooms, array( // Бүх өрөө
                    'room_name' => $select_rooms[$i]['room_name'],
                    'room_price_a' => $select_rooms[$i]['room_price_a'],
                    'room_price_bb' => $select_rooms[$i]['room_price_bb'],
                    'adult_count' => 0,
                    'children_count' => 0,
                    'children_ages' => array(),
                ));
            }
        }
        
        
///////////////////////////////////////////////////////////////
//        Өрөө хуваарилалт - Эхлэл
//        
        $adultMaxLimit = 2;
        $childrenMaxLimit = 2;
        $ageIndex = 0;
                
        for ($i = 0; $i < sizeof($allocatedRooms); $i++) {
            
//            Том хүнийг өрөөнд хуваарилах
            for($j=0; $j < $adultMaxLimit; $j++){
                if($adult_count == 0){
                    break;
                }
                $allocatedRooms[$i]['adult_count'] += 1;
                $this->logger->info("Room No=".$i.", adult in room= ".$allocatedRooms[$i]['adult_count']);
                $adult_count -= 1;
            }
//            Хүүхдүүдийг өрөөнд хуваарилах
            for($j=0; $j < $childrenMaxLimit; $j++){
                if($children_count == 0){
                    break;
                }
                $allocatedRooms[$i]['children_count'] += 1;
                array_push($allocatedRooms[$i]['children_ages'], intval($ageList[$ageIndex++]));
                $this->logger->info("Room No=".$i.", children in room= ".$allocatedRooms[$i]['children_count'].", children age= ".var_export($allocatedRooms[$i]['children_ages'], true));
                $children_count -= 1;
            }
        }
        
        $this->logger->info("ALLOCATED ROOMS:: ".var_export($allocatedRooms, true));
        
//        Өрөө хуваарилалт - Төгсгөл
//////////////////////////////////////////////////////////        
        
///////////////////////////////////////////////////////////////
//        Үнэ тооцох - Эхлэл
//        
//        0-2 нас хүртэл 45% /2B -н 1 том хүний үнийн/ 
//        3-6 нас хүртэл 80% /2B -н 1 том хүний үнийн/
//        7-с дээш нас бол 1А -н 1 том хүний үнээр тооцно.
        
        $adult_amount = 0;
        $children_amount = 0;

        for ($i = 0; $i < sizeof($allocatedRooms); $i++) {
            if(intval($allocatedRooms[$i]['adult_count']) > 1){ // 1 өрөөнд 2 том хүн байвал
                
                $adult_amount += intval($allocatedRooms[$i]['room_price_bb']);
                $this->logger->info("adult_amount=".$adult_amount);
                
                for ($j = 0; $j < $allocatedRooms[$i]['children_count']; $j++) {
                    if($allocatedRooms[$i]['children_ages'][$j] < 3){                                                           // 45%
                        $children_amount += ((intval($allocatedRooms[$i]['room_price_bb']) / 2) * 0.45 );
                    }else if($allocatedRooms[$i]['children_ages'][$j] <= 3 && $allocatedRooms[$i]['children_ages'][$j] < 7){    // 80%
                        $children_amount += ((intval($allocatedRooms[$i]['room_price_bb']) / 2) * 0.80 );
                    }else{                                                                                              // 7-с дээш бол том хүний үнээр
                        $children_amount += (intval($allocatedRooms[$i]['room_price_bb']) / 2);
                    }
                    
                    $this->logger->info("Room No=".$i.", child No=".$j.", children_amount=".$children_amount);
                }
            }else{  // 1 өрөөнд 1 том хүн байвал
                
                $adult_amount += intval($allocatedRooms[$i]['room_price_a']);
                $this->logger->info("adult_amount=".$adult_amount);
                
                for ($j = 0; $j < $allocatedRooms[$i]['children_count']; $j++) {
                    if($allocatedRooms[$i]['children_ages'][$j] < 3){                                                           // 45%
                        $children_amount += (intval($allocatedRooms[$i]['room_price_a']) * 0.45 );
                    }else if($allocatedRooms[$i]['children_ages'][$j] <= 3 && $allocatedRooms[$i]['children_ages'][$j] < 7){    // 80%
                        $children_amount += (intval($allocatedRooms[$i]['room_price_a']) * 0.80 );
                    }else{                                                                                              // 7-с дээш бол том хүний үнээр
                        $children_amount += intval($allocatedRooms[$i]['room_price_a']);
                    }
                    $this->logger->info("Room No=".$i.", child No=".$j.", children_amount=".$children_amount);
                }
            }
        }
        
//        Үнэ тооцох - Төгсгөл
//////////////////////////////////////////////////////////        

        $result = array(
            'adult_amount' => $adult_amount,
            'children_amount' => $children_amount,
            'total_amount' => $adult_amount + $children_amount,
        );
       
        return $result;
    }
    
    public function getUserId($connection, $request, $userID){
        $this->logger->info('================================================');
        
        $logStep = 0;
        $tagName = 'getUserId()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
            SELECT id 
                FROM user 
                WHERE user_account="'.$userID.'"'
                );
 
        $this->logger->info("SELECTED USER ID RESULT:: ".var_export($queryResult, true));

        if ($queryResult) {
            $result = array(
                'user_id' => $queryResult,
            );
        }else{
            $result = array(
                'user_id' => null,
            );
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $result;
    }
    
    public function getUserEmail($connection, $request, $userID){
        $this->logger->info('================================================');
        
        $logStep = 0;
        $tagName = 'getUserEmail()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
            SELECT id, email 
                FROM user 
                WHERE user_account="'.$userID.'"'
                );
 
        $this->logger->info("SELECTED USER ID, EMAIL RESULT:: ".var_export($queryResult, true));
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $queryResult;
    }
    
    public function getOrderDetail($connection, $request, $order_id)
    {
        $this->logger->info('================================================');

        $logStep = 0;
        $tagName = 'getOrderDetail()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
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

        $roomResult = $connection->fetchAllAssociative('
            SELECT room_name 
                FROM order_room
                WHERE order_id='.$order_id
                );
        
        $this->logger->info("ORDER DETAIL RESULT:: ".var_export($roomResult , true));

        if ($queryResult) {
            $result = array(
                'orderInfo' => $queryResult[0],
                'roomInfo' => $roomResult,
            );
        }else{
            $result = array(
                'orderInfo' => null,
                'roomInfo' => null,
            );
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
 
        return $result;
    }
    
    public function getOrderListByUserId($connection, $request, $user_id)
    {
        $this->logger->info('================================================');

        $logStep = 0;
        $tagName = 'getOrderDetail()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $queryResult = $connection->fetchAllAssociative('
            SELECT ot.id as order_trip_id, trip.is_special, ot.order_status, ot.updated_at, 
                    country.country_name, city.city_name
                    FROM order_trip as ot
                    INNER JOIN user as u ON u.id=ot.user_id 
                    INNER JOIN trip_package as trip ON trip.id=ot.trip_id
                    INNER JOIN flight_info as flight ON flight.id=trip.flight_info_id 
                    INNER JOIN hotel_list as hotel ON hotel.id=trip.hotel_list_id 
                    INNER JOIN country_name as country ON country.id=flight.country_id 
                    INNER JOIN city_name as city ON city.id=flight.city_id 
                    WHERE u.id='.$user_id.' /*and ot.order_status != "PROCESSING"*/
                    ORDER by ot.id DESC'
                );
            
        $this->logger->info("ORDER LIST RESULT:: ".var_export($queryResult, true));
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
 
        return $queryResult;
    }
    
}
