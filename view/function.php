<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');

$debug_flg = false;
function debug($str){
    global $debug_flg;
    if($debug_flg){
        error_log('デバック:'.$str);
    }
}

session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);
session_start();
session_regenerate_id();

function debugstart(){
    debug('//////////////////画面表示処理開始/////////////////');
    debug('セッションID:'.session_id());
    debug('セッション変数の中身:'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ:'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}
define('MSG01','入力必須です');
define('MSG02','100文字以内で入力してください');
define('MSG03','6文字以上で入力してください');
define('MSG04','emailの形式で入力してください');
define('MSG05','半角英数字で入力してください');
define('MSG06','パスワードが合っていません');
define('MSG07','emailが既に登録してあります');
define('MSG08','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG09','emailまたはパスワードが違います');
define('MSG10','古いパスワードが違います');
define('MSG11','古いパスワードと同じです');
define('MSG12','文字で入力してください');
define('MSG13','正しくありません');
define('MSG14','有効期限切れです');
define('MSG15','選択してください');
define('SUC01','パスワードを変更しました');
define('SUC02','メールを送信しました');
define('SUC03','パスワードを再発行しました');
define('SUC04','写真を投稿して皆でshareしましょう！');
define('SUC05','写真が投稿されました！');
define('SUC06','写真が削除されました');

$err_msg = array();

function dbConnect(){
    $dsn = 'mysql:dbname=viewtiful;host=localhost;charset=utf8';
    $user = 'root';
    $pass = 'root';
    $option = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        // デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
      );
    $dbh = new PDO($dsn, $user,$pass,$option);
    return $dbh;
}
function queryPost($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
        debug('クエリ失敗');
        $err_msg['common'] = MSG08;
        return false;
    }else{
        debug('クエリ成功');
        return $stmt;
    }
    
    return $stmt;
}
//未入力チェック
function validreq($str,$key){
    if(empty($str)){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}
//最大文字数
function maxLen($str,$key,$len=255){
    if(mb_strlen($str) > $len){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}
//最小文字数
function minLen($str,$key,$len=6){
    if(mb_strlen($str) < $len){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//email形式チェック
function validemail($str,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//半角チェック
function validhalf($str,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
//パスワード照合
function validmatch($str1,$str2,$key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//パスワード
function validpass($str,$key){
    validhalf($str,$key);
    maxLen($str,$key);
    minLen($str,$key);
}
//email重複チェック
function emaildup($str){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email=? AND delete_flg=0';
        $data = array($str);
        $stmt = queryPost($dbh,$sql,$data);
        $resu = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($resu))){
            global $err_msg;
            $err_msg['email'] = MSG07;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG08;
    }
}
//フォーム入力保持
function keep($key){
    if(!empty($_POST[$key])){
        return $_POST[$key];
    }
}
//エラーメッセージ
function errmsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//ユーザー情報取得
function getUser($str){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE id=? AND delete_flg=0';
        $data = array($str);
        $stmt = queryPost($dbh,$sql,$data);
    } catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
//フォーム入力保持
function getFormData($str,$flg=false){
    global $dbData;
    global $err_msg;
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    //ユーザー情報がある場合
    if(!empty($dbData)){
        //フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            //POSTに情報がある場合
            if(isset($method[$str])){
                return $method[$str];
            }else{
                return $dbData[$str];
            }
        }else{
            //POSTに情報がありDBの情報を違う
            if(isset($methos[$str]) && $method[$str] !== $dbData[$str]){
                return $method[$str];
            }else{
                //そもそも変更していない
                return $dbData[$str];
            }
        }
    }else{
        if(isset($method[$str])){
            return $method[$str];
        }
    }
}
function areaData(){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT name,id FROM area';
        $data = array();
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//カテゴリ
function getcate(){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//セッションを1回だけ取得
function getFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}
//メール送信
function sendMail($to,$subject,$comment,$from){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        //文字化けしないように
        mb_language('Japanese');
        mb_internal_encoding("UTF-8");

        $resu = mb_send_mail($to,$subject,$comment,"From:".$from);
        if($resu){
            debug('メール送信成功');
        }else{
            debug('メール送信失敗');
        }
    }
}
//認証キー発行
function makeRand($length=8){
    $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i=0;$i<$length;++$i){
        $str .= $char[mt_rand(0,61)];
    }
    return $str;
}
//固定長チェック
function validlength($str,$key,$len=8){
    if(mb_strlen($str) !== $len){
        global $err_msg;
        $err_msg[$key] = $len.MSG12;
    }
}
//投稿情報取得
function getPost($u_id,$p_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM photo WHERE posted_id=? AND id=? AND delete_flg=0';
        $data = array($u_id,$p_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG08;
    }
}
//アップロード
function uploadimg($file,$key){
    debug('画像アップロード');
    debug('ファイル情報:'.print_r($_FILES,true));
    if(isset($file['error']) && is_int($file['error'])){
        try{
            switch ($file['error']){
                case UPLOAD_ERR_OK:
                break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RnutimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                throw new RuntimeException('その他のエラーが発生しました');
            }

            //画像の形式を判別
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
                throw new RuntimeException('画像形式が未対応です');
            }

            $path = 'upload/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }

            //保存したファイルのパスの権限を変更
            chmod($path,0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス:'.$path);
            return $path;
        }catch (RuntimeException $e){
            global $err_msg;
            debug($e->getMessage());
            $err_msg[$key] = $e->getMessage();
        }
    }
}
//セレクトボックス
function validselect($str,$key){
    if(!preg_match("/^[1-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG15;
    }
}
//サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}
function getpostlist($nowmin = 1, $category, $sort, $span = 20){
    debug('商品情報を取得します。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // 件数用のSQL文作成
      $sql = 'SELECT id FROM photo';
      if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
      
      if(!empty($sort)){
        switch($sort){
          case 1:
            $sql .= ' ORDER BY create_date DESC';
            break;
          case 2:
            $sql .= ' ORDER BY create_date ASC';
            break;
        }
      } 
      $data = array();
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      $rst['total'] = $stmt->rowCount(); //総レコード数
      $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
      if(!$stmt){
        return false;
      }
      
      // ページング用のSQL文作成
      $sql = 'SELECT * FROM photo';
      if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
      
      if(!empty($sort)){
        switch($sort){
          case 1:
            $sql .= ' ORDER BY create_date DESC';
            break;
          case 2:
            $sql .= ' ORDER BY create_date ASC';
            break;
        }
      } 
      $sql .= ' LIMIT '.$span.' OFFSET '.$nowmin;
      $data = array();
      debug('SQL：'.$sql);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
  
      if($stmt){
        // クエリ結果のデータを全レコードを格納
        $rst['data'] = $stmt->fetchAll(); 
        return $rst;
      }else{
        return false;
      }
  
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
  }
//投稿一覧用投稿情報取得
/*function getpostlist($nowmin=1,$category,$sort,$key,$span=20){
    debug('投稿情報取得');
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM photo';
        if(!empty($category)) $sql .= ' WHERE category_id= '.$category;
        if(!empty($key) && !empty($category)){ 
            $sql .= ' AND key1= '.$key.' OR key2= '.$key.' OR key3= '.$key;
        }elseif(!empty($key) && empty($category)){
            $sql .= ' WHERE key1= '.$key.' OR key2= '.$key.' OR key3= '.$key;
        }
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY create_date DESC';
                case 2:
                    $sql .= ' ORDER BY create_date ASC';
                break;
            }
        }
        $data = array();
        $stmt = queryPost($dbh,$sql,$data);
        
            $resu['total'] = $stmt->rowCount();
    
        $resu['total_page'] = ceil($resu['total']/20);
        if(!$stmt){
            return false;
        }
        //ページング用sql
        $sql = 'SELECT * FROM photo';
        if(!empty($category)) $sql .= ' WHERE category_id= '.$category;
        if(!empty($key) && !empty($category)){ 
            $sql .= ' AND key1= '.$key.' OR key2= '.$key.' OR key3= '.$key;
        }elseif(!empty($key) && empty($category)){
            $sql .= ' WHERE key1= '.$key.' OR key2= '.$key.' OR key3= '.$key;
        }
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY create_date DESC';
                case 2:
                    $sql .= ' ORDER BY create_date ASC';
                break;
            }
        }
        $sql .= ' LIMIT '.$span.' OFFSET '.$nowmin;
        $data = array();
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            $resu['data'] = $stmt->fetchAll();
            return $resu;
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}*/
//投稿情報
function getpostone($p_id){
    debug('写真ID:'.$p_id);
    try{
        $dbh = dbConnect();
        $sql = 'SELECT p.id,p.title,p.key1,p.key2,p.key3,p.comment,p.place,p.pic1,p.pic2,p.pic3,p.pic4,p.create_date,p.update_date,p.posted_id,u.pic,u.username,u.intro FROM photo AS p LEFT JOIN users AS u ON p.posted_id=u.id WHERE p.id=? AND p.delete_flg=0 AND u.delete_flg=0';
        $data = array($p_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//画像表示
function showimg($path){
    if(empty($path)){
        return 'img/sample-img.png';
    }else{
        return $path;
    }
}
//GETパラメータ付与
function appendget($del_key=array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$del_key,true)){
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str,0,-1,"UTF-8");
        return $str;
    }
}
//ボード
function msgandbord($id){
    debug('写真ID:'.$id);
    try{
        $dbh = dbConnect();
        $sql = 'SELECT from_user,msg FROM message WHERE photo_id=? AND delete_flg=0';
        $data = array($id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}

function getmsg($id){
    debug('msg情報を取得');
    debug('掲示板ID:'.$id);
    try{
        $dbh = dbConnect();
        $sql = 'SELECT m.id AS m_id,b.photo_id,to_user,bord_id,send_date,from_user,msg,b.create_date,b.sender_id,b.receiver_id FROM message AS m RIGHT JOIN bord AS b ON m.bord_id=b.id WHERE b.id=? ORDER BY send_date ASC';
        $data = array($id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//お気に入り
function islike($u_id,$p_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM favorite WHERE user_id=? AND photo_id=?';
        $data = array($u_id,$p_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt->rowCount()){
            debug('お気に入り');
            return true;
        }else{
            debug('お気に入りでない');
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//フォロー
function isfollow($u_id,$o_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM follow WHERE user_id=? AND opponent_id=?';
        $data = array($u_id,$o_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt->rowCount()){
            debug('フォロー中');
            return true;
        }else{
            debug('フォローしてない');
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function mydata($u_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM photo WHERE posted_id=? AND delete_flg=0';
        $data = array($u_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function mylike($u_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM favorite AS f LEFT JOIN photo AS p ON f.photo_id=p.id WHERE f.user_id=? AND f.delete_flg=0 AND p.delete_flg=0';
        $data = array($u_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function myfollow($u_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM follow AS fo LEFT JOIN users AS u ON fo.opponent_id=u.id WHERE fo.user_id=? AND fo.delete_flg=0 AND u.delete_flg=0';
        $data = array($u_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function lastbord(){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM bord ORDER BY create_date DESC';
        $data = array();
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
?>