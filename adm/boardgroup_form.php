<?php
  $sub_menu = "300200";
  require_once './_common.php';

  auth_check_menu($auth, $sub_menu, 'w');

  if ($is_admin != 'super' && $w == '') {
      alert('최고관리자만 접근 가능합니다.');
  }

  $html_title = '게시판그룹';
  $gr_id_attr = '';
  $sound_only = '';

  if (!isset($group['gr_id'])) {
      $group['gr_id'] = '';
      $group['gr_subject'] = '';
      $group['gr_device'] = '';
  }

  $gr = array('gr_use_access' => 0, 'gr_admin' => '');
  if ($w == '') {
      $gr_id_attr = 'required';
      $sound_only = '<strong class="sound_only"> 필수</strong>';
      $html_title .= ' 생성';
  } elseif ($w == 'u') {
      $gr_id_attr = 'readonly';
      $gr = sql_fetch(" select * from {$g5['group_table']} where gr_id = '$gr_id' ");
      $html_title .= ' 수정';
  } else {
      alert('제대로 된 값이 넘어오지 않았습니다.');
  }

  if (!isset($group['gr_device'])) {
      sql_query(" ALTER TABLE `{$g5['group_table']}` ADD `gr_device` ENUM('both','pc','mobile') NOT NULL DEFAULT 'both' AFTER `gr_subject` ", false);
  }

  // 접근회원수
  $sql1 = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$gr_id}' ";
  $row1 = sql_fetch($sql1);
  $group_member_count = $row1['cnt'];

  $g5['title'] = $html_title;
  require_once './admin.head.php';
?>

<form name="fboardgroup" id="fboardgroup" action="./boardgroup_form_update.php" onsubmit="return fboardgroup_check(this);" method="post" autocomplete="off">
  <input type="hidden" name="w" value="<?php echo $w ?>">
  <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
  <input type="hidden" name="stx" value="<?php echo $stx ?>">
  <input type="hidden" name="sst" value="<?php echo $sst ?>">
  <input type="hidden" name="sod" value="<?php echo $sod ?>">
  <input type="hidden" name="page" value="<?php echo $page ?>">
  <input type="hidden" name="token" value="">


    <h1>게시판그룹관리</h1>
    <div class="margin-div"></div>
    <div class="admin-notice-div">
      📢 게시판을 생성하시려면 1개 이상의 게시판그룹이 필요합니다.<br>
        게시판그룹을 이용하시면 더 효과적으로 게시판을 관리할 수 있습니다.
    </div>

    <div class="margin-div"></div>
    <h2>기본정보</h2>
    <ul class="formList">
      <li>
        <div class="label">
          그룹 ID<span class="red">*</span>
        </div>
        <div class="iptBox">
          <input type="text" name="gr_id" value="<?php echo $group['gr_id'] ?>" id="gr_id" <?php echo $gr_id_attr; ?> class="<?php echo $gr_id_attr; ?> ipt" maxlength="10" placeholder="영문자, 숫자, _ 만 가능 (공백없이)">
        </div>
      </li>
      <li>
        <div class="label">
          그룹 제목<span class="red">*</span>
        </div>
        <div class="iptBox">
          <input type="text" name="gr_subject" value="<?php echo get_text($group['gr_subject']) ?>" id="gr_subject" required class="ipt" size="80">
        </div>
      </li>
    </ul>


    <div class="margin-div"></div>

    <!-- <h2>여분필드</h2>
    <ul class="formList">
      <?php for ($i = 1; $i <= 10; $i++) { ?>
        <li class="half">
          <div class="label">
            여분필드<?php echo $i ?> 제목
          </div>
          <div class="iptBox">
            <input type="text" name="gr_<?php echo $i ?>_subj" value="<?php echo isset($group['gr_' . $i . '_subj']) ? get_text($group['gr_' . $i . '_subj']) : ''; ?>" id="gr_<?php echo $i ?>_subj" class="ipt">
          </div>
        </li>
        <li class="half">
          <div class="label">
            여분필드<?php echo $i ?> 내용
          </div>
          <div class="iptBox">
            <input type="text" name="gr_<?php echo $i ?>" value="<?php echo isset($gr['gr_' . $i]) ? get_sanitize_input($gr['gr_' . $i]) : ''; ?>" id="gr_<?php echo $i ?>" class="ipt">
          </div>
        </li>
      <?php } ?>
    </ul> -->

    <div class="adm-btn-div">
      <a href="./boardgroup_list.php?<?php echo $qstr ?>" class="adm-btn" accesskey="s">목록</a>
      <input type="submit" class="submit-btn adm-btn" accesskey="s" value="확인">
    </div>

</form>

<script>
    function fboardgroup_check(f) {
        f.action = './boardgroup_form_update.php';
        return true;
    }
</script>

<?php
require_once './admin.tail.php';

