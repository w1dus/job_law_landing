<?php 
require_once './_common.php';

$cf_title = isset($_POST['cf_title']) ? trim($_POST['cf_title']) : '';
$cf_analytics = isset($_POST['cf_analytics']) ? trim($_POST['cf_analytics']) : '';
$cf_add_meta = isset($_POST['cf_add_meta']) ? trim($_POST['cf_add_meta']) : '';
$cf_privacy = isset($_POST['cf_privacy']) ? trim($_POST['cf_privacy']) : '';
$cf_stipulation = isset($_POST['cf_stipulation']) ? trim($_POST['cf_stipulation']) : '';
$cf_1_subj = isset($_POST['cf_1_subj']) ? trim($_POST['cf_1_subj']) : '';
$cf_admin_email = isset($_POST['cf_admin_email']) ? trim($_POST['cf_admin_email']) : '';
$cf_admin_email_name = isset($_POST['cf_admin_email_name']) ? trim($_POST['cf_admin_email_name']) : '';
$cf_email_wr_super_admin = isset($_POST['cf_email_wr_super_admin']) ? 1 : 0;
$cf_image_extension = isset($_POST['cf_image_extension']) ? trim($_POST['cf_image_extension']) : '';
$cf_sms_use = isset($_POST['cf_sms_use']) ? trim($_POST['cf_sms_use']) : '';
$cf_sms_type = isset($_POST['cf_sms_type']) ? trim($_POST['cf_sms_type']) : '';
$cf_icode_id = isset($_POST['cf_icode_id']) ? trim($_POST['cf_icode_id']) : '';
$cf_icode_pw = isset($_POST['cf_icode_pw']) ? trim($_POST['cf_icode_pw']) : '';
$cf_icode_token_key = isset($_POST['cf_icode_token_key']) ? trim($_POST['cf_icode_token_key']) : '';
$cf_social_servicelist = !empty($_POST['cf_social_servicelist']) ? implode(',', $_POST['cf_social_servicelist']) : '';

check_admin_token();


$thumbnail = $_FILES['thumbnail'] ?? null;

$thumbnail_delete = isset($_POST['thumbnail_delete']) ? 1 : 0;
// 삭제에 체크가 되어있다면 파일을 삭제합니다.
if($thumbnail_delete==1){
    // 저장할 경로 (G5_IMG_URL은 URL이므로, 실제 경로인 G5_IMG_PATH로 처리해야 함)
    $save_dir = G5_PATH.'/img'; // 예: define('G5_IMG_PATH', $_SERVER['DOCUMENT_ROOT'].'/img');
    $delete_file = $save_dir . '/thumbnail.png';

    if( file_exists($delete_file) ){
        @unlink($delete_file);
    }
}


if ($thumbnail && $thumbnail['error'] === UPLOAD_ERR_OK) {
    // 저장할 경로 (G5_IMG_URL은 URL이므로, 실제 경로인 G5_IMG_PATH로 처리해야 함)
    $save_dir = G5_PATH.'/img'; // 예: define('G5_IMG_PATH', $_SERVER['DOCUMENT_ROOT'].'/img');
    $save_path = $save_dir . '/thumbnail.png';

    // 기존 파일이 있다면 삭제
    if (file_exists($save_path)) {
        unlink($save_path);
    }

    // 파일 이동
    if (!move_uploaded_file($thumbnail['tmp_name'], $save_path)) {
        alert('파일 저장에 실패하였습니다.');
        exit;
    }
}

$favicon = $_FILES['favicon'] ?? null;

$favicon_delete = isset($_POST['favicon_delete']) ? 1 : 0;
// 삭제에 체크가 되어있다면 파일을 삭제합니다.
if($favicon_delete==1){
    // 저장할 경로 (G5_IMG_URL은 URL이므로, 실제 경로인 G5_IMG_PATH로 처리해야 함)
    $save_dir = G5_PATH.'/img'; // 예: define('G5_IMG_PATH', $_SERVER['DOCUMENT_ROOT'].'/img');
    $delete_file = $save_dir . '/favicon.ico';

    if( file_exists($delete_file) ){
        @unlink($delete_file);
    }
}


if ($favicon && $favicon['error'] === UPLOAD_ERR_OK) {
    // 저장할 경로 (G5_IMG_URL은 URL이므로, 실제 경로인 G5_IMG_PATH로 처리해야 함)
    $save_dir = G5_PATH.'/img'; // 예: define('G5_IMG_PATH', $_SERVER['DOCUMENT_ROOT'].'/img');
    $save_path = $save_dir . '/favicon.ico';

    // 기존 파일이 있다면 삭제
    if (file_exists($save_path)) {
        unlink($save_path);
    }

    // 파일 이동
    if (!move_uploaded_file($favicon['tmp_name'], $save_path)) {
        alert('파일 저장에 실패하였습니다.');
        exit;
    }
}


// 여분필드
for ($i = 1; $i <= 10; $i++) {
    $cf_subj[$i] = isset($_POST['cf_'.$i.'_subj']) ? trim($_POST['cf_'.$i.'_subj']) : '';
    $cf_val[$i] = isset($_POST['cf_'.$i]) ? trim($_POST['cf_'.$i]) : '';
}

$sql = "UPDATE {$g5['config_table']} SET
            cf_title = '{$cf_title}',
            cf_social_servicelist   =   '{$cf_social_servicelist}',
            cf_social_login_use = '{$_POST['cf_social_login_use']}',
            cf_analytics = '{$cf_analytics}',
            cf_add_meta = '{$cf_add_meta}',
            cf_privacy = '{$cf_privacy}',
            cf_stipulation = '{$cf_stipulation}',
            cf_1_subj = '{$cf_1_subj}',
            cf_admin_email = '{$cf_admin_email}',
            cf_admin_email_name = '{$cf_admin_email_name}',
            cf_email_wr_super_admin = '{$cf_email_wr_super_admin}',
            cf_image_extension = '{$cf_image_extension}',
            cf_sms_use = '{$cf_sms_use}',
            cf_sms_type = '{$cf_sms_type}',
            cf_icode_id = '{$cf_icode_id}',
            cf_icode_pw = '{$cf_icode_pw}',
            cf_icode_token_key = '{$cf_icode_token_key}',
            cf_1 = '{$cf_val[1]}',
            cf_2_subj = '{$cf_subj[2]}',
            cf_2 = '{$cf_val[2]}',
            cf_3_subj = '{$cf_subj[3]}',
            cf_3 = '{$cf_val[3]}',
            cf_4_subj = '{$cf_subj[4]}',
            cf_4 = '{$cf_val[4]}',
            cf_5_subj = '{$cf_subj[5]}',
            cf_5 = '{$cf_val[5]}',
            cf_6_subj = '{$cf_subj[6]}',
            cf_6 = '{$cf_val[6]}',
            cf_7_subj = '{$cf_subj[7]}',
            cf_7 = '{$cf_val[7]}',
            cf_8_subj = '{$cf_subj[8]}',
            cf_8 = '{$cf_val[8]}',
            cf_9_subj = '{$cf_subj[9]}',
            cf_9 = '{$cf_val[9]}',
            cf_10_subj = '{$cf_subj[10]}',
            cf_10 = '{$cf_val[10]}'";

sql_query($sql);

run_event('admin_config_form_update');

goto_url('./config_form.php', false);


?>