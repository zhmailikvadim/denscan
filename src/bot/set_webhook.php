<?php
	$auth_token = '4ddb8b6d5ca7ddbf-86d1772a758fef0d-4a1f4580b44006b5';
	$webhook = 'https://denscan.belsap.com/bot';

	$jsonData =
	'{
		"auth_token": "'.$auth_token.'",
		"url": "'.$webhook.'",
		"event_types": ["subscribed", "unsubscribed", "delivered", "message", "seen"]
	}';

	$ch = curl_init('https://chatapi.viber.com/pa/set_webhook');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$response = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	if($err) {echo($err);}
	else {echo(json_encode($response));}
?>