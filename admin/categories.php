<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

$categories = mysqli_query($conn,"
SELECT
categories.*,
COUNT(products.id) AS total_products
FROM categories
LEFT JOIN products
ON products.category_id = categories.id
GROUP BY categories.id
ORDER BY categories.id DESC
");

?>

<?php
/* -----------------------------------------------------------------
   Read-only additive stats for the new dashboard cards.
   IMPORTANT: this uses the SAME $categories result already produced
   by the query above — no new SQL is added and the original query
   text is untouched. mysqli_fetch_all() reads the buffered result
   into a plain PHP array, then mysqli_data_seek() rewinds the
   pointer back to the start so the original while() loop further
   down the page runs completely unaffected, exactly as before.
----------------------------------------------------------------- */

$total_categories_count       = mysqli_num_rows($categories);
$total_products_in_categories = 0;
$empty_categories_count       = 0;

$stats_snapshot = mysqli_fetch_all($categories, MYSQLI_ASSOC);

foreach($stats_snapshot as $stats_row){

    $count_for_row = (int)$stats_row['total_products'];

    $total_products_in_categories += $count_for_row;

    if($count_for_row == 0){
        $empty_categories_count++;
    }

}

$avg_products_per_category = $total_categories_count > 0
    ? round($total_products_in_categories / $total_categories_count, 1)
    : 0;

mysqli_data_seek($categories, 0);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>إدارة التصنيفات | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*========================================================
  TOOB SUDAN — CATEGORIES · PREMIUM ADMIN UI
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
    radial-gradient(1000px 520px at 100% -8%, rgba(212,175,55,.10), transparent 60%),
    radial-gradient(900px 500px at -5% 105%, rgba(91,16,40,.06), transparent 55%),
    var(--bg);

  min-height:100vh;
  color:var(--ink);
  padding:32px 22px 60px;
  position:relative;
  overflow-x:hidden;

}

::-webkit-scrollbar{width:9px;height:9px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--gold),var(--burgundy));border-radius:20px;}

body::before{

  content:"";
  position:fixed;
  inset:0;
  pointer-events:none;
  opacity:.05;
  background-image:
    repeating-linear-gradient(45deg, var(--gold) 0 1px, transparent 1px 28px),
    repeating-linear-gradient(-45deg, var(--gold) 0 1px, transparent 1px 28px);
  z-index:0;

}

.page-wrap{

  max-width:1300px;
  margin:0 auto;
  position:relative;
  z-index:1;

}

/*======================
Header
=======================*/

.top-bar{

  background:linear-gradient(135deg,var(--burgundy) 0%,var(--burgundy-deep) 55%,var(--burgundy-darker) 100%);
  border-radius:var(--radius-lg);
  padding:30px 34px;
  box-shadow:var(--shadow-lift);
  color:#fff;
  position:relative;
  overflow:hidden;
  margin-bottom:22px;
  opacity:0;
  animation:fadeSlide .6s var(--ease) forwards;

}

.top-bar::before{

  content:"";
  position:absolute;
  top:-60px;right:-60px;
  width:220px;height:220px;
  border-radius:50%;
  background:radial-gradient(circle,rgba(212,175,55,.28),transparent 70%);

}

.top-bar::after{

  content:"";
  position:absolute;
  bottom:-80px;left:10%;
  width:260px;height:260px;
  border-radius:50%;
  background:radial-gradient(circle,rgba(212,175,55,.12),transparent 70%);

}

.top-bar-row{

  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:18px;
  position:relative;
  z-index:1;

}

.top-bar-titles h1{

  font-size:27px;
  font-weight:800;
  display:flex;
  align-items:center;
  gap:12px;
  margin-bottom:8px;

}

.top-bar-titles .sub{

  color:rgba(255,255,255,.72);
  font-weight:600;
  font-size:13.5px;
  margin-bottom:12px;

}

.breadcrumb{

  display:flex;
  align-items:center;
  gap:8px;
  font-size:13px;
  font-weight:600;
  color:rgba(255,255,255,.72);

}

.breadcrumb a{

  color:rgba(255,255,255,.72);
  text-decoration:none;
  transition:.25s var(--ease);

}

.breadcrumb a:hover{color:var(--gold-soft);}

.breadcrumb i{font-size:10px;color:var(--gold-soft);opacity:.8;}

.breadcrumb .current{color:var(--gold-soft);}

.top-bar-actions{

  display:flex;
  align-items:center;
  gap:14px;
  flex-wrap:wrap;

}

