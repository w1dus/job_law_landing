<?php
  $sub_menu = '100000';
  require_once './_common.php';

  @require_once './safe_check.php';
  if (function_exists('social_log_file_delete')) {
      social_log_file_delete(86400);      //소셜로그인 디버그 파일 24시간 지난것은 삭제
  }

  $g5['title'] = '관리자메인';
  require_once './admin.head.php';
  
?>


<!-- <?php
echo '서버 사용량 : '.get_homepage_byte_size();
?> -->
<div class="box">
    <div class="title-space-box">
        <div class="title-wrap">
            <h1>대시보드</h1>
            <a href="#" download="관리자페이지 설명서.pdf" class="download-btn">
                관리자페이지 설명서
                <img src="<?=G5_IMG_URL;?>/adm/download-icon.svg" alt="다운로드">
            </a>
        </div>
        <div class="toggle-btn-wrap">
            <button type="button" class="toggle-btn <?php echo ($config['cf_admin_status'] == 'on') ? 'show' : ''; ?>" value="on">
                <div class="label">관리자 모드 </div>
                <div class="toggle">
                    <div class="button"></div>
                </div>
            </button>
            <button type="button" class="toggle-btn <?php echo ($config['cf_admin_status'] == 'off') ? 'show' : ''; ?>" value="off">
                <div class="label">개발자 모드 </div>
                <div class="toggle right">
                    <div class="button" value="off"></div>
                </div>
            </button>
        </div>
    </div>

    <script>
        $(function () {
            $('.toggle-btn-wrap .toggle-btn').on('click', function () {
                const $clickedBtn = $(this);
                const status = $clickedBtn.val();

                $.ajax({
                    url: '<?=G5_ADMIN_URL;?>/ajax.admin_toggle.php',
                    type: 'POST',   
                    data: {
                        admin_status: status
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            $('.toggle-btn-wrap .toggle-btn').toggleClass('show');
                            location.reload(); 
                        } else {
                            console.error('상태 변경 실패:', res.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX 오류:', error);
                    }
                });
            });
        });
    </script>
  

  
  
  <div class="margin-div"></div>
  <h2> 게시판 바로가기 </h2>
  <?php 
    $sql = "SELECT * FROM `g5_board` ORDER BY `bo_table` DESC";
    $result = sql_query($sql);
  ?>
  <div class="board-view-section">
    <?php while ($row = sql_fetch_array($result)) { ?>
        <a href="<?=G5_BBS_URL;?>/board.php?bo_table=<?=$row['bo_table']; ?>" target="_blank" class="item">
            <div class="title"><?=$row['bo_subject']; ?></div>
            <div class="view-more">바로가기 <img src="<?=G5_IMG_URL;?>/adm/right-arrow.svg" alt="바로가기" /></div>
        </a>
    <?php } ?>
  </div>

  <div class="margin-div"></div>

   
    <div class="three-box-section">
        <div class="item">
            <?php
                $start = date('Y-m-d', strtotime('-6 days')); // 6일 전
                $end   = date('Y-m-d');                       // 오늘

                // 하루 단위 방문 기록 전체 가져오는 함수 (DATETIME 범위)
                function getDailyVisits($date) {
                    global $g5;
                    $visit_table = $g5['visit_table'];

                    $day_start = $date . " 00:00:00";
                    $day_end   = $date . " 23:59:59";

                    $sql = "
                        SELECT *
                        FROM {$visit_table}
                        WHERE vi_date BETWEEN '{$day_start}' AND '{$day_end}'
                        ORDER BY vi_id DESC
                    ";
                    $result = sql_query($sql);

                    $visits = [];
                    while ($row = sql_fetch_array($result)) {
                        $visits[] = $row;
                    }

                    return $visits;
                }

                // 일주일치 배열 생성 및 방문자 수 집계
                $weekly_visits = [];
                $labels = [];
                $data = [];
                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', strtotime("$start +$i day"));
                    $daily_visits = getDailyVisits($date);
                    $weekly_visits[$date] = $daily_visits;

                    // 그래프용 데이터: 조회수 기준
                    $labels[] = $date;
                    $data[] = count($daily_visits);
                }
            ?>
            <div class="title-space-box">
                <h2>방문자 접속통계</h2>
                <a href="<?=G5_ADMIN_URL;?>/visit_list.php?token=86df70e3c47f36124da16438944c4972&fr_date=<?=$start?>&to_date=<?=$end?>" class="btn_admin">바로가기</a>
            </div>

            <div class="visit-chart">
                <canvas id="visit-chart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const labels = <?php echo json_encode($labels, JSON_UNESCAPED_UNICODE); ?>;
                const dataValues = <?php echo json_encode($data); ?>;

                const data = {
                    labels: labels,
                    datasets: [{
                    label: '일별 방문자 수',
                    backgroundColor: 'rgb(93,91,208)',
                    borderColor: 'rgb(93,91,208)',
                    data: dataValues,
                    }]
                };

                const config = {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                min: 0,            // y축 최소값 0
                                beginAtZero: true, // 0부터 시작
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                };

                const ctx = document.getElementById('visit-chart').getContext('2d');
                const chart = new Chart(ctx, config);
            </script>
        </div>
        <div class="item">
            <div class="title-space-box">
                <h2>웹하드 용량</h2>
                <!-- <a href="#" class="btn_admin">바로가기</a> -->
            </div>
            <?php
                // 웹하드 용량 계산
                $used_bytes = 0;
                if (function_exists('get_homepage_byte_size')) {
                    $used_size_str = get_homepage_byte_size();
                    // 문자열에서 숫자 추출 (예: "123.45 MB" -> 123.45)
                    preg_match('/([\d.]+)\s*([KMGT]?B)/i', $used_size_str, $matches);
                    if (!empty($matches)) {
                        $used_value = floatval($matches[1]);
                        $unit = strtoupper($matches[2]);
                        // 바이트로 변환
                        switch($unit) {
                            case 'TB': $used_bytes = $used_value * 1024 * 1024 * 1024 * 1024; break;
                            case 'GB': $used_bytes = $used_value * 1024 * 1024 * 1024; break;
                            case 'MB': $used_bytes = $used_value * 1024 * 1024; break;
                            case 'KB': $used_bytes = $used_value * 1024; break;
                            default: $used_bytes = $used_value; break;
                        }
                    }
                }
                
                // 전체 용량 100M
                $total_mb = 100;
                $total_bytes = $total_mb * 1024 * 1024;
                $free_bytes = max(0, $total_bytes - $used_bytes);
                
                // MB 단위로 변환
                $used_mb = round($used_bytes / (1024 * 1024), 2);
                $free_mb = round($free_bytes / (1024 * 1024), 2);
                $total_mb_display = round($total_bytes / (1024 * 1024), 2);
            ?>
            <div class="capacity-chart">
                <canvas id="capacity-chart"></canvas>
                <div class="total-capacity">
                    <span class="t1">전체용량</span>
                    <span class="t2">100M</span>
                </div>
            </div>
            <div class="capacity-text">
                <div class="item">
                    <span class="t1">현재 사용량</span>
                    <span class="t2 pink"><?=get_homepage_byte_size();?></span>
                </div>
                <div class="item">
                    <span class="t1">남은 용량</span>
                    <span class="t2 purple"><?=$free_mb;?>M</span>
                </div>
            </div>
            <script>
                // 웹하드 용량 데이터
                const capacityData = {
                    // labels: ['사용된 용량', '남은 용량'],
                    datasets: [{
                        label: '웹하드 용량',
                        data: [<?=$used_mb;?>, <?=$free_mb;?>],
                        backgroundColor: [
                            '#7241DF',
                            '#EBF1F5'
                        ],
                        borderColor: [
                            '#7241DF',
                            '#EBF1F5'
                        ],
                        borderWidth: 1
                    }]
                };

                const capacityConfig = {
                    type: 'pie',
                    data: capacityData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            title: {
                                // display: true,
                                // text: '총 용량: <?php echo $total_gb_display; ?> GB'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed + ' GB';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                };

                const capacityCtx = document.getElementById('capacity-chart');
                if (capacityCtx) {
                    new Chart(capacityCtx, capacityConfig);
                }
            </script>
        </div>
        <div class="item">
            <div class="title-space-box">
                <h2>운영정보</h2>
            </div>
            <ul class="label-list">
                <li>
                    <div class="item">
                        <div class="label">사이트명</div>
                        <div class="conent">
                            <?=$config['cf_title']; ?>
                        </div>
                    </div>
                </li>
                <!-- <li>
                    <div class="item">
                        <div class="label">서버 만료일</div>
                        <div class="conent">
                            2027.01.01  
                        </div>
                    </div>
                </li> -->
                <li>
                    <div class="item">
                        <div class="label">개인정보 처리방침 설정</div>
                        <div class="conent">
                            <?php if($config['cf_privacy'] !== ""){ ?> 
                                <span style="color:#008000">등록완료</span>
                            <?php  } else { ?>
                                <span style="color:#F00">미등록</span>
                            <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <div class="label">팝업설정</div>
                        <div class="conent">
                            <a href="<?=G5_ADMIN_URL;?>/newwinlist.php" style="color:#0155FF">바로가기</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="margin-div"></div>
  
    <!-- <div class="total-hit-count-div">
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
    </div> -->
    <!-- <div class="total-hit-count-div">
        <div class="item">
        <div class="label">네이버 검색</div>
        <div class="count">
        <?php
            $site_url = G5_URL;
            // 메인 페이지 HTML 불러오기
            $html = @file_get_contents($site_url);
            // 네이버 검색 키워드 추출
            $naver_search_keywords = preg_match_all('/<meta name="naver-site-verification" content="([^"]+)"/', $html, $matches);
            echo ($matches[1][0])? '<span style="color:green;">등록됨</span>': '<span style="color:red;">등록안됨</span>';
            ?>
        </div>
        </div>
        <div class="item">
        <div class="label">구글 검색</div>
        <div class="count">
        <?php
            $site_url = G5_URL;
            // 메인 페이지 HTML 불러오기
            $html = @file_get_contents($site_url);
            // 구글 검색 키워드 추출
            $google_search_keywords = preg_match_all('/<meta name="google-site-verification" content="([^"]+)"/', $html, $matches);
            echo ($matches[1][0])? '<span style="color:green;">등록됨</span>': '<span style="color:red;">등록안됨</span>';
            ?>
        </div>
        </div>
        <div class="item">
        <div class="label">사이트 설명</div>
        <div class="count">
            <?php
            $site_url = G5_URL;
            // 메인 페이지 HTML 불러오기
            $html = @file_get_contents($site_url);
            // 설명 추출
            $decription_search_keywords = preg_match_all('/<meta name="description" content="([^"]+)"/', $html, $matches);
            echo ($matches[1][0])? '<span style="color:green;">등록됨</span>': '<span style="color:red;">등록안됨</span>';
            ?>
        </div>
        </div>
        <div class="item">
        <div class="label">사이트 키워드</div>
        <div class="count">
        <?php
            $site_url = G5_URL;
            // 메인 페이지 HTML 불러오기
            $html = @file_get_contents($site_url);
            // 키워드 추출
            $keywords_search_keywords = preg_match_all('/<meta name="keywords" content="([^"]+)"/', $html, $matches);
            echo ($matches[1][0])? '<span style="color:green;">등록됨</span>': '<span style="color:red;">등록안됨</span>';
            ?>
        </div>
        </div>
    </div> -->
  
</div>






<div class="margin-div"></div>


<div class="dashboard-half">
    <div class="box-wrap">
        <div class="title-space-box">
            <h2> 최근 게시물</h2>
        </div>
        <article class="boardArti basicBoardArti">
            <ul class="basicList">
                <li class="boardTitle">
                    <div class="item">
                        <div class="title center">제목</div>
                        <div class="writer center">글쓴이</div>
                        <div class="date center">등록일</div>
                    </div>
                </li>
                <?php
                // 모든 게시판의 최신글 가져오기
                $new_write_rows = 5; // 최신글 개수
                
                // 각 게시판 테이블에서 최신글 가져오기
                $all_boards = sql_query("SELECT bo_table FROM {$g5['board_table']} ORDER BY bo_table");
                $latest_posts = array();
                
                while ($board_row = sql_fetch_array($all_boards)) {
                    $write_table = $g5['write_prefix'] . $board_row['bo_table'];
                    $sql_latest = "SELECT *, '{$board_row['bo_table']}' as bo_table FROM {$write_table} ORDER BY wr_datetime DESC LIMIT 1";
                    $result_latest = sql_query($sql_latest);
                    if ($row_latest = sql_fetch_array($result_latest)) {
                        $latest_posts[] = $row_latest;
                    }
                }
                
                // 날짜순으로 정렬
                usort($latest_posts, function($a, $b) {
                    return strcmp($b['wr_datetime'], $a['wr_datetime']);
                });
                
                // 상위 5개만 선택
                $latest_posts = array_slice($latest_posts, 0, $new_write_rows);
                
                for ($i = 0; $i < count($latest_posts); $i++) {
                    $row = $latest_posts[$i];
                    $tmp_write_table = $g5['write_prefix'] . $row['bo_table'];

                     // 원글
                    if ($row['wr_id'] == $row['wr_parent']) {
                        $comment = "";
                        $comment_link = "";
                        $row2 = $row;

                        $name = get_sideview($row2['mb_id'], get_text(cut_str($row2['wr_name'], $config['cf_cut_name'])), $row2['wr_email'], $row2['wr_homepage']);
                        // 당일인 경우 시간으로 표시함
                        $datetime = substr($row2['wr_datetime'], 0, 10);
                        $datetime2 = $row2['wr_datetime'];
                        if ($datetime == G5_TIME_YMD) {
                            $datetime2 = substr($datetime2, 11, 5);
                        } else {
                            $datetime2 = substr($datetime2, 5, 5);
                        }
                    } else // 코멘트
                    {
                        $comment = '댓글. ';
                        $comment_link = '#c_' . $row['wr_id'];
                        $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_parent']}' ");
                        $row3 = sql_fetch(" select mb_id, wr_name, wr_email, wr_homepage, wr_datetime from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");

                        $name = get_sideview($row3['mb_id'], get_text(cut_str($row3['wr_name'], $config['cf_cut_name'])), $row3['wr_email'], $row3['wr_homepage']);
                        // 당일인 경우 시간으로 표시함
                        $datetime = substr($row3['wr_datetime'], 0, 10);
                        $datetime2 = $row3['wr_datetime'];
                        if ($datetime == G5_TIME_YMD) {
                            $datetime2 = substr($datetime2, 11, 5);
                        } else {
                            $datetime2 = substr($datetime2, 5, 5);
                        }
                    }
                ?>
                    <li>
                        <div class="item">
                            <div class="title">
                                <a href="<?php echo get_pretty_url($row['bo_table'], $row2['wr_id']); ?><?php echo $comment_link ?>" target="_blank">
                                    <?php echo $comment ?><?php echo conv_subject($row2['wr_subject'], 100) ?>
                                </a>
                            </div>
                            <div class="writer center"><?php echo $row2['wr_name'] ?></div>
                            <div class="date center"><?php echo $datetime2 ?></div>
                        </div>
                    </li>
                <?php } ?>
                <?php 
                    if ($i == 0) {
                        echo '<li class="empty_table">등록된 자료가 없습니다.</li>';
                    }
                ?>
            </ul>
        </article>
    </div>
    <div class="box-wrap">
        <?php $board = "apply"; ?>
        <div class="title-space-box">
            <h2> 최근 문의 목록</h2>
            <a href="<?=G5_BBS_URL;?>/board.php?bo_table=<?=$board;?>" target="_blank" class="btn_admin">바로가기</a>
        </div>
        <article class="boardArti basicBoardArti">
            <ul class="basicList">
                <li class="boardTitle">
                    <div class="item">
                        <div class="title center">제목</div>
                        <div class="writer center">글쓴이</div>
                        <div class="date center">등록일</div>
                    </div>
                </li>
                <?php 
                $sql = "SELECT * FROM `g5_write_".$board."` ORDER BY `wr_datetime` DESC LIMIT 0,5";
                $result = sql_query($sql);

                for($i=0; $row=sql_fetch_array($result); $i++) {
                ?>
                <li>
                    <div class="item">
                        <div class="title"><a href="<?=G5_BBS_URL;?>/board.php?bo_table=<?=$board;?>&wr_id=<?=$row['wr_id']; ?>" target="_blank"><?=$row['wr_subject']; ?></a></div>
                        <div class="writer center"><?=$row['wr_name']; ?></div>
                        <div class="date center"><?=date('Y-m-d', strtotime($row['wr_datetime'])); ?></div>
                    </div>
                </li>
                <?php } ?>
                <?php 
                if ($i == 0) {
                    echo '<li class="empty_table">등록된 자료가 없습니다.</li>';
                }
                ?>
            </ul>
        </article>
    </div>
    <div class="box-wrap">
        <div class="title-space-box">
            <h2> 검색노출 (SEO) </h2>
            <a href="<?=G5_ADMIN_URL;?>/config_form.php" class="btn_admin">바로가기</a>
        </div>

        <ul class="label-list">
            <li>
                <div class="item">
                    <div class="label">네이버 검색</div>
                    <div class="conent">
                        <?php
                            $site_url = G5_URL;
                            // 메인 페이지 HTML 불러오기
                            $html = @file_get_contents($site_url);
                            // 네이버 검색 키워드 추출
                            $naver_search_keywords = preg_match_all('/<meta name="naver-site-verification" content="([^"]+)"/', $html, $matches);
                            echo ($matches[1][0])? '<span style="color:#008000;">등록완료</span>': '<span style="color:#F00;">미등록</span>';
                        ?>
                    </div>
                </div>
            </li>
            <li>
                <div class="item">
                    <div class="label">구글 검색</div>
                    <div class="conent">
                        <?php
                            $site_url = G5_URL;
                            // 메인 페이지 HTML 불러오기
                            $html = @file_get_contents($site_url);
                            // 구글 검색 키워드 추출
                            $google_search_keywords = preg_match_all('/<meta name="google-site-verification" content="([^"]+)"/', $html, $matches);
                            echo ($matches[1][0])? '<span style="color:#008000;">등록완료</span>': '<span style="color:#F00;">미등록</span>';
                        ?>
                    </div>
                </div>
            </li>
            <li>
                <div class="item">
                    <div class="label">사이트 설명</div>
                    <div class="conent">
                        <?php
                            $site_url = G5_URL;
                            // 메인 페이지 HTML 불러오기
                            $html = @file_get_contents($site_url);
                            // 설명 추출
                            $decription_search_keywords = preg_match_all('/<meta name="description" content="([^"]+)"/', $html, $matches);
                            echo ($matches[1][0])? '<span style="color:#008000;">등록완료</span>': '<span style="color:#F00;">미등록</span>';
                        ?>
                    </div>
                </div>
            </li>
            <li>
                <div class="item">
                    <div class="label">사이트 키워드</div>
                    <div class="conent">
                        <?php
                            $site_url = G5_URL;
                            // 메인 페이지 HTML 불러오기
                            $html = @file_get_contents($site_url);
                            // 구글 검색 키워드 추출
                            $google_search_keywords = preg_match_all('/<meta name="google-site-verification" content="([^"]+)"/', $html, $matches);
                            echo ($matches[1][0])? '<span style="color:#008000;">등록완료</span>': '<span style="color:#F00;">미등록</span>';
                        ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>


<?php
  require_once './admin.tail.php';
  ?>
