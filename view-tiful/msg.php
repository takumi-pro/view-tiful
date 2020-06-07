<?php
require('function.php');
debug('-----------------メッセージ----------------');

require('auth.php');

$h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';

$photoinfo = '';

$viewData = getmsg($m_id);
debug('取得した情報:'.print_r($viewData,true));

if(empty($viewData)){
    error_log('エラー発生　指定ページに不正な値が入りました');
    header("Location:mypage.php");
}

$photoinfo = getpostone($viewData[0]['photo_id']);
debug('写真情報:'.print_r($photoinfo,true));

if(empty($photoinfo)){
    error_log('写真情報が取得できませんでした');
}

if(!empty($_POST)){
    debug('POST送信あり');
    $msg = (isset($_POST['msg']));
}

//相手のユーザーID
debug('相手のID:'.print_r($h_id,true));
$reciverinfo = getUser($h_id);
debug('相手情報:'.print_r($reciverinfo,true));

$myinfo = getUser($_SESSION['user_id']);
debug('自分情報:'.print_r($myinfo,true));
//自分の情報が取れたかチェック
if(empty($myinfo) || empty($h_id)){
    error_log('指定したページに不正な値が入りました');
}

if(!empty($_POST)){
    debug('POST送信がありました');
    $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
    maxLen($msg,'msg');
    validreq($msg,'msg');
    if(empty($err_msg)){
        debug('バリデーションok');
        
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO message SET send_date=?,from_user=?,msg=?,create_date=?,bord_id=?,to_user=?';
            $data = array(date('Y-m-d H:i:s'),$_SESSION['user_id'],$msg,date('Y-m-d H:i:s'),$m_id,$h_id);
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                $_POST =array();
                debug('自分へ遷移');
                header("Location: ".$_SERVER['PHP_SELF'].'?m_id='.$m_id.'&p_id='.$p_id.'&h_id='.$h_id);
            }
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }

}
/*<?php if(!empty($viewData)){
    foreach($viewData as $key => $val){
        if(!empty($val['from_user']) && $val['from_user'] == $h_id){ ?>*/
            //<?php }else{ 


?>

<?php
$sitetitle = 'メッセージ';
require('head.php');
?>
</head>
<body id="msg">
<?php require('header.php'); ?>
<div class="wrapper">
    <?php require('sheader.php'); ?>
    <div class="msg-img-wrap">
        <div class="msg-wrap">
            <ul >
            <?php if(!empty($viewData[0]['m_id'])){
                    foreach($viewData as $key => $val){
                        if(!empty($val['from_user']) && $val['from_user'] == $h_id){ ?>
                            <li class="left-msg msg">
                                <dl class="flex">
                                    <dt><img src="<?php echo showimg(sanitize($reciverinfo['pic'])); ?>" alt=""></dt>
                                    <dd><p><?php echo $val['msg']; ?></p></dd>
                                </dl>
                                <div class="send" style="font-size:13px;color:#111;margin-top:5px;"><?php echo $val['send_date']; ?></div>
                            </li>
                            <?php 
                        }else{
                            ?>
                            <li class="right-msg msg">
                                <dl class="flex" style="flex-flow:row-reverse;">
                                    <dt><img src="<?php echo showimg(sanitize($myinfo['pic'])); ?>" alt=""></dt>
                                    <dd><p><?php echo $val['msg']; ?></p></dd>                                               
                                </dl>
                                <div class="send" style="font-size:13px;color:#111;margin-top:5px;"><?php echo $val['send_date']; ?></div> 
                            </li>
                           <?php 
                        }
                    }
                }else{ ?>
                <p style="text-align:center;">メッセージ投稿はありません</p>
                <?php } ?>
                           
                   


            </ul>
        </div>
            <div class="txt-wrap">
                <form action="" method="post" style="margin: 0 auto;padding:20px 48px;justify-content: flex-end;" class="flex">
                    <textarea name="msg" id="" cols="30" rows="10"></textarea>
                    <input type="submit" name="submit" class="raf" value=">>">
                </form>
            </div>
            
        
    </div>
</div>
</body>
</html>