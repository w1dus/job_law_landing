<?php
$sub_menu = '100000';
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

// 1️⃣ 현재 DB 값 가져오기
$row = sql_fetch("SELECT cf_admin_status FROM g5_config WHERE cf_id = 1");

$current = $row['cf_admin_status'];
$admin_status = ($current === 'on') ? 'off' : 'on';

// 2️⃣ 업데이트
$sql = "UPDATE g5_config
        SET cf_admin_status = '{$admin_status}'
        WHERE cf_id = 1";

sql_query($sql, true);

// 3️⃣ JSON 응답
echo json_encode([
    'success' => true,
    'admin_status' => $admin_status,
    'message' => '상태가 변경되었습니다.'
], JSON_UNESCAPED_UNICODE);

exit;

?>