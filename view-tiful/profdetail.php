<?php
require('function.php');
debug('-----------------詳細ページ-----------------');
require('auth.php');


$h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';


if(!empty($_POST['submit'])){
    debug('POST送信あり');
    if(empty($_SESSION['last_id'])){
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO bord SET photo_id=?,sender_id=?,receiver_id=?,create_date=?';
            $data = array($p_id,$_SESSION['user_id'],$h_id,date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                debug('messageへ遷移');
                $_SESSION['last_id'] = $dbh->lastInsertID();
                header("Location:msg.php?m_id=".$dbh->lastInsertID().'&h_id='.$h_id);
            }
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }else{
        header("Location:msg.php?m_id=".$_SESSION['last_id'].'&h_id='.$h_id);
    }
}

$getuser = getUser($h_id);

?>

<?php
$sitetitle = 'プロフィール詳細';
require('head.php');
?>
</head>
<body id="profdetail">
<?php require('header.php'); ?>
<div class="wrapper">
    <?php require('sheader.php'); ?>
    <div class="prof-detail-wrap" style="position:relative;z-index:0;">
        <div class="prof-icon"><img src="<?php echo $getuser['pic']; ?>" alt=""></div>
        <div class="prof-img-wrap" style="background:url(img/camera.jpg) no-repeat;background-size:cover;background-attachment:fixed;"></div>
    </div>
    <div class="prof-inner">
        <div class="n-f flex">
            <p class="min" style="width:24%;"><?php echo $getuser['username']; ?></p>
            <div class="flex" style="justify-content: flex-end;width:50%;">
                <form action="" method="post">
                    <?php if($_SESSION['user_id'] === $h_id){ ?>
                    <input style="opacity:0;" class="raf" type="submit" value="message" name="submit">
                    <?php }else{ ?>
                    <input class="raf" type="submit" value="message" name="submit">
                    <?php } ?>
                </form>
                <?php if($_SESSION['user_id'] !== $h_id){ ?>
                <p style="width:300px;"><span class="min js-follow <?php if(isfollow($_SESSION['user_id'],$h_id)) echo 'active'; ?>" data-followid=<?php echo $h_id; ?>>フォロー</span></p>
                <?php }else{ ?>
                <p style="width:300px;opacity:0;"><span class="min js-follow <?php if(isfollow($_SESSION['user_id'],$h_id)) echo 'active'; ?>" data-followid=<?php echo $h_id; ?>><?php if(isfollow($_SESSION['user_id'],$h_id)){ echo 'フォロー中'; }else{ echo 'フォローする'; } ?></span></p>
                <?php } ?>
            </div>
            
        </div>
        <div class="flex detail-de-wrap">
            <div class="detail-de">
                <div class="job de-in flex">
                    <p class="min pjob">職業</p>
                    <p class="min"><?php echo $getuser['job']; ?></p>
                </div>
                <div class="hobby de-in flex">
                    <p class="min phob">趣味</p>
                    <p class="min"><?php echo $getuser['hobby']; ?></p>
                </div>
                <div class="area de-in flex">
                    <p class="min parea">居住エリア</p>
                    <p class="min">東北</p>
                </div>
            </div>
            <div class="de-com">
                <p class="comment" style="padding:14px;"><?php echo $getuser['intro']; ?></p>
            </div>
        </div>
    </div>
</div>
<?php require('footer.php'); ?>