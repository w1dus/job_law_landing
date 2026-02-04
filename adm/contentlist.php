<?php
$sub_menu = '300600';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, "r");

if (!isset($g5['content_table'])) {
    die('<meta charset="utf-8">/data/dbconfig.php 파일에 <strong>$g5[\'content_table\'] = G5_TABLE_PREFIX.\'content\';</strong> 를 추가해 주세요.');
}
//내용(컨텐츠)정보 테이블이 있는지 검사한다.
if (!sql_query(" DESCRIBE {$g5['content_table']} ", false)) {
    if (sql_query(" DESCRIBE {$g5['g5_shop_content_table']} ", false)) {
        sql_query(" ALTER TABLE {$g5['g5_shop_content_table']} RENAME TO `{$g5['content_table']}` ;", false);
    } else {
        $query_cp = sql_query(
            " CREATE TABLE IF NOT EXISTS `{$g5['content_table']}` (
                      `co_id` varchar(20) NOT NULL DEFAULT '',
                      `co_html` tinyint(4) NOT NULL DEFAULT '0',
                      `co_subject` varchar(255) NOT NULL DEFAULT '',
                      `co_content` longtext NOT NULL,
                      `co_hit` int(11) NOT NULL DEFAULT '0',
                      `co_include_head` varchar(255) NOT NULL,
                      `co_include_tail` varchar(255) NOT NULL,
                      PRIMARY KEY (`co_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ",
            true
        );

        // 내용관리 생성
        sql_query(" insert into `{$g5['content_table']}` set co_id = 'company', co_html = '1', co_subject = '회사소개', co_content= '<p align=center><b>회사소개에 대한 내용을 입력하십시오.</b></p>' ", false);
        sql_query(" insert into `{$g5['content_table']}` set co_id = 'privacy', co_html = '1', co_subject = '개인정보 처리방침', co_content= '<p align=center><b>개인정보 처리방침에 대한 내용을 입력하십시오.</b></p>' ", false);
        sql_query(" insert into `{$g5['content_table']}` set co_id = 'provision', co_html = '1', co_subject = '서비스 이용약관', co_content= '<p align=center><b>서비스 이용약관에 대한 내용을 입력하십시오.</b></p>' ", false);
    }
}

$g5['title'] = '메뉴관리';
require_once G5_ADMIN_PATH . '/admin.head.php';

$sql_common = " from {$g5['content_table']} ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
    $page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select * $sql_common order by co_id limit $from_record, {$config['cf_page_rows']} ";
$result = sql_query($sql);
?>



<h1>메뉴관리</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/board_list.php">게시판관리</a> > 
  <a href="<?=G5_ADMIN_URL;?>/contentlist.php">메뉴관리</a>
</div>


<div class="total-box">
   생성된 메뉴 수 <span class="count"><?php echo number_format($total_count) ?></span>개
</div>


<div class="margin-div"></div>

<div class="top-btn-wrap">
  <a href="./contentform.php" id="bo_add" class="adm-btn blue-bg">메뉴 추가</a>
</div>


<article class="boardArti basicBoardArti">
  <ul class="basicList">
    <li class="boardTitle">
      <div class="item">
        <div class="writer center" style="width:calc( 100% / 3)">ID</div>
        <div class="hit center" style="width:calc( 100% / 3)">제목</div>
        <div class="date center" style="width:calc( 100% / 3)">관리</div>
      </div>
    </li>

    <?php for ($i = 0; $row = sql_fetch_array($result); $i++) {
      $bg = 'bg' . ($i % 2);
    ?>
      <li class="">
        <div class="item">
          <div class="writer center" style="width:calc( 100% / 3)"><?php echo $row['co_id']; ?></div>
          <div class="hit center" style="width:calc( 100% / 3)"><?php echo htmlspecialchars2($row['co_subject']); ?></div>
          <div class="date center" style="width:calc( 100% / 3)">
            <a href="./contentform.php?w=u&amp;co_id=<?php echo $row['co_id']; ?>" class="list-btn"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span>수정</a>
            <a href="<?php echo get_pretty_url('content', $row['co_id']); ?>" target="_blank" class="list-btn"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span> 보기</a>
            <a href="./contentformupdate.php?w=d&amp;co_id=<?php echo $row['co_id']; ?>" onclick="return delete_confirm(this);" class="list-btn red-bg"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span>삭제</a>
          </div>
        </div>
      </li>
    <?php } ?>

  </ul>


  <div class="paging-box">
    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>
  </div>

</article>




<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
