<?php
  $sub_menu = "300200";
  require_once './_common.php';

  auth_check_menu($auth, $sub_menu, 'r');

  if (!isset($group['gr_device'])) {
      // 게시판 그룹 사용 필드 추가
      // both : pc, mobile 둘다 사용
      // pc : pc 전용 사용
      // mobile : mobile 전용 사용
      // none : 사용 안함
      sql_query(" ALTER TABLE  `{$g5['group_table']}` ADD  `gr_device` ENUM(  'both',  'pc',  'mobile' ) NOT NULL DEFAULT  'both' AFTER  `gr_subject` ", false);
  }

  $sql_common = " from {$g5['group_table']} ";

  $sql_search = " where (1) ";
  if ($is_admin != 'super') {
      $sql_search .= " and (gr_admin = '{$member['mb_id']}') ";
  }

  if ($stx) {
      $sql_search .= " and ( ";
      switch ($sfl) {
          case "gr_id":
          case "gr_admin":
              $sql_search .= " ({$sfl} = '{$stx}') ";
              break;
          default:
              $sql_search .= " ({$sfl} like '%{$stx}%') ";
              break;
      }
      $sql_search .= " ) ";
  }

  if ($sst) {
      $sql_order = " order by {$sst} {$sod} ";
  } else {
      $sql_order = " order by gr_id asc ";
  }

  $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
  $row = sql_fetch($sql);
  $total_count = $row['cnt'];

  $rows = $config['cf_page_rows'];
  $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
  if ($page < 1) {
      $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
  }
  $from_record = ($page - 1) * $rows; // 시작 열을 구함

  $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
  $result = sql_query($sql);

  $listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">처음</a>';

  $g5['title'] = '게시판그룹설정';
  require_once './admin.head.php';

  $colspan = 10;
?>





<h1>게시판그룹관리</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/board_list.php">게시판관리</a> > 
  <a href="<?=G5_ADMIN_URL;?>/boardgroup_list.php">게시판그룹관리</a>
</div>


<div class="total-box">
   전체그룹 <span class="count"><?php echo number_format($total_count) ?></span>개
</div>




<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">

  <h2>게시판 그룹 검색</h2>
  <div class="admin-search-div">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" class="ipt" id="sfl">
      <option value="gr_subject" <?php echo get_selected($sfl, "gr_subject"); ?>>제목</option>
      <option value="gr_id" <?php echo get_selected($sfl, "gr_id"); ?>>ID</option>
      <option value="gr_admin" <?php echo get_selected($sfl, "gr_admin"); ?>>그룹관리자</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" id="stx" value="<?php echo $stx ?>" required class="ipt">
    <input type="submit" value="검색" class="seach-btn">
  </div>
</form>


<div class="margin-div"></div>


<form name="fboardgrouplist" id="fboardgrouplist" action="./boardgroup_list_update.php" onsubmit="return fboardgrouplist_submit(this);" method="post">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="">
    
    <article class="boardArti basicBoardArti">


      <div class="top-btn-wrap">
          <!-- <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="adm-btn"> -->
          <?php if ($is_admin == 'super') { ?>
              <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="adm-btn">
              <a href="./boardgroup_form.php" id="bo_add" class="adm-btn blue-bg">게시판그룹 추가</a>
          <?php } ?>
      </div>

      <ul class="basicList">
        <li class="boardTitle">
          <div class="item">
            <div class="chk center">
              <label for="chkall" class="sound_only">게시판 전체</label>
              <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
            </div>
            <div class="writer center" style="width:calc( (100% - 22px) / 3)">그룹아이디</div>
            <div class="hit center" style="width:calc( (100% - 22px) / 3)">제목</div>
            <div class="date center" style="width:calc( (100% - 22px) / 3)">관리</div>
          </div>
        </li>
        <?php
          for ($i = 0; $row = sql_fetch_array($result); $i++) {
              // 접근회원수
              $sql1 = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$row['gr_id']}' ";
              $row1 = sql_fetch($sql1);

              // 게시판수
              $sql2 = " select count(*) as cnt from {$g5['board_table']} where gr_id = '{$row['gr_id']}' ";
              $row2 = sql_fetch($sql2);

              $s_upd = '<a href="./boardgroup_form.php?' . $qstr . '&amp;w=u&amp;gr_id=' . $row['gr_id'] . '" class="btn_03 btn">수정</a>';

              $bg = 'bg' . ($i % 2);
        ?>
          <li class="">
            <div class="item">
              <div class="chk center">
                <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['gr_subject']); ?> 그룹</label>
                <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
              </div>
              <div class="writer center" style="width:calc( (100% - 22px) / 3)"><a href="<?php echo G5_BBS_URL ?>/group.php?gr_id=<?php echo $row['gr_id'] ?>" target="_blank"><?php echo $row['gr_id'] ?></a></div>
              <div class="hit center" style="width:calc( (100% - 22px) / 3)"> 
                <a href="<?php echo G5_BBS_URL ?>/group.php?gr_id=<?php echo $row['gr_id'] ?>" target="_blank"><?php echo get_text($row['gr_subject']) ?></a>
              </div>
              <div class="date center" style="width:calc( (100% - 22px) / 3)">
                <a href="./boardgroup_form.php?<?=$qstr;?>&amp;w=u&amp;gr_id=<?=$row['gr_id'];?>" class="list-btn red-bg">수정</a>
              </div>
            </div>
          </li>
        <?php } ?>

      </ul>
    </article>

</form>


<div class="paging-box">
  <?php
  $pagelist = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page=');
  echo $pagelist;
  ?>
</div>

<script>
    function fboardgrouplist_submit(f) {
        if (!is_checked("chk[]")) {
            alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
            return false;
        }

        if (document.pressed == "선택삭제") {
            if (!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
                return false;
            }
        }

        return true;
    }
</script>

<?php
require_once './admin.tail.php';
