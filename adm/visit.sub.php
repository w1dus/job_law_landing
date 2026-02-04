<?php
if (!defined('_GNUBOARD_')) exit;

include_once(G5_LIB_PATH.'/visit.lib.php');
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if (empty($fr_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date) ) $fr_date = G5_TIME_YMD;
if (empty($to_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date) ) $to_date = G5_TIME_YMD;

$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date;
$query_string = $qstr ? '?'.$qstr : '';
?>

<h1>접속자 관리</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/member_list.php">회원관리</a> > 
  <a href="<?=G5_ADMIN_URL;?>/visit_list.php">접속자 관리</a>
</div>
<div class="margin-div"></div>

<div class="title-space-box">
    <h2>방문자 접속통계</h2>
</div>

<div class="total-hit-count-div">
    <div class="item">
    <div class="label">오늘 방문자 수</div>
        <div class="count">
            <?php 
            $sql_today = "SELECT COUNT(*) as cnt FROM g5_visit WHERE vi_date = CURDATE()";
            $result_today = sql_query($sql_today);
            $row_today = sql_fetch_array($result_today);
            $today_count = number_format($row_today['cnt']);

            echo $today_count; 
            ?>
        </div>
    </div>
    <div class="item">
        <div class="label">총 방문자 수</div>
        <div class="count">
            <?php 
            $sql_total = "SELECT COUNT(*) as cnt FROM g5_visit";
            $row_total = sql_fetch_array(sql_query($sql_total));
            $total_count = number_format($row_total['cnt']);
            echo $total_count; 
            ?>
        </div>
    </div>
    <div class="item">
        <div class="label">어제 방문자 수</div>
        <div class="count">
            <?php 
            $sql_yesterday = "SELECT COUNT(*) as cnt FROM g5_visit WHERE vi_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            $row_yesterday = sql_fetch_array(sql_query($sql_yesterday));
            $yesterday_count = number_format($row_yesterday['cnt']);

            echo $yesterday_count;
            ?>
        </div>
    </div>
    <div class="item">
        <div class="label">금주 방문자 수</div>
        <div class="count">
            <?php 
            //이번 주 방문자 수 (월요일 ~ 오늘)
            $sql_week = "SELECT COUNT(*) as cnt FROM g5_visit WHERE YEARWEEK(vi_date, 1) = YEARWEEK(CURDATE(), 1)";
            $row_week = sql_fetch_array(sql_query($sql_week));
            $week_count = number_format($row_week['cnt']);
            echo $week_count;
            ?>
        </div>
    </div>
</div> 

<div class="margin-div"></div>

<form name="fvisit" id="fvisit" class="local_sch03 local_sch" method="get">
<div class="sch_last">
    <strong>기간별검색</strong>
    <input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input" size="11" maxlength="10">
    <label for="fr_date" class="sound_only">시작일</label>
    ~
    <input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input" size="11" maxlength="10">
    <label for="to_date" class="sound_only">종료일</label>
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<ul class="anchor">
    <li><a href="./visit_list.php<?php echo $query_string ?>">접속자</a></li>
    <li><a href="./visit_domain.php<?php echo $query_string ?>">도메인</a></li>
    <li><a href="./visit_browser.php<?php echo $query_string ?>">브라우저</a></li>
    <li><a href="./visit_os.php<?php echo $query_string ?>">운영체제</a></li>
    <?php if(version_compare(phpversion(), '5.3.0', '>=') && defined('G5_BROWSCAP_USE') && G5_BROWSCAP_USE) { ?>
    <li><a href="./visit_device.php<?php echo $query_string ?>">접속기기</a></li>
    <?php } ?>
    <li><a href="./visit_hour.php<?php echo $query_string ?>">시간</a></li>
    <li><a href="./visit_week.php<?php echo $query_string ?>">요일</a></li>
    <li><a href="./visit_date.php<?php echo $query_string ?>">일</a></li>
    <li><a href="./visit_month.php<?php echo $query_string ?>">월</a></li>
    <li><a href="./visit_year.php<?php echo $query_string ?>">년</a></li>
</ul>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}
</script>
