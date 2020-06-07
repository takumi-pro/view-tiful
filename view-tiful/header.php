<header>
        <div class="header-inner">
            <div class="inner">
                <nav class="global-nav">
                    <ul class="flex">
                        <li><a href="#" class="raf">CONTACT</a></li>
                        <li><a href="#" class="raf">ABOUT</a></li>
                        <li><a href="#" class="raf">CONCEPT</a></li>
                        
                    </ul>
                </nav>
            </div>
            <div class="inner">
                <div class="main-logo">
                    <h1><a href="top.php" class="raf">view-tiful</a></h1>
                </div>
            </div>
            <div class="inner">
                <nav class="rig-nav">
                    <ul class="flex">
                        <?php if(!empty($_SESSION['login_date'])){ ?>
                        <li><a href="logout.php" class="raf">Logout</a></li>
                        <li><a href="mypage.php" class="raf">My page</a></li>
                        <?php }else{ ?>
                            <li><a href="login.php" class="raf">Login</a></li>
                        <li><a href="signup.php" class="raf">sign up</a></li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>