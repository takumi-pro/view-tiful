<?php
require('function.php');
debug('---------------投稿ページ---------------');

require('auth.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('GETパラメータ:'.print_r($_GET,true));
//DBから投稿情報を取得
$dbData = (!empty($p_id)) ? getPost($_SESSION['user_id'],$p_id) : '';
//新規投稿か編集か判断
$edit_flg = (empty($dbData)) ? false : true;
//カテゴリ
$dbcategory = getcate();

debug('投稿ID:'.$p_id);
debug('DBphoto情報:'.print_r($dbData,true));
debug('カテゴリー:'.print_r($dbcategory,true));

//getパラメータはあるが改ざんされている場合マイページに遷移
if(!empty($_GET) && empty($dbData)){
    debug('GETパラメータのIDが違います');
    header("Location:mypage.php");
}

if(!empty($_POST)){
    debug('POST情報:'.print_r($_POST,true));
    debug('FILE情報:'.print_r($_FILES,true));

    $title = $_POST['title'];
    $place = $_POST['place'];
    $key1 = $_POST['key1'];
    $key2 = $_POST['key2'];
    $key3 = $_POST['key3'];
    $cate = $_POST['category_id'];
    $comment = $_POST['comment'];
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadimg($_FILES['pic1'],'pic1') : '';
    $pic1 = (empty($pic1) && !empty($dbData['pic1'])) ? $dbData['pic1'] : $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadimg($_FILES['pic2'],'pic2') : '';
    $pic2 = (empty($pic2) && !empty($dbData['pic2'])) ? $dbData['pic2'] : $pic2;
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadimg($_FILES['pic3'],'pic3') : '';
    $pic3 = (empty($pic3) && !empty($dbData['pic3'])) ? $dbData['pic3'] : $pic3;
    $pic4 = (!empty($_FILES['pic4']['name'])) ? uploadimg($_FILES['pic4'],'pic4') : '';
    $pic4 = (empty($pic4) && !empty($dbData['pic4'])) ? $dbData['pic4'] : $pic4;

    if(empty($dbData)){
        validreq($title,'title');
        maxLen($title,'title');
        maxLen($place,'place');
        maxLen($comment,'comment');
        validselect($cate,'category_id');
    }else{
        if($dbData['title'] !== $title){
            validreq($title,'title');
            maxLen($title,'title');
        }
        if($dbData['place'] !== $place){
            maxLen($place,'place');
        }
        if($dbData['comment'] !== $comment){
            maxLen($comment,'comment');
        }
        if($dbData['category_id'] !== $cate){
            validselect($cate,'category_id');
        }
    }

    if(empty($err_msg)){
        debug('バリデーションok');

        try{
            $dbh = dbConnect();
            if($edit_flg){
                $sql = 'UPDATE photo SET title=?,category_id=?,key1=?,key2=?,key3=?,comment=?,place=?,pic1=?,pic2=?,pic3=?,pic4=? WHERE id=? AND posted_id=?';
                $data = array($title,$cate,$key1,$key2,$key3,$comment,$place,$pic1,$pic2,$pic3,$pic4,$p_id,$_SESSION['user_id']);
            }else{
                debug('新規登録です');
                $sql = 'INSERT INTO photo SET title=?,category_id=?,key1=?,key2=?,key3=?,comment=?,place=?,pic1=?,pic2=?,pic3=?,pic4=?,create_date=?,posted_id=?';
                $data = array($title,$cate,$key1,$key2,$key3,$comment,$place,$pic1,$pic2,$pic3,$pic4,date('Y-m-d H:i:s'),$_SESSION['user_id']);
            }
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                $_SESSION['msg_success'] = SUC05;
                header("Location:mypage.php");
            }
        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }
}
?>

<?php
$sitetitle = '投稿';
require('head.php');
?>
</head>
<body id="mypage" class="post">
<?php require('header.php'); ?>
<div class="wrapper">
    <div class="mypage-img">
        <?php require('sheader.php'); ?>
        <div class="flex">
        <?php require('side.php'); ?>
        <div class="content-wrapper">
            <div class="post-wrapper">
            <form action="" method="post" enctype="multipart/form-data">
                        <label for="" class="raf" style="font-size:24px;">title - <span class="min" style="font-size:16px;vertical-align:middle;">タイトル</span><br>
                            <input type="text" name="title" value="<?php echo getFormData('title'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('title'); ?></div>

                        <label for="" class="raf" style="font-size:24px;">place - <span class="min" style="font-size:16px;vertical-align:middle;">場所</span><br>
                            <input type="text" name="place" value="<?php echo getFormData('place'); ?>">
                        </label>
                        <div class="err-msg"><?php echo errmsg('place'); ?></div>

                        <label class="raf key" for="" method="post" style="font-size:24px;">keywords - <span class="min" style="font-size:16px;vertical-align:middle;">キーワード</span><br>
                            <input class="key" type="text" name="key1" value="<?php echo getFormData('key1'); ?>">
                            <input class="key" type="text" name="key2" value="<?php echo getFormData('key2'); ?>">
                            <input class="key" type="text" name="key3" value="<?php echo getFormData('key3'); ?>">
                        </label>
                        <div class="err-msg"></div>
                        
                        <label class="raf" for="" method="post" style="font-size:24px;">category - <span class="min" style="font-size:16px;vertical-align:middle;">カテゴリー</span><br>
                            <select class="min" name="category_id" id="cate" style="font-size:14px;">
                                <option value="0" <?php if(getFormData('category_id') == 0) echo 'selected'; ?>>選択してください</option>
                                <?php foreach($dbcategory as $key => $val): ?>
                                <option value="<?php echo $val['id']; ?>" <?php if(getFormData('category_id') === $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="err-msg"><?php echo errmsg('category_id'); ?></div>

                        <label class="raf" for="" method="post" style="font-size:24px;">comment - <span class="min" style="font-size:16px;vertical-align:middle;">コメント</span><br>
                            <textarea name="comment" id="intro" cols="20" rows="5"><?php echo getFormData('comment'); ?></textarea>
                        </label>
                        <div class="err-msg"><?php echo errmsg('comment'); ?></div>

                        <div class="flex" style="justify-content: space-between;">
                            <div class="flex culom">
                                <label class="raf input-file" for="" method="post" style="font-size:24px;">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic1" class="file">
                                    <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;'; ?>">
                                    drag & drop
                                </label>
                                <div class="err-msg"><?php echo errmsg('pic1'); ?></div>
                            </div>
                            <div class="flex culom">
                                <label class="raf input-file" for="" method="post" style="font-size:24px;">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic2" class="file">
                                    <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;'; ?>">
                                    drag & drop
                                </label>
                                <div class="err-msg"><?php echo errmsg('pic2'); ?></div>
                            </div>
                            
                        </div>
                        <div class="flex" style="justify-content: space-between;margin-bottom:30px;">
                            <div class="flex culom">
                                <label class="raf input-file" for="" method="post" style="font-size:24px;">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic3" class="file">
                                    <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;'; ?>">
                                    drag & drop
                                </label>
                                <div class="err-msg"><?php echo errmsg('pic3'); ?></div>
                            </div>
                            <div class="flex culom">
                                <label class="raf input-file" for="" method="post" style="font-size:24px;">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic4" class="file">
                                    <img src="<?php echo getFormData('pic4'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic4'))) echo 'display:none;'; ?>">
                                    drag & drop
                                </label>
                                <div class="err-msg"><?php echo errmsg('pic4'); ?></div>
                            </div>
                            
                        </div>

                        <!--パスワード再入力-->
                        <div class="flex" style="justify-content:flex-end;">
                        <?php if(!empty($edit_flg)){ ?>
                        <a class="raf delete" href="deletepost.php<?php echo '?p_id='.$p_id; ?>">delete</a>
                        <?php } ?>
                        <p class="btn" style="margin-left:30px;"><input class="raf" type="submit" value="<?php echo (empty($edit_flg)) ? 'post' : 'change'; ?>" name="submit"></p>
                        
                        </div>
                        
                    </form>
            </div>
        </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>