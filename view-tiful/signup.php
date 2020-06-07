<?php
require('function.php');
debug('--------------------新規登録画面です-------------------');
debugstart();

//postしたかどうか
if(!empty($_POST)){
    debug('POST入力があります');
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validreq($name,'name');
    validreq($email,'email');
    validreq($pass,'pass');
    validreq($pass_re,'pass_re');

    if(empty($err_msg)){
        //email形式
        validemail($email,'email');
        maxLen($email,'email');
        emaildup($email);
        
        //パスワード
        maxLen($pass,'pass');
        minLen($pass,'pass');
        maxLen($pass_re,'pass_re');
        minLen($pass_re,'pass_re');
        validhalf($pass,'pass');
        validhalf($pass_re,'pass_re');
        if(empty($err_msg)){
            //パスワード再入力
            validmatch($pass,$pass_re,'pass_re');
        }
        if(empty($err_msg)){
            debug('バリデーションok');
            //例外処理
            try{
                $dbh = dbConnect();
                $sql = 'INSERT INTO users SET username=?,email=?,password=?,create_date=?,login_time=?';
                $data = array($name,$email,password_hash($pass,PASSWORD_DEFAULT),date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));
                $stmt = queryPost($dbh,$sql,$data);
                if($stmt){
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC04;
                    $limit = 60*60;
                    $_SESSION['login_limit'] = $limit;
                    $_SESSION['login_date'] = time();
                    $_SESSION['user_id'] = $dbh->lastInsertID();

                    debug('セッション変数の中身:'.print_r($_SESSION,true));
                    header("Location:mypage.php");
                }else{
                    debug('クエリ失敗');
                    $err_msg['common'] = MSG08;
                }
            } catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
            }
        }
    }
}
?>

<?php
$sitetitle = '新規登録';
require('head.php');
?>
</head>
<body id="signup">
<?php require('header.php'); ?>
<div class="wrap">
    <div class="join-img-wrap">
        <div class="join-wrap">
            <form action="" method="post">

                <p class="join-msg raf"> sign up </p>
                <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                <label for="" class="raf" style="font-size:24px;">username <br>
                    <input type="text" name="name" value="<?php echo keep('name'); ?>">
                </label>
                <div class="err-msg"><?php echo errmsg('name'); ?></div>

                <label for="" class="raf" style="font-size:24px;">email <br>
                    <input type="text" name="email" value="<?php echo keep('email'); ?>">
                </label>
                <div class="err-msg"><?php echo errmsg('email'); ?></div>

                <!--パスワード入力-->
                <label class="raf" style="font-size:24px;">password <br>
                    <input type="password" name="pass" value="<?php echo keep('pass'); ?>">
                </label>
                <div class="err-msg" style="color:red;font-size:12px;"><?php echo errmsg('pass'); ?></div>

                <!--パスワード再入力-->
                <label class="raf" style="font-size:24px;">re-enter password <br>
                    <input type="password" name="pass_re" value="<?php echo keep('pass_re'); ?>">
                </label>
                <div class="err-msg"><?php echo errmsg('pass_re'); ?></div>

                <p class="btn"><input class="raf" type="submit" value="sign up" name="submit"></p>
                
            </form>
            

        </div>
    </div>
</div>
<?php require('footer.php'); ?>