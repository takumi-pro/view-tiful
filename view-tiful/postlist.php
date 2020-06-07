
<?php
require('function.php');
debug('-------------投稿一覧-------------');

$nowpage = (!empty($_GET['p'])) ? $_GET['p'] : 1;//デフォルトは１
//カテゴリー
$cate = (!empty($_GET['category_id'])) ? $_GET['category_id'] : '';
//ソート
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
//キーワード


if(!is_int((int)$nowpage)){
    header("Location:mypage.php");
    error_log('エラー発生:指定ページに不正な値が入りました');
}
//表示件数
$list = 20;
//現在の表示レコード先頭を算出
$nowmin = (($nowpage-1)*$list);
//DBから投稿情報を取得
$dbpostData = getpostlist($nowmin,$cate,$sort);
debug('現在ページ:'.$nowpage);
//カテゴリー情報取得
$dbcate = getcate();


?>

<?php
$sitetitle = '投稿一覧';
require('head.php');
?>
</head>
<body id="postlist">
<?php require('header.php'); ?>
<div class="wrapper">
    <?php require('sheader.php'); ?>
       <div class="post-list-wrap">
            <div class="search-wrap">
                
                <form action="" method="get" style="padding-bottom:14px;">
                    <div>
                      <!--キーワード検索-->
                      <label class="min" style="font-size:18px;">キーワード検索 <br>
                          <input type="text" name="key" value="">
                      </label>
                      <div class="err-msg"></div>

                      <!--カテゴリー-->
                      <label for="" class="min" style="font-size:18px;">カテゴリー <br>
                          <select name="category_id" id="" style="height:35px;font-size:13px">
                              <option value="0" <?php if(getFormData('category_id',true) == false) echo 'selected'; ?>>選択してください</option>
                              <?php foreach($dbcate as $key => $val): ?>
                              <option value="<?php echo $val['id']; ?>" <?php if(getFormData('category_id',true) == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                              <?php endforeach; ?>
                          </select>
                      </label>
                      <div class="err-msg"></div>

                      <!--表示順-->
                      <label class="min" style="font-size:18px;">表示順 <br>
                          <select name="sort" id="" style="height:35px;font-size:13px">
                              <option value="0" <?php if(getFormData('sort',true) ==0) echo 'selected'; ?>>選択してください</option>
                              <option value="1" <?php if(getFormData('sort',true) ==1) echo 'selected'; ?>>新しい順</option>
                              <option value="2" <?php if(getFormData('sort',true) ==2) echo 'selected'; ?>>古い順</option>
                          </select>
                      </label>
                      <div class="err-msg"></div>
                    </div>

                    <input class="raf" type="submit" name="submit" value="search">
                </form>
            </div>

            <!--投稿写真一覧-->
            <div class="photo-list-wrap">
                <div class="discover-wrap flex min">
                    <div class="dis">
                        <p><span class="totalre"><?php echo sanitize($dbpostData['total']); ?></span>件の投稿が見つかりました</p>
                    </div>  
                    <div class="dis">
                        <p><span><?php echo (!empty($dbpostData['data'])) ? $nowmin+1 : 0; ?></span> - <span><?php echo $nowmin+$list; ?></span> / <span><?php echo sanitize($dbpostData['total']); ?></span>件中</p>
                    </div>
                </div>
                <ul class="flex photo-ul">
                    <?php foreach($dbpostData['data'] as $key => $val): ?>
                    <li>
                        <a href="postdetail.php<?php echo (!empty(appendget())) ? appendget().'&p_id='.$val['id'].'&h_id='.$val['posted_id'] : '?p_id='.$val['id'].'&h_id='.$val['posted_id']; ?>">
                            <dl>
                              <dt class="photo"><img src="<?php echo sanitize($val['pic1']); ?>" alt=""></dt>
                              <dd class="ptitle min"><?php echo sanitize($val['title']); ?></dd>
                              <dd class="comment min"><?php echo sanitize(mb_substr($val['comment'],0,27)); ?></dd>
                          </dl>    
                        </a>
                    </li>
                    <?php endforeach; ?>
                    
                    </ul>
                        

                <!--ページネーション-->
                <div class="pagenation-wrap">
                    <ul class="flex" style="">
                    <?php
          $pageColNum = 5;
          $totalPageNum = $dbpostData['total_page'];
          if ($nowpage == $totalPageNum && $totalPageNum >= $pageColNum) {
            $minPageNum = $nowpage - 4;
            $maxPageNum = $nowpage;
          } elseif ($nowpage == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
            $minPageNum = $nowpage - 3;
            $maxPageNum = $nowpage + 1;
          } elseif ($nowpage == 2 && $totalPageNum >= $pageColNum) {
            $minPageNum = $nowpage - 1;
            $maxPageNum = $nowpage + 3;
          } elseif ($nowpage == 1 && $totalPageNum >= $pageColNum) {
            $minPageNum = $nowpage;
            $maxPageNum = 5;
          } elseif ($totalPageNum < $pageColNum) {
            $minPageNum = 1;
            $maxPageNum = $totalPageNum;
          } else {
            $minPageNum = $nowpage - 2;
            $maxPageNum = $nowpage + 2;
          }
          ?>
          <?php if ($nowpage != 1) : ?>
            <li class="list-item"><a href="postlist.php<?php echo (!empty(appendget())) ? appendget().'&p=1' : '?p=1'; ?>" class="raf">&lt;</a></li>
          <?php endif; ?>
          <?php for ($i = $minPageNum; $i <= $maxPageNum; $i++) : ?>
            <li class="raf list-item <?php if ($nowpage == $i) echo 'active'; ?>"><a href="<?php echo (!empty(appendget())) ? appendget().'&p='.$i : '?p='.$i; ?>"><?php echo $i; ?></a></li>
          <?php endfor; ?>
          <?php if ($nowpage != $maxPageNum) : ?>
            <li class="raf list-item"><a href="postlist.php<?php echo (!empty(appendget())) ? appendget().'&p='.$maxPageNum : '?p='.$maxPageNum; ?>">&gt;</a></li>
          <?php endif; ?>
                        
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>