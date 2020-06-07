<?php
require('function.php');
debug('--------------投稿詳細ページ------------');

require('auth.php');
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
$h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';

if(!empty($_POST['submit'])){
    debug('POST情報あり');
    $selfcom = $_POST['selfcom'];
    validreq($selfcom,'selfcom');
    if(empty($err_msg)){
        debug('バリデーションok');
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO message SET send_date=?,from_user=?,msg=?,create_date=?,photo_id=?';
            $data = array(date('Y-m-d H:i:s'),$_SESSION['user_id'],$selfcom,date('Y-m-d H:i:s'),$p_id);
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                $_POST = array();
                header("Location: ".$_SERVER['PHP_SELF'].appendget());
            }
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
}
$viewDatamsg = msgandbord($p_id);
debug('メッセージ情報:'.print_r($viewDatamsg,true));
//DBから投稿情報を取得
debug($p_id);
$viewData = getpostone($p_id);
debug('写真のID:'.$p_id);
$getuser = getUser($h_id);
$myinfo = getUser($_SESSION['user_id']);

if(empty($viewData)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:postlist.php");
}
debug('取得した情報:'.print_r($viewData,true));

?>

<?php
$sitetitle = '投稿詳細';
require('head.php');
?>
</head>
<body id="postdetail">
<?php require('header.php'); ?>
<div class="wrapper">
    <?php require('sheader.php'); ?>
    <div class="flex wrap">
        <div>
            <div class="poster-wrap">
            
                <div class="flex" style="flex-direction: column;">
                    <h3 style="font-size:35px;text-align:center;" class="raf">poster</h3>
                    <div class="poster-img"><img src="<?php echo $getuser['pic']; ?>" alt=""></div>
                    <p class="min" style="margin-top:10px;font-size:20px;"><?php echo $getuser['username']; ?></p>
                    <p><a class="raf" href="profdetail.php<?php echo (empty($h_id)) ? appendget().'&h_id='.$viewData['posted_id'] : appendget(); ?>">profile details >></a></p>
                    <p><?php echo $getuser['intro']; ?></p>
                </div>
            </div>
            <p style="text-align:center;margin-top:10px;"><a style="font-size:24px;color: rgb(62, 122, 179);" class="raf" href="<?php  ?>"><< back</a></p>
        </div>
        
        
        <div class="detail-wrap">
            <div class="detail-title" style="position:relative;">
                <h2 class="raf" style="font-size:48px;color:#333;position:relative;">title -<span class="min" style="font-size:34px;margin-left:10px"><?php echo $viewData['title']; ?></span><i class="fas fa-heart js-like <?php if(islike($_SESSION['user_id'],$viewData['id'])) echo 'active'; ?>" style="position:absolute;top:20px;right:0;color:#ccc;" data-photoid="<?php echo sanitize($viewData['id']); ?>"></i></h2>

            </div>
            <div class="sub-title flex">
                <div class="place-title">
                    <span class="title min">場所</span>
                    <span class="txt"><?php echo sanitize($viewData['place']); ?></span>
                </div>
                <div class="key-title">
                    <span class="title min">キーワード</span>
                    <span class="txt"><span><?php echo sanitize($viewData['key1']); ?></span>,<span><?php echo sanitize($viewData['key2']); ?></span>,<span><?php echo sanitize($viewData['key3']); ?></span></span>
                </div>
                <div class="cate-title">
                    <span class="title min">カテゴリー</span>
                    <span class="txt"></span>
                </div>
            </div>
            <div class="detail-img-wrap">
                <div class="detail-main"><img id="js-switch-img-main" src="<?php echo showimg(sanitize($viewData['pic1'])); ?>" alt=""></div>
                <div class="sub-img-wrap flex">
                    <div class="sub-img"><img class="js-switch-img-sub" src="<?php echo showimg(sanitize($viewData['pic1'])); ?>" alt=""></div>
                    <div class="sub-img"><img class="js-switch-img-sub" src="<?php echo showimg(sanitize($viewData['pic2'])); ?>" alt=""></div>
                    <div class="sub-img"><img class="js-switch-img-sub" src="<?php echo showimg(sanitize($viewData['pic3'])); ?>" alt=""></div>
                    <div class="sub-img"><img class="js-switch-img-sub" src="<?php echo showimg(sanitize($viewData['pic4'])); ?>" alt=""></div>
                </div>
            </div>
            <div class="com-wrap">
                <h3 class="raf" style="font-size:32px;color:#333;">comment -<span class="min" style="font-size:18px;">コメント</span></h3>
                <p class="comment min" style="font-size:20px"><?php echo sanitize($viewData['comment']); ?></p>
            </div>
            <div class="word-wrap">
                <ul>
                <?php foreach($viewDatamsg as $key => $val): ?>
                    <li>
                        <dl class="flex">
                            <dt><img src="<?php echo $myinfo['pic']; ?>" alt=""></dt>
                            <dd><?php echo $val['msg']; ?></dd>
                        </dl>
                    </li>
                <?php endforeach; ?>
                </ul>
                <form class="self-com" action="" method="post" style="margin-top:30px;text-align:right;">
                    <textarea class="raf" name="selfcom" id="" cols="30" rows="2" placeholder="leave a comment"></textarea>
                    <div class="err-msg"><?php echo errmsg('selfcom'); ?></div>
                    <input type="submit" name="submit" class="raf send-btn" value="send">
                </form>
            </div>
        </div>
    </div>
</div>
<?php require('footer.php'); ?>