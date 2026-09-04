<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

$orders = mysqli_query($conn,"
SELECT *
FROM orders
ORDER BY id DESC
");

$totalOrders = mysqli_num_rows($orders);

$totalSales = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(total) AS total
FROM orders
"));

$newOrders = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM orders
WHERE status='جديد'
"));

$processingOrders = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM orders
WHERE status='قيد التجهيز'
"));

$shippingOrders = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM orders
WHERE status='تم الشحن'
"));

$completedOrders = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM orders
WHERE status='مكتمل'
"));

mysqli_data_seek($orders,0);

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>إدارة الطلبات</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*========================================================
  TOOB SUDAN — ORDERS · PREMIUM ADMIN UI
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
  --green:#22965A;
  --orange:#C9861A;
  --red:#C83232;
  --blue:#2E7BC4;
  --purple:#6f42c1;

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
    radial-gradient(1000px 520px at 100% -8%, rgba(212,175,55,.10), transparent 60%),
    radial-gradient(900px 500px at -5% 105%, rgba(91,16,40,.06), transparent 55%),
    var(--bg);

  min-height:100vh;
  color:var(--ink);
  position:relative;

}

::-webkit-scrollbar{width:9px;height:9px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--gold),var(--burgundy));border-radius:20px;}

/*======================
Admin shell
=======================*/

.admin-shell{

  display:flex;
  min-height:100vh;
  position:relative;

}

/* ---------- Sidebar ---------- */

.sidebar{

  width:264px;
  flex-shrink:0;
  position:fixed;
  top:0;
  right:0;
  height:100vh;
  background:linear-gradient(180deg,var(--burgundy-darker) 0%,var(--burgundy-deep) 55%,var(--burgundy) 100%);
  color:#fff;
  z-index:100;
  display:flex;
  flex-direction:column;
  box-shadow:-2px 0 30px rgba(0,0,0,.18);
  transition:width .35s var(--ease), transform .35s var(--ease);
  overflow-y:auto;
  overflow-x:hidden;

}

.sidebar::-webkit-scrollbar{width:6px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(212,175,55,.4);border-radius:20px;}

.sidebar-brand{

  display:flex;
  align-items:center;
  gap:14px;
  padding:26px 22px;
  border-bottom:1px solid rgba(255,255,255,.08);
  flex-shrink:0;

}

.sidebar-brand .brand-mark{

  width:44px;
  height:44px;
  border-radius:13px;
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  display:flex;
  align-items:center;
  justify-content:center;
  color:var(--burgundy-darker);
  font-size:19px;
  flex-shrink:0;
  box-shadow:var(--shadow-gold);

}

.sidebar-brand .brand-text{

  font-weight:800;
  font-size:16.5px;
  white-space:nowrap;
  overflow:hidden;

}

.sidebar-brand .brand-text span{

  display:block;
  font-size:11px;
  font-weight:600;
  color:var(--gold-soft);
  margin-top:2px;

}

.sidebar-nav{

  list-style:none;
  padding:18px 14px;
  flex:1;
  display:flex;
  flex-direction:column;
  gap:4px;

}

.sidebar-nav li a{

  display:flex;
  align-items:center;
  gap:14px;
  padding:13px 16px;
  border-radius:13px;
  color:rgba(255,255,255,.72);
  text-decoration:none;
  font-weight:700;
  font-size:14.5px;
  transition:.3s var(--ease);
  position:relative;
  white-space:nowrap;

}

.sidebar-nav li a i{

  width:20px;
  text-align:center;
  font-size:16px;
  flex-shrink:0;

}

.sidebar-nav li a:hover{

  background:rgba(255,255,255,.08);
  color:#fff;

}

.sidebar-nav li a.active{

  background:linear-gradient(120deg,var(--gold),var(--gold-soft));
  color:var(--burgundy-darker);
  box-shadow:var(--shadow-gold);

}

.sidebar-nav li a.active i{color:var(--burgundy-darker);}

.sidebar-nav li.logout-item{

  margin-top:10px;
  padding-top:10px;
  border-top:1px solid rgba(255,255,255,.08);

}

