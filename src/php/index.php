<?php

$auth_token = "4ddb8b6d5ca7ddbf-86d1772a758fef0d-4a1f4580b44006b5";
$send_name = "Безопасная школа";
$is_log = true;

function put_log_in($data)
{
	global $is_log;
	if($is_log) {file_put_contents("tmp_in.txt", $data."\n", FILE_APPEND);}
}

function put_log_out($data)
{
	global $is_log;
	if($is_log) {file_put_contents("tmp_out.txt", $data."\n", FILE_APPEND);}
}

function sendReq($data)
{
	$request_data = json_encode($data);
	put_log_out($request_data);

	//here goes the curl to send data to user
	$ch = curl_init("https://chatapi.viber.com/pa/send_message");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$response = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	if($err) {return $err;}
	else {return $response;}
}

function sendMsg($sender_id, $text, $type, $tracking_data = Null, $arr_asoc = Null)
{
	global $auth_token, $send_name;

	$data['auth_token'] = $auth_token;
	$data['receiver'] = $sender_id;
	if($text != Null) {$data['text'] = $text;}
	$data['type'] = $type;
	//$data['min_api_version'] = $input['sender']['api_version'];
	$data['sender']['name'] = $send_name;
	//$data['sender']['avatar'] = $input['sender']['avatar'];
	if($tracking_data != Null) {$data['tracking_data'] = $tracking_data;}
	if($arr_asoc != Null)
	{
		foreach($arr_asoc as $key => $val) {$data[$key] = $val;}
	}

	return sendReq($data);
}


function sendPicMsg($sender_id, $text, $type, $media, $tracking_data = Null, $arr_asoc = Null)
{
	global $auth_token, $send_name;

	$data['auth_token'] = $auth_token;
	$data['receiver'] = $sender_id;
	if($text != Null) {$data['text'] = $text;}
	if($media != Null) {$data['media'] = $media;}
	$data['type'] = $type;
	//$data['min_api_version'] = $input['sender']['api_version'];
	$data['sender']['name'] = $send_name;
	//$data['sender']['avatar'] = $input['sender']['avatar'];
	if($tracking_data != Null) {$data['tracking_data'] = $tracking_data;}
	if($arr_asoc != Null)
	{
		foreach($arr_asoc as $key => $val) {$data[$key] = $val;}
	}

	return sendReq($data);
}



function sendMsgText($sender_id, $text, $tracking_data = Null)
{
	return sendMsg($sender_id, $text, "text", $tracking_data);
}



$request = file_get_contents("php://input");
$input = json_decode($request, true);
put_log_in($request);


$type = $input['message']['type']; //type of message received (text/picture)
$text = $input['message']['text']; //actual message the user has sent
$sender_id = $input['sender']['id']; //unique viber id of user who sent the message
$sender_name = $input['sender']['name']; //name of the user who sent the message

if($input['event'] == 'webhook')
{
  $webhook_response['status'] = 0;
  $webhook_response['status_message'] = "ok";
  $webhook_response['event_types'] = 'delivered';
  echo json_encode($webhook_response);
  die;
}
else if($input['event'] == "subscribed")
{
  sendMsgText($sender_id, "Спасибо, что подписались на нас!");
}
else if($input['event'] == "conversation_started")
{
  sendMsgText($sender_id, "Беседа началась!");
}
elseif($input['event'] == "message")
{

	require_once __DIR__ . '/phpqrcode/qrlib.php';


	$qr_path = __DIR__ . '/'.$sender_name.'.png';

	QRcode::png($sender_id, $qr_path);
	
	$send_path = 'https://denscan.belsap.com/php/'.$sender_name.'.png';
	
	$message = 'Здравствуйте '.$sender_name.'! Отправляем Вам ваш QR код';
	
  	sendMsg($sender_id, $message, $type );
  	
  	sendPicMsg($sender_id, "Ваш QR Код", "picture", $send_path );
  
  	require_once 'db_connection.php';
  	
    	$viber_id = mysqli_real_escape_string($db_conn, trim($sender_id ));  	
    	
    	
    	
	$cyrillicTxt=$sender_name;
	$cyrillicPattern  = array('а','б','в','г','д','e', 'ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у', 
	        'ф','х','ц','ч','ш','щ','ъ','ь', 'э', 'ы', 'ю','я','А','Б','В','Г','Д','Е', 'Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У',
	        'Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ь', 'Э', 'Ы', 'Ю','Я' );
	$latinPattern = array( 'a','b','v','g','d','e','jo','zh','z','i','y','k','l','m','n','o','p','r','s','t','u',
	        'f' ,'h' ,'ts' ,'ch','sh' ,'sht', '', '`', 'je','ji','yu' ,'ya','A','B','V','G','D','E','Jo','Zh',
	        'Z','I','Y','K','L','M','N','O','P','R','S','T','U',
	        'F' ,'H' ,'Ts' ,'Ch','Sh','Sht', '', '`', 'Je' ,'Ji' ,'Yu' ,'Ya' );
	$cyrillicTxt = str_replace($cyrillicPattern, $latinPattern, $cyrillicTxt);    	
    	
    	
    	$viber_name = mysqli_real_escape_string($db_conn, trim($cyrillicTxt));
       	
   	
    	$query_insert = "INSERT INTO `viber_id_list`(`viber_id`, `viber_name`) VALUES ('$viber_id','$viber_name')";

    $insertRow = mysqli_query($db_conn, $query_insert);
        if ($insertRow) {
            $last_id = mysqli_insert_id($db_conn);
            echo json_encode(["success" => 1, "msg" => "Inserted.", "id"=>$last_id,"time" => $time]);
        } else {
            echo json_encode(["success" => 0, "msg" => "Not Inserted!"]);
        }
 
} else {
}

?>