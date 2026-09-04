<?php

session_start();

if(!isset($_SESSION['admin_id'])){

    header("Location: ../login.php");
    exit;

}

include "../includes/db.php";

/* Notifications center — creates the table only if it does not already
   exist, and never touches any other table. Used to power the topbar
   bell badge and the notifications.php page. */

mysqli_query($conn,"
CREATE TABLE IF NOT EXISTS notifications(
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255),
message TEXT,
type VARCHAR(50),
is_read INT DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

/* Mark a single notification as read */

if(isset($_GET['mark_read'])){

$mark_id = (int) $_GET['mark_read'];

mysqli_query($conn,"UPDATE notifications SET is_read=1 WHERE id=$mark_id");

header("Location: notifications.php");
exit;

}

/* Mark every notification as read */

if(isset($_GET['mark_all_read'])){

mysqli_query($conn,"UPDATE notifications SET is_read=1 WHERE is_read=0");

header("Location: notifications.php");
exit;

}

$unread_notifications = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM notifications WHERE is_read=0"));

$new_messages_count = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM contact_messages"));

$all_notifications = mysqli_query($conn,"SELECT * FROM notifications ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>الإشعارات | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*========================================================
  TOOB SUDAN — LUXURY ADMIN DASHBOARD
  Design tokens
========================================================*/

:root{

  --burgundy:#5B1028;
  --burgundy-deep:#3E091A;
  --burgundy-darker:#2A0611;
  --gold:#D4AF37;
  --gold-soft:#E8C766;
  --gold-mist:#F7EFD9;
  --ink:#111111;
  --paper:#FFFFFF;
  --bg:#F5F5F5;
  --muted:#8A8690;
  --line:#ECE7E9;

  --radius-lg:24px;
  --radius-md:18px;
  --radius-sm:12px;

  --shadow-soft:0 10px 30px rgba(91,16,40,.08);
  --shadow-lift:0 20px 45px rgba(91,16,40,.16);
  --shadow-gold:0 8px 20px rgba(212,175,55,.35);

  --ease:cubic-bezier(.16,1,.3,1);

}

*{

margin:0;
padding:0;
box-sizing:border-box;
font-family:"Cairo",sans-serif;

}

html{scroll-behavior:smooth;}

body{

background:
  radial-gradient(1200px 600px at 100% -10%, rgba(212,175,55,.06), transparent 60%),
  var(--bg);

display:flex;

overflow-x:hidden;

color:var(--ink);

min-height:100vh;

}

::-webkit-scrollbar{width:9px;height:9px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--gold),var(--burgundy));border-radius:20px;}

/*======================
Signature texture — Toub weave lattice, used as a quiet
recurring motif across sidebar / header accents
=======================*/

.weave-texture{

position:absolute;
inset:0;
pointer-events:none;
opacity:.07;
background-image:
  repeating-linear-gradient(45deg, var(--gold) 0 1px, transparent 1px 26px),
  repeating-linear-gradient(-45deg, var(--gold) 0 1px, transparent 1px 26px);

}

/*======================
Sidebar
=======================*/

.sidebar{

width:280px;

background:linear-gradient(165deg,var(--burgundy) 0%,var(--burgundy-deep) 60%,var(--burgundy-darker) 100%);

height:100vh;

position:fixed;

top:0;

right:0;

padding:34px 22px;

box-shadow:-18px 0 40px rgba(0,0,0,.22);

display:flex;

flex-direction:column;

z-index:200;

transition:transform .45s var(--ease);

overflow:hidden;

}

.sidebar-inner{

position:relative;

z-index:2;

display:flex;

flex-direction:column;

height:100%;

}

.logo{

text-align:center;

margin-bottom:36px;

padding-bottom:28px;

border-bottom:1px solid rgba(212,175,55,.25);

}

