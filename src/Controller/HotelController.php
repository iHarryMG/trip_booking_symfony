<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\DBAL\Connection;

use Psr\Log\LoggerInterface;
use App\Util\Util;
use App\Service\ControllerService;


class HotelController extends AbstractController
{
    private $logger;
    private $util;
    private $connection;
    private $service;
    private $requestStack;

    public function __construct(LoggerInterface $logger, Connection $connection, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->connection = $connection;
        $this->util = new Util();
        $this->service = new ControllerService($logger);
        $this->requestStack = $requestStack;
    }
    
    /**
     * @Route("/hotel/list/{city_id}/{country_id}", name="get_hotel")
     */
    public function getHotelList($city_id, $country_id)
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'get_hotel(city_id, country_id)';
        $logId = 'client_ip='.$request->getClientIp();
        
        $date = $this->util->isNull($request->get('date'));
        if($date != null){
            $where_clause = 'and flight.departure_datetime like "'.$date.'%"';
        }else{
            $where_clause = '';
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $this->logger->info("DATE PARAMETER=".$date);
        $this->logger->info("WHERE CLAUSE=".$where_clause);
        
        $repoResult = $this->connection->fetchAllAssociative('
                SELECT 
                        trip.hotel_list_id as hotel_id, trip.id as trip_id, 
                        photo.hotel_img, hotel.hotel_star, hotel.hotel_name, 
                        flight.departure_datetime, flight.arrival_datetime, 
                        flight.night_count, flight.night_count_plus, room.price_bb, trip.is_special
                FROM flight_info as flight 
                INNER JOIN trip_package as trip 
                INNER JOIN hotel_list as hotel 
                INNER JOIN room_price as room 
                INNER JOIN hotel_photo as photo  

                ON(
                trip.flight_info_id=flight.id AND
                hotel.id=trip.hotel_list_id AND photo.hotel_list_id=trip.hotel_list_id
                )

                WHERE flight.country_id='.$country_id.' and flight.city_id='.$city_id.'
                and room.trip_id = trip.id 
                and photo.is_active=2 
                and photo.is_special=0
                and trip.is_active=1
                '.$where_clause.'
                GROUP BY room.trip_id
                ORDER BY room.price_bb ASC
            ');

        $this->logger->info("REPO RESULT:: ".var_export($repoResult, true));
        
//        $date_set = $this->connection->fetchAllAssociative('
//            SELECT departure_datetime FROM flight_info
//                WHERE country_id='.$country_id.' and city_id='.$city_id.'
//                GROUP BY departure_datetime
//                ORDER by departure_datetime ASC
//            ');
        
        $date_set = $this->connection->fetchAllAssociative('
            SELECT flight.departure_datetime 
                FROM flight_info as flight 
                INNER JOIN trip_package as trip 
                INNER JOIN hotel_list as hotel 
                INNER JOIN room_price as room 
                INNER JOIN hotel_photo as photo  

                ON(
                trip.flight_info_id=flight.id AND
                hotel.id=trip.hotel_list_id AND photo.hotel_list_id=trip.hotel_list_id
                )

                WHERE flight.country_id='.$country_id.' and flight.city_id='.$city_id.'
                and room.trip_id = trip.id 
                and photo.is_active=2 
                and photo.is_special=0
                and trip.is_active=1
                GROUP BY flight.departure_datetime
                ORDER by flight.departure_datetime ASC
            ');
        
        
//        foreach($repoResult as $key => $item){
//            array_push($date_set, $item['departure_datetime']);
//        }
//        $date_set = array_unique($date_set);
        
        $this->logger->info("start_date RESULT:: ".var_export($date_set, true));
        
        if ($repoResult) {
            $result = array(
                'result' => $repoResult,
                'date_set' => $date_set,
                'country_id' => $country_id,
                'city_id' => $city_id,
            );
        }else{
            $result = array(
                'result' => null,
            );
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $this->render('hotel/hotel_list.html.twig', $result);
    }
    
    /**
     * @Route("/hotel/detail/{hotel_id}/{trip_id}/{is_special}", name="get_hotel_detail")
     */
    public function getHotelDetail($hotel_id, $trip_id, $is_special)
    {  
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'get_hotel_detail(hotel_id)';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $photoResult = $this->connection->fetchAllAssociative('
            SELECT *
                FROM hotel_photo
                WHERE hotel_list_id = '.$hotel_id.'
                ORDER BY id ASC
            ');
        $this->logger->info("HOTEL PHOTO RESULT:: ".var_export($photoResult, true));
        
        $serviceResult = $this->connection->fetchAllAssociative('
                SELECT 
                    trip.id as trip_id, trip.hotel_list_id as hotel_id, 
                    hotel.hotel_name, hotel.hotel_star, trip.flight_info_id as flight_id, 
                    trip.meal, trip.insurance, trip.welcome_service, trip.is_special, 
                    flight.country_id, country.country_name, flight.city_id, city.city_name, flight.direction, 
                    DATE_FORMAT(flight.departure_datetime, "%Y.%m.%d") as departure_datetime, DATE_FORMAT(flight.arrival_datetime, "%Y.%m.%d") as arrival_datetime,
                    flight.night_count, flight.night_count_plus
                    FROM `trip_package` as trip 
                    INNER JOIN hotel_list as hotel on hotel.id=trip.hotel_list_id
                    INNER JOIN flight_info as flight on flight.id=trip.flight_info_id
                    INNER JOIN country_name as country on country.id=flight.country_id
                    INNER JOIN city_name as city on city.id=flight.city_id
                    WHERE trip.id='.$trip_id .' and trip.is_special='.$is_special
                );
        $this->logger->info("SERVICE INFO RESULT:: ".var_export($serviceResult, true));
        
        $roomResult = $this->connection->fetchAllAssociative('
            SELECT  trip.id as trip_id, trip.is_special, 
                    flight.night_count, flight.night_count_plus, 
                    room_name.room_name, room.price_bb, room.price_discounted_bb
                FROM trip_package as trip 
                INNER JOIN flight_info as flight 
                INNER JOIN room_price as room 
                INNER JOIN room_name as room_name 
                ON (flight.id=trip.flight_info_id AND room.trip_id=trip.id AND room_name.id=room.room_name_id)
                WHERE trip.id='.$trip_id
            );
        $this->logger->info("HOTEL ROOMS RESULT:: ".var_export($roomResult, true));
        

        if ($photoResult && $serviceResult) {
            $result = array(
                'photo' => $photoResult,
                'service' => $serviceResult,
                'rooms' => $roomResult,
            );
        }else{
            $result = array(
                'photo' => null,
                'service' => null,
                'rooms' => null,
            );
        }
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        return $this->render('hotel/hotel_detail.html.twig', $result);
    }
    
    /**
     * @Route("/room/list", name="get_room_list")
     */
    public function getRoomList()
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'get_room_list(trip_id)';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $trip_id = $request->get('trip_id');
        $adult_count = $request->get('adult_count');
        $children_count = $request->get('children_count');
        $children_age = $request->get('children_age');
        $hotel_id = $request->get('hotel_id');
        $is_special = $request->get('is_special');
        
        $this->logger->info("PARAMETER:: trip_id=".$trip_id
                    .", adult_count=".$adult_count
                    .", children_count=".$children_count
                    .", children_age=".var_export($children_age, true)
                    .", hotel_id=".var_export($hotel_id, true)
                    .", is_special=".var_export($is_special, true)
                    );
        
        $roomResult = $this->connection->fetchAllAssociative('
            SELECT  trip.id as trip_id, trip.is_special, 
                    flight.night_count, flight.night_count_plus, 
                    room.id as room_id, room_name.room_name, room.price_a, room.price_discounted, room.price_bb, room.price_discounted_bb
                FROM trip_package as trip 
                INNER JOIN flight_info as flight 
                INNER JOIN room_price as room 
                INNER JOIN room_name as room_name 
                ON (flight.id=trip.flight_info_id AND room.trip_id=trip.id AND room_name.id=room.room_name_id)
                WHERE trip.id='.$trip_id
            );
        $this->logger->info("HOTEL ROOMS RESULT:: ".var_export($roomResult, true));
        
        
        $room_qty = array(
            0=> ceil($adult_count / 2),
            1=> ceil($children_count / 2)
        );

        $min_room_qty = max($room_qty);
        $this->logger->info("MIN ROOM QTY :: ".$min_room_qty);
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'END'));
        
        $carResult = $this->connection->fetchAllAssociative('
            SELECT car_price.id as car_price_id, flight_info.id as flight_info_id, 
                    flight_info.direction as flight_direction, car_info.direction as car_direction, 
                    car_price.way, car_price.adult_price, car_price.child_price
                FROM car_info
                INNER JOIN car_price ON car_price.car_info_id=car_info.id 
                INNER JOIN flight_info ON flight_info.id=car_info.flight_info_id 
                WHERE flight_info.id=(SELECT flight_info_id  FROM trip_package WHERE id='.$trip_id.')
                ORDER BY car_price.id ASC'
            );
        $this->logger->info("CAR RESULT:: ".var_export($carResult, true));
        
        $is_skip_user = 0;
        $white_list = ['99042989', '99906149'];
        $session = $this->requestStack->getSession();

        if($session->get('user_session')){
            $user_phone = $session->get('user_session')['user_phone'];
            $this->logger->info("USER SESSON:: USER PHONE=".$user_phone);
            
            for($i=0; $i < sizeof($white_list); $i++){
                if($user_phone == $white_list[$i]){
                    $this->logger->info("MATCH::TEST USER=".$user_phone);
                    $is_skip_user = 1;
                    break;
                }else{
                    $is_skip_user = 0;
                }
            }
        }
        
        if($roomResult){
            $result = array(
                'is_skip_user' => $is_skip_user,
                'trip_id' => $trip_id,
                'hotel_id' => $hotel_id,
                'is_special' => $is_special,
                'rooms' => $roomResult,
                'min_room' => $min_room_qty,
                'adult_count' => $adult_count,
                'children_count' => $children_count,
                'children_age' => $children_age,
                'car_info' => (sizeof($carResult) < 1)? null : $carResult,
            );
        }else{
            $result = array(
                'is_skip_user' => $is_skip_user,
                'trip_id' => $trip_id,
                'hotel_id' => $hotel_id,
                'is_special' => $is_special,
                'rooms' => null,
                'min_room' => null,
                'adult_count' => null,
                'children_count' => null,
                'children_age' => null,
                'car_info' => null,
            );
        }

        return $this->render('hotel/room_list.html.twig', $result);
    }
    
    /**
     * @Route("/get/amount", name="get_total_amount")
     */
    public function getTotalAmount()
    {
        $this->logger->info('================================================');
        $request = Request::createFromGlobals();
        
        $logStep = 0;
        $tagName = 'get_total_amount()';
        $logId = 'client_ip='.$request->getClientIp();
        
        $this->logger->info($this->util->generateLogMessage($tagName, $logId, $logStep, 'START'));
        
        $select_rooms = $request->get('select_rooms');
        $adult_count = $request->get('adult_count');
        $children_count = $request->get('children_count');
        $children_age = $request->get('children_age');
 
        
        $this->logger->info("PARAMETER:: select_rooms=".var_export($select_rooms, true)
                    .", adult_count=".$adult_count
                    .", children_count=".$children_count
                    .", children_age=".var_export($children_age, true)
                    );
        
        $amount_result = $this->service->getTotalAmount($adult_count, $children_count, $children_age, $select_rooms);
        $this->logger->info("TOTAL AMOUNT RESULT:: ".var_export($amount_result, true));
        
        return $this->json($amount_result);
    }
    
    
}
