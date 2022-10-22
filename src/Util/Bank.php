<?php
namespace App\Util;


class Bank {

    public static $LENDMN = array(
        'code' =>'lendmn',
        'name'=>'Lend MN',
        'content_type'=>'application/x-www-form-urlencoded',
//        'redirect_uri_real' =>'http://leisure-team.com',
        'redirect_uri_real' =>'http://192.168.30.11:8000',
//        'redirect_uri_real' =>'http://192.168.1.4:8000',
        'redirect_uri_local_test' =>'http://192.168.30.11:8000',
        'redirect_url' =>'/pay/lendmn',
        'request_uri_lend' =>'https://mgw.test.lending.mn',
        'request_uri_lend_real' =>'https://mgw.lend.mn',
        'client_id' =>'73_1r47y2922ykg8k4c8wo0o0c8sscws0c4csko4og4g48oow880s',
        'client_secret' =>'43m5itw9e5c08sg4s8o44ggw0ckg8kgo0ookcoksww44cc0wk4',
        'ecommer_token' =>'YmI3ZjNjODRiYjk0MjRjMjJiMjM2ZDBhYzljMmM2NmRiNTdjZDdjYjg0NjMzNTcyN2UyNzMyNjM2N2IwNGU4Zg',
        'grant_type' =>'authorization_code',
        'invoice_expires_in' => 900,
    );

    public static function getErrorCode()
    {
        return array(
            "0" => ["OK","Success! ErrorMessage-д хүсэлтийн дата буцаж ирнэ. Дэлгэрэнгүйг доорхи api болгонд дээрээс харна уу."],
            "1" => ["Missing parameter {{param}}","Аль нэг parameter дутуу явуулсан тохиолдолд гарах алдаа."],
            "2" =>  ["Invalid parameter {{param}}","Аль нэг parameter буруу явуулсан тохиолдолд гарах алдаа."],
            "3" =>  ["Sorry. Features is not yet implemented","Сервис maintance хийгдэх эсвэл шинэчлэгдэх үед гарч ирэх алдаа."],
            "4" => ["Invalid Response.","Бусдын сервер дуудаад алдаатай response ирсэн үед гарах алдаа."],
            "126" => ["Invoice not found.","Нэхэмжлэл олдоогүй."],
            "127" => ["Invoice amount cannot be modified","Нэхэмжлэлийн дүнг өөрчлөх боломжгүй."],
            "128" => ["Invoice already paid.","Нэхэмжлэл төлөгдсөн."],
            "129" => ["Line item not found.","Нэхэмжлэл дээрхи бараануудын аль нэг нь хасагдсан үед гарна."],
            "130" => ["Invoice cancelled.","Нэхэмжлэл цуцлагдсан"],
            "131" => ["Invoice expired.","Нэхэмжлэлийн хүчинтэй хугацаа дууссан."],
            "132" => ["Invoice amount not match.","Нэхэмжлэлийн үнийн дүн зөрөөтэй."],
            "201" => ["User not found.","Хэрэглэгч олдсонгүй."],
            "401" => ["Access denied","Буруу, хугацаа дууссан токен эсвэл тухайн үйлдлийг хийхэд permission хүрэхгүй үед."],
            "404" => ["Not Found","Хандсан api олдоогүй тохиолдолд"],
            "405" => ["Method not allowed","Method not allowed"],
            "406" => ["Not Acceptable","Returned when an invalid format is specified in the request."],
            "410" => ["Gone","This resource is gone. Used to indicate that an API endpoint has been turned off."],
            "500" => ["Internal Server Error","Something is broken. This is usually a temporary error, for example in a high load situation or if an endpoint is temporarily having issues."],
            "504" => ["Gateway timeout","Лэндмн сервер дотоод үйл ажилгаанаас шалтгаалан хүсэлтийг биелүүлж чадаагүй тохиолдолд буцаана. Энэ тохиолдолд ахиж дуудах хэрэгтэй."],
        );
    }
            
}