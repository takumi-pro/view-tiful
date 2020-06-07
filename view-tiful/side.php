<?php
$getuser = getUser($_SESSION['user_id']);

?>

<div class="side-wrap">
            <div>
                <div class="side-inner over">
                    <div class="prof-img"><img src="<?php echo showimg($getuser['pic']); ?>" alt=""></div>
                    <p class="prof-name min"><?php echo $getuser['username']; ?></p>
                    <p><a href="profedit.php" class="min">プロフィールを編集</a></p>
                </div>
                <div class="side-inner bottom">
                    <ul>
                        <li class=""><a class="min" href="passedit.php">パスワード変更</a></li>
                        <li class=""><a class="min" href="">ダイレクトメッセージ</a></li>
                        <li class=""><a class="min" href="">トークルームを作成</a></li>
                        <li class=""><a class="min" href="">トークルームに参加</a></li>
                        <li class=""><a class="min" href="logout.php">ログアウト</a></li>
                        <p><a class="min" href="post.php">投稿する</a></p>
                    </ul>
                </div>
            </div>
        </div>