.logo-badge{

width:92px;

height:92px;

margin:0 auto 16px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

background:radial-gradient(circle at 30% 30%,rgba(212,175,55,.35),rgba(212,175,55,0) 70%);

border:1px solid rgba(212,175,55,.5);

box-shadow:0 0 0 6px rgba(212,175,55,.06), 0 10px 25px rgba(0,0,0,.25);

}

.logo img{

width:56px;

filter:drop-shadow(0 2px 6px rgba(0,0,0,.35));

}

.logo h2{

color:#fff;

font-size:24px;

font-weight:800;

letter-spacing:.3px;

}

.logo .tag{

display:block;

margin-top:6px;

color:var(--gold-soft);

font-size:11px;

font-weight:600;

letter-spacing:2px;

text-transform:uppercase;

opacity:.85;

}

.menu{

list-style:none;

flex:1;

overflow-y:auto;

}

.menu li{

margin-bottom:8px;

}

.menu a{

position:relative;

display:flex;

align-items:center;

gap:14px;

padding:14px 16px;

text-decoration:none;

color:rgba(255,255,255,.78);

border-radius:16px;

transition:.3s var(--ease);

font-weight:600;

font-size:15px;

overflow:hidden;

}

.menu a::before{

content:"";

position:absolute;

inset:0;

background:rgba(255,255,255,.06);

transform:translateX(110%);

transition:transform .35s var(--ease);

border-radius:16px;

}

.menu a:hover::before,
.menu a.active::before{

transform:translateX(0);

}

.menu a:hover,
.menu a.active{

color:#fff;

padding-right:20px;

}

.menu a.active{

background:rgba(212,175,55,.14);

box-shadow:inset 0 0 0 1px rgba(212,175,55,.4);

}

.menu a.active .menu-icon{

background:var(--gold);

color:var(--burgundy-darker);

box-shadow:0 4px 14px rgba(212,175,55,.4);

}

.menu-icon{

width:36px;

height:36px;

min-width:36px;

border-radius:11px;

display:flex;

align-items:center;

justify-content:center;

background:rgba(255,255,255,.08);

transition:.3s var(--ease);

font-size:15px;

}

.menu a:hover .menu-icon{

background:var(--gold);

color:var(--burgundy-darker);

}

.sidebar-foot{

padding-top:18px;

margin-top:10px;

border-top:1px solid rgba(212,175,55,.2);

text-align:center;

color:rgba(255,255,255,.4);

font-size:11px;

letter-spacing:1px;

}

/*======================
Mobile sidebar controls
=======================*/

.menu-toggle{

display:none;

position:fixed;

top:20px;

right:20px;

z-index:300;

width:50px;

height:50px;

border-radius:15px;

background:linear-gradient(160deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

border:1px solid rgba(212,175,55,.4);

box-shadow:var(--shadow-soft);

align-items:center;

justify-content:center;

font-size:19px;

cursor:pointer;

}

.sidebar-overlay{

display:none;

position:fixed;

inset:0;

background:rgba(17,17,17,.55);

backdrop-filter:blur(2px);

z-index:190;

opacity:0;

transition:opacity .35s var(--ease);

}

.sidebar-overlay.show{

display:block;

opacity:1;

}

/*======================
Content
=======================*/

.content{

width:calc(100% - 280px);

margin-right:280px;

padding:32px;

}

/*======================
Topbar
=======================*/

.topbar{

background:rgba(255,255,255,.75);

backdrop-filter:blur(16px) saturate(160%);

-webkit-backdrop-filter:blur(16px) saturate(160%);

padding:22px 32px;

border-radius:var(--radius-lg);

display:flex;

justify-content:space-between;

align-items:center;

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.18);

margin-bottom:30px;

}

.topbar-title .eyebrow{

display:block;

color:var(--gold);

font-size:12px;

font-weight:700;

letter-spacing:2px;

text-transform:uppercase;

margin-bottom:4px;

}

.topbar h1{

font-size:28px;

color:var(--burgundy);

font-weight:800;

}

.topbar-actions{

display:flex;

align-items:center;

gap:14px;

}

