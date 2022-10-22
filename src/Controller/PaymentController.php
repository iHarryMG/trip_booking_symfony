<?php

namespace App\Controller;

use Curl;
use App\Util\Bank;
use App\Util\Util;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Entity\PassportPhoto;
use Doctrine\DBAL\Connection;
// use Symfony\Bundle\MonologBundle\SwiftMailer;

use App\Form\PassportPhotoType;
use App\Service\ControllerService;
use Symfony\Component\Mime\Message;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PaymentController extends AbstractController
{
    private $logger;
    private $util;
    private $requestStack;
    private $service;
    private $connection;
    // private $mailer;
    
    public function __construct(LoggerInterface $logger, RequestStack $requestStack, Connection $connection/*, \Swift_Mailer $mailer*/)
    {
        $this->logger = $logger;
        $this->util = new Util();
        $this->requestStack = $requestStack;
        $this->service = new ControllerService($logger);
        $this->connection = $connection;
        // $this->mailer = $mailer;
    }
    
    
    /**
     * @Route("/pay/lendmn", name="lendmn_pay")
     */
    public function payLendMn(ManagerRegistry $doctrine)
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'lendmn_pay()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));

        // if($this->service->is_prod == true){
            $data = null;
        
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json') || $this->service->is_prod != true) {

                $result = null;
                if($this->service->is_prod == true){
                    $this->logger->info("WEBHOOK CALLBACK RESULT:: ".var_export($request->getContent(), true));
                    $response = json_decode($request->getContent(), true);
                    $request->request->replace(is_array($response) ? $response : array());
                    $data = $response['data'];
                    $this->logger->info("WEBHOOK CALLBACK RESULT:: ".var_export($response, true));
                    // validate private key with public key
                    $result = $this->util->validateKey($response, $this->logger);
                    $this->logger->info("VALIDATE RESULT:: ".var_export($result, true));
                }else{
                    $this->logger->info("WEBHOOK CALLBACK RESULT::  ".var_export($request->get('data'), true));
                    $data = $request->get('data');
                    $result = 1;
                    $this->logger->info("WEBHOOK CALLBACK RESULT::DATA:: ".var_export($data, true));
                }
                
                if ($result) { // хэрэв төлбөр бүрэн амжилттай төлөгдөж дууссан бол order-н төлөвийг бүрэн дууссан болгох. PAID.
                    $this->logger->info("VALID");
                    
                    if($data['status'] === '1'){ // PAID
                    
                        $order_id = $this->service->updateOrderByInvoiceNum($request, $doctrine->getManager(), $data);
                            
                        $param = array(
                            'order_id' => $order_id,
                            'paid_amount' => $data['amount'],
                            'order_status' => 'PAID',
                        );
                                
                        //захиалга update
                        $order_room_id = $this->service->updateOrderRoomByOrderId($request, $doctrine->getManager(), $param);

                        $this->logger->info("order id=".$order_id.", order room ids=".var_export($order_room_id, true));
                        
                        if($order_id != null){ // ORDER UPDATED
                            
                            // $this->sendEmail($this->mailer, $order_id, $param); // Swift mailer ажиллахгүй байгаа тул түр хаав.
                            
                            $payment_result = array(
                                'payment_status' => 1,
                                'paid_amount' => $data['amount'],
                                'description' => 'Захиалга амжилттай бүртгэгдлээ',
                            );
                        }else{ // INVOICE COULDN'T CREATED
                            $payment_result = array(
                                'payment_status' => -1,
                                'paid_amount' => null,
                                'description' => 'Төлбөрийн нэхэмжлэх үүсээгүй байна',
                            );
                        }
                    }elseif ($data['status'] === '2'){ // CANCELLED
                        $payment_result = array(
                            'payment_status' => 2,
                            'paid_amount' => null,
                            'description' => 'Төлбөр төлөлт цуцлагдсан байна',
                        );
                    }elseif ($data['status'] === '3'){ // EXPIRED
                        $payment_result = array(
                            'payment_status' => 3,
                            'paid_amount' => null,
                            'description' => 'Төлбөр хийх хугацаа дууссан байна',
                        );
                    }elseif ($data['status'] === '0'){ // PENDING
    //                    invoice detail-г лэндээс авч төлөвийг дахин шалгах
                        
                        $payment_result = array(
                            'payment_status' => 0,
                            'paid_amount' => null,
                            'description' => 'Төлбөрийн мэдээллийг дахин шалгаж байна...',
                        );
                    }else{
                        $payment_result = array(
                            'payment_status' => $data['status'],
                            'paid_amount' => null,
                            'description' => 'Төлбөр төлөлт амжилтгүй боллоо.',
                        );
                    }
                    
                } else {
                    $this->logger->info("INVALID SIGNATURE:: ".$response['signature']);
                    $payment_result = array(
                        'payment_status' => 999,
                        'paid_amount' => null,
                        'description' => 'Хүчингүй хариу. Төлбөр хийгдсэнгүй.',
                    );
                }

            }else{
               $payment_result = array(
                   'payment_status' => null,
                   'paid_amount' => null,
                   'description' => 'Контент хоосон. Төлбөр төлөлтөд алдаатай хариу ирлээ.',
               );
                // $payment_result = array(
                //     'payment_status' => 1,
                //     'paid_amount' => $data['amount'],
                //     'description' => 'Захиалга амжилттай бүртгэгдлээ',
                // );
                $this->logger->info("PAYMENT SUCCESSFULLY COMPLETED BUT CONTENT IS NULL. TRY TO CHECK INVOICE DETAIL.");
            }

            $this->logger->info("PAYMENT CONFIRMATION RESULT:: ".var_export($payment_result, true));
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
            
        // }else{
        //     $payment_result = array(
        //         'payment_status' => 1,
        //         'paid_amount' => 100000,
        //         'description' => 'Booking successfully done.',
        //     );
        //     $this->logger->info("LOCAL ENV DUMMY RESPONSE SENT.");
        // }
        
        return $this->render('payment/order_result.html.twig', $payment_result);
    }
    
    public function sendEmail($mailer, $order_id, $payment_info)
    {
        $orderDetail = $this->service->getOrderDetail($this->connection, Request::createFromGlobals(), $order_id);
        
        $carPriceDetail = null;
        if($orderDetail['orderInfo']['car_price_id'] != null){
            $carPriceDetail = $this->service->getCarPriceForCreateInvoice($this->connection, Request::createFromGlobals(), $orderDetail['orderInfo']['car_price_id']);
        }
        
        $mailParam = [
                'updated_at' => $orderDetail['orderInfo']['updated_at'],
                'last_name' => $orderDetail['orderInfo']['last_name'],
                'first_name' => $orderDetail['orderInfo']['first_name'],
                'country_name' => $orderDetail['orderInfo']['country_name'],
                'city_name' => $orderDetail['orderInfo']['city_name'],
                'hotel_name' => $orderDetail['orderInfo']['hotel_name'],
                'hotel_star' => $orderDetail['orderInfo']['hotel_star'],
                'adult_count' => $orderDetail['orderInfo']['adult_count'],
                'children_count' => $orderDetail['orderInfo']['children_count'],
                'children_age' => $orderDetail['orderInfo']['children_age'],
                'departure_datetime' => $orderDetail['orderInfo']['departure_datetime'],
                'arrival_datetime' => $orderDetail['orderInfo']['arrival_datetime'],
                'direction' => $orderDetail['orderInfo']['direction'],
                'room_info' => $orderDetail['roomInfo'],
                'total_amount' => $payment_info['paid_amount'],
                'order_status' => $payment_info['order_status'],
                'car_order' => $carPriceDetail,
            ];
        
        $message = new \Swift_Message('Шинэ захиалга');
        $headers = $message->getHeaders();
        $headers->setCharset('utf-8');
        
        $headers->addIdHeader('Message-ID', time() . '.' . uniqid('lsrtm') . '@leisure-team.com');
        $headers->addTextHeader('MIME-Version', '1.0');
        $headers->addTextHeader('Content-Type', 'text/html');
        $headers->addTextHeader('X-Mailer', 'PHP v' . phpversion());
        $message->setFrom('contact@leisure-team.com')
        ->setTo('piraterdene0220@gmail.com')
        ->setBcc('commanmn@gmail.com')
        ->setBody(
            $this->renderView(
                'email/confirmation.html.twig',
                $mailParam
            ),
            'text/html'
        );
        
        $sentResult = $mailer->send($message);
        
        $this->logger->info("MAIL SENT RESULT:: ".var_export($sentResult, true));
                
        $result = array(
            'result' => $sentResult,
        );
       
        return $this->json($result);
    }

    
    /**
     * @Route("/invoice/create", name="create_invoice")
     */
    public function createInvoice(ManagerRegistry $doctrine)
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'create_invoice()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $session = $this->requestStack->getSession();
        if($session->has('user_session')){
            $userID = $session->get('user_session')['user_id'];
            $accessToken = $session->get('user_session')['access_token'];
            
            $trip_id = $request->get('trip_id');
            $select_rooms = $request->get('select_rooms');
            $adult_count = $request->get('adult_count');
            $children_count = $request->get('children_count');
            $children_age = $request->get('children_age');
            $car_id = $request->get('car_id');
            
            $this->logger->info("USER SESSON:: USER ACCOUNT ID=".$userID.", ACCESS TOKEN=".$accessToken);
            $this->logger->info("PATAMETER:: trip_id=".$trip_id
                    .", adult_count=".$adult_count
                    .", children_count=".$children_count
                    .", children_age=".var_export($children_age, true)
                    .", selected_rooms=".var_export($select_rooms, true)
                    .", car_id=".$car_id
                    );

//            нислэгийн бүх мэдээлэл авчрах         
            $flights = $this->service->getFlightForCreateInvoice($this->connection, $request, $trip_id);
            
//            өрөөний бүх мэдээлэл авчрах
            $room_ids = array();
            for($i=0; $i< sizeof($select_rooms); $i++ ){
                array_push($room_ids, $select_rooms[$i]['room_id']);
            }
            $rooms = $this->service->getRoomForCreateInvoice($this->connection, $request, $room_ids);
            
//            үнэ тооцоолох
            $amount_result = $this->service->getTotalAmount($adult_count, $children_count, $children_age, $select_rooms);
            $this->logger->info("ORDER TOTAL AMOUNT RESULT:: ".var_export($amount_result, true));
            
            $total_amount = $amount_result['total_amount'];
            
            $car_adult_amount = null;
            $car_child_amount = null;
            $car_info = null;
            if($car_id != null){
                $car_info = $this->service->getCarPriceForCreateInvoice($this->connection, $request, $car_id);

                $ages = explode(',',$children_age);
                $car_child_count = 0;
                for($i=0; $i < sizeof($ages); $i++){
                    if(intval($ages[$i] > 1) ){
                        $car_child_count += 1;
                    }
                }

                $car_adult_amount = intval($car_info[0]['adult_price']) * $adult_count;
                $car_child_amount = intval($car_info[0]['child_price']) * $car_child_count;
                $total_amount = $total_amount + $car_adult_amount +$car_child_amount;
                $this->logger->info("ORDER TOTAL AMOUNT INCLUDING CAR PRICE:: ".$total_amount);
            }
            
//            User table-н ID утга авах
            $user = $this->service->getUserId($this->connection, $request, $userID);
            
            $data = array(
                'user_id' => $user['user_id'][0]['id'],
                'adult_count' => $adult_count,
                'children_count' => $children_count,
                'children_age' => $children_age,
                'trip_id' => $trip_id,
                'car_price_id' => $car_id,
                'total_amount' => $total_amount,
                'order_status' => 'CREATED',
                'ip_address' => $request->getClientIp(),
            );

            //захиалга үүсгэх
            $order_id = $this->service->createOrder($request, $doctrine->getManager(), $data);
            
            // order_item үүсгэх - Хэрэггүй болсон.
            
            $dataOrderRoom = array(
                'order_id' => $order_id,
                'selected_rooms' => $select_rooms,
                'adult_price' => $amount_result['adult_amount'],
                'children_price' => $amount_result['children_amount'],
                'total_price' => $amount_result['total_amount'],
                'paid_price' => null,
                'order_status' => 'CREATED',
            );
            // order_room үүсгэх
            $order_room_ids = $this->service->addOrderRoom($request, $doctrine->getManager(), $dataOrderRoom);
            $this->logger->info("ORDER ROOM IDS RESULT:: ".var_export($order_room_ids, true));
            
            $invoiceResult = null;
                    
            if($this->service->is_prod == true){
                
            //lend invoice create болвол - захиалга үүсгэх
                $invoiceResult = $this->service->sendCreateInvoiceRequest($accessToken, $request, $total_amount);
                $this->logger->info("LEND CREATE INVOICE RESULT:: ".var_export($invoiceResult, true));
            }else{
                $invoiceNumber = $this->util->generateCode(16);
                $invoiceResult = array(
                    'invoiceNumber' => $invoiceNumber,
                    'amount' => $total_amount,
                    'qr_string' => 'https://pay.and.global/i/m/'.$invoiceNumber,
                    'qr_link' => 'andpay://lend.mn/i/m/'.$invoiceNumber,
                );
            }
            
            if($invoiceResult != null){
                $data = array(
                    'order_id' => $order_id,
                    'invoice_num' => $invoiceResult['invoiceNumber'],
                    'paid_amount' => $invoiceResult['amount'],
                    'order_status' => 'PROCESSING',
                );
                //захиалга update
                $order_id = $this->service->updateOrder($request, $doctrine->getManager(), $data);
                $order_room_ids = $this->service->updateOrderRoom($request, $doctrine->getManager(), $data, $order_room_ids);
                
                $result = array(
                    'invoiceResult' => $invoiceResult,
                    'flights' => $flights,
                    'rooms' => $rooms,
                    'adult_count' => $adult_count,
                    'children_count' => $children_count,
                    'children_age' => $children_age,
                    'car_info' => $car_info != null ? $car_info[0] : $car_info,
                    'car_total_amount' => ($car_adult_amount + $car_child_amount),
                    'user_id' => $user['user_id'][0]['id']
                );
            }else{
                $result = array(
                    'invoiceResult' => null,
                    'flights' => null,
                    'rooms' => null,
                    'adult_count' => null,
                    'children_count' => null,
                    'children_age' => null,
                    'car_info' => null,
                    'car_total_amount' => null,
                    'user_id' => null,
                );
            }
            
        }else{
            $this->logger->info("NO USER SESSION HERE.");
            
            $result = array(
                'invoiceResult' => null,
                'flights' => null,
                'rooms' => null,
                'adult_count' => null,
                'children_count' => null,
                'children_age' => null,
                'car_info' => null,
                'car_total_amount' => null,
                'user_id' => null,
            );
        }
               
        return $this->json($result);
    }
    
    /**
     * @Route("/pay/confirm", name="payment_confirm")
     */
    public function confirmPayment()
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'payment_confirm';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
 
        $total_amount = $request->get('total_amount');
        $adult_count = $request->get('adult_count');
        $children_count = $request->get('children_count');
        $children_age = $request->get('children_age');
        $invoice_number = $request->get('invoice_number');
        $qr_string = $request->get('qr_string');
        $qr_link = $request->get('qr_link');
        $trip_id = $request->get('trip_id');
        $hotel_id = $request->get('hotel_id');
        $is_special = $request->get('is_special');
        $car_adult_price = $request->get('car_adult_price');
        $car_child_price = $request->get('car_child_price');
        $car_way = $request->get('car_way');
        $car_direction = $request->get('car_direction');
        $car_total_amount = $request->get('car_total_amount');
        
        
        $this->logger->info("PASSING PARAMETER:: total_amount=".$total_amount
                    .", adult_count=".$adult_count
                    .", children_count=".$children_count
                    .", children_age=".$children_age
                    .", invoice_number=".$invoice_number
                    .", qr_string=".$qr_string
                    .", qr_link=".$qr_link
                    .", trip_id=".$trip_id
                    .", hotel_id=".$hotel_id
                    .", is_special=".$is_special
                    .", car_adult_price=".$car_adult_price
                    .", car_child_price=".$car_child_price
                    .", car_way=".$car_way
                    .", car_direction=".$car_direction
                    .", car_total_amount=".$car_total_amount
                    );
                
        $user_info = null;
        
        $session = $this->requestStack->getSession();
        if($session->has('user_session')){
            if($this->service->is_prod == true){ 
                $userID = $session->get('user_session')['user_id'];
                $accessToken = $session->get('user_session')['access_token'];
            }else{
                $userID = 'GAS8FjZSle5snF37BSu2uA==';
                $accessToken = 'NzBiYTgyNjc0MmU3Zjk1NDgzYTMxMGU5NDU1OGE1N2Q0NGZjMDBhNjg3NGQ3ODM4NmYwMjFkOTBmZDk0Zjg2MQ';
            }
            $this->logger->info("USER SESSON:: USER ACCOUNT ID=".$userID.", ACCESS TOKEN=".$accessToken);
            
//            User table-н ID утга авах
            $user_info = $this->service->getUserEmail($this->connection, $request, $userID);
            $this->logger->info("USER INFO:: ".var_export($user_info, true));
            
        }else{
            $user_info = null;
        }
        
        $tripResult = $this->connection->fetchAllAssociative('
                SELECT 
                    country.country_name, city.city_name
                    FROM `trip_package` as trip 
                    INNER JOIN flight_info as flight on flight.id=trip.flight_info_id
                    INNER JOIN country_name as country on country.id=flight.country_id
                    INNER JOIN city_name as city on city.id=flight.city_id
                    WHERE trip.id='.$trip_id
                );
        $this->logger->info("TRIP RESULT:: ".var_export($tripResult, true));
        
        if($car_total_amount != null && $car_total_amount != ''){
            $trip_amount = $total_amount - $car_total_amount;
        }else{
            $trip_amount = $total_amount;
            $car_total_amount = 0;
        }
        
        $result = array(
            "adult_count" => $adult_count,
            "children_count" => $children_count,
            "children_age" => $children_age,
            "invoice_number" => $invoice_number,
            "qr_string" => $qr_string,
            "qr_link" => $qr_link,
            "trip_id" => $trip_id,
            "hotel_id" => $hotel_id,
            "is_special" => $is_special,
            
            "trip_info" => $tripResult[0],
            
            "car_way" => $car_way,
            "car_direction" => $car_direction,
            "user_info" => $user_info,
            
            "total_amount" => $total_amount,
            "trip_amount" => $trip_amount,
            "car_total_amount" => $car_total_amount,

            "is_prod" => $this->service->is_prod,
        );
       
        return $this->render('payment/order_confirm.html.twig', $result);
    }
    
    /**
     * @Route("/email/update", name="update_email")
     */
    public function updateUserEmail(ManagerRegistry $doctrine)
    {    
        $this->logger->info('-----------------------------------------------');
        $request = Request::createFromGlobals();
        $logStep = 0;
        $tagName = 'updateUserEmail()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $id = $request->get('user_id');
        $email = $request->get('email');
        $this->logger->info("PARAMETER:: USER ID=".$id.", EMAIL=".$email);
        
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->logger->info('No USER found for id='.$id);
            $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
            $result = array(
                'result' => 0,
            );
               
            return $this->json($result);
        }
        
        $now = new \DateTime();
        $user->setEmail($email);
        $user->setUpdatedAt($now);
        $entityManager->flush();

        $this->logger->info("UPDATE USER EMAIL RESULT:: USER ID=".$user->getId().", EMAIL=".$email);
            
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        $result = array(
                'result' => $user->getId(),
            );
               
        return $this->json($result);
    }
    
    /**
     * @Route("/photo", name="photo")
     */
    public function photo()
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'photo()';
        $logId = 'client_ip='.$request->getClientIp();
        $params = $request->get('params');
        $adult_count = $request->get('adult_count');
        $children_count = $request->get('children_count');
        $children_age = $request->get('children_age');
        $trip_id = $request->get('trip_id');
        $hotel_id = $request->get('hotel_id');
        $is_special = $request->get('is_special');
        
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'PARAMETERS: ADULT COUNT='.$adult_count
                .' CHILDREN COUNT='.$children_count
                .' CHILDREN AGES='.$children_age
                .' TRIP ID='.$trip_id
                .' PARAMS='.$params
                ));
        
        $userAgent = $request->headers->get('User-Agent');
        $regResult = preg_match("/Android/i", $userAgent);
        $this->logger->info("Regex result=" .$regResult." User agent: " .$userAgent );
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));

        $result = array(
            'upload_success' => null,
            'user_id' => null,
            'photo_id' => null,
            'photo_path' => null,
            'photo_names' => null,
            'adult_count' => $adult_count,
            'children_count' => $children_count,
            'children_age' => $children_age,
            'person_count' => $adult_count + $children_count,
            'params' => $params,
            'trip_id' => $trip_id,
            'hotel_id' => $hotel_id,
            'is_special' => $is_special,
        );
        
        if($regResult > 0){
            return $this->render('payment/photo_info.html.twig', $result);
        }else{
            return $this->render('payment/photo_upload.html.twig', $result);
        }
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    
    /**
     * @Route("/photo/upload", name="upload_photo")
     */
    public function uploadPhoto(ManagerRegistry $doctrine)
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'upload_photo()';
        $logId = 'client_ip='.$request->getClientIp();
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
//        $photo_count = $this->util->isNull($request->get('photo_count'));      
//        $trip_id = $this->util->isNull($request->get('trip_id'));  
        $photo_count = $request->get('photo_count');
        $trip_id = $request->get('trip_id');
        
        $adult_count = $request->get('adult_count');
        $children_count = $request->get('children_count');
        $children_age = $request->get('children_age');
        $hotel_id = $request->get('hotel_id');
        $is_special = $request->get('is_special');
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 
                'PARAMETERS: PHOTO COUNT='.$photo_count
                .', TRIP ID='.$trip_id
                .', PARAMS ID='.$request->get('params')
                .', HOTEL ID='.$request->get('hotel_id')
                .', IS SPECIAL='.$request->get('is_special')
                .', ADULT COUNT='.$request->get('adult_count')
                .', CHILDREN COUNT='.$request->get('children_count')
                .', CHILDREN AGE='.$request->get('children_age')
                ));

        $session = $this->requestStack->getSession();
        $userID = $session->get('user_session')['user_id'];
        $user = $this->service->getUserId($this->connection, $request, $userID);
        $user_id = $user['user_id'][0]['id'];
        
        $this->logger->info("USER ID:: ".$user_id);
        
        $now = new \DateTime();
        $photo_ids = [];
        $photo_names = [];
        
        if($photo_count != null){
            try {
                for($i=1; $i <=$photo_count; $i++ ){
                    
                    $photo = new PassportPhoto();
                    $file = $request->files->get('passport_photo_'.$i);
                    if($file){
                        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                        $file->move(
                            $this->getParameter('passport_images_directory'),
                            $fileName
                        );

                        $photo->setPassportPhoto($fileName);
                        $photo->setUserId($user_id);
                        $photo->setTripId($trip_id);
                        $photo->setCreatedAt($now);
                        $photo->setUpdatedAt($now);

                        $this->logger->info("USER PASSPORT PHOTO RESULT:: ".var_export( $photo, true));

                        $em = $doctrine->getManager();
                        $em->persist($photo);
                        $em->flush();

                        array_push($photo_ids, $photo->getId());
                        array_push($photo_names, $fileName);
                    }
                }
                
            } catch (FileException $e) {
                $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'EXCEPTION OCCURRED!'));
                return $this->render('payment/photo_upload.html.twig', [
                        'upload_success' => false,
                        'adult_count' => $request->get('adult_count'),
                        'children_count' => $request->get('children_count'),
                        'children_age' => $request->get('children_age'),
                        'trip_id' => $request->get('trip_id'),
                        'hotel_id' => $request->get('hotel_id'),
                        'is_special' => $request->get('is_special'),
                    ]);
            }
        }
        
//        return $this->forward('App\Controller\MainController::home');
        
        
        return $this->render('payment/photo_upload.html.twig', [
            'upload_success' => true,
            'user_id' => $user_id,
            'photo_id' => implode(',',$photo_ids),
            'photo_count' => $photo_count,
            'person_count' => $request->get('person_count'),
            'adult_count' => $request->get('adult_count'),
            'children_count' => $request->get('children_count'),
            'children_age' => $request->get('children_age'),
            'params' => $request->get('params'),
            'trip_id' => $request->get('trip_id'),
            'hotel_id' => $request->get('hotel_id'),
            'is_special' => $request->get('is_special'),
        ]);
    }
    
    
    
    /**
     * @Route("/test", name="test_function")
     */
    public function testFunction()
    {
        return $this->render('main/landing.html.twig');
    }
    
    
}
