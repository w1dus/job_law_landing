<?php
  $sub_menu = "300100";
  require_once './_common.php';

  auth_check_menu($auth, $sub_menu, 'r');

  $sql_common = " from {$g5['board_table']} a ";
  $sql_search = " where (1) ";

  if ($is_admin != "super") {
      $sql_common .= " , {$g5['group_table']} b ";
      $sql_search .= " and (a.gr_id = b.gr_id and b.gr_admin = '{$member['mb_id']}') ";
  }

  if ($stx) {
      $sql_search .= " and ( ";
      switch ($sfl) {
          case "bo_table":
              $sql_search .= " ($sfl like '$stx%') ";
              break;
          case "a.gr_id":
              $sql_search .= " ($sfl = '$stx') ";
              break;
          default:
              $sql_search .= " ($sfl like '%$stx%') ";
              break;
      }
      $sql_search .= " ) ";
  }

  if (!$sst) {
      $sst  = "a.gr_id, a.bo_table";
      $sod = "asc";
  }
  $sql_order = " order by $sst $sod ";

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

  $listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

  $g5['title'] = '게시판관리';
  require_once './admin.head.php';

  $colspan = 15;
?>





<h1>게시판관리</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/board_list.php">게시판관리</a> > 
  <a href="<?=G5_ADMIN_URL;?>/board_list.php">게시판관리</a>
</div>


<div class="total-box">
   생성된 게시판 수 <span class="count"><?php echo number_format($total_count) ?></span>개
</div>


<?php if($config['cf_admin_status'] !== 'on'){ ?>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
  <h2>게시판 검색</h2>
  <div class="admin-search-div">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" class="ipt" id="sfl">
        <option value="bo_table" <?php echo get_selected($sfl, "bo_table", true); ?>>TABLE</option>
        <option value="bo_subject" <?php echo get_selected($sfl, "bo_subject"); ?>>제목</option>
        <option value="a.gr_id" <?php echo get_selected($sfl, "a.gr_id"); ?>>그룹ID</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="ipt">
    <input type="submit" value="검색" class="seach-btn">
  </div>
</form>

<div class="margin-div"></div>
<?php } ?>

<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>">

    <article class="boardArti basicBoardArti">

        <?php if($config['cf_admin_status'] !== 'on'){ ?>
            <div class="top-btn-wrap">
                <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="adm-btn">
                <?php if ($is_admin == 'super') { ?>
                    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="adm-btn">
                    <a href="./board_form.php" id="bo_add" class="adm-btn blue-bg">게시판 추가</a>
                <?php } ?>
            </div>
        <?php } ?>

      <ul class="basicList">
          <li class="boardTitle">
              <div class="item">
                <?php if($config['cf_admin_status'] !== 'on'){ ?>
                  <div class="chk center">
                    <label for="chkall" class="sound_only">게시판 전체</label>
                    <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                  </div>
                  <div class="writer center" style="width:calc( (100% - 22px) / 4)">그룹</div>
                  <div class="hit center" style="width:calc( (100% - 22px) / 4)">스킨</div>
                <?php } ?>
                  <div class="date center" style=" <?php echo ($config['cf_admin_status'] === 'on') ? 'flex-grow:1; width:auto;':'width:calc( (100% - 22px) / 4); display:flex; align-items:center; justify-content:center;';?>">제목</div>
                  <div class="date center" style="width:calc( (100% - 22px) / 4)">관리</div>
              </div>
          </li>
          <?php
            for ($i = 0; $row = sql_fetch_array($result); $i++) {
                $one_update = '<a href="./board_form.php?w=u&amp;bo_table=' . $row['bo_table'] . '&amp;' . $qstr . '" class="btn btn_03">수정</a>';
                $one_copy = '<a href="./board_copy.php?bo_table=' . $row['bo_table'] . '" class="board_copy btn btn_02" target="win_board_copy">복사</a>';
          ?>
            <li>
                <div class="item">
                    <?php if($config['cf_admin_status'] !== 'on'){ ?>
                        <div class="chk center">
                        <input type="hidden" name="board_table[<?php echo $i ?>]" value="<?php echo $row['bo_table'] ?>">
                            <input type="hidden" name="bo_subject[<?php echo $i ?>]" value="<?php echo get_text($row['bo_subject']) ?>" id="bo_subject_<?php echo $i ?>" required class="required tbl_input bo_subject full_input" size="10">

                            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bo_subject']) ?></label>
                            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
                        </div>
                        <div class="writer center" style="width:calc( (100% - 22px) / 4)">
                            <?php if ($is_admin == 'super') { ?>
                                <?php echo get_group_select("gr_id[$i]", $row['gr_id']) ?>
                            <?php } else { ?>
                                <input type="hidden" name="gr_id[<?php echo $i ?>]" value="<?php echo $row['gr_id'] ?>"><?php echo $row['gr_subject'] ?>
                            <?php } ?>
                        </div>
                        <div class="hit center" style="width:calc( (100% - 22px) / 4)">
                            <?php echo get_skin_select('board', 'bo_skin_' . $i, "bo_skin[$i]", $row['bo_skin']); ?>
                        </div>
                    <?php } ?>
                    <div class="date center" style=" <?php echo ($config['cf_admin_status'] === 'on') ? 'flex-grow:1; width:auto;':'width:calc( (100% - 22px) / 4); display:flex; align-items:center; justify-content:center;';?>">
                     <a href="<?php echo get_pretty_url($row['bo_table']) ?>" target="_blank"><?php echo get_text($row['bo_subject']) ?></a>
                    </div>
                    <div class="date center" style="width:calc( (100% - 22px) / 4); display:flex; align-items:center; justify-content:center;">
                        <?php if($config['cf_admin_status'] !== 'on'){ ?>
                            <a href="./board_form.php?w=u&amp;bo_table='<?=$row['bo_table'];?>&amp;<?=$qstr;?>" class="list-btn">관리</a>
                        <?php } else { ?>
                                <a href="<?php echo get_pretty_url($row['bo_table']) ?>" id="bo_add" class="list-btn blue-bg">게시물 관리</a>
                        <?php } ?>
                    </div>
                </div>
            </li>
          <?php } ?>
      </ul>
    </article>

</form>

<div class="paging-box">
    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
</div>

<script>

    function check_all(f) {
        var chk = document.getElementsByName("chk[]");
            for (i=0; i<chk.length; i++)
                chk[i].checked = f.chkall.checked;
    }


    function fboardlist_submit(f) {
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

    $(function() {
        $(".board_copy").click(function() {
            window.open(this.href, "win_board_copy", "left=100,top=100,width=550,height=450");
            return false;
        });
    });
</script>

<?php
require_once './admin.tail.php';
