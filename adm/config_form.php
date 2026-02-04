<?php
    $sub_menu = "100100";
    require_once './_common.php';

    auth_check_menu($auth, $sub_menu, 'r');

    if ($is_admin != 'super') {
        alert('최고관리자만 접근 가능합니다.');
    }

    if (!isset($config['cf_add_script'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_add_script` TEXT NOT NULL AFTER `cf_admin_email_name` ",
            true
        );
    }

    if (!isset($config['cf_mobile_new_skin'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_mobile_new_skin` VARCHAR(255) NOT NULL AFTER `cf_memo_send_point`,
                        ADD `cf_mobile_search_skin` VARCHAR(255) NOT NULL AFTER `cf_mobile_new_skin`,
                        ADD `cf_mobile_connect_skin` VARCHAR(255) NOT NULL AFTER `cf_mobile_search_skin`,
                        ADD `cf_mobile_member_skin` VARCHAR(255) NOT NULL AFTER `cf_mobile_connect_skin` ",
            true
        );
    }

    if (isset($config['cf_gcaptcha_mp3'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        CHANGE `cf_gcaptcha_mp3` `cf_captcha_mp3` VARCHAR(255) NOT NULL DEFAULT '' ",
            true
        );
    } else if (!isset($config['cf_captcha_mp3'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_captcha_mp3` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_mobile_member_skin` ",
            true
        );
    }

    if (!isset($config['cf_editor'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_editor` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_captcha_mp3` ",
            true
        );
    }

    if (!isset($config['cf_googl_shorturl_apikey'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_googl_shorturl_apikey` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_captcha_mp3` ",
            true
        );
    }

    if (!isset($config['cf_mobile_pages'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_mobile_pages` INT(11) NOT NULL DEFAULT '0' AFTER `cf_write_pages` ",
            true
        );
        sql_query(" UPDATE `{$g5['config_table']}` SET cf_mobile_pages = '5' ", true);
    }

    if (!isset($config['cf_facebook_appid'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_facebook_appid` VARCHAR(255) NOT NULL AFTER `cf_googl_shorturl_apikey`,
                        ADD `cf_facebook_secret` VARCHAR(255) NOT NULL AFTER `cf_facebook_appid`,
                        ADD `cf_twitter_key` VARCHAR(255) NOT NULL AFTER `cf_facebook_secret`,
                        ADD `cf_twitter_secret` VARCHAR(255) NOT NULL AFTER `cf_twitter_key` ",
            true
        );
    }

    // uniqid 테이블이 없을 경우 생성
    if (!sql_query(" DESC {$g5['uniqid_table']} ", false)) {
        sql_query(
            " CREATE TABLE IF NOT EXISTS `{$g5['uniqid_table']}` (
                    `uq_id` bigint(20) unsigned NOT NULL,
                    `uq_ip` varchar(255) NOT NULL,
                    PRIMARY KEY (`uq_id`)
                    ) ",
            false
        );
    }

    if (!sql_query(" SELECT uq_ip from {$g5['uniqid_table']} limit 1 ", false)) {
        sql_query(" ALTER TABLE {$g5['uniqid_table']} ADD `uq_ip` VARCHAR(255) NOT NULL ");
    }

    // 임시저장 테이블이 없을 경우 생성
    if (!sql_query(" DESC {$g5['autosave_table']} ", false)) {
        sql_query(
            " CREATE TABLE IF NOT EXISTS `{$g5['autosave_table']}` (
                    `as_id` int(11) NOT NULL AUTO_INCREMENT,
                    `mb_id` varchar(20) NOT NULL,
                    `as_uid` bigint(20) unsigned NOT NULL,
                    `as_subject` varchar(255) NOT NULL,
                    `as_content` text NOT NULL,
                    `as_datetime` datetime NOT NULL,
                    PRIMARY KEY (`as_id`),
                    UNIQUE KEY `as_uid` (`as_uid`),
                    KEY `mb_id` (`mb_id`)
                    ) ",
            false
        );
    }

    if (!isset($config['cf_admin_email'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_admin_email` VARCHAR(255) NOT NULL AFTER `cf_admin` ",
            true
        );
    }

    if (!isset($config['cf_admin_email_name'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_admin_email_name` VARCHAR(255) NOT NULL AFTER `cf_admin_email` ",
            true
        );
    }

    if (!isset($config['cf_cert_use'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_cert_use` TINYINT(4) NOT NULL DEFAULT '0' AFTER `cf_editor`,
                        ADD `cf_cert_ipin` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_use`,
                        ADD `cf_cert_hp` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_ipin`,
                        ADD `cf_cert_kcb_cd` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_hp`,
                        ADD `cf_cert_kcp_cd` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_kcb_cd`,
                        ADD `cf_cert_limit` INT(11) NOT NULL DEFAULT '0' AFTER `cf_cert_kcp_cd` ",
            true
        );
        sql_query(
            " ALTER TABLE `{$g5['member_table']}`
                        CHANGE `mb_hp_certify` `mb_certify` VARCHAR(20) NOT NULL DEFAULT '' ",
            true
        );
        sql_query(" update {$g5['member_table']} set mb_certify = 'hp' where mb_certify = '1' ");
        sql_query(" update {$g5['member_table']} set mb_certify = '' where mb_certify = '0' ");
        sql_query(
            " CREATE TABLE IF NOT EXISTS `{$g5['cert_history_table']}` (
                    `cr_id` int(11) NOT NULL auto_increment,
                    `mb_id` varchar(255) NOT NULL DEFAULT '',
                    `cr_company` varchar(255) NOT NULL DEFAULT '',
                    `cr_method` varchar(255) NOT NULL DEFAULT '',
                    `cr_ip` varchar(255) NOT NULL DEFAULT '',
                    `cr_date` date NOT NULL DEFAULT '0000-00-00',
                    `cr_time` time NOT NULL DEFAULT '00:00:00',
                    PRIMARY KEY (`cr_id`),
                    KEY `mb_id` (`mb_id`)
                    )",
            true
        );
    }

    if (!isset($config['cf_analytics'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_analytics` TEXT NOT NULL AFTER `cf_intercept_ip` ",
            true
        );
    }

    if (!isset($config['cf_add_meta'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_add_meta` TEXT NOT NULL AFTER `cf_analytics` ",
            true
        );
    }

    if (!isset($config['cf_syndi_token'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_syndi_token` VARCHAR(255) NOT NULL AFTER `cf_add_meta` ",
            true
        );
    }

    if (!isset($config['cf_syndi_except'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_syndi_except` TEXT NOT NULL AFTER `cf_syndi_token` ",
            true
        );
    }

    if (!isset($config['cf_sms_use'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_sms_use` varchar(255) NOT NULL DEFAULT '' AFTER `cf_cert_limit`,
                        ADD `cf_icode_id` varchar(255) NOT NULL DEFAULT '' AFTER `cf_sms_use`,
                        ADD `cf_icode_pw` varchar(255) NOT NULL DEFAULT '' AFTER `cf_icode_id`,
                        ADD `cf_icode_server_ip` varchar(255) NOT NULL DEFAULT '' AFTER `cf_icode_pw`,
                        ADD `cf_icode_server_port` varchar(255) NOT NULL DEFAULT '' AFTER `cf_icode_server_ip` ",
            true
        );
    }

    if (!isset($config['cf_mobile_page_rows'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_mobile_page_rows` int(11) NOT NULL DEFAULT '0' AFTER `cf_page_rows` ",
            true
        );
    }

    if (!isset($config['cf_cert_req'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_cert_req` tinyint(4) NOT NULL DEFAULT '0' AFTER `cf_cert_limit` ",
            true
        );
    }

    if (!isset($config['cf_faq_skin'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_faq_skin` varchar(255) NOT NULL DEFAULT '' AFTER `cf_connect_skin`,
                        ADD `cf_mobile_faq_skin` varchar(255) NOT NULL DEFAULT '' AFTER `cf_mobile_connect_skin` ",
            true
        );
    }

    // LG유플러스 본인확인 필드 추가
    if (!isset($config['cf_lg_mid'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_lg_mid` varchar(255) NOT NULL DEFAULT '' AFTER `cf_cert_kcp_cd`,
                        ADD `cf_lg_mert_key` varchar(255) NOT NULL DEFAULT '' AFTER `cf_lg_mid` ",
            true
        );
    }

    if (!isset($config['cf_optimize_date'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_optimize_date` date NOT NULL default '0000-00-00' AFTER `cf_popular_del` ",
            true
        );
    }

    // 카카오톡링크 api 키
    if (!isset($config['cf_kakao_js_apikey'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_kakao_js_apikey` varchar(255) NOT NULL DEFAULT '' AFTER `cf_googl_shorturl_apikey` ",
            true
        );
    }

    // SMS 전송유형 필드 추가
    if (!isset($config['cf_sms_type'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_sms_type` varchar(10) NOT NULL DEFAULT '' AFTER `cf_sms_use` ",
            true
        );
    }

    // 접속자 정보 필드 추가
    if (!sql_query(" select vi_browser from {$g5['visit_table']} limit 1 ")) {
        sql_query(
            " ALTER TABLE `{$g5['visit_table']}`
                        ADD `vi_browser` varchar(255) NOT NULL DEFAULT '' AFTER `vi_agent`,
                        ADD `vi_os` varchar(255) NOT NULL DEFAULT '' AFTER `vi_browser`,
                        ADD `vi_device` varchar(255) NOT NULL DEFAULT '' AFTER `vi_os` ",
            true
        );
    }

    //소셜 로그인 관련 필드 및 구글 리챕챠 필드 추가
    if (!isset($config['cf_social_login_use'])) {
        sql_query(
            "ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_social_login_use` tinyint(4) NOT NULL DEFAULT '0' AFTER `cf_googl_shorturl_apikey`,
                    ADD `cf_google_clientid` varchar(100) NOT NULL DEFAULT '' AFTER `cf_twitter_secret`,
                    ADD `cf_google_secret` varchar(100) NOT NULL DEFAULT '' AFTER `cf_google_clientid`,
                    ADD `cf_naver_clientid` varchar(100) NOT NULL DEFAULT '' AFTER `cf_google_secret`,
                    ADD `cf_naver_secret` varchar(100) NOT NULL DEFAULT '' AFTER `cf_naver_clientid`,
                    ADD `cf_kakao_rest_key` varchar(100) NOT NULL DEFAULT '' AFTER `cf_naver_secret`,
                    ADD `cf_social_servicelist` varchar(255) NOT NULL DEFAULT '' AFTER `cf_social_login_use`,
                    ADD `cf_payco_clientid` varchar(100) NOT NULL DEFAULT '' AFTER `cf_social_servicelist`,
                    ADD `cf_payco_secret` varchar(100) NOT NULL DEFAULT '' AFTER `cf_payco_clientid`,
                    ADD `cf_captcha` varchar(100) NOT NULL DEFAULT '' AFTER `cf_kakao_js_apikey`,
                    ADD `cf_recaptcha_site_key` varchar(100) NOT NULL DEFAULT '' AFTER `cf_captcha`,
                    ADD `cf_recaptcha_secret_key` varchar(100) NOT NULL DEFAULT '' AFTER `cf_recaptcha_site_key`
        ",
            true
        );
    }

    //소셜 로그인 관련 필드 카카오 클라이언트 시크릿 추가
    if (!isset($config['cf_kakao_client_secret'])) {
        sql_query(
            "ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_kakao_client_secret` varchar(100) NOT NULL DEFAULT '' AFTER `cf_kakao_rest_key`
        ",
            true
        );
    }

    // 회원 이미지 관련 필드 추가
    if (!isset($config['cf_member_img_size'])) {
        sql_query(
            "ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_member_img_size` int(11) NOT NULL DEFAULT '0' AFTER `cf_member_icon_height`,
                    ADD `cf_member_img_width` int(11) NOT NULL DEFAULT '0' AFTER `cf_member_img_size`,
                    ADD `cf_member_img_height` int(11) NOT NULL DEFAULT '0' AFTER `cf_member_img_width`
        ",
            true
        );

        $sql = " update {$g5['config_table']} set cf_member_img_size = 50000, cf_member_img_width = 60, cf_member_img_height = 60 ";
        sql_query($sql, false);

        $config['cf_member_img_size'] = 50000;
        $config['cf_member_img_width'] = 60;
        $config['cf_member_img_height'] = 60;
    }

    // 소셜 로그인 관리 테이블 없을 경우 생성
    if (!sql_query(" DESC {$g5['social_profile_table']} ", false)) {
        sql_query(
            " CREATE TABLE IF NOT EXISTS `{$g5['social_profile_table']}` (
                    `mp_no` int(11) NOT NULL AUTO_INCREMENT,
                    `mb_id` varchar(255) NOT NULL DEFAULT '',
                    `provider` varchar(50) NOT NULL DEFAULT '',
                    `object_sha` varchar(45) NOT NULL DEFAULT '',
                    `identifier` varchar(255) NOT NULL DEFAULT '',
                    `profileurl` varchar(255) NOT NULL DEFAULT '',
                    `photourl` varchar(255) NOT NULL DEFAULT '',
                    `displayname` varchar(150) NOT NULL DEFAULT '',
                    `description` varchar(255) NOT NULL DEFAULT '',
                    `mp_register_day` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `mp_latest_day` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    UNIQUE KEY `mp_no` (`mp_no`),
                    KEY `mb_id` (`mb_id`),
                    KEY `provider` (`provider`)
                    ) ",
            true
        );
    }

    // 짧은 URL 주소를 사용 여부 필드 추가
    if (!isset($config['cf_bbs_rewrite'])) {
        sql_query(
            " ALTER TABLE `{$g5['config_table']}`
                        ADD `cf_bbs_rewrite` tinyint(4) NOT NULL DEFAULT '0' AFTER `cf_link_target` ",
            true
        );
    }

    // 읽지 않은 메모 수 칼럼 추가
    if (!isset($member['mb_memo_cnt'])) {
        sql_query(
            " ALTER TABLE `{$g5['member_table']}`
                    ADD `mb_memo_cnt` int(11) NOT NULL DEFAULT '0' AFTER `mb_memo_call`",
            true
        );
    }

    // 스크랩 읽은 수 추가
    if (!isset($member['mb_scrap_cnt'])) {
        sql_query(
            " ALTER TABLE `{$g5['member_table']}`
                    ADD `mb_scrap_cnt` int(11) NOT NULL DEFAULT '0' AFTER `mb_memo_cnt`",
            true
        );
    }

    // 아이코드 토큰키 추가
    if (!isset($config['cf_icode_token_key'])) {
        $sql = "ALTER TABLE `{$g5['config_table']}` 
                ADD COLUMN `cf_icode_token_key` VARCHAR(100) NOT NULL DEFAULT '' AFTER `cf_icode_server_port`; ";
        sql_query($sql, false);
    }
    // 아이디/비밀번호 찾기에 본인확인 사용 여부 필드 추가
    if (!isset($config['cf_cert_find'])) {
        $sql = "ALTER TABLE `{$g5['config_table']}` 
                ADD COLUMN `cf_cert_find` TINYINT(4) NOT NULL DEFAULT '0' AFTER `cf_cert_use`; ";
        sql_query($sql, false);
    }
    // 간편인증 필드 추가
    if (!isset($config['cf_cert_simple'])) {
        $sql = "ALTER TABLE `{$g5['config_table']}` 
                ADD COLUMN `cf_cert_simple` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_hp`; ";
        sql_query($sql, false);
    }
    if (!isset($config['cf_cert_kg_cd'])) {
        $sql = "ALTER TABLE `{$g5['config_table']}`
                ADD COLUMN `cf_cert_kg_cd` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_simple`; ";
        sql_query($sql, false);
    }
    if (!isset($config['cf_cert_kg_mid'])) {
        $sql = "ALTER TABLE `{$g5['config_table']}`
                ADD COLUMN `cf_cert_kg_mid` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_kg_cd`; ";
        sql_query($sql, false);
    }
    if (!$config['cf_faq_skin']) {
        $config['cf_faq_skin'] = "basic";
    }
    if (!$config['cf_mobile_faq_skin']) {
        $config['cf_mobile_faq_skin'] = "basic";
    }

    $g5['title'] = '환경설정';
    require_once './admin.head.php';

    if (!$config['cf_icode_server_ip']) {
        $config['cf_icode_server_ip'] = '211.172.232.124';
    }
    if (!$config['cf_icode_server_port']) {
        $config['cf_icode_server_port'] = '7295';
    }

    $userinfo = array('payment' => '');
    if ($config['cf_sms_use'] && $config['cf_icode_id'] && $config['cf_icode_pw']) {
        $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);
    }
?>


<form name="fconfigform" id="fconfigform" action="./config_form_update2.php" method="post"    enctype="multipart/form-data" onsubmit="return fconfigform_submit(this);" >
    <input type="hidden" name="token" value="" id="token">

    <h1>기본환경설정</h1>
    <div class="map-div">
      <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
      <a href="<?=G5_ADMIN_URL;?>/config_form.php">환경설정</a> > 
      <a href="<?=G5_ADMIN_URL;?>/config_form.php">기본환경설정</a>
    </div>
    <div class="margin-div"></div>

    <h2>홈페이지 설정</h2>
    <ul class="formList">
        <li>
            <div class="label">홈페이지 제목 <span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_title" class="ipt" placeholder="홈페이지 제목을 입력해주세요." value="<?php echo get_sanitize_input($config['cf_title']); ?>" id="cf_title" required />
            </div>
        </li>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li>
            <div class="label">방문자분석 스크립트<span class="red">*</span></div>
            <div class="iptBox">
                <div class="notice">
                    ✅ 구글 애널리틱스
                </div>
                <textarea name="cf_analytics" id="cf_analytics" class="ipt" placeholder="방문자분석 스크립트 코드를 입력합니다."><?php echo get_text($config['cf_analytics']); ?></textarea>
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li>
            <div class="label">추가 메타태그<span class="red">*</span></div>
            <div class="iptBox">
                <div class="notice">
                    ✅ 추가로 사용하실 meta 태그를 입력합니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;관리자 페이지에서는 이 코드를 사용하지 않습니다.
                </div>
                <textarea name="cf_add_meta" id="cf_add_meta" class="ipt" placeholder="추가로 사용하실 meta 태그를 입력합니다."><?php echo get_text($config['cf_add_meta']); ?></textarea>
            </div>
        </li>
        <?php } ?>
        <li>
            <div class="label">썸네일 설정<span class="red">*</span></div>
            <div class="iptBox">
                <div class="notice">
                    ✅ 카카오 채팅방 등에서 보여지는 미리보기 썸네일 이미지 설정입니다.<br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(권장 사이즈 : 860px 440px)
                </div>
                <!-- <img src="<?=G5_ADMIN_URL;?>/img/sns_site_ex.png" class="admin_seo"/> -->
                  <input type="file" name="thumbnail" accept="image/*">
                <? if(file_exists(G5_IMG_PATH.'/thumbnail.png')){ ?> 
                  <img class="thumbnail" src="<?=G5_IMG_URL.'/thumbnail.png'?>?ver=<?=time()?>" alt="썸네일"> 
                  <label><input type="checkbox" name="thumbnail_delete"/> 썸네일 파일 삭제 (체크 후 저장)</label>
                <? } ?>
            </div>
        </li>
        <li>
            <div class="label">파비콘 설정<span class="red">*</span></div>
            <div class="iptBox">
                <div class="notice">
                    ✅ 파비콘 설정입니다.(권장 사이즈 : 16px 16px )
                </div>
                <input type="file" name="favicon" accept="image/*">
                <? if(file_exists(G5_IMG_PATH.'/favicon.ico')){ ?>
                  <img class="favicon" src="<?=G5_IMG_URL.'/favicon.ico'?>?ver=<?=time()?>" alt="파비콘">
                  <label><input type="checkbox" name="favicon_delete"/> 파비콘 파일 삭제 (체크 후 저장)</label>
                <? } ?>
            </div>
        </li>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">나이스페이먼츠<br/>CLIENTID</div>
            <div class="iptBox flex">
                <input type="text" name="cf_5_subj" value="<?php echo $config['cf_5_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <?php } ?>
        <li class="half">
            <div class="label">나이스페이먼츠<br/>SECRETKEY</div>
            <div class="iptBox flex">
                <input type="text" name="cf_6_subj" value="<?php echo $config['cf_6_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li>
            <div class="label">개인정보 처리방침</div>
            <div class="iptBox">
                <textarea name="cf_privacy" id="cf_privacy" class="ipt" placeholder="해당 홈페이지에 맞는 개인정보 처리방침을 입력합니다."><?php echo get_text($config['cf_privacy']); ?></textarea>
            </div>
        </li>
        <li>
            <div class="label">회원가입 약관</div>
            <div class="iptBox">
                <textarea name="cf_stipulation" id="cf_stipulation" class="ipt" placeholder="해당 홈페이지에 맞는 회원가입약관을 입력합니다."><?php echo get_text($config['cf_stipulation']); ?></textarea>
            </div>
        </li>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li>
            <div class="label">이용약관</div>
            <div class="iptBox">
                <textarea name="cf_1_subj" id="cf_1_subj" class="ipt" placeholder="해당 홈페이지에 맞는 이용약관을 입력합니다."><?php echo get_text($config['cf_1_subj']); ?></textarea>
            </div>
        </li>   
        <?php } ?>

        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">주소<span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_1" class="ipt" placeholder="사업장 주소를 입력해주세요." value="<?php echo get_sanitize_input($config['cf_1']); ?>"/>
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">사업자<span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_2_subj" class="ipt" placeholder="사업자 성함을 입력해주세요." value="<?php echo get_sanitize_input($config['cf_2_subj']); ?>"/>
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">전화번호<span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_2" class="ipt" placeholder="홈페이지에 노출할 연락처를 입력해주세요." value="<?php echo get_sanitize_input($config['cf_2']); ?>"/>
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">팩스<span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_3_subj" class="ipt" placeholder="팩스 번호를 입력해주세요." value="<?php echo get_sanitize_input($config['cf_3_subj']); ?>"/>
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">영업시간<span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_3" class="ipt" placeholder="영업시간을 입력해주세요." value="<?php echo get_sanitize_input($config['cf_3']); ?>"/>
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li class="half">
            <div class="label">이메일<span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_4_subj" class="ipt" placeholder="이메일 주소를 입력해주세요." value="<?php echo get_sanitize_input($config['cf_4_subj']); ?>" />
            </div>
        </li>
        <?php } ?>
        <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <li>
            <div class="label">지도 iframe<span class="red">*</span></div>
            <div class="iptBox">
                <textarea type="text" name="cf_4" class="ipt" placeholder="이메일 주소를 입력해주세요."><?php echo get_sanitize_input($config['cf_4']); ?></textarea>
            </div>
        </li>
        <?php } ?>
    </ul>

    <div class="margin-div"></div>

    <h2>메일 설정</h2>
    <ul class="formList">
        <li>
            <div class="label">관리자 메일 주소 <span class="red">*</span></div>
            <div class="iptBox">
                <input type="email" name="cf_admin_email" value="<?php echo get_sanitize_input($config['cf_admin_email']); ?>" id="cf_admin_email" required class="ipt" placeholder="관리자 메일주소를 입력해주세요" />
            </div>
        </li>
        <li>
            <div class="label">관리자 메일 발송이름 <span class="red">*</span></div>
            <div class="iptBox">
                <input type="text" name="cf_admin_email_name" value="<?php echo get_sanitize_input($config['cf_admin_email_name']); ?>" id="cf_admin_email_name" required class="ipt" placeholder="관리자 메일 발송이름을 입력해주세요" />
            </div>
        </li>
        <li>
            <div class="label">글등록 메일 <span class="red">*</span></div>
            <div class="iptBox">
                <div class="notice">
                    ✅ 게시판 글작성시 최고관리자에게 메일을 발송합니다.
                </div>
                <label>
                    <input type="checkbox" name="cf_email_wr_super_admin" value="1" id="cf_email_wr_super_admin" <?php echo $config['cf_email_wr_super_admin'] ? 'checked' : ''; ?>/>
                    <span class="text"> 사용 </span>
                </label>
            </div>
        </li>
    </ul>

    <div class="margin-div"></div>

    <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <h2>게시판 기본 설정</h2>
        <ul class="formList">
            <li>
                <div class="label">이미지 업로드 확장자 <span class="red">*</span></div>
                <div class="iptBox">
                    <div class="notice">
                        ✅ 게시판 글작성시 이미지 파일 업로드 가능 확장자. | 로 구분
                    </div>
                    <input type="text" name="cf_image_extension" value="<?php echo get_sanitize_input($config['cf_image_extension']); ?>" id="cf_image_extension"  class="ipt" placeholder=""/>
                </div>
            </li>
        </ul>
    <?php } ?>


    <!-- 📢 SMS 서비스 사용시 주석 해제 -->
    <?php if($config['cf_admin_status'] !== 'on'){ ?>
    <div class="margin-div"></div>
    <h2>SMS</h2>
    <ul class="formList">
        <li>
            <div class="label">SMS사용</div>
            <div class="iptBox">
                <select id="cf_sms_use" name="cf_sms_use" class="ipt">
                    <option value="" <?php echo get_selected($config['cf_sms_use'], ''); ?>>사용안함</option>
                    <option value="icode" <?php echo get_selected($config['cf_sms_use'], 'icode'); ?>>아이코드</option>
                </select>
            </div>
        </li>
        <li>
            <div class="label">SMS 전송유형</div>
            <div class="iptBox">
                <select id="cf_sms_type" name="cf_sms_type" class="ipt">
                    <option value="" <?php echo get_selected($config['cf_sms_type'], ''); ?>>SMS</option>
                    <option value="LMS" <?php echo get_selected($config['cf_sms_type'], 'LMS'); ?>>LMS</option>
                </select>
            </div>
        </li>
        <li class="half">
            <div class="label">아이코드 ID 구버전</div>
            <div class="iptBox">
                <input type="text" name="cf_icode_id" value="<?php echo get_sanitize_input($config['cf_icode_id']); ?>" id="cf_icode_id" class="ipt" size="20">
            </div>
        </li>
        <li class="half">
            <div class="label">아이코드 PW 구버전</div>
            <div class="iptBox">
                <input type="password" name="cf_icode_pw" value="<?php echo get_sanitize_input($config['cf_icode_pw']); ?>" id="cf_icode_pw" class="ipt">
            </div>
        </li>
        <li class="half" style="<?php if (!(isset($userinfo['payment']) && $userinfo['payment'])) { echo 'display:none'; } ?>">
            <div class="label">요금제 구버전</div>
            <div class="iptBox">
                <input type="hidden" name="cf_icode_server_ip" value="<?php echo get_sanitize_input($config['cf_icode_server_ip']); ?>" class="ipt">
                <?php
                if ($userinfo['payment'] == 'A') {
                    echo '충전제';
                    echo '<input type="hidden" name="cf_icode_server_port" value="7295" class="ipt">';
                } elseif ($userinfo['payment'] == 'C') {
                    echo '정액제';
                    echo '<input type="hidden" name="cf_icode_server_port" value="7296" class="ipt">';
                } else {
                    echo '가입해주세요.';
                    echo '<input type="hidden" name="cf_icode_server_port" value="7295" class="ipt">';
                }
                ?>
            </div>
        </li>
        <li>
            <div class="label">아이코드 토큰키 (JSON)</div>
            <div class="iptBox">
                <div class="notice">
                    ✅ 아이코드 JSON 버전의 경우 아이코드 토큰키를 입력시 실행됩니다.<br>SMS 전송유형을 LMS로 설정시 90바이트 이내는 SMS, 90 ~ 2000 바이트는 LMS 그 이상은 절삭 되어 LMS로 발송됩니다.
                </div>
                <input type="text" name="cf_icode_token_key" value="<?php echo isset($config['cf_icode_token_key']) ? get_sanitize_input($config['cf_icode_token_key']) : ''; ?>" id="cf_icode_token_key" class="ipt" size="40">
                <div class="notice">
                    ✅ 아이코드 사이트 -> 토큰키관리 메뉴에서 생성한 토큰키를 입력합니다.
                </div>
                💻 서버아이피 : <?php echo $_SERVER['SERVER_ADDR']; ?>
            </div>
        </li>
        <li>
            <div class="label">아이코드 가입</div>
            <div class="iptBox">
                <a href="http://icodekorea.com/res/join_company_fix_a.php?sellid=sir2" target="_blank" class="btn_admin">아이코드 회원가입</a>
            </div>
        </li>
    </ul>
    <?php } ?>


    <!-- 📢 소셜 네트워크 서비스 사용시 주석 해제 -->
    <?php if($config['cf_admin_status'] !== 'on'){ ?>
        <div class="margin-div"></div>
        <h2>소셜네트워크서비스(SNS : Social Network Service)</h2>
        <ul class="formList">
            <li>
                <div class="label">소셜로그인설정</div>
                <div class="iptBox">
                    <div class="notice">
                    ✅ 소셜로그인을 사용합니다. <a href="https://sir.kr/manual/g5/276" class="btn_admin" target="_blank" style="margin-left:10px" >설정 관련 메뉴얼 보기</a> 
                    </div>
                    <input type="checkbox" name="cf_social_login_use" value="1" id="cf_social_login_use" <?php echo (!empty($config['cf_social_login_use'])) ? 'checked' : ''; ?>> 사용
                </div>
            </li>
            <li>
                <div class="label">네이버 로그인</div>
                <div class="iptBox">
                    <div class="notice">
                        네이버 로그인을 사용합니다.
                        <input type="checkbox" name="cf_social_servicelist[]" id="check_social_naver" value="naver" <?php echo option_array_checked('naver', $config['cf_social_servicelist']); ?>>
                    </div>
                    <div> 
                        <h3>네이버 CallbackURL</h3>
                        <p><?php echo get_social_callbackurl('naver'); ?></p>
                    </div>
                </div>
            </li>
            <li>
                <div class="label">카카오 로그인</div>
                <div class="iptBox">
                    <div class="notice">
                        카카오 로그인을 사용합니다.
                        <input type="checkbox" name="cf_social_servicelist[]" id="check_social_kakao" value="kakao" <?php echo option_array_checked('kakao', $config['cf_social_servicelist']); ?>>
                    </div>
                    <div>
                        <h3>카카오 로그인 Redirect URI</h3>
                        <p><?php echo get_social_callbackurl('kakao', true); ?></p>
                    </div>
                </div>
            </li>
            <li>
                <div class="label">페이스북 로그인</div>
                <div class="iptBox">
                    <div class="notice">
                        페이스북 로그인을 사용합니다.
                        <input type="checkbox" name="cf_social_servicelist[]" id="check_social_facebook" value="facebook" <?php echo option_array_checked('facebook', $config['cf_social_servicelist']); ?>>
                    </div>
                    <div>
                        <h3>페이스북 유효한 OAuth 리디렉션 URI</h3>
                        <p><?php echo get_social_callbackurl('facebook'); ?></p>
                    </div>
                </div>
            </li>
            <li>
                <div class="label">구글 로그인</div>
                <div class="iptBox">
                    <div class="notice">
                        구글 로그인을 사용합니다.
                                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_google" value="google" <?php echo option_array_checked('google', $config['cf_social_servicelist']); ?>>
                    </div>
                    <div>
                        <h3>구글 승인된 리디렉션 URI</h3>
                        <p><?php echo get_social_callbackurl('google'); ?></p>
                    </div>
                </div>
            </li>
            <li>
                <div class="label">트위터 로그인</div>
                <div class="iptBox">
                    <div class="notice">
                        트위터 로그인을 사용합니다.
                        <input type="checkbox" name="cf_social_servicelist[]" id="check_social_twitter" value="twitter" <?php echo option_array_checked('twitter', $config['cf_social_servicelist']); ?>>
                    </div>
                    <div>
                        <h3>트위터 CallbackURL</h3>
                        <p><?php echo get_social_callbackurl('twitter'); ?></p>
                    </div>
                </div>
            </li>
            <li>
                <div class="label">페이코 로그인</div>
                <div class="iptBox">
                    <div class="notice">
                        페이코 로그인을 사용합니다.
                        <input type="checkbox" name="cf_social_servicelist[]" id="check_social_payco" value="payco" <?php echo option_array_checked('payco', $config['cf_social_servicelist']); ?>>
                    </div>
                    <div>
                        <h3>페이코 CallbackURL</h3>
                        <p><?php echo get_social_callbackurl('payco', false, true); ?></p>
                    </div>
                </div>
            </li>
        </ul>
    <?php } ?>

    <!-- 📢 여분필드 사용시 주석 해제 -->     
    <!-- <div class="margin-div"></div>
    <h2>여분필드</h2>
    <ul class="formList">
        <li class="half">
            <div class="label">cf_5</div>
            <div class="iptBox">
                <input type="text" name="cf_5" value="<?php echo $config['cf_5']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_6_subj</div>
            <div class="iptBox">
                <input type="text" name="cf_6_subj" value="<?php echo $config['cf_6_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_6</div>
            <div class="iptBox">
                <input type="text" name="cf_6" value="<?php echo $config['cf_6']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_7_subj</div>
            <div class="iptBox">
                <input type="text" name="cf_7_subj" value="<?php echo $config['cf_7_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_7</div>
            <div class="iptBox">
                <input type="text" name="cf_7" value="<?php echo $config['cf_7']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_8_subj</div>
            <div class="iptBox">
                <input type="text" name="cf_8_subj" value="<?php echo $config['cf_8_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_8</div>
            <div class="iptBox">
                <input type="text" name="cf_8" value="<?php echo $config['cf_8']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_9_subj</div>
            <div class="iptBox">
                <input type="text" name="cf_9_subj" value="<?php echo $config['cf_9_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_9</div>
            <div class="iptBox">
                <input type="text" name="cf_9" value="<?php echo $config['cf_9']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_10_subj</div>
            <div class="iptBox">
                <input type="text" name="cf_10_subj" value="<?php echo $config['cf_10_subj']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
        <li class="half">
            <div class="label">cf_10</div>
            <div class="iptBox">
                <input type="text" name="cf_10" value="<?php echo $config['cf_10']; ?>" class="ipt" placeholder=""/>
            </div>
        </li>
    </ul> -->

    <div class="adm-btn-div">
        <button type="submit" class="submit-btn adm-btn" accesskey="s">저장</button>
    </div>
</form>



<script>

    // 각 요소의 초기값 저장
    var initialValues = {
        cf_admin: $('#cf_admin').val(),
        cf_analytics: $('#cf_analytics').val(),
        cf_add_meta: $('#cf_add_meta').val(),
        cf_add_script: $('#cf_add_script').val()
    };

    function check_config_captcha_open() {
        var isChanged = false;

        // 현재 값이 있는 경우에만 변경 여부 체크
        if ($('#cf_admin').val()) {
            isChanged = isChanged || $('#cf_admin').val() !== initialValues.cf_admin;
        }
        if ($('#cf_analytics').val()) {
            isChanged = isChanged || $('#cf_analytics').val() !== initialValues.cf_analytics;
        }
        if ($('#cf_add_meta').val()) {
            isChanged = isChanged || $('#cf_add_meta').val() !== initialValues.cf_add_meta;
        }
        if ($('#cf_add_script').val()) {
            isChanged = isChanged || $('#cf_add_script').val() !== initialValues.cf_add_script;
        }
        
        var $wrap = $("#config_captcha_wrap"),
            tooptipid = "mp_captcha_tooltip",
            $p_text = $("<p>", {id:tooptipid, style:"font-size:0.95em;letter-spacing:-0.1em"}).html("중요정보를 수정할 경우 캡챠를 입력해야 합니다."),
            $children = $wrap.children(':first'),
            is_invisible_recaptcha = $("#captcha").hasClass("invisible_recaptcha");

        if(isChanged){
            $wrap.show();
            if(! is_invisible_recaptcha) {
                $wrap.css("margin-top","1em");
                if(! $("#"+tooptipid).length){ $children.after($p_text) }
            }
        } else {
            $wrap.hide();
            if($("#"+tooptipid).length && ! is_invisible_recaptcha){ $children.next("#"+tooptipid).remove(); }
        }
        
        return isChanged;
    }
        
    function fconfigform_submit(f) {

       /*  var current_user_ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
        var cf_intercept_ip_val = f.cf_intercept_ip.value;
        
        
        if (cf_intercept_ip_val && current_user_ip) {
            var cf_intercept_ips = cf_intercept_ip_val.split("\n");

            for (var i = 0; i < cf_intercept_ips.length; i++) {
                if (cf_intercept_ips[i].trim()) {
                    cf_intercept_ips[i] = cf_intercept_ips[i].replace(".", "\.");
                    cf_intercept_ips[i] = cf_intercept_ips[i].replace("+", "[0-9\.]+");

                    var re = new RegExp(cf_intercept_ips[i]);
                    if (re.test(current_user_ip)) {
                        alert("현재 접속 IP : " + current_user_ip + " 가 차단될수 있기 때문에, 다른 IP를 입력해 주세요.");
                        return false;
                    }
                }
            }
        }

        f.action = "./config_form_update2.php";
        return true; */
    }
    
    jQuery(function($){
        $("#captcha_key").prop('required', false).removeAttr("required").removeClass("required");
        
        // 최고관리자 변경시
        $(document).on('change', '#cf_admin', check_config_captcha_open);

        // 방문자분석 스크립트 변경시
        $(document).on('input', '#cf_analytics', check_config_captcha_open);
        
        // 추가 메타태그 변경시
        $(document).on('input', '#cf_add_meta', check_config_captcha_open);
        
        // 추가 script, css 변경시
        $(document).on('input', '#cf_add_script', check_config_captcha_open);
    });
</script>

<?php require_once './admin.tail.php'; ?>
