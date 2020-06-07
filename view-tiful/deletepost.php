<?php
require('function.php');
debug('--------------投稿削除機能----------------');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('削除写真ID:'.$p_id);

try{
    $dbh = dbConnect();
    $sql = 'DELETE FROM photo WHERE id=?';
    $data = array($p_id);
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
        $_SESSION['msg_success'] = SUC06;
        debug('マイページへ遷移');
        header("Location:mypage.php");
    }
}catch (Exception $e){
    error_log('エラー発生:'.$e->getMessage());
}
?>