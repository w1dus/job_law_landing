<?php
    $sub_menu = "200800";
    include_once('./_common.php');

    auth_check_menu($auth, $sub_menu, 'r');

    $fr_date = isset($_REQUEST['fr_date']) ? preg_replace('/[^0-9 :\-]/i', '', $_REQUEST['fr_date']) : G5_TIME_YMD;
    $to_date = isset($_REQUEST['to_date']) ? preg_replace('/[^0-9 :\-]/i', '', $_REQUEST['to_date']) : G5_TIME_YMD;

    $g5['title'] = '접속자집계';
    include_once('./visit.sub.php');

    $colspan = 6;

    $sql_common = " from {$g5['visit_table']} ";
    $sql_search = " where vi_date between '{$fr_date}' and '{$to_date}' ";
    if (isset($domain))
        $sql_search .= " and vi_referer like '%{$domain}%' ";

    $sql = " select count(*) as cnt
                {$sql_common}
                {$sql_search} ";
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $rows = $config['cf_page_rows'];
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = " select *
                {$sql_common}
                {$sql_search}
                order by vi_id desc
                limit {$from_record}, {$rows} ";
    $result = sql_query($sql);

    // echo $sql;
?>



<article class="boardArti basicBoardArti">   

    <ul class="basicList">
        <li class="boardTitle">
            <div class="item">
                <div class="writer center">IP</div>
                <div class="title center">접속 경로</div>
                <div class="writer center">브라우저</div>
                <div class="hit center">OS</div>
                <div class="date center">접속기기</div>
                <div class="date center" style="width:200px">일시</div>
            </div>
        </li>
        <?php
            for ($i=0; $row=sql_fetch_array($result); $i++) {
                $brow = $row['vi_browser'];
                if(!$brow)
                    $brow = get_brow($row['vi_agent']);

                $os = $row['vi_os'];
                if(!$os)
                    $os = get_os($row['vi_agent']);

                $device = $row['vi_device'];

                $link = '';
                $link2 = '';
                $referer = '';
                $title = '';
                if ($row['vi_referer']) {

                    $referer = get_text(cut_str($row['vi_referer'], 255, ''));
                    $referer = urldecode($referer);

                    if (!is_utf8($referer)) {
                        $referer = iconv_utf8($referer);
                    }

                    $title = str_replace(array('<', '>', '&'), array("&lt;", "&gt;", "&amp;"), $referer);
                    $link = '<a href="'.get_text($row['vi_referer']).'" target="_blank">';
                    $link = str_replace('&', "&amp;", $link);
                    $link2 = '</a>';
                }

                if ($is_admin == 'super')
                    $ip = $row['vi_ip'];
                else
                    $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $row['vi_ip']);

                if ($brow == '기타') { $brow = '<span title="'.get_text($row['vi_agent']).'">'.$brow.'</span>'; }
                if ($os == '기타') { $os = '<span title="'.get_text($row['vi_agent']).'">'.$os.'</span>'; }

                $bg = 'bg'.($i%2);
            ?>
       <li>
            <div class="item">
                <div class="writer center"><?php echo $ip ?></div>
                <div class="title center"><?php echo $link ?><?php echo $title ?><?php echo $link2 ?></div>
                <div class="writer center"><?php echo $brow ?></div>
                <div class="hit center"><?php echo $os ?></div>
                <div class="date center"><?php echo $device; ?></div>
                <div class="date center" style="width:200px"><?php echo $row['vi_date'] ?> <?php echo $row['vi_time'] ?></div>
            </div>
        </li>
        <?php 
            }
            if ($i == 0) {
                echo '<div class="empty_table">등록된 자료가 없습니다.</div>';
            }
        ?>
    </ul>   

    <div class="paging-box">
      <?php
          if (isset($domain))
              $qstr .= "&amp;domain=$domain";
          $qstr .= "&amp;page=";

          $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
          echo $pagelist;
      ?>
    </div>

</article>




<?php include_once('./admin.tail.php'); ?>