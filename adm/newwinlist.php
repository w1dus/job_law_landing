<?php

  $sub_menu = '100310';
  require_once './_common.php';

  auth_check_menu($auth, $sub_menu, "r");

  if (!isset($g5['new_win_table'])) {
      die('<meta charset="utf-8">/data/dbconfig.php 파일에 <strong>$g5[\'new_win_table\'] = G5_TABLE_PREFIX.\'new_win\';</strong> 를 추가해 주세요.');
  }
  
  //내용(컨텐츠)정보 테이블이 있는지 검사한다.
  if (!sql_query(" DESCRIBE {$g5['new_win_table']} ", false)) {
    if (sql_query(" DESCRIBE {$g5['g5_shop_new_win_table']} ", false)) {
        sql_query(" ALTER TABLE {$g5['g5_shop_new_win_table']} RENAME TO `{$g5['new_win_table']}` ;", false);
    } else {
        $query_cp = sql_query(
            " CREATE TABLE IF NOT EXISTS `{$g5['new_win_table']}` (
                      `nw_id` int(11) NOT NULL AUTO_INCREMENT,
                      `nw_division` varchar(10) NOT NULL DEFAULT 'both',
                      `nw_device` varchar(10) NOT NULL DEFAULT 'both',
                      `nw_begin_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                      `nw_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                      `nw_disable_hours` int(11) NOT NULL DEFAULT '0',
                      `nw_left` int(11) NOT NULL DEFAULT '0',
                      `nw_top` int(11) NOT NULL DEFAULT '0',
                      `nw_height` int(11) NOT NULL DEFAULT '0',
                      `nw_width` int(11) NOT NULL DEFAULT '0',
                      `nw_subject` text NOT NULL,
                      `nw_content` text NOT NULL,
                      `nw_content_html` tinyint(4) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`nw_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ",
            true
        );
    }
  }

  $g5['title'] = '팝업레이어 관리';
  require_once G5_ADMIN_PATH . '/admin.head.php';

  $sql_common = " from {$g5['new_win_table']} ";

  // 테이블의 전체 레코드수만 얻음
  $sql = " select count(*) as cnt " . $sql_common;
  $row = sql_fetch($sql);
  $total_count = $row['cnt'];

  $sql = "select * $sql_common order by nw_id desc ";
  $result = sql_query($sql);
  
?>



<h1>팝업레이어 관리</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/config_form.php">환경설정</a> > 
  <a href="<?=G5_ADMIN_URL;?>/newwinlist.php">팝업레이어 관리</a>
</div>

<div class="top-btn-wrap">
  <a href="./newwinform.php" class="adm-btn blue-bg">새창관리 추가</a>
</div>

<div class="total-box">
  전체 <span class="count"><?php echo $total_count; ?></span>건
</div>

<article class="boardArti basicBoardArti">
  <ul class="basicList">
      <li class="boardTitle">
          <div class="item">
              <div class="number center">번호</div>
              <div class="title center">제목</div>
              <div class="writer center">시작일시</div>
              <div class="hit center">종료일시</div>
              <div class="date center">관리</div>
          </div>
      </li>
      <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          $bg = 'bg' . ($i % 2);
          switch ($row['nw_device']) {
          case 'pc':
            $nw_device = 'PC';
            break;
          case 'mobile':
            $nw_device = '모바일';
            break;
          default:
            $nw_device = '모두';
            break;
          }
        ?>
      <li>
          <div class="item">
              <div class="number center"><?php echo $row['nw_id']; ?></div>
              <div class="title"><a href="./newwinform.php?w=u&amp;nw_id=<?php echo $row['nw_id']; ?>"><?php echo $row['nw_subject']; ?></a></div>
              <div class="writer center"><?php echo substr($row['nw_begin_time'], 2, 14); ?></div>
              <div class="hit center"><?php echo substr($row['nw_end_time'], 2, 14); ?></div>
              <div class="date center"><a href="./newwinformupdate.php?w=d&amp;nw_id=<?php echo $row['nw_id']; ?>" onclick="return delete_confirm(this);" class="list-btn red-bg">삭제</a></div>
          </div>
      </li>
      <?php 
        } 
        if ($i == 0) {
            echo '<div class="empty_table">등록된 자료가 없습니다.</div>';
        }
      ?>
  </ul>

  <!-- <div class="paging-box">페이징 위치입니다.</div> -->
</article>




<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