.topbar-icon{

position:relative;

width:50px;

height:50px;

min-width:50px;

border-radius:16px;

display:flex;

align-items:center;

justify-content:center;

background:rgba(255,255,255,.55);

backdrop-filter:blur(10px);

-webkit-backdrop-filter:blur(10px);

border:1px solid rgba(212,175,55,.35);

box-shadow:var(--shadow-soft);

color:var(--burgundy);

font-size:18px;

text-decoration:none;

transition:.35s var(--ease);

}

.topbar-icon:hover{

transform:translateY(-4px);

background:linear-gradient(160deg,var(--burgundy),var(--burgundy-deep));

color:var(--gold-soft);

box-shadow:var(--shadow-lift);

border-color:transparent;

}

.topbar-icon i{

transition:.35s var(--ease);

}

.topbar-icon:hover i{

transform:rotate(-8deg) scale(1.05);

}

.topbar-icon .badge-dot{

position:absolute;

top:-6px;

left:-6px;

min-width:20px;

height:20px;

padding:0 5px;

border-radius:50%;

background:linear-gradient(135deg,var(--gold),var(--burgundy));

color:#fff;

font-size:11px;

font-weight:800;

display:flex;

align-items:center;

justify-content:center;

box-shadow:0 4px 10px rgba(91,16,40,.35);

border:2px solid #fff;

}

@media(max-width:700px){

.topbar-actions{

width:100%;

justify-content:flex-start;

}

}

.admin{

display:flex;

align-items:center;

gap:14px;

padding:8px 18px 8px 8px;

border-radius:50px;

background:linear-gradient(90deg,rgba(212,175,55,.08),transparent);

}

.admin .avatar-ring{

width:56px;

height:56px;

border-radius:50%;

padding:3px;

background:linear-gradient(135deg,var(--gold),var(--burgundy));

display:flex;

align-items:center;

justify-content:center;

}

.admin img{

width:100%;

height:100%;

border-radius:50%;

object-fit:cover;

border:2px solid #fff;

}

.admin h3{

font-size:16px;

font-weight:800;

color:var(--ink);

}

.admin span{

font-size:13px;

color:var(--muted);

font-weight:600;

}

/*======================
Cards Area
=======================*/

.cards{

display:grid;

grid-template-columns:repeat(4,1fr);

gap:20px;

}

.card{

position:relative;

background:linear-gradient(180deg,rgba(255,255,255,.9),rgba(255,255,255,.7));

backdrop-filter:blur(10px);

padding:26px;

border-radius:var(--radius-md);

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.15);

transition:.4s var(--ease);

cursor:pointer;

overflow:hidden;

opacity:0;

transform:translateY(18px);

animation:cardIn .6s var(--ease) forwards;

}

.card::after{

content:"";

position:absolute;

top:0;left:0;right:0;

height:4px;

background:linear-gradient(90deg,var(--gold),var(--burgundy));

transform:scaleX(0);

transform-origin:right;

transition:transform .45s var(--ease);

}

.card:hover::after{transform:scaleX(1);}

.card:hover{

transform:translateY(-8px);

box-shadow:var(--shadow-lift);

border-color:rgba(212,175,55,.4);

}

@keyframes cardIn{

from{opacity:0;transform:translateY(18px);}

to{opacity:1;transform:translateY(0);}

}

.cards .card:nth-child(1){animation-delay:.05s;}
.cards .card:nth-child(2){animation-delay:.12s;}
.cards .card:nth-child(3){animation-delay:.19s;}
.cards .card:nth-child(4){animation-delay:.26s;}
.cards .card:nth-child(5){animation-delay:.33s;}

.card .icon{

width:60px;

height:60px;

background:linear-gradient(145deg,var(--burgundy),var(--burgundy-deep));

border-radius:16px;

display:flex;

justify-content:center;

align-items:center;

color:var(--gold-soft);

font-size:24px;

margin-bottom:22px;

box-shadow:0 10px 22px rgba(91,16,40,.3);

}

