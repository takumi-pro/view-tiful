<?php
require('function.php');
debug('パスワード編集画面');

//ログイン認証
require('auth.php');

$userpass = getUser($_SESSION['user_id']);

if(!empty($_POST)){
    debug('POST情報:'.print_r($_POST,true));

    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $renew_pass = $_POST['renew_pass'];

    validreq($old_pass,'old_pass');
    validreq($new_pass,'new_pass');
    validreq($renew_pass,'renew_pass');
    if(empty($err_msg)){

        validpass($old_pass,'old_pass');
        validpass($new_pass,'new_pass');

        if(!password_verify($old_pass,$userpass['password'])){
            $err_msg['old_pass'] = MSG10;
        }

        if($old_pass === $new_pass){
            $err_msg['new_pass'] = MSG11;
        }

        validmatch($new_pass,$renew_pass,'renew_pass');

        if(empty($err_msg)){
            debug('バリデーションok');

            try{
                $dbh = dbConnect();
                $sql = 'UPDATE users SET password=? WHERE id=?';
                $data = array(password_hash($renew_pass,PASSWORD_DEFAULT),$userpass['id']);
                $stmt = queryPost($dbh,$sql,$data);
                if($stmt){
                    $_SESSION['msg_success'] = SUC01;
                    debug('セッション変数中身:'.print_r($_SESSION,true));
                    //メールを送信
                    $username = (!empty($userpass['username'])) ? $userpass['username'] : '名無し';
                    $from = 'info@viewtiful.com';
                    $to = $userpass['email'];
                    $subject = 'パスワード変更通知 | view-tiful';
                    $comment = <<<EOT
                    {$username}さん
                    パスワードが変更されました。
                    EOT;
                    sendMail($to,$subject,$comment,$from);

                    debug('マイページへ遷移');
                    header("Location:mypage.php");}
            }catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
                $err_msg['common'] = MSG08;
            }
        }
    }
    
}
?>

<?php
$sitetitle = 'パスワード変更';
require('head.php');
?>
</head>
<body id="mypage">
<?php require('header.php'); ?>
<div class="wrapper">
    <div class="mypage-img">
        <?php require('sheader.php'); ?>
        <div class="flex">
            <?php require('side.php'); ?>
            <div class="content-wrapper">
                <div class="pass-wrapper">
                <form action="" method="post">
                        <label for="" class="raf" style="font-size:24px;">old password - <span class="min" style="font-size:16px;vertical-align:middle;">古いパスワード</span><br>
                            <input type="text" name="old_pass" value="<?php echo getFormData('old_pass'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('old_pass'); ?></div>

                        <label for="" class="raf" style="font-size:24px;">new_password - <span class="min" style="font-size:16px;vertical-align:middle;">新しいパスワード</span><br>
                            <input type="text" name="new_pass" value="<?php echo getFormData('new_pass'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('new_pass'); ?></div>

                        <label class="raf" for="" method="post" style="font-size:24px;">re-enter password - <span class="min" style="font-size:16px;vertical-align:middle;">パスワード再入力</span><br>
                            <input type="text" name="renew_pass" value="<?php echo getFormData('renew_pass'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('renew_pass'); ?></div>

                        <p class="btn"><input class="raf" type="submit" value="change" name="submit"></p>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>