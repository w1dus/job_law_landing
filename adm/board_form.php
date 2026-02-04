<?php
$sub_menu = "300100";
require_once './_common.php';
require_once G5_EDITOR_LIB;

auth_check_menu($auth, $sub_menu, 'w');

$sql = " select count(*) as cnt from {$g5['group_table']} ";
$row = sql_fetch($sql);
if (!$row['cnt']) {
    alert('게시판그룹이 한개 이상 생성되어야 합니다.', './boardgroup_form.php');
}

$html_title = '게시판';

if (!isset($board['bo_device'])) {
    // 게시판 사용 필드 추가
    // both : pc, mobile 둘다 사용
    // pc : pc 전용 사용
    // mobile : mobile 전용 사용
    // none : 사용 안함
    sql_query(" ALTER TABLE  `{$g5['board_table']}` ADD  `bo_device` ENUM(  'both',  'pc',  'mobile' ) NOT NULL DEFAULT  'both' AFTER  `bo_subject` ", false);
}

if (!isset($board['bo_mobile_skin'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_skin` VARCHAR(255) NOT NULL DEFAULT '' AFTER `bo_skin` ", false);
}

if (!isset($board['bo_gallery_width'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_gallery_width` INT NOT NULL AFTER `bo_gallery_cols`,  ADD `bo_gallery_height` INT NOT NULL DEFAULT '0' AFTER `bo_gallery_width`,  ADD `bo_mobile_gallery_width` INT NOT NULL DEFAULT '0' AFTER `bo_gallery_height`,  ADD `bo_mobile_gallery_height` INT NOT NULL DEFAULT '0' AFTER `bo_mobile_gallery_width` ", false);
}

if (!isset($board['bo_mobile_subject_len'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_subject_len` INT(11) NOT NULL DEFAULT '0' AFTER `bo_subject_len` ", false);
}

if (!isset($board['bo_mobile_page_rows'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_page_rows` INT(11) NOT NULL DEFAULT '0' AFTER `bo_page_rows` ", false);
}

if (!isset($board['bo_mobile_content_head'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_content_head` TEXT NOT NULL AFTER `bo_content_head`, ADD `bo_mobile_content_tail` TEXT NOT NULL AFTER `bo_content_tail`", false);
}

if (!isset($board['bo_use_cert'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_cert` ENUM('','cert','adult') NOT NULL DEFAULT '' AFTER `bo_use_email` ", false);
}

if (!isset($board['bo_use_sns'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_sns` TINYINT NOT NULL DEFAULT '0' AFTER `bo_use_cert` ", false);

    $result = sql_query(" select bo_table from `{$g5['board_table']}` ");
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        sql_query(
            " ALTER TABLE `{$g5['write_prefix']}{$row['bo_table']}`
                    ADD `wr_facebook_user` VARCHAR(255) NOT NULL DEFAULT '' AFTER `wr_ip`,
                    ADD `wr_twitter_user` VARCHAR(255) NOT NULL DEFAULT '' AFTER `wr_facebook_user` ", false
        );
    }
}

$sql = " SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_use_cert' ";
$row = sql_fetch($sql);
if (strpos($row['Type'], 'hp-') === false) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` CHANGE `bo_use_cert` `bo_use_cert` ENUM('','cert','adult','hp-cert','hp-adult') NOT NULL DEFAULT '' ", false);
}

if (!isset($board['bo_use_list_file'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_list_file` TINYINT NOT NULL DEFAULT '0' AFTER `bo_use_list_view` ", false);

    $result = sql_query(" select bo_table from `{$g5['board_table']}` ");
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        sql_query(
            " ALTER TABLE `{$g5['write_prefix']}{$row['bo_table']}`
                    ADD `wr_file` TINYINT NOT NULL DEFAULT '0' AFTER `wr_datetime` ", false
        );
    }
}

if (!isset($board['bo_mobile_subject'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_subject` VARCHAR(255) NOT NULL DEFAULT '' AFTER `bo_subject` ", false);
}

if (!isset($board['bo_use_captcha'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_captcha` TINYINT NOT NULL DEFAULT '0' AFTER `bo_use_sns` ", false);
}

if (!isset($board['bo_select_editor'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_select_editor` VARCHAR(50) NOT NULL DEFAULT '' AFTER `bo_use_dhtml_editor` ", false);
}

$board_default = array(
'bo_mobile_subject'=>'',
'bo_device'=>'',
'bo_use_category'=>0,
'bo_category_list'=>'',
'bo_admin'=>'',
'bo_list_level'=>0,
'bo_read_level'=>0,
'bo_write_level'=>0,
'bo_reply_level'=>0,
'bo_comment_level'=>0,
'bo_link_level'=>0,
'bo_upload_level'=>0,
'bo_download_level'=>0,
'bo_html_level'=>0,
'bo_use_sideview'=>0,
'bo_select_editor'=>'',
'bo_use_rss_view'=>0,
'bo_use_good'=>0,
'bo_use_nogood'=>0,
'bo_use_name'=>0,
'bo_use_signature'=>0,
'bo_use_ip_view'=>0,
'bo_use_list_content'=>0,
'bo_use_list_file'=>0,
'bo_use_list_view'=>0,
'bo_use_email'=>0,
'bo_use_file_content'=>0,
'bo_use_cert'=>'',
'bo_write_min'=>0,
'bo_write_max'=>0,
'bo_comment_min'=>0,
'bo_comment_max'=>0,
'bo_use_sns'=>0,
'bo_order'=>0,
'bo_use_captcha'=>0,
'bo_content_head'=>'',
'bo_content_tail'=>'',
'bo_mobile_content_head'=>'',
'bo_mobile_content_tail'=>'',
'bo_insert_content'=>'',
'bo_sort_field'=>'',
);

for ($i = 0; $i <= 10; $i++) {
    $board_default['bo_'.$i.'_subj'] = '';
    $board_default['bo_'.$i] = '';
}

$board = array_merge($board_default, $board);

run_event('adm_board_form_before', $board, $w);

$required = "";
$readonly = "";
$sound_only = "";
$required_valid = "";
if ($w == '') {
    $html_title .= ' 생성';

    $required = 'required';
    $required_valid = 'alnum_';
    $sound_only = '<strong class="sound_only">필수</strong>';

    $board['bo_count_delete'] = 1;
    $board['bo_count_modify'] = 1;
    $board['bo_read_point'] = $config['cf_read_point'];
    $board['bo_write_point'] = $config['cf_write_point'];
    $board['bo_comment_point'] = $config['cf_comment_point'];
    $board['bo_download_point'] = $config['cf_download_point'];

    $board['bo_gallery_cols'] = 4;
    $board['bo_gallery_width'] = 202;
    $board['bo_gallery_height'] = 150;
    $board['bo_mobile_gallery_width'] = 125;
    $board['bo_mobile_gallery_height'] = 100;
    $board['bo_table_width'] = 100;
    $board['bo_page_rows'] = $config['cf_page_rows'];
    $board['bo_mobile_page_rows'] = $config['cf_page_rows'];
    $board['bo_subject_len'] = 60;
    $board['bo_mobile_subject_len'] = 30;
    $board['bo_new'] = 24;
    $board['bo_hot'] = 100;
    $board['bo_image_width'] = 600;
    $board['bo_upload_count'] = 2;
    $board['bo_upload_size'] = 1048576;
    $board['bo_reply_order'] = 1;
    $board['bo_use_search'] = 1;
    $board['bo_skin'] = 'basic';
    $board['bo_mobile_skin'] = 'basic';
    $board['gr_id'] = $gr_id;
    $board['bo_use_secret'] = 0;
    $board['bo_include_head'] = '_head.php';
    $board['bo_include_tail'] = '_tail.php';
} elseif ($w == 'u') {
    $html_title .= ' 수정';

    if (!$board['bo_table']) {
        alert('존재하지 않은 게시판 입니다.');
    }

    if ($is_admin == 'group') {
        if ($member['mb_id'] != $group['gr_admin']) {
            alert('그룹이 틀립니다.');
        }
    }

    $readonly = 'readonly';
}

if ($is_admin != 'super') {
    $group = get_group($board['gr_id']);
    $is_admin = is_admin($member['mb_id']);
}

$g5['title'] = $html_title;
require_once './admin.head.php';

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_bo_basic">기본 설정</a></li>
    <li><a href="#anc_bo_auth">권한 설정</a></li>
    <li><a href="#anc_bo_function">기능 설정</a></li>
    <li><a href="#anc_bo_design">디자인/양식</a></li>
    <li><a href="#anc_bo_point">포인트 설정</a></li>
    <li><a href="#anc_bo_extra">여분필드</a></li>
</ul>';

?>

<form name="fboardform" id="fboardform" action="./board_form_update.php" onsubmit="return fboardform_submit(this)" method="post" enctype="multipart/form-data">
<input type="hidden" name="bo_include_head" value="<?php echo $board['bo_include_head'] ?>" id="bo_include_head" class="frm_input" size="50">
<input type="hidden" name="bo_include_tail" value="<?php echo $board['bo_include_tail'] ?>" id="bo_include_tail" class="frm_input" size="50">

<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<!-- <div style="display:none;">
    <th scope="row"><label for="bo_mobile_skin">모바일<br>스킨 디렉토리<strong class="sound_only">필수</strong></label></th>
    <?php echo get_mobile_skin_select('board', 'bo_mobile_skin', 'bo_mobile_skin', $board['bo_mobile_skin'], 'required'); ?>
</div> -->
<input type="hidden" name="bo_subject_len" value="<?php echo $board['bo_subject_len'] ?>" id="bo_subject_len" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_mobile_subject_len" value="<?php echo $board['bo_mobile_subject_len'] ?>" id="bo_mobile_subject_len" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_mobile_page_rows" value="<?php echo $board['bo_mobile_page_rows'] ?>" id="bo_mobile_page_rows" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_gallery_cols" value="<?php echo $board['bo_gallery_cols'] ?>" id="bo_gallery_cols" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_gallery_width" value="<?php echo $board['bo_gallery_width'] ?>" id="bo_gallery_width" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_gallery_height" value="<?php echo $board['bo_gallery_height'] ?>" id="bo_gallery_height" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_mobile_gallery_width" value="<?php echo $board['bo_mobile_gallery_width'] ?>" id="bo_mobile_gallery_width" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_mobile_gallery_height" value="<?php echo $board['bo_mobile_gallery_height'] ?>" id="bo_mobile_gallery_height" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_table_width" value="<?php echo $board['bo_table_width'] ?>" id="bo_table_width" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_image_width" value="<?php echo $board['bo_image_width'] ?>" id="bo_image_width" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_new" value="<?php echo $board['bo_new'] ?>" id="bo_new" required class="numeric frm_input" size="4">
<input type="hidden" name="bo_hot" value="<?php echo $board['bo_hot'] ?>" id="bo_hot" required class="numeric frm_input" size="4">

<input type="hidden" name="bo_read_point" value="<?php echo $board['bo_read_point'] ?>" id="bo_read_point" required class="frm_input" size="5">
<input type="hidden" name="bo_write_point" value="<?php echo $board['bo_write_point'] ?>" id="bo_write_point" required class="frm_input" size="5">
<input type="hidden" name="bo_comment_point" value="<?php echo $board['bo_comment_point'] ?>" id="bo_comment_point" required class="frm_input" size="5">
<input type="hidden" name="bo_download_point" value="<?php echo $board['bo_download_point'] ?>" id="bo_download_point" required class="frm_input" size="5">


<h2>게시판 기본 설정</h2>
<div class="margin-div"></div>
<ul class="formList">
    <li class="half">
        <div class="label">테이블</div>
        <div class="iptBox">
            <input type="text" name="bo_table" value="<?php echo $board['bo_table'] ?>" placeholder="영문자, 숫자, _ 만 가능 (공백없이 20자 이내)" id="bo_table" class="ipt" <?php echo $required ?> <?php echo $readonly ?> class="frm_input <?php echo $readonly ?> <?php echo $required ?> <?php echo $required_valid ?>" maxlength="20">
        </div>
    </li>
    <li class="half">
        <div class="label">그룹</div>
        <div class="iptBox">
            <?php echo get_group_select('gr_id', $board['gr_id'], 'required'); ?>
        </div>
    </li>
    <li>
        <div class="label">게시판 제목 <span class="red">*</span></div>
        <div class="iptBox">
            <input type="text" name="bo_subject" value="<?php echo get_text($board['bo_subject']) ?>" class="ipt" id="bo_subject" required class="required frm_input" size="80"  maxlength="120">
        </div>
    </li>
    <li>
        <div class="label">분류</div>
        <div class="iptBox">
            <?php echo help('📢 분류와 분류 사이는 | 로 구분하세요. (예: 질문|답변) 첫자로 #은 입력하지 마세요. (예: #질문|#답변 [X])'."\n".'분류명에 일부 특수문자 ()/ 는 사용할수 없습니다.'); ?>
            <input type="text" name="bo_category_list" class="ipt" value="<?php echo get_text($board['bo_category_list']) ?>" id="bo_category_list" class="frm_input" size="70">
            <input type="checkbox" name="bo_use_category" value="1" id="bo_use_category" <?php echo $board['bo_use_category']?'checked':''; ?>>
            <label for="bo_use_category">사용</label>
        </div>
    </li>
    <li >
        <div class="label">카운트 조정</div>
        <div class="iptBox ">
            <?php echo help('현재 원글수 : '.number_format($board['bo_count_write']).', 현재 댓글수 : '.number_format($board['bo_count_comment'])."\n".'게시판 목록에서 글의 번호가 맞지 않을 경우에 체크하십시오.') ?>
            <input type="checkbox" name="proc_count" value="1" id="proc_count">
        </div>
    </li>
     <li >
        <div class="label">리스트 정렬 필드</div>
        <div class="iptBox ">
             <select id="bo_sort_field" name="bo_sort_field" class="ipt">
                <?php foreach (get_board_sort_fields($board) as $v) {
                    $option_value = $order_by_str = $v[0];
                    if ($v[0] === 'wr_num, wr_reply') {
                        $selected = (! $board['bo_sort_field']) ? 'selected="selected"' : '';
                        $option_value = '';
                    } else {
                        $selected = ($board['bo_sort_field'] === $v[0]) ? 'selected="selected"' : '';
                    }
                    
                    if ($order_by_str !== 'wr_num, wr_reply') {
                        $tmp = explode(',', $v[0]);
                        $order_by_str = $tmp[0];
                    }

                    echo '<option value="'.$option_value.'" '.$selected.' >'.$order_by_str.' : '.$v[1].'</option>';
                } //end foreach ?>
            </select>
        </div>
    </li>
</ul>

<div class="margin-div"></div>

<h2>게시판 권한 설정</h2>
<ul class="formList">
    <li class="half">
        <div class="label">목록보기 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_list_level', 1, 10, $board['bo_list_level']) ?>
        </div>  
    </li>
    <li class="half">
        <div class="label">글읽기 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_read_level', 1, 10, $board['bo_read_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">글쓰기 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_write_level', 1, 10, $board['bo_write_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">글답변 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_reply_level', 1, 10, $board['bo_reply_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">댓글쓰기 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_comment_level', 1, 10, $board['bo_comment_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">링크 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_link_level', 1, 10, $board['bo_link_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">업로드 권한</div>
        <div class="iptBox ">
             <?php echo get_member_level_select('bo_upload_level', 1, 10, $board['bo_upload_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">다운로드 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_download_level', 1, 10, $board['bo_download_level']) ?>
        </div>  
    </li>
     <li class="half">
        <div class="label">dhtml 권한</div>
        <div class="iptBox ">
            <?php echo get_member_level_select('bo_html_level', 1, 10, $board['bo_html_level']) ?>
        </div>  
    </li>
    <li class="half">
        <div class="label">dhtml 사용여부</div>
        <div class="iptBox flex" >
            <label>
                <input type="checkbox" name="bo_use_dhtml_editor" value="1" <?php echo $board['bo_use_dhtml_editor']?'checked':''; ?> id="bo_use_dhtml_editor">
                사용
            </label>
        </div>  
    </li>
    
</ul>

<div class="margin-div"></div>
<h2>게시판 기능 설정</h2>

<ul class="formList">
     <li class="">
        <div class="label">스킨</div>
        <div class="iptBox ">
            <?php echo get_skin_select('board', 'bo_skin', 'bo_skin', $board['bo_skin'], 'required'); ?>
        </div>  
    </li>
    <li >
        <div class="label">페이지당 목록 수 </div>
        <div class="iptBox ">
          <input type="text" name="bo_page_rows" value="<?php echo $board['bo_page_rows'] ?>" id="bo_page_rows" required class="ipt small" size="4">
        </div>
    </li>
    <li class="">
        <div class="label">비밀글 사용</div>
        <div class="iptBox ">
            <select id="bo_use_secret" name="bo_use_secret" class="ipt">
                <?php echo option_selected(0, $board['bo_use_secret'], "사용하지 않음"); ?>
                <?php echo option_selected(1, $board['bo_use_secret'], "체크박스"); ?>
                <?php echo option_selected(2, $board['bo_use_secret'], "무조건"); ?>
            </select>
        </div>  
    </li>
    <li class="">
        <div class="label">파일 업로드 개수</div>
        <div class="iptBox">
            <?php echo help('게시물 한건당 업로드 할 수 있는 파일의 최대 개수 (0 은 파일첨부 사용하지 않음)') ?>
            <input type="text" name="bo_upload_count" value="<?php echo $board['bo_upload_count'] ?>" id="bo_upload_count" required class="required numeric ipt small" size="4">
        </div>  
    </li>
    <li class="">
        <div class="label">파일 업로드 용량</div>
        <div class="iptBox">
            <?php echo help('최대 '.ini_get("upload_max_filesize").' 이하 업로드 가능, 1 MB = 1,048,576 bytes') ?>
            업로드 파일 한개당 <input type="text" name="bo_upload_size" value="<?php echo $board['bo_upload_size'] ?>" id="bo_upload_size" required class="required numeric small ipt"  size="10"> bytes 이하
        </div>  
    </li>
    <li class="">
        <div class="label">메일발송 사용</div>
        <div class="iptBox flex">
          <label>
            <input type="checkbox" name="bo_use_email" value="1" id="bo_use_email" <?php echo $board['bo_use_email']?'checked':''; ?>>
            사용
          </label>
        </div>  
    </li>
</ul>






<div class="adm-btn-div">

    <a href="./board_list.php?sst=&amp;sod=&amp;sfl=&amp;stx=&amp;page=0" class="adm-btn" accesskey="s">목록</a>
    <?php if ($bo_table && $w) { ?>
        <a href="<?php echo get_pretty_url($board['bo_table']); ?>" target="_blank" class="adm-btn">게시판 바로가기</a>
        <!-- <a href="./board_copy.php?bo_table=<?php echo $board['bo_table']; ?>" id="board_copy" target="win_board_copy" class="adm-btn">게시판복사</a>
        <a href="./board_thumbnail_delete.php?bo_table=<?php echo $board['bo_table'].'&amp;'.$qstr;?>" onclick="return delete_confirm2('게시판 썸네일 파일을 삭제하시겠습니까?');" class="adm-btn">게시판 썸네일 삭제</a> -->
    <?php } ?>
    <input type="submit" value="확인" class="submit-btn adm-btn" accesskey="s">
</div>

</form>

<script>
$(function(){
    $("#board_copy").click(function(){
        window.open(this.href, "win_board_copy", "left=10,top=10,width=500,height=400");
        return false;
    });

    $(".get_theme_galc").on("click", function() {
        if(!confirm("현재 테마의 게시판 이미지 설정을 적용하시겠습니까?"))
            return false;

        $.ajax({
            type: "POST",
            url: "./theme_config_load.php",
            cache: false,
            async: false,
            data: { type: "board" },
            dataType: "json",
            success: function(data) {
                if(data.error) {
                    alert(data.error);
                    return false;
                }

                var field = Array('bo_gallery_cols', 'bo_gallery_width', 'bo_gallery_height', 'bo_mobile_gallery_width', 'bo_mobile_gallery_height', 'bo_image_width');
                var count = field.length;
                var key;

                for(i=0; i<count; i++) {
                    key = field[i];

                    if(data[key] != undefined && data[key] != "")
                        $("input[name="+key+"]").val(data[key]);
                }
            }
        });
    });
});

function board_copy(bo_table) {
    window.open("./board_copy.php?bo_table="+bo_table, "BoardCopy", "left=10,top=10,width=500,height=200");
}

function set_point(f) {
    if (f.chk_grp_point.checked) {
        f.bo_read_point.value = "<?php echo $config['cf_read_point'] ?>";
        f.bo_write_point.value = "<?php echo $config['cf_write_point'] ?>";
        f.bo_comment_point.value = "<?php echo $config['cf_comment_point'] ?>";
        f.bo_download_point.value = "<?php echo $config['cf_download_point'] ?>";
    } else {
        f.bo_read_point.value     = f.bo_read_point.defaultValue;
        f.bo_write_point.value    = f.bo_write_point.defaultValue;
        f.bo_comment_point.value  = f.bo_comment_point.defaultValue;
        f.bo_download_point.value = f.bo_download_point.defaultValue;
    }
}

var captcha_chk = false;

function use_captcha_check(){
    $.ajax({
        type: "POST",
        url: g5_admin_url+"/ajax.use_captcha.php",
        data: { admin_use_captcha: "1" },
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {
        }
    });
}

function frm_check_file(){
    var bo_include_head = "<?php echo $board['bo_include_head']; ?>";
    var bo_include_tail = "<?php echo $board['bo_include_tail']; ?>";
    var head = jQuery.trim(jQuery("#bo_include_head").val());
    var tail = jQuery.trim(jQuery("#bo_include_tail").val());

    if(bo_include_head !== head || bo_include_tail !== tail){
        // 캡챠를 사용합니다.
        jQuery("#admin_captcha_box").show();
        captcha_chk = true;

        use_captcha_check();

        return false;
    } else {
        jQuery("#admin_captcha_box").hide();
    }

    return true;
}

jQuery(function($){
    if( window.self !== window.top ){   // frame 또는 iframe을 사용할 경우 체크
        $("#bo_include_head, #bo_include_tail").on("change paste keyup", function(e) {
            frm_check_file();
        });

        use_captcha_check();
    }
});

function fboardform_submit(f)
{
    <?php
    if (!$w) {
        $js_array = get_bo_table_banned_word();
        echo "var banned_array = ". json_encode($js_array) . ";\n";
    }
    ?>

    // 게시판명이 금지된 단어로 되어 있으면
    if( (typeof banned_array != 'undefined') && jQuery.inArray(f.bo_table.value, banned_array) !== -1 ){
        alert("입력한 게시판 TABLE명을 사용할수 없습니다. 다른 이름으로 입력해 주세요.");
        return false;
    }

    <?php echo get_editor_js("bo_content_head"); ?>
    <?php echo get_editor_js("bo_content_tail"); ?>
    <?php echo get_editor_js("bo_mobile_content_head"); ?>
    <?php echo get_editor_js("bo_mobile_content_tail"); ?>

    if (parseInt(f.bo_count_modify.value) < 0) {
        alert("원글 수정 불가 댓글수는 0 이상 입력하셔야 합니다.");
        f.bo_count_modify.focus();
        return false;
    }

    if (parseInt(f.bo_count_delete.value) < 1) {
        alert("원글 삭제 불가 댓글수는 1 이상 입력하셔야 합니다.");
        f.bo_count_delete.focus();
        return false;
    }

    if( captcha_chk ) {
        <?php echo isset($captcha_js) ? $captcha_js : ''; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>
    }

    return true;
}
</script>

<?php
require_once './admin.tail.php';