.card h2{

font-size:34px;

color:var(--burgundy);

margin-bottom:6px;

font-weight:800;

letter-spacing:.3px;

}

.card p{

color:var(--muted);

font-weight:700;

font-size:14px;

}

@media(max-width:1200px){

.cards{

grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:900px){

.sidebar{

transform:translateX(105%);

width:280px;

}

.sidebar.open{

transform:translateX(0);

}

.menu-toggle{

display:flex;

}

.content{

margin-right:0;

width:100%;

padding:90px 18px 30px;

}

.cards{

grid-template-columns:repeat(4,minmax(180px,1fr));

grid-auto-flow:column;

overflow-x:auto;

overflow-y:hidden;

scroll-snap-type:x mandatory;

padding-bottom:10px;

-ms-overflow-style:none;

scrollbar-width:none;

}

.cards::-webkit-scrollbar{display:none;}

.card{

scroll-snap-align:start;

min-width:190px;

}

}

@media(max-width:700px){

.topbar{

flex-direction:column;

gap:16px;

align-items:flex-start;

}

.admin{width:100%;justify-content:flex-start;}

}

/*======================
Panel — shared card style for every content section
=======================*/

.panel{

background:#fff;

padding:26px;

border-radius:var(--radius-lg);

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.12);

position:relative;

}

.panel h2{

color:var(--burgundy);

margin-bottom:20px;

font-size:22px;

font-weight:800;

display:flex;

align-items:center;

gap:10px;

}

.panel h2::before{

content:"";

width:5px;

height:22px;

border-radius:4px;

background:linear-gradient(var(--gold),var(--burgundy));

display:inline-block;

}

.table-box{

margin-top:32px;

}

.table-scroll{

overflow-x:auto;

border-radius:var(--radius-sm);

}

table{

width:100%;

border-collapse:collapse;

min-width:620px;

}

