<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

/* -----------------------------------------------------------------
   Read-only additive stats for the new dashboard cards.
   These are brand-new SELECT/SHOW COLUMNS queries only — the
   original $search / $sql / $result logic below is untouched.
   Optional columns (featured / is_new) are detected safely so the
   page never breaks if they don't exist in the current schema.
----------------------------------------------------------------- */

$total_products_count = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM products"));

$low_stock_count = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM products WHERE quantity>0 AND quantity<=5"));

$out_of_stock_count = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM products WHERE quantity<=0"));

$total_stock_row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) AS total FROM products"));
$total_stock_qty = $total_stock_row['total'] ?? 0;

$has_featured_col = mysqli_num_rows(mysqli_query($conn,"SHOW COLUMNS FROM products LIKE 'featured'")) > 0;
$featured_count = $has_featured_col ? mysqli_num_rows(mysqli_query($conn,"SELECT id FROM products WHERE featured=1")) : 0;

$has_isnew_col = mysqli_num_rows(mysqli_query($conn,"SHOW COLUMNS FROM products LIKE 'is_new'")) > 0;
$new_count = $has_isnew_col ? mysqli_num_rows(mysqli_query($conn,"SELECT id FROM products WHERE is_new=1")) : 0;

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>إدارة المنتجات | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*========================================================
  TOOB SUDAN — PRODUCTS · LUXURY ADMIN UI
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

width:86px;

height:86px;

margin:0 auto 14px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

background:radial-gradient(circle at 30% 30%,rgba(212,175,55,.35),rgba(212,175,55,0) 70%);

border:1px solid rgba(212,175,55,.5);

box-shadow:0 0 0 6px rgba(212,175,55,.06), 0 10px 25px rgba(0,0,0,.25);

}

