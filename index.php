<?php
session_start();
$login_user = '';

// 如果有会话，就去数据库校验
if (!empty($_SESSION['user'])) {
    $conn = mysqli_connect('localhost','root','root','web_db');
    $username = mysqli_real_escape_string($conn, $_SESSION['user']);
    $res = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");

    // 数据库里没这个人了 → 踢下线
    if (mysqli_num_rows($res) === 0) {
        // 销毁会话
        $_SESSION = [];
        session_destroy();
        // 清cookie
        setcookie(session_name(), '', time()-3600, '/');
        $login_user = '';
    } else {
        $login_user = $_SESSION['user'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html {
            scroll-behavior: smooth;
        }
        body {
            background-image: url("all.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }
        .nav {
            background: black;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 999;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        .nav a {
            color: white;
            text-decoration: none;
        }
        .nav a:hover {
            color: #ffcc00;
        }

        .user-menu {
            position: fixed;
            top: 12px;
            right: 25px;
            z-index: 1000;
            cursor: pointer;
            color: white;
            font-size: 15px;
            padding: 8px 12px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            object-fit: cover;
            border: 2px solid #fff;
            display: block;
        }
        .user-card {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 260px;
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: none;
            z-index: 998;
        }
        .user-card.show {
            display: block;
        }
        .user-avatar-lg {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #ddd;
            margin: 0 auto 15px;
        }
        .user-info {
            text-align: center;
        }
        .user-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .user-info p {
            font-size: 14px;
            color: #666;
            margin: 4px 0;
        }
        .profile-btn, .logout-btn {
            margin-top:15px;
            width:100%;
            padding:10px;
            border:none;
            border-radius:10px;
            cursor:pointer;
        }
        .profile-btn {
            background:#007bff;
            color:#fff;
        }
        .logout-btn {
            background:#dc3545;
            color:#fff;
        }

        .page-container {
            height: 100vh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
        }
        .section {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            scroll-snap-align: start;
        }

        .banner {
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: 72% 26%;
            grid-template-rows: 75% 23%;
            gap: 2%;
            padding: 2% 3%;
            position: relative;
        }
        .banner-left {
            grid-column: 1 / 2;
            grid-row: 1 / 2;
            overflow: hidden;
            border-radius: 15px;
            position: relative;
        }
        .banner-right {
            grid-column: 2 / 3;
            grid-row: 1 / 2;
            background: rgba(255,255,255,0.75);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }
        .home-bottom-card{
            grid-column: 1 / 3;
            grid-row: 2 / 3;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px;
        }
        .banner-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            transition: transform 0.8s ease;
        }
        .banner-item {
            width: 100%;
            height: 100%;
            flex-shrink: 0;
        }
        .banner-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }
        .dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 99;
        }
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
        }
        .dot.active {
            background: #fff;
        }
        .small-box{
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            backdrop-filter: blur(5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .small-box h4{
            font-size: 22px;
            color: #222;
            margin-bottom: 12px;
        }
        .small-box p{
            font-size: 15px;
            color: #555;
            text-align: center;
            line-height: 1.7;
        }

        .container {
            width: 90%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            background: rgba(71, 70, 70, 0.7);
            padding: 20px;
            border-radius: 17px;
        }
        .box {
            padding: 30px;
            background: #f5f5f9;
            text-align: center;
            width: 45%;
            border-radius: 30px;
        }

        .history-section {
            width: 100%;
            height: 100vh;
            position: relative;
            overflow: hidden;
            background: #fff;
            padding: 0 60px;
            display: flex;
            align-items: center;
        }
        .history-wrap {
            display: flex;
            gap: 120px;
            transition: transform 0.7s ease;
            position: relative;
        }
        .history-line {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 2px;
            background: #eee;
            z-index: 1;
        }
        .history-item {
            min-width: 340px;
            padding: 35px 30px;
            background: #fafafa;
            border-radius: 14px;
            z-index: 2;
        }
        .history-item:nth-child(odd) { transform: translateY(-80px); }
        .history-item:nth-child(even) { transform: translateY(80px); }
        .history-item h3 { font-size:21px; margin-bottom:10px; color:#222; }
        .history-item p { font-size:15px; color:#666; line-height:1.6; }

    </style>
</head>
<body>

<div class="user-menu" id="userStatus">
    <?php if(!empty($login_user)): ?>
        <img class="avatar" src="https://picsum.photos/200/200" alt="头像">
    <?php else: ?>
        未登录
    <?php endif; ?>
</div>

<div class="user-card" id="userCard">
    <div class="user-avatar-lg"></div>
    <div class="user-info">
        <h3><?php echo $login_user; ?></h3>
        <p>账号：<?php echo $login_user; ?></p>
        <button class="profile-btn" onclick="window.location.href='profile.html'">个人主页</button>
        <button class="logout-btn" onclick="window.location.href='logout.php'">退出登录</button>
    </div>
</div>

<div class="nav">
    <a href="#page1">首页</a>
    <a href="#page2">关于我们</a>
    <a href="#page3">发展历程</a>
    <a href="register.html">注册</a>
</div>

<div class="page-container">
    <section class="section" id="page1">
        <div class="banner">
            <div class="banner-left">
                <div class="banner-wrap">
                    <div class="banner-item"><img src="1.jpg"></div>
                    <div class="banner-item"><img src="2.jpg"></div>
                    <div class="banner-item"><img src="3.jpg"></div>
                </div>
                <div class="dots">
                    <div class="dot active"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
            <div class="banner-right">
                <h2>欢迎来到官网</h2>
                <p style="margin-top:20px;line-height:1.8;">
                    这里可以放网站简介<br>
                    图片轮播依旧正常自动切换<br>
                    右边框固定不动
                </p>
            </div>
            <div class="home-bottom-card">
                <div class="small-box">
                    <h4>团队简介</h4>
                    <p>这是简介</p>
                </div>
                <div class="small-box">
                    <h4>服务特色</h4>
                    <p>服务特色</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="page2">
        <div class="container">
            <div class="box">
                <h2>首页部分</h2>
                <p>这是首页</p>
            </div>
            <div class="box">
                <h2>关于我们</h2>
                <p>这是关于我们</p>
            </div>
            <div class="box" id="page3">
                <h2>联系我们</h2>
                <p>这是联系我们</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="history-section">
            <div class="history-line"></div>
            <div class="history-wrap">
                <div class="history-item"><h3>2022 · 成立</h3><p>团队正式组建</p></div>
                <div class="history-item"><h3>2023 · 成长</h3><p>稳步发展</p></div>
                <div class="history-item"><h3>2024 · 突破</h3><p>完成重要项目</p></div>
                <div class="history-item"><h3>2025 · 创新</h3><p>持续创新</p></div>
                <div class="history-item"><h3>2026 · 展望</h3><p>未来更加精彩</p></div>
            </div>
        </div>
    </section>
</div>

<script>
    const wrap = document.querySelector('.banner-wrap');
    const items = document.querySelectorAll('.banner-item');
    const dots = document.querySelectorAll('.dot');
    let currentIndex = 0;
    function switchBanner() {
        currentIndex = (currentIndex + 1) % items.length;
        wrap.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === currentIndex));
    }
    setInterval(switchBanner, 3000);

    const hWrap = document.querySelector('.history-wrap');
    const hSection = document.querySelector('.history-section');
    let pos = 0;
    const step = 460;
    const count = document.querySelectorAll('.history-item').length;
    const max = -(count - 1) * step;

    window.addEventListener('wheel', (e) => {
        const rect = hSection.getBoundingClientRect();
        const inView = rect.top <= 100 && rect.bottom >= -100;

        if (inView) {
            if (e.deltaY > 0) {
                if (pos > max) {
                    e.preventDefault();
                    pos -= step;
                }
            } else {
                if (pos < 0) {
                    e.preventDefault();
                    pos += step;
                }
            }
            hWrap.style.transform = `translateX(${pos}px)`;
        }
    }, { passive: false });

    const userStatus = document.getElementById('userStatus');
    const userCard = document.getElementById('userCard');

    userStatus.onclick = function(){
        <?php if(empty($login_user)): ?>
            location.href = "login.html";
        <?php else: ?>
            userCard.classList.toggle('show');
        <?php endif; ?>
    }
</script>

</body>
</html>