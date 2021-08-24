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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->user_name)
    && isset($data->user_email)
    && !empty(trim($data->user_name))
    && !empty(trim($data->user_email))
) {
    $qr_code = mysqli_real_escape_string($conn, trim($data->qr_code));
    $date = mysqli_real_escape_string($conn, trim($data->date));
    $time = mysqli_real_escape_string($conn, trim($data->time));

    $insertRow = mysqli_query($conn, "INSERT INTO `qr_code_list`(`qr_code`,`date`, 'time') VALUES('$qr_code','$date','$time')");
        if ($insertRow) {
            $last_id = mysqli_insert_id($db_conn);
            echo json_encode(["success" => 1, "msg" => "Inserted.", "time" => $time]);
        } else {
            echo json_encode(["success" => 0, "msg" => "Not Inserted!"]);
        }
 
} else {
    echo json_encode(["success" => 0, "msg" => "Please fill all the required fields!"]);
}

?>