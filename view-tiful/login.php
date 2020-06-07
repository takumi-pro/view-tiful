<?php
require('function.php');
debug('-----------------ログインページ-----------------');
debugstart();

//ログイン認証
require('auth.php');

if(!empty($_POST)){
    debug('POST送信があります');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //未入力チェック
    validreq($email,'email');
    validreq($pass,'pass');

    //email形式チェック
    validemail($email,'email');
    //email最大文字数
    maxLen($email,'email');

    //半角チェック
    validhalf($pass,'pass');
    //最大文字数
    maxLen($pass,'pass');
    //最小文字数
    minLen($pass,'pass');

    if(empty($err_msg)){
        debug('バリデーションok');

        try{
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email=? AND delete_flg=0';
            $data = array($email);
            $stmt = queryPost($dbh,$sql,$data);
            $resu = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('クエリ結果の中身:'.print_r($resu,true));
            if(!empty($resu) && password_verify($pass,array_shift($resu))){
                debug('パスワードがマッチしました');

                $limit = 60*60;
                $_SESSION['login_date'] = time();

                if($pass_save){
                    debug('ログイン保持にチェックがあります');
                    $_SESSION['login_limit'] = $limit*24*30;
                }else{
                    debug('ログイン保持にチェックはありません');
                    $_SESSION['login_limit'] = $limit;
                }
                $_SESSION['user_id'] = $resu['id'];

                debug('セッション変数の中身:'.print_r($_SESSION,true));
                header("Location:mypage.php");
            }else{
                debug('パスワードがマッチしませんでした');
                $err_msg['common'] = MSG09;
            }
            
        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
}
?>

<?php
$sitetitle = 'ログイン';
require('head.php');
?>
</head>
<body style="position:relative;z-index:100;">
<p id="js-show-msg" class="msg-slide" style="display:none;z-index:300;"><?php echo getFlash('msg_success'); ?></p>
<?php require('header.php'); ?>
<div class="wrap">

    <div class="join-img-wrap">
        <div class="join-wrap">
            <form action="" method="post">

                <p class="join-msg raf"> Login </p>
                <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                <label for="" class="raf" style="font-size:24px;">email <br>
                    <input type="text" name="email" value="<?php echo keep('email'); ?>">
                </label>
                <div class="err-msg"><?php echo errmsg('email'); ?></div>


                <label class="raf" for="" method="post" style="font-size:24px;">password <br>
                    <input type="text" name="pass" value="<?php echo keep('pass'); ?>">
                </label>
                <div class="err-msg"><?php echo errmsg('pass'); ?></div>

                <!--次回ログインを保持-->
                <label class="raf" style="font-size:20px;">
                <input type="checkbox" name="pass_save" style="display:inline-block;vertical-align:middle;;margin-right:15px;width:20px;height:20px;"><< keep login
                </label>
                
                
                <p class="btn"><input class="raf" type="submit" value="Login" name="submit"></p>
                <!--パスワード忘れた人-->
                <span class="raf forgot" style="font-size:30px;">forgot password  >> </span><a href="remindsend.php" class="raf forgot" style="font-size:30px;">  password issued</a>
                
            </form>
            

        </div>
    </div>
</div>
<?php require('footer.php'); ?>