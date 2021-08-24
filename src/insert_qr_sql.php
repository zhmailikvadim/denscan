<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "belsapco_denscan";
$password = "Malinababki130";
$dbname = "belsapco_denscan";

ini_set('display_errors','On');
error_reporting(E_ALL | E_STRICT);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$json = file_get_contents('php://input');

$data = json_decode(file_get_contents("php://input"));

$qr_code = $data->qr_code;
$time =  $data->time;
$date = $data->date;

if 
(
    isset($qr_code)
) {
    $qr_code = mysqli_real_escape_string($conn, trim($qr_code ));
    $date = mysqli_real_escape_string($conn, trim($date));
    $time = mysqli_real_escape_string($conn, trim($time));   
    $query_insert = "INSERT INTO `gr_code_list`(`qr_code`, `date`, `time`) VALUES ('$qr_code','$date','$time')";

    $insertRow = mysqli_query($conn, $query_insert);
        if ($insertRow) {
            $last_id = mysqli_insert_id($conn);
            echo json_encode(["success" => 1, "msg" => "Inserted.", "id"=>$last_id,"time" => $time]);
        } else {
            echo json_encode(["success" => 0, "msg" => "Not Inserted!"]);
        }
 
} else {
    echo json_encode(["success" => 0, "msg" => "Please fill12 all the required fields!"]);
}
?>