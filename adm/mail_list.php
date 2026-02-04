<?php
    $sub_menu = '200300';
    require_once './_common.php';

    auth_check_menu($auth, $sub_menu, 'r');

    $sql_common = " from {$g5['mail_table']} ";

    // 테이블의 전체 레코드수만 얻음
    $sql = " select COUNT(*) as cnt {$sql_common} ";
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $page = 1;

    $sql = " select * {$sql_common} order by ma_id desc ";
    $result = sql_query($sql);

    $g5['title'] = '회원메일발송';
    require_once './admin.head.php';

    $colspan = 7;
?>

<h1>회원메일발송</h1>
<div class="map-div">
  <a href="<?=G5_ADMIN_URL;?>"><img src="./img/home.svg" alt="home" class="icon"/></a> > 
  <a href="<?=G5_ADMIN_URL;?>/member_list.php">회원관리</a> > 
  <a href="<?=G5_ADMIN_URL;?>/mail_list.php">회원메일발송</a>
</div>
<div class="margin-div"></div>
<div class="admin-notice-div">
    📢 테스트는 등록된 최고관리자의 이메일로 테스트 메일을 발송합니다.<br/>
    ✅ 현재 등록된 메일은 총 0건입니다.<br/>
    <span class="red">🚨 수신자가 동의하지 않은 대량 메일 발송에는 적합하지 않습니다. 수십건 단위로 발송해 주십시오.</span>
</div>
<div class="margin-div"></div>


<form name="fmaillist" id="fmaillist" action="./mail_delete.php" method="post">
    <div class="top-btn-wrap">
        <input type="submit" value="선택삭제" class="adm-btn">
        <a href="./mail_form.php" class="adm-btn blue-bg">메일내용추가</a>
    </div>

    <article class="boardArti basicBoardArti">
        <ul class="basicList">
            <li class="boardTitle">
                <div class="item">
                    <div class="chk center">
                        <label class="allchk">
                            <input type="checkbox" name="chkall" value="1" id="chkall" title="현재 페이지 목록 전체선택" onclick="check_all(this.form)">
                        </label>
                    </div>
                    <div class="number center">번호</div>
                    <div class="title center">제목</div>
                    <div class="writer center" style="width:200px">작성일시</div>
                    <div class="hit center">테스트</div>
                    <div class="date center">보내기</div>
                    <div class="date center">미리보기</div>
                </div>
            </li>
            <?php
                for ($i = 0; $row = sql_fetch_array($result); $i++) {
                    $s_vie = '<a href="./mail_preview.php?ma_id=' . $row['ma_id'] . '" target="_blank"  class="list-btn red-bg">미리보기</a>';

                    $num = number_format($total_count - ($page - 1) * $config['cf_page_rows'] - $i);

                    $bg = 'bg' . ($i % 2);
            ?>
                <li>
                    <div class="item">
                        <div class="chk center">
                            <input type="checkbox" id="chk_<?php echo $i ?>" name="chk[]" value="<?php echo $row['ma_id'] ?>">
                        </div>
                        <div class="number center"><?php echo $num ?></div>
                        <div class="title center"><a href="./mail_form.php?w=u&amp;ma_id=<?php echo $row['ma_id'] ?>"><?php echo $row['ma_subject'] ?></a></div>
                        <div class="writer center" style="width:200px"><?php echo $row['ma_time'] ?></div>
                        <div class="hit center"><a href="./mail_test.php?ma_id=<?php echo $row['ma_id'] ?>">테스트</a></div>
                        <div class="date center"><a href="./mail_select_form.php?ma_id=<?php echo $row['ma_id'] ?>">보내기</a></div>
                        <div class="date center"><?php echo $s_vie ?></div>
                    </div>
                </li>
            <?php } ?>
            <?php 
                if ($i == 0) {
                    echo '<div class="empty_table">등록된 자료가 없습니다.</div>';
                }
            ?>
        </ul>   
    </article>

</form>

<script>
    $(function() {
        $('#fmaillist').submit(function() {
            if (confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
                if (!is_checked("chk[]")) {
                    alert("선택삭제 하실 항목을 하나 이상 선택하세요.");
                    return false;
                }

                return true;
            } else {
                return false;
            }
        });
    });
</script>

<?php
require_once './admin.tail.php';
