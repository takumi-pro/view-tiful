<?php
require('function.php');
debug('パスワード再発行メール送信ページ-----------');
debugstart();

if(!empty($_POST)){
    $email = $_POST['email'];

    //未入力チェック
    validreq($email,'email');
    if(empty($err_msg)){
        maxLen($email,'email');
        validemail($email,'email');

        if(empty($err_msg)){
            debug('バリデーションok');
            try{
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email=? AND delete_flg=0';
                $data = array($email);
                $stmt = queryPost($dbh,$sql,$data);
                $resu = $stmt->fetch(PDO::FETCH_ASSOC);
                if($stmt && array_shift($resu)){
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC02;
                    $authkey = makeRand();
                    $from = 'info@viewtiful.com';
                    $to = $email;
                    $subject = 'パスワード再発行認証';
                    $comment = <<<EOT
          本メールアドレス宛にパスワード再発行のご依頼がありました。
          下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

          パスワード再発行認証キー入力ページ：http://localhost:8888/view-tiful/remindrecieve.php
          認証キー：{$authkey}
          ※認証キーの有効期限は30分となります

          認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
          http://localhost:8888/view-tiful/remindsend.php
          EOT;
                    sendMail($to,$subject,$comment,$from);

                    //認証に必要な情報をSESSIONに保存
                    $_SESSION['auth_key'] = $authkey;
                    $_SESSION['auth_email'] = $email;
                    //認証キー有効期限は30分
                    $_SESSION['auth_limit'] = time()+(60*30);
                    debug('セッション変数中身:'.print_r($_SESSION,true));
                    header("Location:remindreceive.php");
                }else{
                    debug('クエエイに失敗したかDBに登録のないemailが入力されました');
                    $err_msg['common'] = MSG08;
                }
            }catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
                $err_msg['common'] = MSG08;
            }
        }
    }
}
?>

<?php
$sitetitle = '退会ページ';
require('head.php');
?>
</head>
<body id="remindsend">
<?php require('header.php'); ?>
<div class="wrapper" style="margin-top:50px;">
    
    <div class="join-img-wrap">
        <div class="join-wrap">
        <form action="" method="post">

            <p class="join-msg min" style="font-size:14px;text-align:left;"> ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。 </p>
            <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
            <label class="raf" for="" method="post" style="font-size:24px;">email <br>
                <input type="text" name="email" value="<?php echo keep('email'); ?>">
            </label>
            <div class="err-msg"><?php echo errmsg('email'); ?></div>
            <p class="btn"><input class="raf" type="submit" value="send" name="submit"></p>

            
        </form>

        </div>
    </div>
</div>

<?php require('footer.php'); ?>