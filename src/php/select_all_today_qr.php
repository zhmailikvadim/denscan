<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';

$allUsers = mysqli_query($db_conn, "SELECT
            `qr_code`, `gr_code_list`.`date`, `gr_code_list`.`time`, `viber_id_list`.`name`, 
            FROM
            `gr_code_list`
            INNER JOIN `viber_id_list` ON `viber_id_list`.`viber_id` = `gr_code_list`.`qr_code`
            WHERE
            YEARWEEK(`viber_id_list`.`date`) = YEARWEEK(NOW())");
if (mysqli_num_rows($allUsers) > 0) {
    $all_qr = mysqli_fetch_all($allQr, MYSQLI_ASSOC);
    echo json_encode(["success" => 1, "users" => $all_qr]);
} else {
    echo json_encode(["success" => 0]);
}