.total-badge{

  display:inline-flex;
  align-items:center;
  gap:9px;
  background:rgba(255,255,255,.1);
  border:1px solid rgba(212,175,55,.4);
  color:var(--gold-soft);
  padding:12px 20px;
  border-radius:14px;
  font-weight:800;
  font-size:14px;
  backdrop-filter:blur(6px);

}

.add-btn{

  display:inline-flex;
  align-items:center;
  gap:10px;
  background:linear-gradient(120deg,var(--gold),var(--gold-soft));
  color:var(--burgundy-darker);
  padding:14px 26px;
  border-radius:14px;
  text-decoration:none;
  font-weight:800;
  font-size:14.5px;
  box-shadow:var(--shadow-gold);
  transition:.35s var(--ease);

}

.add-btn:hover{

  transform:translateY(-4px);
  background:#fff;
  box-shadow:0 14px 30px rgba(0,0,0,.2);

}

@keyframes fadeSlide{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}

/*======================
Stats cards
=======================*/

.stat-grid{

  display:grid;
  grid-template-columns:repeat(4,1fr);
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
  opacity:0;
  transform:translateY(16px);
  animation:cardIn .55s var(--ease) forwards;

}

.stat-grid .stat-card:nth-child(1){animation-delay:.03s;}
.stat-grid .stat-card:nth-child(2){animation-delay:.09s;}
.stat-grid .stat-card:nth-child(3){animation-delay:.15s;}
.stat-grid .stat-card:nth-child(4){animation-delay:.21s;}

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
  min-width:170px;
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

.reset-btn,
.add-btn-inline{

  padding:15px 24px;
  border:1.6px solid var(--line);
  background:#fff;
  color:var(--burgundy);
  border-radius:14px;
  cursor:pointer;
  font-weight:800;
  font-size:14px;
  font-family:inherit;
  display:inline-flex;
  align-items:center;
  gap:8px;
  transition:.3s var(--ease);
  text-decoration:none;
  white-space:nowrap;

}

.reset-btn:hover{

  background:var(--burgundy);
  color:#fff;
  border-color:var(--burgundy);

}

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

table{width:100%;border-collapse:collapse;min-width:900px;}

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

.cat-image-wrap{

  width:64px;
  height:64px;
  margin:0 auto;
  border-radius:14px;
  padding:2px;
  background:linear-gradient(135deg,var(--gold),var(--burgundy));
  overflow:hidden;

}

.cat-image{

  width:100%;
  height:100%;
  object-fit:cover;
  border-radius:12px;
  border:2px solid #fff;
  display:block;
  transition:.4s var(--ease);

}

.cat-image-wrap:hover .cat-image{transform:scale(1.18);}

.cat-name{font-weight:800;color:var(--ink);font-size:15px;}

.count-badge{

  display:inline-flex;
  align-items:center;
  gap:7px;
  padding:8px 16px;
  border-radius:30px;
  font-size:13px;
  font-weight:800;
  color:#fff;

}