.sidebar-nav li.logout-item a{color:#f2a7a7;}
.sidebar-nav li.logout-item a:hover{background:rgba(200,50,50,.18);color:#fff;}

.sidebar-close-btn{

  display:none;
  position:absolute;
  top:18px;
  left:18px;
  width:36px;
  height:36px;
  border-radius:10px;
  background:rgba(255,255,255,.08);
  color:#fff;
  border:none;
  align-items:center;
  justify-content:center;
  font-size:15px;
  cursor:pointer;

}

/* ---------- Overlay (mobile drawer) ---------- */

.sidebar-overlay{

  display:none;
  position:fixed;
  inset:0;
  background:rgba(20,5,10,.55);
  backdrop-filter:blur(2px);
  z-index:90;
  opacity:0;
  pointer-events:none;
  transition:opacity .3s var(--ease);

}

.sidebar-overlay.show{

  display:block;
  opacity:1;
  pointer-events:auto;

}

/* ---------- Main wrap ---------- */

.main-wrap{

  flex:1;
  margin-right:264px;
  min-width:0;
  transition:margin-right .35s var(--ease);

}

.topbar{

  position:sticky;
  top:0;
  z-index:80;
  background:rgba(255,255,255,.85);
  backdrop-filter:blur(14px);
  border-bottom:1px solid rgba(212,175,55,.18);
  padding:16px 28px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;

}

.topbar-left{

  display:flex;
  align-items:center;
  gap:16px;
  min-width:0;

}

.hamburger-btn{

  display:none;
  width:42px;
  height:42px;
  border-radius:12px;
  border:1.6px solid var(--line);
  background:#fff;
  color:var(--burgundy);
  align-items:center;
  justify-content:center;
  font-size:17px;
  cursor:pointer;
  flex-shrink:0;
  transition:.3s var(--ease);

}

.hamburger-btn:hover{background:var(--burgundy);color:#fff;border-color:var(--burgundy);}

.topbar-titles .hi{

  font-weight:800;
  font-size:15.5px;
  color:var(--ink);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;

}

.topbar-titles .hi span{color:var(--burgundy);}

.topbar-titles .role{

  font-size:12px;
  font-weight:600;
  color:var(--muted);

}

.topbar-right{

  display:flex;
  align-items:center;
  gap:10px;
  flex-shrink:0;

}

.icon-btn{

  position:relative;
  width:42px;
  height:42px;
  border-radius:12px;
  background:#fff;
  border:1.6px solid var(--line);
  color:var(--burgundy);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:15.5px;
  cursor:pointer;
  text-decoration:none;
  transition:.3s var(--ease);

}

.icon-btn:hover{

  background:var(--burgundy);
  color:#fff;
  border-color:var(--burgundy);
  transform:translateY(-2px);

}

.icon-btn .dot{

  position:absolute;
  top:6px;
  left:6px;
  width:8px;
  height:8px;
  border-radius:50%;
  background:var(--gold);
  border:2px solid #fff;

}

.admin-avatar{

  width:42px;
  height:42px;
  border-radius:12px;
  background:linear-gradient(135deg,var(--burgundy),var(--burgundy-deep));
  color:var(--gold-soft);
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:800;
  font-size:15px;
  flex-shrink:0;
  box-shadow:0 8px 18px rgba(91,16,40,.28);

}

.main-content{

  min-height:calc(100vh - 74px);
  position:relative;
  overflow-x:hidden;

}

.page-wrap{

  max-width:1400px;
  padding:26px 26px 50px;
  position:relative;
  z-index:1;

}

/*======================
Page header
=======================*/

.page-header{

  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:18px;
  flex-wrap:wrap;
  margin-bottom:24px;
  opacity:0;
  animation:fadeSlide .6s var(--ease) forwards;

}

@keyframes fadeSlide{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}

.page-header h1{

  font-size:27px;
  font-weight:800;
  color:var(--burgundy);
  display:flex;
  align-items:center;
  gap:12px;
  margin-bottom:8px;

}

.page-header .sub{

  color:var(--muted);
  font-weight:600;
  font-size:13.5px;

}

.header-actions{

  display:flex;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;

}

.ghost-btn{

  display:inline-flex;
  align-items:center;
  gap:8px;
  background:#fff;
  border:1.6px solid var(--line);
  color:var(--burgundy);
  padding:13px 20px;
  border-radius:14px;
  font-weight:800;
  font-size:13.5px;
  cursor:pointer;
  transition:.3s var(--ease);
  font-family:inherit;
  white-space:nowrap;

}

.ghost-btn:hover{

  background:var(--burgundy);
  color:#fff;
  border-color:var(--burgundy);
  transform:translateY(-3px);

}

/*======================
Stats cards
=======================*/

.stat-grid{

  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:18px;
  margin-bottom:22px;

}

.stat-card{

  position:relative;
  background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(255,255,255,.72));
  backdrop-filter:blur(10px);
  padding:22px;
  border-radius:var(--radius-md);
  box-shadow:var(--shadow-soft);
  border:1px solid rgba(212,175,55,.15);
  transition:.4s var(--ease);
  overflow:hidden;
  display:flex;
  align-items:center;
  gap:16px;
  opacity:0;
  transform:translateY(16px);
  animation:cardIn .55s var(--ease) forwards;

}

.stat-grid .stat-card:nth-child(1){animation-delay:.03s;}
.stat-grid .stat-card:nth-child(2){animation-delay:.09s;}
.stat-grid .stat-card:nth-child(3){animation-delay:.15s;}
.stat-grid .stat-card:nth-child(4){animation-delay:.21s;}
.stat-grid .stat-card:nth-child(5){animation-delay:.27s;}

@keyframes cardIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

.stat-card::after{

  content:"";
  position:absolute;
  top:0;left:0;right:0;
  height:4px;
  background:linear-gradient(90deg,var(--gold),var(--burgundy));
  transform:scaleX(0);
  transform-origin:right;
  transition:transform .45s var(--ease);

}

.stat-card:hover::after{transform:scaleX(1);}

.stat-card:hover{

  transform:translateY(-6px);
  box-shadow:var(--shadow-lift);
  border-color:rgba(212,175,55,.4);

}

.stat-card .icon{

  width:56px;
  height:56px;
  border-radius:15px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:22px;
  color:#fff;
  flex-shrink:0;
  box-shadow:0 10px 20px rgba(0,0,0,.15);

}

.stat-card .icon.blue{background:linear-gradient(145deg,var(--blue),#1c5a92);}
.stat-card .icon.green{background:linear-gradient(145deg,var(--green),#1c7a49);}
.stat-card .icon.orange{background:linear-gradient(145deg,var(--orange),#a86a10);}
.stat-card .icon.purple{background:linear-gradient(145deg,var(--purple),#4c2c8a);}
.stat-card .icon.red{background:linear-gradient(145deg,var(--red),#a12727);}

.stat-card h2{

  font-size:24px;
  color:var(--ink);
  font-weight:800;
  margin-bottom:3px;
  white-space:nowrap;

}

.stat-card p{color:var(--muted);font-weight:700;font-size:12.5px;white-space:nowrap;}

/*======================
Toolbar
=======================*/

.panel{

  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(255,255,255,.9));
  backdrop-filter:blur(10px);
  padding:22px 24px;
  border-radius:var(--radius-lg);
  box-shadow:var(--shadow-soft);
  border:1px solid rgba(212,175,55,.14);
  margin-bottom:22px;
  opacity:0;
  transform:translateY(16px);
  animation:fadeSlide .55s var(--ease) forwards;
  animation-delay:.1s;

}

.toolbar-row{

  display:flex;
  flex-wrap:wrap;
  gap:14px;
  align-items:center;

}

.search-wrap{

  position:relative;
  flex:2;
  min-width:220px;

}

.search-wrap i{

  position:absolute;
  top:50%;right:18px;
  transform:translateY(-50%);
  color:var(--muted);
  font-size:15px;

}

.search-wrap input{

  width:100%;
  padding:15px 46px 15px 16px;
  border:1.6px solid var(--line);
  border-radius:14px;
  font-weight:600;
  font-size:14.5px;
  font-family:inherit;
  transition:.3s var(--ease);
  background:#fbfbfb;
  outline:none;

}

.search-wrap input:focus{

  border-color:var(--gold);
  background:#fff;
  box-shadow:0 0 0 4px rgba(212,175,55,.14);

}

.toolbar-row select{

  flex:1;
  min-width:180px;
  padding:15px 16px;
  border:1.6px solid var(--line);
  border-radius:14px;
  font-weight:600;
  font-size:14px;
  font-family:inherit;
  color:var(--ink);
  background:#fbfbfb;
  cursor:pointer;
  transition:.3s var(--ease);
  outline:none;

}

.toolbar-row select:focus{border-color:var(--gold);background:#fff;}

/*======================
Table
=======================*/

.table-box{

  background:linear-gradient(180deg,rgba(255,255,255,.97),rgba(255,255,255,.92));
  backdrop-filter:blur(10px);
  padding:24px;
  border-radius:var(--radius-lg);
  box-shadow:var(--shadow-soft);
  border:1px solid rgba(212,175,55,.14);
  overflow:auto;
  opacity:0;
  transform:translateY(16px);
  animation:fadeSlide .55s var(--ease) forwards;
  animation-delay:.16s;

}

table{width:100%;border-collapse:collapse;min-width:980px;}

thead th{

  position:sticky;
  top:0;
  z-index:2;
  background:linear-gradient(90deg,var(--burgundy),var(--burgundy-deep));
  color:#fff;
  padding:16px 14px;
  font-size:13.5px;
  font-weight:700;
  white-space:nowrap;

}

th:first-child{border-radius:12px 0 0 12px;}
th:last-child{border-radius:0 12px 12px 0;}

td{

  padding:16px 14px;
  text-align:center;
  border-bottom:1px solid var(--line);
  font-weight:600;
  font-size:14px;
  white-space:nowrap;

}

tbody tr{transition:.25s var(--ease);}

tbody tr:nth-child(even){background:rgba(247,239,217,.25);}

tbody tr:hover{background:var(--gold-mist);}

.id-cell{color:var(--muted);font-weight:700;}

.customer-name{font-weight:800;color:var(--ink);font-size:14.5px;}

.phone-cell{color:var(--muted);direction:ltr;}

.total-cell{color:var(--burgundy);font-weight:800;}

.status{

  padding:8px 16px;
  border-radius:30px;
  font-size:12.5px;
  font-weight:800;
  display:inline-block;
  white-space:nowrap;

}

.status-new{background:linear-gradient(120deg,#fff3cd,#ffe9a8);color:#856404;}
.status-processing{background:linear-gradient(120deg,#cfe2ff,#a9c9ff);color:#084298;}
.status-shipping{background:linear-gradient(120deg,#d1ecf1,#a7dee6);color:#0c5460;}
.status-completed{background:linear-gradient(120deg,#d4edda,#a9e0b5);color:#155724;}

.actions{display:flex;align-items:center;justify-content:center;gap:6px;}

.actions a,
.actions button{

  position:relative;
  width:40px;
  height:40px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:12px;
  text-decoration:none;
  color:#fff;
  font-size:14px;
  overflow:hidden;
  transition:.3s var(--ease);
  border:none;
  cursor:pointer;
  font-family:inherit;

}

.actions a:hover,
.actions button:hover{transform:translateY(-4px);box-shadow:0 10px 18px rgba(0,0,0,.18);}

.actions a::after,
.actions button::after{

  content:attr(data-tip);
  position:absolute;
  bottom:120%;
  left:50%;
  transform:translateX(-50%) translateY(6px);
  background:var(--ink);
  color:#fff;
  font-size:11px;
  font-weight:700;
  padding:5px 10px;
  border-radius:8px;
  white-space:nowrap;
  opacity:0;
  pointer-events:none;
  transition:.25s var(--ease);

}

.actions a:hover::after,
.actions button:hover::after{opacity:1;transform:translateX(-50%) translateY(0);}

.view{background:linear-gradient(135deg,#2ecc71,#22965A);}
.edit{background:linear-gradient(135deg,var(--gold),var(--gold-soft));color:var(--burgundy-darker)!important;}
.print-btn{background:linear-gradient(135deg,#2E7BC4,#1c5a92);}
.delete{background:linear-gradient(135deg,#e74c3c,#a12727);}

.ripple-el{

  position:absolute;
  border-radius:50%;
  background:rgba(255,255,255,.55);
  transform:scale(0);
  animation:ripple .55s ease-out;
  pointer-events:none;

}

@keyframes ripple{to{transform:scale(16);opacity:0;}}

/*======================
Empty state
=======================*/

.empty-state{

  text-align:center;
  padding:70px 20px;

}

.empty-state i{

  font-size:56px;
  color:var(--gold);
  margin-bottom:20px;
  display:block;

}

.empty-state h3{color:var(--burgundy);font-size:21px;margin-bottom:8px;}

.empty-state p{color:var(--muted);font-weight:600;}

/*======================
Responsive
=======================*/

@media(max-width:1150px){

  .sidebar{width:84px;}

  .sidebar-brand{padding:22px 0;justify-content:center;}

  .sidebar-brand .brand-text{display:none;}

  .sidebar-nav{padding:14px 10px;align-items:center;}

  .sidebar-nav li a{justify-content:center;padding:13px;}

  .sidebar-nav li a span.link-text{display:none;}

  .main-wrap{margin-right:84px;}

  .stat-grid{grid-template-columns:repeat(3,1fr);}

}

@media(max-width:900px){

  .page-wrap{padding:20px 16px 40px;}

  .stat-grid{

    grid-template-columns:repeat(5,minmax(175px,1fr));
    grid-auto-flow:column;
    overflow-x:auto;
    overflow-y:hidden;
    scroll-snap-type:x mandatory;
    padding-bottom:8px;
    -ms-overflow-style:none;
    scrollbar-width:none;

  }

  .stat-grid::-webkit-scrollbar{display:none;}

  .stat-card{scroll-snap-align:start;min-width:180px;}

}

@media(max-width:760px){

  .sidebar{

    width:280px;
    transform:translateX(100%);
    box-shadow:none;

  }

  .sidebar.open{

    transform:translateX(0);
    box-shadow:-10px 0 40px rgba(0,0,0,.35);

  }

  .sidebar-brand{padding:22px 20px;justify-content:flex-start;}

  .sidebar-brand .brand-text{display:block;}

  .sidebar-nav{padding:16px 14px;align-items:stretch;}

  .sidebar-nav li a{justify-content:flex-start;padding:14px 16px;min-height:48px;}

  .sidebar-nav li a span.link-text{display:inline;}

  .sidebar-close-btn{display:flex;}

  .main-wrap{margin-right:0;}

  .topbar{padding:14px 16px;}

  .hamburger-btn{display:flex;}

  .topbar-titles .role{display:none;}

  .icon-btn,
  .admin-avatar{width:40px;height:40px;}

  .page-header{flex-direction:column;align-items:stretch;}

  .header-actions{width:100%;}

  .ghost-btn{flex:1;justify-content:center;}

  .toolbar-row{flex-direction:column;align-items:stretch;}

  .search-wrap{min-width:0;}

  .toolbar-row select{min-height:48px;}

  /* Table -> stacked premium cards */

  .table-box{padding:14px;overflow:visible;}

  table{min-width:0;}

  thead{display:none;}

  tbody tr{

    display:block;
    background:#fff;
    border:1px solid var(--line);
    border-radius:18px;
    margin-bottom:16px;
    padding:16px;
    box-shadow:var(--shadow-soft);

  }

  tbody tr:hover,
  tbody tr:nth-child(even){background:#fff;}

  td{

    display:flex;
    align-items:center;
    justify-content:space-between;
    text-align:right;
    border-bottom:1px dashed var(--line);
    padding:11px 4px;
    white-space:normal;
    min-height:44px;
    gap:10px;

  }

  td:last-child{border-bottom:none;}

  td::before{

    content:attr(data-label);
    font-weight:800;
    color:var(--burgundy);
    font-size:12.5px;
    flex-shrink:0;

  }

  td.actions{justify-content:flex-start;}

  .actions a,
  .actions button{width:44px;height:44px;}

}

@media(max-width:420px){

  .topbar-titles .hi{font-size:14px;max-width:150px;}

  .topbar-right{gap:7px;}

}

/* Accessibility */

a:focus-visible,
button:focus-visible,
input:focus-visible,
select:focus-visible{
  outline:3px solid var(--gold);
  outline-offset:2px;
}

</style>

</head>

<body>

<div class="admin-shell">

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="sidebar" id="adminSidebar">

    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="إغلاق القائمة">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="sidebar-brand">
      <div class="brand-mark"><i class="fa-solid fa-crown"></i></div>
      <div class="brand-text">توب سودان<span>لوحة التحكم</span></div>
    </div>

    <ul class="sidebar-nav">

      <li><a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i><span class="link-text">الرئيسية</span></a></li>

      <li><a href="products.php"><i class="fa-solid fa-shirt"></i><span class="link-text">المنتجات</span></a></li>

      <li><a href="categories.php"><i class="fa-solid fa-folder-tree"></i><span class="link-text">التصنيفات</span></a></li>

      <li><a href="orders.php" class="active"><i class="fa-solid fa-box"></i><span class="link-text">الطلبات</span></a></li>

      <li><a href="customers.php"><i class="fa-solid fa-users"></i><span class="link-text">العملاء</span></a></li>

      <li><a href="reviews.php"><i class="fa-solid fa-star"></i><span class="link-text">التقييمات</span></a></li>

      <li><a href="messages.php"><i class="fa-solid fa-comment-dots"></i><span class="link-text">الرسائل</span></a></li>

      <li><a href="notifications.php"><i class="fa-solid fa-bell"></i><span class="link-text">الإشعارات</span></a></li>

      <li><a href="settings.php"><i class="fa-solid fa-gear"></i><span class="link-text">الإعدادات</span></a></li>

      <li class="logout-item"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span class="link-text">تسجيل الخروج</span></a></li>

    </ul>

  </aside>

  <div class="main-wrap">

    <header class="topbar">

      <div class="topbar-left">

        <button type="button" class="hamburger-btn" id="hamburgerBtn" aria-label="فتح القائمة">
          <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-titles">
          <div class="hi">مرحباً، <span>Admin</span> 👋</div>
          <div class="role">مدير المتجر</div>
        </div>

      </div>

      <div class="topbar-right">

        <button type="button" class="icon-btn" title="بحث" id="topSearchBtn">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <a href="notifications.php" class="icon-btn" title="الإشعارات">
          <i class="fa-solid fa-bell"></i>
          <span class="dot"></span>
        </a>

        <a href="messages.php" class="icon-btn" title="الرسائل">
          <i class="fa-solid fa-comment-dots"></i>
        </a>

        <a href="settings.php" class="icon-btn" title="الإعدادات">
          <i class="fa-solid fa-gear"></i>
        </a>

        <div class="admin-avatar">A</div>

      </div>

    </header>

    <main class="main-content">

<div class="page-wrap">

  <div class="page-header">

    <div>
      <h1><i class="fa-solid fa-box"></i> إدارة الطلبات</h1>
      <p class="sub">إدارة جميع طلبات العملاء ومتابعة حالتها بسهولة.</p>
    </div>

    <div class="header-actions">

      <button type="button" class="ghost-btn" id="refreshBtn">
        <i class="fa-solid fa-rotate-right"></i> تحديث
      </button>

      <button type="button" class="ghost-btn" id="exportBtn">
        <i class="fa-solid fa-file-export"></i> تصدير
      </button>

    </div>

  </div>

  <div class="stat-grid">

    <div class="stat-card">

      <i class="fa-solid fa-box icon blue"></i>

      <div>

        <h2><?php echo $totalOrders; ?></h2>

        <p>إجمالي الطلبات</p>

      </div>

    </div>

    <div class="stat-card">

      <i class="fa-solid fa-money-bill-wave icon green"></i>

      <div>

        <h2>

          <?php echo number_format($totalSales['total'] ?? 0,2); ?>

        </h2>

        <p>إجمالي المبيعات</p>

      </div>

    </div>

    <div class="stat-card">

      <i class="fa-solid fa-clock icon orange"></i>

      <div>

        <h2><?php echo $newOrders['total']; ?></h2>

        <p>طلبات جديدة</p>

      </div>

    </div>

    <div class="stat-card">

      <i class="fa-solid fa-gears icon purple"></i>

      <div>

        <h2><?php echo $processingOrders['total']; ?></h2>

        <p>قيد التجهيز</p>

      </div>

    </div>

    <div class="stat-card">

      <i class="fa-solid fa-truck icon red"></i>

      <div>

        <h2>

          <?php

          echo $shippingOrders['total'] + $completedOrders['total'];

          ?>

        </h2>

        <p>تم الشحن / مكتملة</p>

      </div>

    </div>

  </div>

  <div class="panel">

    <div class="toolbar-row">

      <div class="search-wrap">

        <input

        type="text"

        id="search"

        placeholder="ابحث باسم العميل أو الهاتف...">

        <i class="fa-solid fa-magnifying-glass"></i>

      </div>

      <select id="statusFilter">

        <option value="">كل الحالات</option>

        <option value="جديد">جديد</option>

        <option value="قيد التجهيز">قيد التجهيز</option>

        <option value="تم الشحن">تم الشحن</option>

        <option value="مكتمل">مكتمل</option>

      </select>

    </div>

  </div>

  <div class="table-box">

    <?php if($totalOrders == 0){ ?>

    <div class="empty-state">
      <i class="fa-solid fa-box-open"></i>
      <h3>لا توجد طلبات حالياً</h3>
      <p>ستظهر هنا طلبات العملاء بمجرد وصولها.</p>
    </div>

    <?php }else{ ?>

    <table>

      <thead>

        <tr>

          <th>#</th>

          <th>العميل</th>

          <th>الهاتف</th>

          <th>الدفع</th>

          <th>الإجمالي</th>

          <th>الحالة</th>

          <th>التاريخ</th>

          <th>الإجراءات</th>

        </tr>

      </thead>

      <tbody id="ordersTable">
          <?php while($row = mysqli_fetch_assoc($orders)){ ?>

      <tr>

      <td class="id-cell" data-label="#"><?php echo $row['id']; ?></td>

      <td class="customer-name" data-label="العميل"><?php echo htmlspecialchars($row['name']); ?></td>

      <td class="phone-cell" data-label="الهاتف"><?php echo htmlspecialchars($row['phone']); ?></td>

      <td data-label="الدفع"><?php echo htmlspecialchars($row['payment_method']); ?></td>

      <td class="total-cell" data-label="الإجمالي">

      <?php echo number_format($row['total'],2); ?> ج.س

      </td>

      <td data-label="الحالة">

      <?php

      $class="status-new";

      if($row['status']=="قيد التجهيز"){

      $class="status-processing";

      }elseif($row['status']=="تم الشحن"){

      $class="status-shipping";

      }elseif($row['status']=="مكتمل"){

      $class="status-completed";

      }

      ?>

      <span class="status <?php echo $class; ?>">

      <?php echo $row['status']; ?>

      </span>

      </td>

      <td data-label="التاريخ">

      <?php echo date("Y-m-d",strtotime($row['created_at'])); ?>

      </td>

      <td class="actions" data-label="الإجراءات">

      <a

      href="view_order.php?id=<?php echo $row['id']; ?>"

      class="view"

      data-tip="عرض">

      <i class="fa-solid fa-eye"></i>

      </a>

      <a

      href="edit_order.php?id=<?php echo $row['id']; ?>"

      class="edit"

      data-tip="تعديل">

      <i class="fa-solid fa-pen"></i>

      </a>

      <button

      type="button"

      class="print-btn"

      data-tip="طباعة"

      onclick="printOrderRow(this)">

      <i class="fa-solid fa-print"></i>

      </button>

      <a

      href="delete_order.php?id=<?php echo $row['id']; ?>"

      class="delete"

      data-tip="حذف"

      onclick="return confirm('هل تريد حذف الطلب؟')">

      <i class="fa-solid fa-trash"></i>

      </a>

      </td>

      </tr>

      <?php } ?>

      </tbody>

    </table>

    <?php } ?>

  </div>

</div>

    </main>

  </div>

</div>

<script>

/* ---------- Sidebar drawer toggle (mobile) — additive UI only ---------- */

(function(){

  const sidebar = document.getElementById("adminSidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const closeBtn = document.getElementById("sidebarCloseBtn");

  function openSidebar(){
    sidebar.classList.add("open");
    overlay.classList.add("show");
  }

  function closeSidebar(){
    sidebar.classList.remove("open");
    overlay.classList.remove("show");
  }

  if(hamburgerBtn) hamburgerBtn.addEventListener("click", openSidebar);
  if(closeBtn) closeBtn.addEventListener("click", closeSidebar);
  if(overlay) overlay.addEventListener("click", closeSidebar);

  window.addEventListener("resize", function(){
    if(window.innerWidth > 760){
      closeSidebar();
    }
  });

})();

/* ---------- Topbar search icon focuses the existing search box ---------- */

const topSearchBtn = document.getElementById("topSearchBtn");

if(topSearchBtn){

  topSearchBtn.addEventListener("click", function(){

    const searchBox = document.getElementById("search");

    if(searchBox){
      searchBox.scrollIntoView({behavior:"smooth", block:"center"});
      searchBox.focus();
    }

  });

}

/* ---------- Refresh button (frontend only) ---------- */

const refreshBtn = document.getElementById("refreshBtn");

if(refreshBtn){

  refreshBtn.addEventListener("click", function(){
    window.location.reload();
  });

}

/* ---------- Export button — client-side CSV export of visible rows (frontend only) ---------- */

const exportBtn = document.getElementById("exportBtn");

if(exportBtn){

  exportBtn.addEventListener("click", function(){

    const table = document.getElementById("ordersTable");

    if(!table) return;

    const rows = table.querySelectorAll("tr");

    let csv = "رقم,العميل,الهاتف,الدفع,الإجمالي,الحالة,التاريخ\n";

    rows.forEach(row=>{

      if(row.style.display === "none") return;

      const cells = row.querySelectorAll("td");

      if(cells.length < 7) return;

      const values = [
        cells[0].textContent.trim(),
        cells[1].textContent.trim(),
        cells[2].textContent.trim(),
        cells[3].textContent.trim(),
        cells[4].textContent.trim(),
        cells[5].textContent.trim(),
        cells[6].textContent.trim()
      ];

      csv += values.map(v=>`"${v.replace(/"/g,'""')}"`).join(",") + "\n";

    });

    const blob = new Blob(["\uFEFF" + csv], {type:"text/csv;charset=utf-8;"});
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "orders.csv";
    link.click();

  });

}

/* ---------- Print single order row (frontend only, opens a print-friendly window) ---------- */

function printOrderRow(btn){

  const row = btn.closest("tr");

  if(!row) return;

  const cells = row.querySelectorAll("td");

  const id = cells[0].textContent.trim();
  const name = cells[1].textContent.trim();
  const phone = cells[2].textContent.trim();
  const payment = cells[3].textContent.trim();
  const total = cells[4].textContent.trim();
  const status = cells[5].textContent.trim();
  const date = cells[6].textContent.trim();

  const printWin = window.open("", "_blank", "width=480,height=640");

  printWin.document.write(`
    <html lang="ar" dir="rtl">
    <head>
      <meta charset="UTF-8">
      <title>طلب رقم ${id}</title>
      <style>
        body{font-family:Arial,Tahoma,sans-serif;padding:30px;color:#111;}
        h2{color:#5B1028;margin-bottom:18px;}
        table{width:100%;border-collapse:collapse;}
        td{padding:10px;border-bottom:1px solid #eee;font-size:14px;}
        td:first-child{font-weight:bold;color:#5B1028;width:35%;}
      </style>
    </head>
    <body>
      <h2>فاتورة / طلب رقم #${id}</h2>
      <table>
        <tr><td>العميل</td><td>${name}</td></tr>
        <tr><td>الهاتف</td><td>${phone}</td></tr>
        <tr><td>طريقة الدفع</td><td>${payment}</td></tr>
        <tr><td>الإجمالي</td><td>${total}</td></tr>
        <tr><td>الحالة</td><td>${status}</td></tr>
        <tr><td>التاريخ</td><td>${date}</td></tr>
      </table>
    </body>
    </html>
  `);

  printWin.document.close();
  printWin.focus();
  printWin.print();

}

/* ---------- Ripple feedback ---------- */

document.querySelectorAll('.ghost-btn, .actions a, .actions button').forEach(el=>{

  el.addEventListener('click', function(e){

    const r = document.createElement('span');
    r.className = 'ripple-el';
    r.style.left = (e.offsetX - 8) + 'px';
    r.style.top  = (e.offsetY - 8) + 'px';
    r.style.width = r.style.height = '16px';

    this.style.position = 'relative';
    this.style.overflow = 'hidden';
    this.appendChild(r);

    setTimeout(()=>r.remove(), 550);

  });

});

/* ---------- Existing search + filter logic (kept: same #search, #statusFilter, #ordersTable ids/behavior) ---------- */

const search=document.getElementById("search");

const filter=document.getElementById("statusFilter");

const rows=document.querySelectorAll("#ordersTable tr");

function filterTable(){

const text=search.value.toLowerCase();

const status=filter.value;

rows.forEach(row=>{

const content=row.innerText.toLowerCase();

const rowStatus=row.querySelector(".status").innerText.trim();

const okText=content.includes(text);

const okStatus=status==="" || rowStatus===status;

row.style.display=(okText && okStatus) ? "" : "none";

});

}

search.addEventListener("keyup",filterTable);

filter.addEventListener("change",filterTable);

</script>

</body>

</html>