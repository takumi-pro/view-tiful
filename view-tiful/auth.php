<?php
if(!empty($_SESSION['login_date'])){
    debug('ログイン済ユーザーです。');
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('有効期限切れです。');
        session_destroy();
        header("Location:login.php");
    }else{
        debug('有効期限内です');
        $_SESSION['login_date'] = time();
        
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('マイページに遷移します');
            header("Location:mypage.php");
        }
    }
}else{
    debug('未ログインユーザー');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:login.php");
    }
}
?>