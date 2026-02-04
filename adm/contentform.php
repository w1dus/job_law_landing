<?php
$sub_menu = '300600';
require_once './_common.php';
require_once G5_EDITOR_LIB;

auth_check_menu($auth, $sub_menu, "w");

$co_id = isset($_REQUEST['co_id']) ? preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['co_id']) : '';

// 상단, 하단 파일경로 필드 추가
if (!sql_query(" select co_include_head from {$g5['content_table']} limit 1 ", false)) {
    $sql = " ALTER TABLE `{$g5['content_table']}`  ADD `co_include_head` VARCHAR( 255 ) NOT NULL ,
                                                    ADD `co_include_tail` VARCHAR( 255 ) NOT NULL ";
    sql_query($sql, false);
}

// html purifier 사용여부 필드
if (!sql_query(" select co_tag_filter_use from {$g5['content_table']} limit 1 ", false)) {
    sql_query(
        " ALTER TABLE `{$g5['content_table']}`
                    ADD `co_tag_filter_use` tinyint(4) NOT NULL DEFAULT '0' AFTER `co_content` ",
        true
    );
    sql_query(" update {$g5['content_table']} set co_tag_filter_use = '1' ");
}

// 모바일 내용 추가
if (!sql_query(" select co_mobile_content from {$g5['content_table']} limit 1", false)) {
    sql_query(
        " ALTER TABLE `{$g5['content_table']}`
                    ADD `co_mobile_content` longtext NOT NULL AFTER `co_content` ",
        true
    );
}

// 스킨 설정 추가
if (!sql_query(" select co_skin from {$g5['content_table']} limit 1 ", false)) {
    sql_query(
        " ALTER TABLE `{$g5['content_table']}`
                    ADD `co_skin` varchar(255) NOT NULL DEFAULT '' AFTER `co_mobile_content`,
                    ADD `co_mobile_skin` varchar(255) NOT NULL DEFAULT '' AFTER `co_skin` ",
        true
    );
    sql_query(" update {$g5['content_table']} set co_skin = 'basic', co_mobile_skin = 'basic' ");
}

$html_title = "내용";
$g5['title'] = $html_title . ' 관리';
$readonly = '';

if ($w == "u") {
    $html_title .= " 수정";
    $readonly = " readonly";

    $sql = " select * from {$g5['content_table']} where co_id = '$co_id' ";
    $co = sql_fetch($sql);
    if (!$co['co_id']) {
        alert('등록된 자료가 없습니다.');
    }
} else {
    $html_title .= ' 입력';
    $co = array(
        'co_id' => '',
        'co_subject' => '',
        'co_content' => '',
        'co_mobile_content' => '',
        'co_include_head' => '',
        'co_include_tail' => '',
        'co_tag_filter_use' => 1,
        'co_html' => 2,
        'co_skin' => 'basic',
        'co_mobile_skin' => 'basic'
    );
}

require_once G5_ADMIN_PATH . '/admin.head.php';
?>

<form name="frmcontentform" action="./contentformupdate.php" onsubmit="return frmcontentform_check(this);" method="post" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="co_html" value="1">
    <input type="hidden" name="token" value="">



    <ul class="formList">
      <li>
        <div class="label">ID</div>
        <div class="iptBox">
          <input type="text" value="<?php echo $co['co_id']; ?>" placeholder="20자 이내의 영문자, 숫자, _ 만 가능합니다." name="co_id" id="co_id" required <?php echo $readonly; ?> class="required <?php echo $readonly; ?> ipt" size="20" maxlength="20">
        </div>
      </li>
      <li>
        <div class="label">제목</div>
        <div class="iptBox">
          <input type="text" name="co_subject" value="<?php echo htmlspecialchars2($co['co_subject']); ?>" id="co_subject" required class="ipt" size="90">
        </div>
      </li>
      <li style="display:none">
        <div class="label">내용</div>
        <div class="iptBox">
          <input type="text" name="co_content" value="<div></div>" id="co_subject" required class="ipt" size="90">
        </div>
      </li>
    </ul>

    <div class="adm-btn-div">
      <a href="./contentlist.php" class="adm-btn">목록</a>
      <button type="submit" class="submit-btn adm-btn" accesskey="s">저장</button>
    </div>


</form>

<?php
// [KVE-2018-2089] 취약점 으로 인해 파일 경로 수정시에만 자동등록방지 코드 사용
?>
<script>
    var captcha_chk = false;

    function use_captcha_check() {
        $.ajax({
            type: "POST",
            url: g5_admin_url + "/ajax.use_captcha.php",
            data: {
                admin_use_captcha: "1"
            },
            cache: false,
            async: false,
            dataType: "json",
            success: function(data) {}
        });
    }

    function frm_check_file() {
        var co_include_head = "<?php echo $co['co_include_head']; ?>";
        var co_include_tail = "<?php echo $co['co_include_tail']; ?>";
        var head = jQuery.trim(jQuery("#co_include_head").val());
        var tail = jQuery.trim(jQuery("#co_include_tail").val());

        if (co_include_head !== head || co_include_tail !== tail) {
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

    jQuery(function($) {
        if (window.self !== window.top) { // frame 또는 iframe을 사용할 경우 체크
            $("#co_include_head, #co_include_tail").on("change paste keyup", function(e) {
                frm_check_file();
            });

            use_captcha_check();
        }
    });

    function frmcontentform_check(f) {
        errmsg = "";
        errfld = "";

        <?php echo get_editor_js('co_content'); ?>
        <?php echo chk_editor_js('co_content'); ?>
        <?php echo get_editor_js('co_mobile_content'); ?>

        check_field(f.co_id, "ID를 입력하세요.");
        check_field(f.co_subject, "제목을 입력하세요.");
        check_field(f.co_content, "내용을 입력하세요.");

        if (errmsg != "") {
            alert(errmsg);
            errfld.focus();
            return false;
        }

        if (captcha_chk) {
            <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함 ?>
        }

        return true;
    }
</script>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
