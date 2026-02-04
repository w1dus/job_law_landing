<?php
  $sub_menu = "200100";
  require_once './_common.php';

  auth_check_menu($auth, $sub_menu, 'r');

  $sql_common = " from {$g5['member_table']} ";

  $sql_search = " where (1) ";
  if ($stx) {
      $sql_search .= " and ( ";
      switch ($sfl) {
          case 'mb_point':
              $sql_search .= " ({$sfl} >= '{$stx}') ";
              break;
          case 'mb_level':
              $sql_search .= " ({$sfl} = '{$stx}') ";
              break;
          case 'mb_tel':
          case 'mb_hp':
              $sql_search .= " ({$sfl} like '%{$stx}') ";
              break;
          default:
              $sql_search .= " ({$sfl} like '{$stx}%') ";
              break;
      }
      $sql_search .= " ) ";
  }

  if ($is_admin != 'super') {
      $sql_search .= " and mb_level <= '{$member['mb_level']}' ";
  }

  if (!$sst) {
      $sst = "mb_datetime";
      $sod = "desc";
  }

  $sql_order = " order by {$sst} {$sod} ";

  $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
  $row = sql_fetch($sql);
  $total_count = $row['cnt'];

  $rows = $config['cf_page_rows'];
  $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
  if ($page < 1) {
      $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
  }
  $from_record = ($page - 1) * $rows; // 시작 열을 구함

  // 탈퇴회원수
  $sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
  $row = sql_fetch($sql);
  $leave_count = $row['cnt'];

  // 차단회원수
  $sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
  $row = sql_fetch($sql);
  $intercept_count = $row['cnt'];

  $listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

  $g5['title'] = '회원관리';
  require_once './admin.head.php';

  $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
  $result = sql_query($sql);

  $colspan = 16;
?>




<h1>회원관리</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/member_list.php">회원관리</a> > 
  <a href="<?=G5_ADMIN_URL;?>/member_list.php">회원관리</a>
</div>
<div class="margin-div"></div>
<div class="admin-notice-div">
  📢 회원자료 삭제 시 다른 회원이 기존 회원아이디를 사용하지 못하도록 회원아이디, 이름, 닉네임은 삭제하지 않고 영구 보관합니다.
</div>
<div class="margin-div"></div>



<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
  <h2>회원 검색</h2>
  <div class="admin-search-div">
    <select name="sfl" class="ipt" id="sfl">
      <option value="mb_id" <?php echo get_selected($sfl, "mb_id"); ?>>회원아이디</option>
      <option value="mb_nick" <?php echo get_selected($sfl, "mb_nick"); ?>>닉네임</option>
      <option value="mb_name" <?php echo get_selected($sfl, "mb_name"); ?>>이름</option>
      <option value="mb_level" <?php echo get_selected($sfl, "mb_level"); ?>>권한</option>
      <option value="mb_email" <?php echo get_selected($sfl, "mb_email"); ?>>E-MAIL</option>
      <option value="mb_tel" <?php echo get_selected($sfl, "mb_tel"); ?>>전화번호</option>
      <option value="mb_hp" <?php echo get_selected($sfl, "mb_hp"); ?>>휴대폰번호</option>
      <option value="mb_point" <?php echo get_selected($sfl, "mb_point"); ?>>포인트</option>
      <option value="mb_datetime" <?php echo get_selected($sfl, "mb_datetime"); ?>>가입일시</option>
      <option value="mb_ip" <?php echo get_selected($sfl, "mb_ip"); ?>>IP</option>
      <option value="mb_recommend" <?php echo get_selected($sfl, "mb_recommend"); ?>>추천인</option>
    </select>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="ipt">
    <input type="submit" class="seach-btn" value="검색">
  </div>
</form>

<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
  <input type="hidden" name="sst" value="<?php echo $sst ?>">
  <input type="hidden" name="sod" value="<?php echo $sod ?>">
  <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
  <input type="hidden" name="stx" value="<?php echo $stx ?>">
  <input type="hidden" name="page" value="<?php echo $page ?>">
  <input type="hidden" name="token" value="">


  <h2>회원 목록</h2>
  <div class="total-box">
    총 회원 수 <span class="count"><?php echo $total_count; ?></span>명,
    <a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>" data-tooltip-text="차단된 순으로 정렬합니다.&#xa;전체 데이터를 출력합니다."> 
      <span class="">차단 </span>
      <span class="count"><?php echo number_format($intercept_count) ?>명</span>
    </a>,
    <a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>" class="" data-tooltip-text="탈퇴된 순으로 정렬합니다.&#xa;전체 데이터를 출력합니다."> 
      <span class="">탈퇴 </span><span class="count"><?php echo number_format($leave_count) ?>명</span>
    </a>
  </div>
  <div class="top-btn-wrap">
    <!-- <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="adm-btn">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="adm-btn"> -->
    <a href="./member_form.php" class="adm-btn blue-bg">회원추가</a>
  </div>

  

  <article class="boardArti basicBoardArti">
    <ul class="basicList">
        <li class="boardTitle">
            <div class="item">
                <div style="width:calc(100% / 5)" class="box center">아이디</div>
                <div style="width:calc(100% / 5)" class="box center">최종접속</div>
                <div style="width:calc(100% / 5)" class="box center">가입일</div>
                <div style="width:calc(100% / 5)" class="box center">이름</div>
                <div style="width:calc(100% / 5)" class="box center">관리</div>
            </div>
        </li>
        <?php
            for ($i = 0; $row = sql_fetch_array($result); $i++) {
                // 접근가능한 그룹수
                $sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
                $row2 = sql_fetch($sql2);
                
                $address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';
        ?>
        <li>
            <div class="item">
                <div style="width:calc(100% / 5)" class="box center">
                    <a href="./member_form.php?<?php echo $qstr; ?>&amp;w=u&amp;mb_id=<?php echo $row['mb_id']; ?>"><?=get_text($row['mb_id']);?></a>
                </div>
                <div style="width:calc(100% / 5)" class="box center">
                    <a href="./member_form.php?<?php echo $qstr; ?>&amp;w=u&amp;mb_id=<?php echo $row['mb_id']; ?>">
                        <?php echo substr($row['mb_today_login'], 2, 8); ?>
                    </a>
                </div>
                <div style="width:calc(100% / 5)" class="box center">
                    <a href="./member_form.php?<?php echo $qstr; ?>&amp;w=u&amp;mb_id=<?php echo $row['mb_id']; ?>">
                        <?php echo substr($row['mb_datetime'], 2, 8); ?>
                    </a>
                </div>
                <div style="width:calc(100% / 5)" class="box center">
                    <a href="./member_form.php?<?php echo $qstr; ?>&amp;w=u&amp;mb_id=<?php echo $row['mb_id']; ?>">
                        <?php echo get_text($row['mb_name']); ?>
                    </a>
                </div>
                <div style="width:calc(100% / 5)" class="box center">
                    <a href="./member_form.php?<?php echo $qstr; ?>&amp;w=u&amp;mb_id=<?php echo $row['mb_id']; ?>" class="list-btn red-bg">수정</a>
                    <!-- <a href="./boardgroupmember_form.php?mb_id=<?php echo $row['mb_id']; ?>" class="list-btn">그룹</a> -->
                </div>
            </div>
        </li>
        <?php } ?>
    </ul>
  </article>

  <?php 
     if ($i == 0) {
      echo '<div class="empty_table">등록된 자료가 없습니다.</div>';
    }
  ?>

  <div class="paging-box">
    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . $qstr . '&amp;page='); ?>
  </div>


</form>















<script>
    function fmemberlist_submit(f) {
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