.logo h2{

color:#fff;

font-size:23px;

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

.menu li{margin-bottom:8px;}

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
.menu a.active::before{transform:translateX(0);}

.menu a:hover,
.menu a.active{color:#fff;padding-right:20px;}

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

.menu a:hover .menu-icon{background:var(--gold);color:var(--burgundy-darker);}

.sidebar-foot{

padding-top:18px;

margin-top:10px;

border-top:1px solid rgba(212,175,55,.2);

text-align:center;

color:rgba(255,255,255,.4);

font-size:11px;

letter-spacing:1px;

}

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

.sidebar-overlay.show{display:block;opacity:1;}

/*======================
Content
=======================*/

.content{

width:calc(100% - 280px);

margin-right:280px;

padding:32px;

}

/*======================
Page header
=======================*/

.page-head{

background:rgba(255,255,255,.75);

backdrop-filter:blur(16px) saturate(160%);

-webkit-backdrop-filter:blur(16px) saturate(160%);

padding:26px 32px;

border-radius:var(--radius-lg);

display:flex;

justify-content:space-between;

align-items:center;

flex-wrap:wrap;

gap:18px;

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.18);

margin-bottom:28px;

}

.page-head .eyebrow{

display:block;

color:var(--gold);

font-size:12px;

font-weight:700;

letter-spacing:2px;

text-transform:uppercase;

margin-bottom:6px;

}

.page-head h1{

font-size:28px;

color:var(--burgundy);

font-weight:800;

margin-bottom:6px;

}

.page-head p{

color:var(--muted);

font-weight:600;

font-size:14.5px;

}

.add{

display:inline-flex;

align-items:center;

gap:10px;

background:linear-gradient(120deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

padding:16px 28px;

border-radius:16px;

text-decoration:none;

font-weight:800;

font-size:15px;

box-shadow:0 12px 26px rgba(91,16,40,.3);

transition:.35s var(--ease);

border:1px solid transparent;

}

.add i{font-size:15px;}

.add:hover{

transform:translateY(-4px);

background:linear-gradient(120deg,var(--gold),var(--gold-soft));

color:var(--burgundy-darker);

box-shadow:var(--shadow-gold);

}

/*======================
Stats cards
=======================*/

.stat-grid{

display:grid;

grid-template-columns:repeat(5,1fr);

gap:18px;

margin-bottom:28px;

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

width:50px;

height:50px;

background:linear-gradient(145deg,var(--burgundy),var(--burgundy-deep));

border-radius:14px;

display:flex;

align-items:center;

justify-content:center;

color:var(--gold-soft);

font-size:19px;

margin-bottom:16px;

box-shadow:0 10px 20px rgba(91,16,40,.28);

}

.stat-card h2{

font-size:27px;

color:var(--burgundy);

font-weight:800;

margin-bottom:4px;

}

.stat-card p{color:var(--muted);font-weight:700;font-size:13px;}

@media(max-width:1300px){.stat-grid{grid-template-columns:repeat(3,1fr);}}

/*======================
Toolbar
=======================*/

.panel{

background:#fff;

padding:24px;

border-radius:var(--radius-lg);

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.12);

margin-bottom:26px;

}

.tools-form{

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

.tools-form input[type="text"]{

width:100%;

padding:14px 44px 14px 16px;

border:1.5px solid var(--line);

border-radius:14px;

font-weight:600;

font-size:14.5px;

transition:.3s var(--ease);

background:#fbfbfb;

}

.tools-form input[type="text"]:focus{

outline:none;

border-color:var(--gold);

background:#fff;

box-shadow:0 0 0 4px rgba(212,175,55,.14);

}

.tools-form select{

flex:1;

min-width:160px;

padding:14px 16px;

border:1.5px solid var(--line);

border-radius:14px;

font-weight:600;

font-size:14px;

color:var(--ink);

background:#fbfbfb;

cursor:pointer;

transition:.3s var(--ease);

}

.tools-form select:focus{

outline:none;

border-color:var(--gold);

background:#fff;

}

.tools-form button{

padding:14px 30px;

background:linear-gradient(120deg,var(--gold),var(--gold-soft));

color:var(--burgundy-darker);

border:none;

border-radius:14px;

cursor:pointer;

font-weight:800;

font-size:14.5px;

display:inline-flex;

align-items:center;

gap:8px;

box-shadow:var(--shadow-gold);

transition:.35s var(--ease);

}

.tools-form button:hover{

transform:translateY(-3px);

background:linear-gradient(120deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

}

/*======================
Bulk action bar
=======================*/

.bulk-bar{

display:none;

align-items:center;

justify-content:space-between;

flex-wrap:wrap;

gap:14px;

background:linear-gradient(90deg,var(--gold-mist),#fff);

border:1px solid rgba(212,175,55,.35);

border-radius:16px;

padding:14px 20px;

margin-bottom:20px;

}

.bulk-bar.show{display:flex;}

.bulk-info{font-weight:800;color:var(--burgundy);font-size:14px;}

.bulk-actions-group{display:flex;gap:10px;flex-wrap:wrap;}

.bulk-btn{

border:none;

padding:10px 18px;

border-radius:30px;

font-weight:700;

font-size:13px;

cursor:pointer;

display:inline-flex;

align-items:center;

gap:8px;

transition:.3s var(--ease);

}

.bulk-btn.select-all{background:#fff;color:var(--burgundy);border:1px solid rgba(91,16,40,.2);}
.bulk-btn.activate{background:rgba(34,150,90,.12);color:var(--green);}
.bulk-btn.deactivate{background:rgba(201,134,26,.14);color:var(--orange);}
.bulk-btn.delete{background:rgba(200,50,50,.1);color:var(--red);}

.bulk-btn:hover{transform:translateY(-2px);filter:brightness(.96);}

/*======================
Table
=======================*/

.table-box{

background:#fff;

padding:24px;

border-radius:var(--radius-lg);

box-shadow:var(--shadow-soft);

border:1px solid rgba(212,175,55,.12);

overflow:auto;

}

table{width:100%;border-collapse:collapse;min-width:1100px;}

th{

background:linear-gradient(90deg,var(--burgundy),var(--burgundy-deep));

color:#fff;

padding:15px 12px;

font-size:13.5px;

font-weight:700;

white-space:nowrap;

}

th:first-child{border-radius:12px 0 0 12px;}
th:last-child{border-radius:0 12px 12px 0;}

td{

padding:16px 12px;

text-align:center;

border-bottom:1px solid var(--line);

font-weight:600;

font-size:14px;

white-space:nowrap;

}

tbody tr{transition:.25s var(--ease);}

tbody tr:hover{background:var(--gold-mist);}

.row-check,.head-check{

width:18px;

height:18px;

accent-color:var(--burgundy);

cursor:pointer;

}

.product-image-wrap{

width:64px;

height:64px;

margin:0 auto;

border-radius:14px;

padding:2px;

background:linear-gradient(135deg,var(--gold),var(--burgundy));

overflow:hidden;

}

.product-image{

width:100%;

height:100%;

object-fit:cover;

border-radius:12px;

border:2px solid #fff;

display:block;

transition:.4s var(--ease);

}

.product-image-wrap:hover .product-image{transform:scale(1.18);}

.prod-name{font-weight:800;color:var(--ink);}

.price-old{

display:block;

color:var(--muted);

text-decoration:line-through;

font-size:12.5px;

font-weight:600;

}

.status{

display:inline-block;

padding:7px 16px;

border-radius:30px;

font-size:12.5px;

font-weight:800;

color:#fff;

}

.green{background:linear-gradient(120deg,var(--green),#1c7a49);}
.orange{background:linear-gradient(120deg,var(--orange),#a86a10);}
.red{background:linear-gradient(120deg,var(--red),#a12727);}

.mini-badge{

display:inline-flex;

align-items:center;

gap:5px;

padding:5px 12px;

border-radius:30px;

font-size:11.5px;

font-weight:800;

white-space:nowrap;

}

.mini-badge.featured{background:linear-gradient(120deg,var(--gold-soft),var(--gold));color:var(--burgundy-darker);}
.mini-badge.new{background:rgba(46,123,196,.12);color:var(--blue);}
.mini-badge.none{color:var(--muted);font-size:12px;font-weight:600;}

.date-cell{color:var(--muted);font-size:13px;font-weight:600;}

.actions{display:flex;align-items:center;justify-content:center;gap:6px;}

.actions a{

position:relative;

width:38px;

height:38px;

display:inline-flex;

align-items:center;

justify-content:center;

border-radius:12px;

text-decoration:none;

color:#fff;

font-size:14px;

overflow:hidden;

transition:.3s var(--ease);

}

.actions a:hover{transform:translateY(-4px);box-shadow:0 10px 18px rgba(0,0,0,.18);}

.actions a::after{

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

.actions a:hover::after{opacity:1;transform:translateX(-50%) translateY(0);}

.view{background:linear-gradient(135deg,#2ecc71,#22965A);}
.edit{background:linear-gradient(135deg,#3498db,#2E7BC4);}
.stock{background:linear-gradient(135deg,var(--gold),var(--gold-soft));color:var(--burgundy-darker);}
.delete{background:linear-gradient(135deg,#e74c3c,#a12727);}

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

.empty-state p{color:var(--muted);font-weight:600;margin-bottom:22px;}

.empty-state .add{padding:14px 26px;}

/*======================
Responsive
=======================*/

@media(max-width:900px){

.sidebar{transform:translateX(105%);width:280px;}

.sidebar.open{transform:translateX(0);}

.menu-toggle{display:flex;}

.content{margin-right:0;width:100%;padding:90px 16px 30px;}

.stat-grid{

grid-template-columns:repeat(5,minmax(170px,1fr));

grid-auto-flow:column;

overflow-x:auto;

overflow-y:hidden;

scroll-snap-type:x mandatory;

padding-bottom:8px;

-ms-overflow-style:none;

scrollbar-width:none;

}

.stat-grid::-webkit-scrollbar{display:none;}

.stat-card{scroll-snap-align:start;min-width:175px;}

.page-head{padding:20px;}

}

@media(max-width:760px){

/* Table becomes stacked cards */

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

tbody tr:hover{background:#fff;}

td{

display:flex;

align-items:center;

justify-content:space-between;

text-align:right;

border-bottom:1px dashed var(--line);

padding:10px 4px;

white-space:normal;

}

td:last-child{border-bottom:none;}

td::before{

content:attr(data-label);

font-weight:800;

color:var(--burgundy);

font-size:12.5px;

}

td.actions{justify-content:flex-start;}

td.check-cell::before{content:"تحديد";}

.product-image-wrap{margin:0;}

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

<img src="../assets/images/logo.png" style="width:52px;filter:drop-shadow(0 2px 6px rgba(0,0,0,.35));">

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

<li><a href="../logout.php"><span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span><span>خروج</span></a></li>

</ul>

<div class="sidebar-foot">TOOB SUDAN ADMIN</div>

</div>

</div>

<div class="content">

<div class="page-head">

<div>

<span class="eyebrow">إدارة المخزون</span>

<h1>المنتجات</h1>

<p>إدارة جميع منتجات متجر توب سودان.</p>

</div>

<a class="add" href="add_product.php">

<i class="fa-solid fa-plus"></i>

إضافة منتج جديد

</a>

</div>

<div class="stat-grid">

<div class="stat-card">

<div class="icon"><i class="fa-solid fa-shirt"></i></div>

<h2><?php echo $total_products_count; ?></h2>

<p>عدد المنتجات</p>

</div>

<div class="stat-card">

<div class="icon"><i class="fa-solid fa-star"></i></div>

<h2><?php echo $featured_count; ?></h2>

<p>المنتجات المميزة</p>

</div>

<div class="stat-card">

<div class="icon"><i class="fa-solid fa-sparkles"></i></div>

<h2><?php echo $new_count; ?></h2>

<p>المنتجات الجديدة</p>

</div>

<div class="stat-card">

<div class="icon"><i class="fa-solid fa-triangle-exclamation"></i></div>

<h2><?php echo $low_stock_count; ?></h2>

<p>منتجات قليلة المخزون</p>

</div>

<div class="stat-card">

<div class="icon"><i class="fa-solid fa-boxes-stacked"></i></div>

<h2><?php echo number_format($total_stock_qty); ?></h2>

<p>إجمالي الكمية بالمخزون</p>

</div>

</div>

<div class="panel">

<form class="tools-form">

<div class="search-wrap">

<input
type="text"
name="search"
placeholder="ابحث باسم المنتج..."
value="<?php echo $search; ?>">

<i class="fa-solid fa-magnifying-glass"></i>

</div>

<select name="category">

<option value="">كل التصنيفات</option>

<?php

$cats=mysqli_query($conn,"SELECT * FROM categories");

while($cat=mysqli_fetch_assoc($cats)){

?>

<option value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category']==$cat['id']) ? 'selected' : ''; ?>>

<?php echo $cat['name']; ?>

</option>

<?php } ?>

</select>

<select name="gender">

<option value="">كل الأنواع</option>

<option value="men" <?php echo (($_GET['gender'] ?? '')=='men') ? 'selected' : ''; ?>>رجالي</option>

<option value="women" <?php echo (($_GET['gender'] ?? '')=='women') ? 'selected' : ''; ?>>نسائي</option>

<option value="kids" <?php echo (($_GET['gender'] ?? '')=='kids') ? 'selected' : ''; ?>>أطفال</option>

</select>

<select name="status">

<option value="">كل الحالات</option>

<option value="available" <?php echo (($_GET['status'] ?? '')=='available') ? 'selected' : ''; ?>>متوفر</option>

<option value="low" <?php echo (($_GET['status'] ?? '')=='low') ? 'selected' : ''; ?>>قليل</option>

<option value="out" <?php echo (($_GET['status'] ?? '')=='out') ? 'selected' : ''; ?>>نفد</option>

</select>

<select name="sort">

<option value="">ترتيب حسب</option>

<option value="newest" <?php echo (($_GET['sort'] ?? '')=='newest') ? 'selected' : ''; ?>>الأحدث</option>

<option value="oldest" <?php echo (($_GET['sort'] ?? '')=='oldest') ? 'selected' : ''; ?>>الأقدم</option>

<option value="price" <?php echo (($_GET['sort'] ?? '')=='price') ? 'selected' : ''; ?>>السعر</option>

<option value="quantity" <?php echo (($_GET['sort'] ?? '')=='quantity') ? 'selected' : ''; ?>>الكمية</option>

<option value="name" <?php echo (($_GET['sort'] ?? '')=='name') ? 'selected' : ''; ?>>الاسم</option>

</select>

<button type="submit"><i class="fa-solid fa-magnifying-glass"></i> بحث</button>

</form>

</div>

<div class="bulk-bar" id="bulkBar">

<div class="bulk-info"><span id="bulkCount">0</span> عنصر محدد</div>

<div class="bulk-actions-group">

<button type="button" class="bulk-btn select-all" id="selectAllBtn"><i class="fa-solid fa-check-double"></i> تحديد الكل</button>

<button type="button" class="bulk-btn activate" onclick="bulkPlaceholder()"><i class="fa-solid fa-toggle-on"></i> تفعيل المحدد</button>

<button type="button" class="bulk-btn deactivate" onclick="bulkPlaceholder()"><i class="fa-solid fa-toggle-off"></i> تعطيل المحدد</button>

<button type="button" class="bulk-btn delete" onclick="bulkPlaceholder()"><i class="fa-solid fa-trash"></i> حذف المحدد</button>

</div>

</div>

<div class="table-box">

<?php

$sql = "
SELECT
products.*,
categories.name AS category_name
FROM products
LEFT JOIN categories
ON products.category_id = categories.id
WHERE products.name LIKE '%$search%'
ORDER BY products.id DESC
";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) === 0){

?>

<div class="empty-state">

<i class="fa-solid fa-box-open"></i>

<h3>لا توجد منتجات حالياً</h3>

<p>ابدأ بإضافة أول منتج لمتجر توب سودان.</p>

<a class="add" href="add_product.php"><i class="fa-solid fa-plus"></i> إضافة أول منتج</a>

</div>

<?php } else { ?>

<table>

<tr>

<th><input type="checkbox" class="head-check" id="headCheck"></th>

<th>الصورة</th>

<th>الاسم</th>

<th>التصنيف</th>

<th>السعر</th>

<th>السعر القديم</th>

<th>الكمية</th>

<th>الحالة</th>

<th>المميز</th>

<th>الجديد</th>

<th>التاريخ</th>

<th>العمليات</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result)){

if($row['quantity'] > 5){

$status="متوفر";
$class="green";

}elseif($row['quantity'] > 0){

$status="قليل";
$class="orange";

}else{

$status="نفد";
$class="red";

}

?>

<tr>

<td class="check-cell" data-label="تحديد"><input type="checkbox" class="row-check"></td>

<td data-label="الصورة">

<div class="product-image-wrap">

<img
src="../uploads/products/<?php echo $row['image']; ?>"
class="product-image">

</div>

</td>

<td data-label="الاسم" class="prod-name">

<?php echo $row['name']; ?>

</td>

<td data-label="التصنيف">

<?php echo $row['category_name']; ?>

</td>

<td data-label="السعر">

<?php echo number_format($row['price']); ?> ج.س

</td>

<td data-label="السعر القديم">

<?php

if(array_key_exists('old_price',$row) && $row['old_price'] > 0){

echo '<span class="price-old">'.number_format($row['old_price']).' ج.س</span>';

}else{

echo '<span class="mini-badge none">—</span>';

}

?>

</td>

<td data-label="الكمية">

<?php echo $row['quantity']; ?>

</td>

<td data-label="الحالة">

<span class="status <?php echo $class; ?>">

<?php echo $status; ?>

</span>

</td>

<td data-label="المميز">

<?php

if(array_key_exists('featured',$row) && $row['featured'] == 1){

echo '<span class="mini-badge featured">⭐ مميز</span>';

}else{

echo '<span class="mini-badge none">—</span>';

}

?>

</td>

<td data-label="الجديد">

<?php

if(array_key_exists('is_new',$row) && $row['is_new'] == 1){

echo '<span class="mini-badge new">🆕 جديد</span>';

}else{

echo '<span class="mini-badge none">—</span>';

}

?>

</td>

<td data-label="التاريخ" class="date-cell">

<?php

echo array_key_exists('created_at',$row) ? $row['created_at'] : '—';

?>

</td>

<td class="actions" data-label="العمليات">

<a
href="view_product.php?id=<?php echo $row['id']; ?>"
class="view"
data-tip="عرض">

<i class="fa-solid fa-eye"></i>

</a>

<a
href="edit_product.php?id=<?php echo $row['id']; ?>"
class="edit"
data-tip="تعديل">

<i class="fa-solid fa-pen"></i>

</a>

<a
href="edit_product.php?id=<?php echo $row['id']; ?>"
class="stock"
data-tip="تحديث المخزون">

<i class="fa-solid fa-box"></i>

</a>

<a
href="delete_product.php?id=<?php echo $row['id']; ?>"
class="delete"
data-tip="حذف"
onclick="return confirm('هل تريد حذف المنتج؟')">

<i class="fa-solid fa-trash"></i>

</a>

</td>

</tr>

<?php

}

?>

</table>

<?php } ?>

</div>

</div>

<script>

/* Mobile off-canvas sidebar */

const sidebar=document.getElementById('sidebar');

const menuToggle=document.getElementById('menuToggle');

const overlay=document.getElementById('sidebarOverlay');

function openSidebar(){sidebar.classList.add('open');overlay.classList.add('show');document.body.style.overflow='hidden';}

function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('show');document.body.style.overflow='';}

menuToggle?.addEventListener('click',()=>{sidebar.classList.contains('open') ? closeSidebar() : openSidebar();});

overlay?.addEventListener('click',closeSidebar);

/* Active menu link based on current file */

const currentPage=location.pathname.split('/').pop() || 'products.php';

document.querySelectorAll('.menu a').forEach(a=>{

const href=a.getAttribute('href').split('/').pop();

if(href===currentPage){a.classList.add('active');}

a.addEventListener('click',closeSidebar);

});

/* Bulk selection UI (front-end only — not wired to any delete/update
   endpoint, per the "don't change existing logic" requirement) */

const headCheck=document.getElementById('headCheck');

const bulkBar=document.getElementById('bulkBar');

const bulkCount=document.getElementById('bulkCount');

const selectAllBtn=document.getElementById('selectAllBtn');

function rowChecks(){return document.querySelectorAll('.row-check');}

function refreshBulkBar(){

const checked=[...rowChecks()].filter(c=>c.checked).length;

bulkCount.textContent=checked;

bulkBar.classList.toggle('show',checked>0);

if(headCheck){headCheck.checked = checked>0 && checked===rowChecks().length;}

}

document.addEventListener('change',(e)=>{

if(e.target.classList.contains('row-check')){refreshBulkBar();}

});

headCheck?.addEventListener('change',()=>{

rowChecks().forEach(c=>c.checked=headCheck.checked);

refreshBulkBar();

});

selectAllBtn?.addEventListener('click',()=>{

const allChecked=[...rowChecks()].every(c=>c.checked) && rowChecks().length>0;

rowChecks().forEach(c=>c.checked=!allChecked);

refreshBulkBar();

});

function bulkPlaceholder(){

alert('هذا الإجراء جزء من واجهة التصميم فقط حالياً. لتفعيله فعلياً على قاعدة البيانات يجب ربطه بكود خلفي (Backend) جديد.');

}

/* Simple ripple feedback on primary buttons */

document.querySelectorAll('.add, .tools-form button').forEach(btn=>{

btn.addEventListener('click',function(e){

const r=document.createElement('span');

r.style.position='absolute';

r.style.borderRadius='50%';

r.style.background='rgba(255,255,255,.5)';

r.style.transform='scale(0)';

r.style.animation='ripple .5s ease-out';

r.style.left=(e.offsetX-6)+'px';

r.style.top=(e.offsetY-6)+'px';

r.style.width=r.style.height='12px';

r.style.pointerEvents='none';

this.style.position='relative';

this.style.overflow='hidden';

this.appendChild(r);

setTimeout(()=>r.remove(),500);

});

});

</script>

<style>

@keyframes ripple{to{transform:scale(18);opacity:0;}}

</style>

</body>

</html>