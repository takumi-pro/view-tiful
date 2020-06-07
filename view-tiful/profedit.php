<?php
require('function.php');
debug('-----------------プロフィール編集ページ-------------');

//ログイン認証
require('auth.php');

//DBからユーザー情報取得
$dbData = getUser($_SESSION['user_id']);
$areaData = areaData();
debug('ユーザー情報:'.print_r($dbData,true));
debug('エリア情報:'.print_r($areaData,true));

if(!empty($_POST)){
    debug('POST情報:'.print_r($_POST,true));
    $username = $_POST['username'];
    $email = $_POST['email'];
    $job = $_POST['job'];
    $hobby = $_POST['hobby'];
    $area = $_POST['area'];
    $intro = $_POST['intro'];
    $pic = (!empty($_FILES['pic']['name'])) ? uploadimg($_FILES['pic'],'pic') : '';
    $pic = (empty($pic) && !empty($dbData['pic'])) ? $dbData['pic'] : $pic;

    if($dbData['username'] !== $username){
        maxLen($username,'username');
    }
    if($dbData['job'] !== $job){
        maxLen($job,'job');
    }
    if($dbData['hobby'] !== $hobby){
        maxLen($job,'hobby');
    }
    if($dbData['email'] !== $email){
        maxLen($email,'email');
        if(empty($err_msg['email'])){
            emaildup($email);
        }
        validreq($email,'email');
        validemail($email,'email');
    }

    if(empty($err_msg)){
        debug('バリデーションok');

        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET username=?,email=?,job=?,hobby=?,intro=?,area=?,pic=? WHERE id=?';
            $data = array($username,$email,$job,$hobby,$intro,$area,$pic,$dbData['id']);
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                debug('マイページへ遷移');
                header("Location:mypage.php");
            
            }
        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }

}
?>

<?php
$sitetitle = 'プロフィール編集';
require('head.php');
?>
</head>
<body id="profedit">
<?php require('header.php'); ?>
<div class="wrapper">
    <div class="mypage-img">
        <?php require('sheader.php'); ?>
        <div class="flex">
            <?php require('side.php'); ?>
            <div class="content-wrapper">
                <div class="prof-wrapper">
                    <form action="" method="post" enctype="multipart/form-data">
                        <label for="" class="raf" style="font-size:24px;">name - <span class="min" style="font-size:16px;vertical-align:middle;">お名前</span><br>
                            <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('name'); ?></div>

                        <label for="" class="raf" style="font-size:24px;">email - <span class="min" style="font-size:16px;vertical-align:middle;">メールアドレス</span><br>
                            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('email'); ?></div>

                        <label class="raf" for="" method="post" style="font-size:24px;">job - <span class="min" style="font-size:16px;vertical-align:middle;">職業</span><br>
                            <input type="text" name="job" value="<?php echo getFormData('job'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('job'); ?></div>

                        <label class="raf" for="" method="post" style="font-size:24px;">hobby - <span class="min" style="font-size:16px;vertical-align:middle;">趣味</span><br>
                            <input type="text" name="hobby" value="<?php echo getFormData('hobby'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('hobby'); ?></div>
                        <!--パスワード入力-->
                        <label class="raf" for="" method="post" style="font-size:24px;">regidential area - <span class="min" style="font-size:16px;vertical-align:middle;">居住エリア</span><br>
                            <select class="min" name="area" id="area" style="font-size:14px;">
                            <option value="0" <?php if(getFormData('area') == 0) echo 'selected'; ?>>選択してください</option>
                            <?php foreach($areaData as $key => $val){ ?>                           
                                <option value="<?php echo $val['id']; ?>" <?php if(getFormData('area') == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                            <?php } ?>
                            </select>
                        </label>
                        <div class="err-msg"><?php echo errmsg('area'); ?></div>

                        <label class="raf" for="" method="post" style="font-size:24px;">self introduction - <span class="min" style="font-size:16px;vertical-align:middle;">自己紹介</span><br>
                            <textarea name="intro" id="intro" cols="20" rows="5"><?php echo getFormData('intro'); ?></textarea>
                        </label>
                        <div class="err-msg"><?php echo errmsg('intro'); ?></div>

                        <div class="flex" style="justify-content: space-between;">
                            <div class="flex culom">
                                <label class="raf input-file " for="" method="post" style="font-size:24px;">
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic" class="file">
                                    
                                    <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="border-radius:50%;max-height:400px; <?php if(empty(getFormData('pic'))) echo 'display:none;'; ?>">
                                    drag & drop
                                </label>
                                <div class="err-msg"><?php echo errmsg('pic'); ?></div>
                            </div>
                            
                            
                        </div>
                        
                        <!--パスワード再入力-->
                        

                        <p class="btn"><input class="raf" type="submit" value="change" name="submit"></p>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>