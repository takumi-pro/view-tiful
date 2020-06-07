<?php
require('function.php');
debug('---------------退会ページ------------');

require('auth.php');

if(!empty($_POST)){
    debug('POST送信があります');
    try{
        $dbh = dbConnect();
        $sql1 = 'UPDATE users SET delete_flg=1 WHERE id=?';
        $sql2 = 'UPDATE photo SET delete_flg=1 WHERE posted_id=?';
        $sql3 = 'UPDATE favorite SET delete_flg=1 WHERE user_id=?';

        $data = array($_SESSION['user_id']);

        $stmt1 = queryPost($dbh,$sql1,$data);

        if($stmt1){
            debug('クエリ成功');
            
            //セッション削除
            session_destroy();
            debug('トップページへ遷移します');
            header("Location:top.php");
        }else{
            debug('クエリ失敗');
        }
    } catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
?>
<?php
$sitetitle = '退会ページ';
require('head.php');
?>
</head>
<body id="drawal">
<?php require('header.php'); ?>
<div class="wrapper">
    <?php require('sheader.php'); ?>
    <div class="join-img-wrap">
        <div class="join-wrap">
        <form action="" method="post">

            <p class="join-msg raf"> with drawal </p>
            <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
            <div class="flex" style="margin-top:20px;">
                <p class="btn" style="margin-top:0;"><input class="raf" type="submit" value="yes" name="submit"></p>
                <a href="mypage.php" class="raf" style="padding:5px 24px;font-size:25px;">no</a>
            </div>
        </form>

        </div>
    </div>
</div>

<?php require('footer.php'); ?>