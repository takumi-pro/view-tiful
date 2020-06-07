<?php
require('function.php');
debug('--------------パスワード再発行認証キー入力ページ----------');
debugstart();

if(empty($_SESSION['auth_key'])){
    header("Location:remindsend.php");
}
if(!empty($_POST)){
    debug('POST情報があります');

    $authkey = $_POST['auth_key'];

    validreq($authkey,'auth_key');

    if(empty($err_msg)){
        //固定長チェック
        validlength($authkey,'auth_key');
        validhalf($authkey,'auth_key');

        if(empty($err_msg)){
            debug('バリデーションok');

            //入力したものと認証キーを照合
            if($authkey !== $_SESSION['auth_key']){
                $err_msg['auth_key'] = MSG13;
            }

            if(time() > $_SESSION['auth_limit']){
                $err_msg['auth_key'] = MSG14;
            }

            if(empty($err_msg)){
                debug('認証ok');
                //パスワード生成
                $pass = makeRand();

                try{
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password=? WHERE email=? AND delete_flg=0';
                    $data = array(password_hash($pass,PASSWORD_DEFAULT),$_SESSION['auth_email']);
                    $stmt = queryPost($dbh,$sql,$data);
                    if($stmt){
                        debug('クエリ成功');
                        

                        $from = 'info@viewtiful.com';
                        $to = $_SESSION['auth_email'];
                        $subject = 'パスワード再発行認証';
                        $comment = <<<EOT
                        本メールアドレス宛にパスワードの再発行を致しました。
                        下記のURLにて再発行パスワードをご入力頂き、ログインください。
            
                        ログインページ：http://localhost:8888/view-tiful/login.php
                        再発行パスワード：{$pass}
                        ※ログイン後、パスワードのご変更をお願い致します
                        EOT;
                        sendMail($to,$subject,$comment,$from);
                        //セッション削除
                        session_unset();
                        $_SESSION['msg_success'] = SUC03;
                        debug('セッション変数中身:'.print_r($_SESSION,true));
                        debug('ログインに遷移');
                        header("Location:login.php");

                    }else{
                        debug('クエリに失敗しました');
                        $err_msg['common'] = MSG08;
                    }
                }catch (Exception $e){
                    error_log('エラー発生:'.$e->getMessage());
                    $err_msg['common'] = MSG08;
                }
            }
        }
    }
}
?>

<?php
$sitetitle = '';
require('head.php');
?>
</head>
<body id="remindsend"　style="position:relative;z-index:100;">
<p id="js-show-msg" class="msg-slide" style="display:none;z-index:300;top:50px;"><?php echo getFlash('msg_success'); ?></p>
<?php require('header.php'); ?>
<div class="wrapper" style="margin-top:50px;">
    
    <div class="join-img-wrap">
        <div class="join-wrap">
        <form action="" method="post">

            <p class="join-msg min" style="font-size:14px;text-align:left;"> ご指定のメールアドレスお送りした【パスワード再発行認証メール】内にある「認証キー」をご入力ください。 </p>
            <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
            <label class="min" for="" method="post" style="font-size:24px;">認証キー <br>
                <input type="text" name="auth_key" value="<?php echo keep('auth_key'); ?>">
            </label>
            <div class="err-msg"><?php echo errmsg('auth_key'); ?></div>
            <p class="btn"><input class="raf" type="submit" value="send" name="submit"></p>

            
        </form>

        </div>
    </div>
</div>

<?php require('footer.php'); ?>