table th{

background:linear-gradient(90deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

padding:15px;

font-size:14px;

font-weight:700;

white-space:nowrap;

}

table th:first-child{border-radius:12px 0 0 12px;}
table th:last-child{border-radius:0 12px 12px 0;}

table td{

padding:16px 15px;

border-bottom:1px solid var(--line);

font-weight:600;

font-size:14.5px;

color:var(--ink);

white-space:nowrap;

}

table tr:hover td{

background:var(--gold-mist);

}

.badge{

display:inline-block;

padding:6px 16px;

border-radius:30px;

font-size:12.5px;

font-weight:700;

background:var(--line);

color:var(--muted);

}

.badge.st-completed,.badge.st-مكتمل,.badge.st-مكتملة{background:rgba(34,150,90,.12);color:#22965A;}
.badge.st-pending,.badge.st-قيد-الانتظار,.badge.st-معلق{background:rgba(212,175,55,.16);color:#9c7a17;}
.badge.st-cancelled,.badge.st-ملغي,.badge.st-ملغى{background:rgba(200,50,50,.1);color:#c83232;}

/*======================
Charts + Stock
=======================*/

.chart-grid{

display:grid;

grid-template-columns:2fr 1fr;

gap:24px;

margin-top:32px;

}

.bar{

display:flex;

align-items:flex-end;

justify-content:space-between;

height:260px;

margin-top:38px;

gap:8px;

}

.column{

flex:1;

max-width:55px;

background:linear-gradient(var(--gold),var(--burgundy));

border-radius:14px 14px 4px 4px;

position:relative;

transition:.4s var(--ease);

height:0;

box-shadow:0 8px 18px rgba(91,16,40,.18);

}

.column.grow{height:var(--h);}

.column:hover{

transform:scaleY(1.04);

filter:brightness(1.08);

}

.column span{

position:absolute;

bottom:-32px;

left:50%;

transform:translateX(-50%);

font-weight:700;

font-size:12.5px;

color:var(--muted);

white-space:nowrap;

}

.column p{

position:absolute;

top:-28px;

left:50%;

transform:translateX(-50%);

font-size:12.5px;

font-weight:800;

color:var(--burgundy);

white-space:nowrap;

}

.stock-item{

display:flex;

justify-content:space-between;

align-items:center;

padding:14px 4px;

border-bottom:1px solid var(--line);

}

.stock-item:last-child{border:none;}

.stock-name{font-weight:700;font-size:14.5px;}

.stock-count{

background:linear-gradient(120deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

padding:6px 15px;

border-radius:30px;

font-size:12.5px;

font-weight:700;

box-shadow:0 6px 14px rgba(91,16,40,.25);

}

/*======================
Messages / Reviews
=======================*/

.bottom-grid{

display:grid;

grid-template-columns:1fr 1fr;

gap:24px;

margin-top:32px;

}

.item{

display:flex;

justify-content:space-between;

align-items:center;

gap:14px;

padding:16px 4px;

border-bottom:1px solid var(--line);

}

.item:last-child{border:none;}

.item h4{font-size:15.5px;margin-bottom:5px;font-weight:700;}

.item p{font-size:13.5px;color:var(--muted);}

.item span{

background:linear-gradient(120deg,var(--gold-soft),var(--gold));

color:var(--burgundy-darker);

padding:6px 15px;

border-radius:30px;

font-size:12px;

font-weight:800;

white-space:nowrap;

box-shadow:var(--shadow-gold);

}

.footer{

margin-top:36px;

padding:22px;

text-align:center;

background:#fff;

border-radius:var(--radius-lg);

color:var(--muted);

font-weight:600;

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.12);

font-size:13.5px;

}

.footer strong{color:var(--burgundy);}

@media(max-width:900px){

.chart-grid,
.bottom-grid{

grid-template-columns:1fr;

}

}

/*======================
Notifications center
=======================*/

.notif-toolbar{

display:flex;

align-items:center;

justify-content:space-between;

gap:16px;

flex-wrap:wrap;

margin-bottom:6px;

}

.notif-count{

color:var(--muted);

font-weight:700;

font-size:14px;

}

.notif-count strong{color:var(--burgundy);}

.mark-all-btn{

display:inline-flex;

align-items:center;

gap:10px;

padding:12px 22px;

border-radius:30px;

background:linear-gradient(120deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

font-weight:700;

font-size:14px;

text-decoration:none;

box-shadow:var(--shadow-soft);

transition:.35s var(--ease);

border:1px solid transparent;

}

.mark-all-btn:hover{

transform:translateY(-3px);

box-shadow:var(--shadow-lift);

background:linear-gradient(120deg,var(--gold),var(--gold-soft));

color:var(--burgundy-darker);

}

.notif-grid{

display:grid;

grid-template-columns:repeat(auto-fill,minmax(320px,1fr));

gap:18px;

margin-top:24px;

}

.notif-card{

position:relative;

background:#fff;

border:1px solid rgba(212,175,55,.14);

border-radius:var(--radius-md);

padding:22px;

box-shadow:var(--shadow-soft);

transition:.4s var(--ease);

opacity:0;

transform:translateY(16px);

animation:cardIn .55s var(--ease) forwards;

overflow:hidden;

}

.notif-card::before{

content:"";

position:absolute;

top:0;right:0;

width:5px;

height:100%;

background:linear-gradient(var(--gold),var(--burgundy));

opacity:0;

transition:opacity .3s var(--ease);

}

.notif-card.unread::before{opacity:1;}

.notif-card:hover{

transform:translateY(-6px);

box-shadow:var(--shadow-lift);

border-color:rgba(212,175,55,.4);

}

.notif-card.unread{

background:linear-gradient(180deg,var(--gold-mist),#fff 55%);

}

.notif-head{

display:flex;

align-items:flex-start;

justify-content:space-between;

gap:12px;

margin-bottom:10px;

}

.notif-title{

font-size:16.5px;

font-weight:800;

color:var(--ink);

display:flex;

align-items:center;

gap:10px;

}

.notif-dot{

width:9px;

height:9px;

border-radius:50%;

background:var(--gold);

box-shadow:0 0 0 4px rgba(212,175,55,.18);

flex:none;

}

.notif-msg{

color:var(--muted);

font-size:14px;

line-height:1.9;

font-weight:600;

margin-bottom:16px;

}

.notif-foot{

display:flex;

align-items:center;

justify-content:space-between;

gap:10px;

flex-wrap:wrap;

}

.notif-date{

font-size:12.5px;

color:var(--muted);

font-weight:700;

display:flex;

align-items:center;

gap:6px;

}

.notif-status{

padding:5px 14px;

border-radius:30px;

font-size:12px;

font-weight:800;

}

.notif-status.is-unread{background:rgba(212,175,55,.16);color:#9c7a17;}
.notif-status.is-read{background:rgba(34,150,90,.12);color:#22965A;}

.notif-read-link{

font-size:12.5px;

font-weight:800;

color:var(--burgundy);

text-decoration:none;

padding:7px 16px;

border-radius:30px;

border:1px solid rgba(91,16,40,.2);

transition:.3s var(--ease);

}

.notif-read-link:hover{

background:var(--burgundy);

color:#fff;

border-color:var(--burgundy);

}

.notif-empty{

text-align:center;

padding:70px 20px;

color:var(--muted);

}

.notif-empty i{

font-size:46px;

color:var(--gold);

margin-bottom:18px;

display:block;

}

.notif-empty h3{

color:var(--burgundy);

font-size:20px;

margin-bottom:8px;

}

</style>

</head>

<body>

<button class="menu-toggle" id="menuToggle" aria-label="فتح القائمة">

<i class="fa-solid fa-bars"></i>

</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">

<div class="weave-texture"></div>

<div class="sidebar-inner">

<div class="logo">

<div class="logo-badge">

<img src="../assets/images/logo.png">

</div>

<h2>توب سودان</h2>

<span class="tag">Luxury Sudanese Fashion</span>

</div>

<ul class="menu">

<li><a href="dashboard.php"><span class="menu-icon"><i class="fa-solid fa-house"></i></span><span>الرئيسية</span></a></li>

<li><a href="products.php"><span class="menu-icon"><i class="fa-solid fa-shirt"></i></span><span>المنتجات</span></a></li>

<li><a href="categories.php"><span class="menu-icon"><i class="fa-solid fa-layer-group"></i></span><span>التصنيفات</span></a></li>

<li><a href="orders.php"><span class="menu-icon"><i class="fa-solid fa-cart-shopping"></i></span><span>الطلبات</span></a></li>

<li><a href="users.php"><span class="menu-icon"><i class="fa-solid fa-users"></i></span><span>العملاء</span></a></li>

<li><a href="reviews.php"><span class="menu-icon"><i class="fa-solid fa-star"></i></span><span>التقييمات</span></a></li>

<li><a href="notifications.php"><span class="menu-icon"><i class="fa-solid fa-bell"></i></span><span>الإشعارات</span></a></li>

<li><a href="messages.php"><span class="menu-icon"><i class="fa-solid fa-envelope"></i></span><span>الرسائل</span></a></li>

<li><a href="settings.php"><span class="menu-icon"><i class="fa-solid fa-gear"></i></span><span>الإعدادات</span></a></li>

<li><a href="../logout.php"><span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span><span>تسجيل الخروج</span></a></li>

</ul>

<div class="sidebar-foot">TOOB SUDAN ADMIN</div>

</div>

</div>

<div class="content">

<div class="topbar">

<div class="topbar-title">

<span class="eyebrow">مركز الإشعارات</span>

<h1>

الإشعارات

</h1>

</div>

<div class="topbar-actions">

<a href="notifications.php" class="topbar-icon" title="الإشعارات">

<i class="fa-solid fa-bell"></i>

<?php if($unread_notifications > 0){ ?>

<span class="badge-dot"><?php echo $unread_notifications; ?></span>

<?php } ?>

</a>

<a href="messages.php" class="topbar-icon" title="الرسائل">

<i class="fa-solid fa-comment-dots"></i>

<?php if($new_messages_count > 0){ ?>

<span class="badge-dot"><?php echo $new_messages_count; ?></span>

<?php } ?>

</a>

<a href="settings.php" class="topbar-icon" title="الإعدادات">

<i class="fa-solid fa-gear"></i>

</a>

</div>

<div class="admin">

<div class="avatar-ring">

<img src="../assets/images/admin.png">

</div>

<div>

<h3>

<?php echo $_SESSION['admin_name']; ?>

</h3>

<span>

مدير النظام

</span>

</div>

</div>

</div>

<div class="panel">

<div class="notif-toolbar">

<h2>كل الإشعارات</h2>

<div class="notif-count">

عدد غير المقروء: <strong><?php echo $unread_notifications; ?></strong>

</div>

<?php if($unread_notifications > 0){ ?>

<a href="notifications.php?mark_all_read=1" class="mark-all-btn">

<i class="fa-solid fa-check-double"></i>

تعليم الكل كمقروء

</a>

<?php } ?>

</div>

<?php if(mysqli_num_rows($all_notifications) === 0){ ?>

<div class="notif-empty">

<i class="fa-solid fa-bell-slash"></i>

<h3>لا توجد إشعارات حالياً</h3>

<p>ستظهر هنا كل التنبيهات الجديدة الخاصة بالمتجر.</p>

</div>

<?php } else { ?>

<div class="notif-grid">

<?php while($n = mysqli_fetch_assoc($all_notifications)){

$is_unread = ((int)$n['is_read'] === 0);

?>

<div class="notif-card<?php echo $is_unread ? ' unread' : ''; ?>">

<div class="notif-head">

<div class="notif-title">

<?php if($is_unread){ ?><span class="notif-dot"></span><?php } ?>

<?php echo $n['title']; ?>

</div>

</div>

<div class="notif-msg">

<?php echo $n['message']; ?>

</div>

<div class="notif-foot">

<span class="notif-date">

<i class="fa-regular fa-clock"></i>

<?php echo $n['created_at']; ?>

</span>

<?php if($is_unread){ ?>

<span class="notif-status is-unread">غير مقروء</span>

<a href="notifications.php?mark_read=<?php echo $n['id']; ?>" class="notif-read-link">

تعليم كمقروء

</a>

<?php } else { ?>

<span class="notif-status is-read">مقروء</span>

<?php } ?>

</div>

</div>

<?php } ?>

</div>

<?php } ?>

</div>

<div class="footer">

© <?php echo date("Y"); ?>

<strong>Toob Sudan</strong> Dashboard

</div>

</div>

<script>

/* Mobile off-canvas sidebar */

const sidebar=document.getElementById('sidebar');

const menuToggle=document.getElementById('menuToggle');

const overlay=document.getElementById('sidebarOverlay');

function openSidebar(){

sidebar.classList.add('open');

overlay.classList.add('show');

document.body.style.overflow='hidden';

}

function closeSidebar(){

sidebar.classList.remove('open');

overlay.classList.remove('show');

document.body.style.overflow='';

}

menuToggle?.addEventListener('click',()=>{

sidebar.classList.contains('open') ? closeSidebar() : openSidebar();

});

overlay?.addEventListener('click',closeSidebar);

/* Active menu link based on current file */

const currentPage=location.pathname.split('/').pop() || 'dashboard.php';

document.querySelectorAll('.menu a').forEach(a=>{

const href=a.getAttribute('href').split('/').pop();

if(href===currentPage){a.classList.add('active');}

a.addEventListener('click',closeSidebar);

});

/* Card hover lift (kept from original behaviour, refined) */

const cards=document.querySelectorAll('.card');

cards.forEach(card=>{

card.addEventListener('mouseenter',()=>{card.style.transform='translateY(-8px)';});

card.addEventListener('mouseleave',()=>{card.style.transform='translateY(0)';});

});

</script>

</body>

</html>
