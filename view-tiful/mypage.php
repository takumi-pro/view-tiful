<?php
require('function.php');

//ログイン認証
require('auth.php');

//自分情報取得
$myinfo = mydata($_SESSION['user_id']);
debug('投稿写真情報:'.print_r($myinfo,true));

//お気に入り情報取得
$mylike = mylike($_SESSION['user_id']);
debug('お気に入り情報:'.print_r($mylike,true));

//フォロー
$follow = myfollow($_SESSION['user_id']);
debug('フォローしたユーザー:'.print_r($follow,true));
?>

<?php
$sitetitle = 'マイページ';
require('head.php');
?>
</head>
<body id="mypage" style="position:relative;">
<p id="js-show-msg" class="msg-slide" style="display:none;"><?php echo getFlash('msg_success'); ?></p>
<?php require('header.php'); ?>
<div class="wrapper">
    <div class="mypage-img">
        <?php require('sheader.php'); ?>
        <div class="flex">
        <?php require('side.php'); ?>
        <div class="content-wrapper">
            <div class="content-wrap">
                <div class="posted con">
                    <div class="main-txt">
                        <h3 class="raf right">posted photo - <span class="min" style="font-size:16px;vertical-align:middle;">投稿写真</span></h3>
                    </div>
                    <ul class="flex">
                    <?php if(!empty($myinfo[0]['id'])){
                        foreach($myinfo as $key => $val){ ?>
                        <li>
                            <a href="post.php<?php echo (!empty(appendget())) ? appendget().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>">
                               <dl>
                                  <dt class="photo"><img src="<?php echo sanitize($val['pic1']); ?>" alt=""></dt>
                                  <dd class="ptitle min"><?php echo sanitize($val['title']); ?></dd>
                                  <dd class="comment min"><?php echo sanitize($val['comment']); ?></dd>
                              </dl>    
                            </a>                   
                        </li>
                        <?php }
                        }else{ ?>
                        <p style="margin:0 0 14px 24px;color: rgb(121, 121, 121);font-size:14px;" class="min">お気に入り情報はありません</p>
                        <?php } ?>
                    </ul>
                </div>
                <div class="favo con">
                    <div class="main-txt">
                        <h3 class="raf right">favorite - <span class="min" style="font-size:16px;vertical-align:middle;">お気に入り写真</span></h3>
                    </div>
                    <ul class="flex">
                    <?php if(!empty($mylike[0]['user_id'])){
                        foreach($mylike as $key => $val){ ?>
                        <li>
                            <a href="postdetail.php<?php echo '?h_id='.$val['posted_id'].'&p_id='.$val['id']; ?>">
                               <dl>
                                  <dt class="photo"><img src="<?php echo sanitize($val['pic1']); ?>" alt=""></dt>
                                  <dd class="ptitle min"><?php echo sanitize($val['title']); ?></dd>
                                  <dd class="comment min"><?php echo sanitize($val['comment']); ?></dd>
                              </dl>    
                            </a>                   
                        </li>
                        <?php }
                        }else{ ?>
                        <p style="margin:0 0 14px 24px;color: rgb(121, 121, 121);font-size:14px;" class="min">お気に入り情報はありません</p>
                        <?php } ?>
                    </ul>
                </div>
                <div class="follo con">
                    <div class="main-txt">
                        <h3 class="raf right">follows - <span class="min" style="font-size:16px;vertical-align:middle;">フォローしたユーザー</span></h3>
                    </div>
                    <ul class="flex">
                    <?php if(!empty($follow[0]['user_id'])){
                        foreach($follow as $key => $val){ ?>
                    
                        <li>
                            <a href="profdetail.php<?php echo '?h_id='.$val['opponent_id']; ?>">
                               <dl>
                                  <dt class="photo"><img src="<?php echo sanitize($val['pic']); ?>" alt=""></dt>
                                  <dd class="ptitle min"><?php echo sanitize($val['username']); ?></dd>
                              </dl>    
                            </a>                   
                        </li>
                        <?php }
                        }else{ ?>
                        <p style="margin:0 0 14px 24px;color: rgb(121, 121, 121);font-size:14px;" class="min">フォローしているユーザーはいません</p>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>