.count-badge.green{background:linear-gradient(120deg,var(--green),#1c7a49);}
.count-badge.orange{background:linear-gradient(120deg,var(--orange),#a86a10);}
.count-badge.red{background:linear-gradient(120deg,var(--red),#a12727);}

.date-cell{color:var(--muted);font-size:13px;font-weight:600;}

.actions{display:flex;align-items:center;justify-content:center;gap:6px;}

.actions a{

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
.edit{background:linear-gradient(135deg,var(--gold),var(--gold-soft));color:var(--burgundy-darker)!important;}
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

.empty-state p{color:var(--muted);font-weight:600;margin-bottom:22px;}

.empty-state .add-btn{padding:14px 26px;color:var(--burgundy-darker);}

/*======================
Responsive
=======================*/

@media(max-width:1150px){

  .stat-grid{grid-template-columns:repeat(2,1fr);}

}

@media(max-width:900px){

  body{padding:20px 14px 40px;}

  .top-bar{padding:24px;}

  .stat-grid{

    grid-template-columns:repeat(4,minmax(170px,1fr));
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

}

@media(max-width:760px){

  .top-bar-row{flex-direction:column;align-items:flex-start;}

  .top-bar-actions{width:100%;}

  .add-btn{flex:1;justify-content:center;}

  .total-badge{flex:1;justify-content:center;}

  .toolbar-row{flex-direction:column;align-items:stretch;}

  .search-wrap{min-width:0;}

  .reset-btn{width:100%;justify-content:center;min-height:48px;}

  .toolbar-row select{min-height:48px;}

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
    min-height:48px;

  }

  td:last-child{border-bottom:none;}

  td::before{

    content:attr(data-label);
    font-weight:800;
    color:var(--burgundy);
    font-size:12.5px;

  }

  td.actions{justify-content:flex-start;}

  .actions a{width:44px;height:44px;}

  .cat-image-wrap{margin:0;}

}

/*========================================================
  ADMIN SHELL — SIDEBAR + TOPBAR (layout only, additive)
========================================================*/

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

/* ---------- Main wrap (topbar + content) ---------- */

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

.welcome-text{

  min-width:0;

}

.welcome-text .hi{

  font-weight:800;
  font-size:15.5px;
  color:var(--ink);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;

}

.welcome-text .hi span{color:var(--burgundy);}

.welcome-text .role{

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

}

/* content area now lives inside .main-content, background matches page */

.main-content .page-wrap{

  padding:26px 26px 50px;
  max-width:1300px;

}

/* ---------- Responsive: tablet — icon-only collapsible sidebar ---------- */

@media(max-width:1150px){

  .sidebar{width:84px;}

  .sidebar-brand{padding:22px 0;justify-content:center;}

  .sidebar-brand .brand-text{display:none;}

  .sidebar-nav{padding:14px 10px;align-items:center;}

  .sidebar-nav li a{

    justify-content:center;
    padding:13px;

  }

  .sidebar-nav li a span.link-text{display:none;}

  .main-wrap{margin-right:84px;}

}

/* ---------- Responsive: mobile — hamburger + slide-out drawer ---------- */

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

  .welcome-text .role{display:none;}

  .icon-btn,
  .admin-avatar{width:40px;height:40px;}

  .main-content .page-wrap{padding:16px 12px 40px;}

}

@media(max-width:420px){

  .welcome-text .hi{font-size:14px;max-width:140px;}

  .topbar-right{gap:7px;}

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

      <li><a href="categories.php" class="active"><i class="fa-solid fa-folder-tree"></i><span class="link-text">التصنيفات</span></a></li>

      <li><a href="orders.php"><i class="fa-solid fa-cart-shopping"></i><span class="link-text">الطلبات</span></a></li>

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

        <div class="welcome-text">
          <div class="hi">مرحباً، <span>Admin</span> 👋</div>
          <div class="role">مدير المتجر</div>
        </div>

      </div>

      <div class="topbar-right">

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

  <div class="top-bar">
    <div class="top-bar-row">
      <div class="top-bar-titles">
        <h1><i class="fa-solid fa-folder-tree"></i> التصنيفات</h1>
        <p class="sub">إدارة جميع تصنيفات المتجر بسهولة.</p>
        <nav class="breadcrumb">
          <a href="dashboard.php">الرئيسية</a>
          <i class="fa-solid fa-chevron-left"></i>
          <span class="current">التصنيفات</span>
        </nav>
      </div>
      <div class="top-bar-actions">
        <span class="total-badge"><i class="fa-solid fa-layer-group"></i> <?php echo $total_categories_count; ?> تصنيف</span>
        <a href="add_category.php" class="add-btn"><i class="fa-solid fa-plus"></i> إضافة تصنيف</a>
      </div>
    </div>
  </div>

  <div class="stat-grid">

    <div class="stat-card">
      <div class="icon"><i class="fa-solid fa-folder-tree"></i></div>
      <h2><?php echo $total_categories_count; ?></h2>
      <p>إجمالي التصنيفات</p>
    </div>

    <div class="stat-card">
      <div class="icon"><i class="fa-solid fa-shirt"></i></div>
      <h2><?php echo $total_products_in_categories; ?></h2>
      <p>إجمالي المنتجات داخل التصنيفات</p>
    </div>

    <div class="stat-card">
      <div class="icon"><i class="fa-solid fa-chart-simple"></i></div>
      <h2><?php echo $avg_products_per_category; ?></h2>
      <p>متوسط المنتجات لكل تصنيف</p>
    </div>

    <div class="stat-card">
      <div class="icon"><i class="fa-solid fa-box-open"></i></div>
      <h2><?php echo $empty_categories_count; ?></h2>
      <p>تصنيفات بدون منتجات</p>
    </div>

  </div>

  <div class="panel">
    <div class="toolbar-row">

      <div class="search-wrap">
        <input
          type="text"
          id="search"
          placeholder="ابحث عن تصنيف..."
          onkeyup="searchTable()">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>

      <select id="sortSelect" onchange="sortTable()">
        <option value="default">ترتيب افتراضي</option>
        <option value="name">الاسم (أ-ي)</option>
        <option value="products-desc">الأكثر منتجات</option>
        <option value="products-asc">الأقل منتجات</option>
        <option value="newest">الأحدث</option>
      </select>

      <button type="button" class="reset-btn" onclick="resetToolbar()">
        <i class="fa-solid fa-rotate-right"></i> إعادة تعيين
      </button>

    </div>
  </div>

  <div class="table-box">

    <?php if($total_categories_count == 0){ ?>

    <div class="empty-state">
      <i class="fa-solid fa-folder-open"></i>
      <h3>لا توجد تصنيفات حالياً</h3>
      <p>ابدأ بإضافة أول تصنيف لمتجر توب سودان.</p>
      <a class="add-btn" href="add_category.php"><i class="fa-solid fa-plus"></i> إضافة أول تصنيف</a>
    </div>

    <?php }else{ ?>

    <table id="categoryTable">

      <thead>
        <tr>
          <th>#</th>
          <th>الصورة</th>
          <th>اسم التصنيف</th>
          <th>عدد المنتجات</th>
          <th>تاريخ الإنشاء</th>
          <th>الإجراءات</th>
        </tr>
      </thead>

      <tbody>

      <?php while($row=mysqli_fetch_assoc($categories)){ ?>

      <tr data-created="<?php echo strtotime($row['created_at']); ?>">

        <td class="id-cell" data-label="#"><?php echo $row['id']; ?></td>

        <td data-label="الصورة">
          <div class="cat-image-wrap">
            <img class="cat-image" src="../uploads/categories/<?php echo $row['image']; ?>">
          </div>
        </td>

        <td class="cat-name" data-label="اسم التصنيف"><?php echo $row['name']; ?></td>

        <td data-label="عدد المنتجات">
          <span class="count-badge" data-count="<?php echo $row['total_products']; ?>">
            <i class="fa-solid fa-shirt"></i> <?php echo $row['total_products']; ?>
          </span>
        </td>

        <td class="date-cell" data-label="تاريخ الإنشاء"><?php echo date("Y-m-d",strtotime($row['created_at'])); ?></td>

        <td class="actions" data-label="الإجراءات">

          <a href="view_category.php?id=<?php echo $row['id']; ?>" class="view" data-tip="عرض">
            <i class="fa-solid fa-eye"></i>
          </a>

          <a href="edit_category.php?id=<?php echo $row['id']; ?>" class="edit" data-tip="تعديل">
            <i class="fa-solid fa-pen"></i>
          </a>

          <a
            href="delete_category.php?id=<?php echo $row['id']; ?>"
            class="delete"
            data-tip="حذف"
            onclick="return confirm('هل تريد حذف هذا التصنيف؟')">
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

/* ---------- Existing search (kept: same #search id + searchTable() name/behavior) ---------- */

function searchTable(){

  let input = document.getElementById("search").value.toLowerCase();
  let table = document.getElementById("categoryTable");
  if(!table) return;

  let rows = table.getElementsByTagName("tr");

  for(let i=1;i<rows.length;i++){

    let text = rows[i].innerText.toLowerCase();
    rows[i].style.display = text.indexOf(input) > -1 ? "" : "none";

  }

}

/* ---------- Client-side sort (UI only — no backend involved) ---------- */

function sortTable(){

  const table = document.getElementById("categoryTable");
  if(!table) return;

  const tbody = table.querySelector("tbody");
  const mode = document.getElementById("sortSelect").value;
  const rows = Array.from(tbody.querySelectorAll("tr"));

  if(mode === "default"){
    rows.sort((a,b)=> b.querySelector(".id-cell").textContent - a.querySelector(".id-cell").textContent);
  }else if(mode === "name"){
    rows.sort((a,b)=> a.querySelector(".cat-name").textContent.localeCompare(b.querySelector(".cat-name").textContent,'ar'));
  }else if(mode === "products-desc"){
    rows.sort((a,b)=> b.querySelector(".count-badge").dataset.count - a.querySelector(".count-badge").dataset.count);
  }else if(mode === "products-asc"){
    rows.sort((a,b)=> a.querySelector(".count-badge").dataset.count - b.querySelector(".count-badge").dataset.count);
  }else if(mode === "newest"){
    rows.sort((a,b)=> b.dataset.created - a.dataset.created);
  }

  rows.forEach(r=>tbody.appendChild(r));

}

function resetToolbar(){

  document.getElementById("search").value = "";
  document.getElementById("sortSelect").value = "default";
  searchTable();
  sortTable();

}

/* ---------- Stock-count badge colors (UI only, derived from data-count) ---------- */

document.querySelectorAll('.count-badge').forEach(badge=>{

  const count = parseInt(badge.dataset.count, 10);

  if(count > 5){
    badge.classList.add('green');
  }else if(count > 0){
    badge.classList.add('orange');
  }else{
    badge.classList.add('red');
  }

});

/* ---------- Ripple feedback ---------- */

document.querySelectorAll('.add-btn, .reset-btn, .actions a').forEach(el=>{

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

</script>

</body>
</html>