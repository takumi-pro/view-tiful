<?php
require('function.php');

if(isset($_POST['photoId']) && isset($_SESSION['user_id'])){
    debug('POST送信あり');
    $p_id = $_POST['photoId'];
    debug('商品ID:'.$p_id);
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM favorite WHERE photo_id=? AND user_id=?';
        $data = array($p_id,$_SESSION['user_id']);
        $stmt = queryPost($dbh,$sql,$data);
        $resu = $stmt->rowCount();
        if(!empty($resu)){
            $sql = 'DELETE FROM favorite WHERE photo_id=? AND user_id=?';
            $data = array($p_id,$_SESSION['user_id']);
            $stmt = queryPost($dbh,$sql,$data);
            debug('削除完了');
        }else{
            $sql = 'INSERT INTO favorite SET photo_id=?,user_id=?,create_date=?';
            $data = array($p_id,$_SESSION['user_id'],date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh,$sql,$data);
            debug('インサート完了');
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}

if(isset($_POST['followIdkey']) && isset($_SESSION['user_id'])){
    debug('POST送信あり');
    $o_id = $_POST['followIdkey'];
    debug('相手ID:'.$o_id);
    debug('自分ID:'.$_SESSION['user_id']);
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM follow WHERE user_id=? AND opponent_id=?';
        $data = array($_SESSION['user_id'],$o_id);
        $stmt = queryPost($dbh,$sql,$data);
        $resu = $stmt->rowCount();
        if(!empty($resu)){
            $sql = 'DELETE FROM follow WHERE user_id=? AND opponent_id=?';
            $data = array($_SESSION['user_id'],$o_id);
            $stmt = queryPost($dbh,$sql,$data);
            debug('フォロー解除');
        }else{
            $sql = 'INSERT INTO follow SET opponent_id=?,user_id=?,create_date=?';
            $data = array($o_id,$_SESSION['user_id'],date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh,$sql,$data);
            debug('フォローしました');
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
?>