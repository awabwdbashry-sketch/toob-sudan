<?php

session_start();

include 'includes/db.php';


if(!isset($_SESSION['user_id'])){

header("Location:login.php");
exit;

}



$user_id = $_SESSION['user_id'];



$orders = mysqli_query($conn,

"
SELECT * FROM orders

WHERE user_id='$user_id'

ORDER BY id DESC

"

);

/*
 UI-only helpers below. No SQL is added or modified — the query above is
 untouched. These simply organize the already-fetched rows for the
 luxury stats cards / status badges / progress tracker.
*/

$orders_list = array();

if($orders){
    while($row = mysqli_fetch_assoc($orders)){
        $orders_list[] = $row;
    }
}

$total_orders_count = count($orders_list);
$pending_count = 0;
$processing_count = 0;
$shipping_count = 0;
$delivered_count = 0;
$cancelled_count = 0;

function toobsudan_normalize_status($raw_status){
    $s = strtolower(trim((string)$raw_status));

    $pending_words    = array('pending','قيد الانتظار','في الانتظار','معلق');
    $processing_words = array('processing','قيد المعالجة','المعالجة','قيد المراجعة');
    $shipping_words   = array('shipping','shipped','قيد الشحن','الشحن','تم الشحن');
    $delivered_words  = array('delivered','completed','تم التوصيل','تم التسليم','مكتمل');
    $cancelled_words  = array('cancelled','canceled','تم الإلغاء','ملغي','ملغى','الملغية');

    if(in_array($s,$pending_words)) return 'pending';
    if(in_array($s,$processing_words)) return 'processing';
    if(in_array($s,$shipping_words)) return 'shipping';
    if(in_array($s,$delivered_words)) return 'delivered';
    if(in_array($s,$cancelled_words)) return 'cancelled';

    return 'pending';
}

foreach($orders_list as $order_row){
    $norm = toobsudan_normalize_status($order_row['status']);
    if($norm === 'pending') $pending_count++;
    elseif($norm === 'processing') $processing_count++;
    elseif($norm === 'shipping') $shipping_count++;
    elseif($norm === 'delivered') $delivered_count++;
    elseif($norm === 'cancelled') $cancelled_count++;
}

?>


<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>طلباتي | توب سودان</title>


<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<style>

:root{
  --burgundy:#5c1326;
  --burgundy-deep:#4b0f1f;
  --wine:#3A0D18;
  --black:#1a0509;
  --gold:#D4AF37;
  --gold-light:#f0d878;
  --gold-dim:rgba(212,175,55,.35);
  --cream:#E8D5C0;
  --white:#ffffff;
}

*{
box-sizing:border-box;
font-family:'Cairo',sans-serif;
margin:0;
padding:0;
}

html{
scroll-behavior:smooth;
}

body{

background:
radial-gradient(circle at 10% 0%, rgba(212,175,55,.08), transparent 45%),
radial-gradient(circle at 90% 15%, rgba(212,175,55,.06), transparent 40%),
linear-gradient(180deg,var(--black) 0%,var(--burgundy-deep) 45%,var(--black) 100%);
background-attachment:fixed;
color:var(--white);
padding-bottom:60px;

}

/* ===== HERO ===== */

.orders-hero{

text-align:center;

padding:80px 6% 50px;

position:relative;

overflow:hidden;

}

.orders-hero::before{

content:'';

position:absolute;

inset:0;

background:radial-gradient(ellipse at center, rgba(212,175,55,.12), transparent 60%);

pointer-events:none;

}

.orders-hero .eyebrow{

color:var(--gold);

font-size:15px;

letter-spacing:5px;

font-weight:700;

display:inline-block;

animation:fadeUp .7s ease both;

}

.orders-hero h1{

font-size:46px;

font-weight:900;

margin-top:14px;

text-shadow:0 4px 30px rgba(212,175,55,.25);

animation:fadeUp .7s ease .1s both;

}

.orders-hero p{

margin-top:14px;

color:var(--cream);

font-size:17px;

max-width:520px;

margin-left:auto;

margin-right:auto;

animation:fadeUp .7s ease .2s both;

}

.gold-divider{

width:90px;

height:3px;

margin:24px auto 0;

background:linear-gradient(90deg,transparent,var(--gold),transparent);

border-radius:10px;

animation:fadeUp .7s ease .3s both;

}

@keyframes fadeUp{

from{opacity:0; transform:translateY(24px);}

to{opacity:1; transform:translateY(0);}

}

/* ===== STATS CARDS ===== */

.stats-wrap{

padding:0 6% 10px;

max-width:1300px;

margin:0 auto;

}

.stats-row{

display:grid;

grid-template-columns:repeat(5,1fr);

gap:20px;

}

.stat-card{

background:linear-gradient(160deg, rgba(92,19,38,.85), rgba(26,5,9,.9));

border:1px solid var(--gold-dim);

border-radius:20px;

padding:22px 18px;

text-align:center;

position:relative;

overflow:hidden;

transition:transform .4s ease, box-shadow .4s ease, border-color .4s ease;

opacity:0;

transform:translateY(20px);

animation:fadeUp .6s ease forwards;

}

.stat-card:hover{

transform:translateY(-8px);

border-color:var(--gold);

box-shadow:0 18px 34px rgba(0,0,0,.4), 0 0 22px rgba(212,175,55,.2);

}

.stat-card i{

font-size:26px;

color:var(--gold);

margin-bottom:10px;

filter:drop-shadow(0 0 6px rgba(212,175,55,.35));

}

.stat-card .stat-num{

font-size:30px;

font-weight:900;

color:var(--white);

display:block;

}

.stat-card .stat-label{

font-size:13px;

color:var(--cream);

margin-top:4px;

display:block;

}

.stat-card.stat-total{border-top:3px solid var(--gold);}
.stat-card.stat-pending{border-top:3px solid #e0b34d;}
.stat-card.stat-processing{border-top:3px solid #4aa3e0;}
.stat-card.stat-shipping{border-top:3px solid #e08a3c;}
.stat-card.stat-delivered{border-top:3px solid #3fcf7f;}

/* ===== ORDERS LIST ===== */

.orders-wrap{

padding:50px 6% 20px;

max-width:1000px;

margin:0 auto;

}

.order-card{

background:linear-gradient(160deg, rgba(58,13,24,.92), rgba(26,5,9,.95));

border:1px solid var(--gold-dim);

border-radius:24px;

padding:32px;

margin-bottom:28px;

position:relative;

transition:transform .4s ease, box-shadow .4s ease, border-color .4s ease;

opacity:0;

transform:translateY(24px);

animation:fadeUp .6s ease forwards;

}

.order-card:hover{

transform:translateY(-6px);

border-color:var(--gold);

box-shadow:0 20px 40px rgba(0,0,0,.4), 0 0 25px rgba(212,175,55,.15);

}

.order-card-header{

display:flex;

justify-content:space-between;

align-items:center;

flex-wrap:wrap;

gap:14px;

margin-bottom:22px;

padding-bottom:18px;

border-bottom:1px solid rgba(212,175,55,.2);

}

.order-card-header h2{

font-size:21px;

font-weight:800;

color:var(--white);

}

.order-card-header h2 span{

color:var(--gold);

}

/* status badges */

.status-badge{

display:inline-flex;

align-items:center;

gap:8px;

padding:9px 20px;

border-radius:30px;

font-weight:800;

font-size:13px;

letter-spacing:.3px;

backdrop-filter:blur(6px);

}

.status-pending{
background:rgba(224,179,77,.18);
color:#f2cc7a;
border:1px solid rgba(242,204,122,.4);
}

.status-processing{
background:rgba(74,163,224,.18);
color:#8fc7f2;
border:1px solid rgba(143,199,242,.4);
}

.status-shipping{
background:rgba(224,138,60,.18);
color:#f2a866;
border:1px solid rgba(242,168,102,.4);
}

.status-delivered{
background:rgba(63,207,127,.18);
color:#7ee0a3;
border:1px solid rgba(126,224,163,.4);
}

.status-cancelled{
background:rgba(220,53,69,.18);
color:#ff9aa5;
border:1px solid rgba(255,154,165,.4);
}

/* order info grid */

.order-info-grid{

display:grid;

grid-template-columns:repeat(auto-fit,minmax(160px,1fr));

gap:18px;

margin-bottom:26px;

}

.info-item{

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.07);

border-radius:14px;

padding:14px 16px;

}

.info-item .info-label{

color:var(--gold);

font-size:12px;

font-weight:700;

letter-spacing:.5px;

display:block;

margin-bottom:6px;

opacity:.9;

}

.info-item .info-value{

color:var(--white);

font-size:16px;

font-weight:700;

}

/* progress tracker */

.progress-tracker{

display:flex;

align-items:center;

justify-content:space-between;

margin:30px 0 10px;

position:relative;

}

.progress-step{

display:flex;

flex-direction:column;

align-items:center;

flex:1;

position:relative;

z-index:2;

}

.progress-step .dot{

width:34px;

height:34px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

background:rgba(255,255,255,.06);

border:2px solid rgba(255,255,255,.15);

color:rgba(255,255,255,.4);

font-size:14px;

transition:.4s;

margin-bottom:10px;

}

.progress-step .label{

font-size:12px;

color:rgba(255,255,255,.4);

font-weight:700;

text-align:center;

transition:.4s;

}

.progress-step.completed .dot{

background:var(--gold);

border-color:var(--gold);

color:var(--wine);

}

.progress-step.completed .label{

color:var(--gold-light);

}

.progress-step.current .dot{

background:var(--gold);

border-color:var(--gold);

color:var(--wine);

box-shadow:0 0 0 6px rgba(212,175,55,.2), 0 0 20px rgba(212,175,55,.5);

animation:pulseDot 1.8s ease-in-out infinite;

}

.progress-step.current .label{

color:var(--gold-light);

}

@keyframes pulseDot{

0%,100%{box-shadow:0 0 0 6px rgba(212,175,55,.2), 0 0 20px rgba(212,175,55,.5);}

50%{box-shadow:0 0 0 10px rgba(212,175,55,.1), 0 0 30px rgba(212,175,55,.7);}

}

.progress-line{

position:absolute;

top:17px;

right:0;

left:0;

height:2px;

background:rgba(255,255,255,.12);

z-index:1;

}

.progress-line-fill{

position:absolute;

top:0;

right:0;

height:100%;

background:linear-gradient(90deg,var(--gold-light),var(--gold));

transition:width .8s ease;

}

.progress-cancelled-note{

display:flex;

align-items:center;

gap:10px;

background:rgba(220,53,69,.1);

border:1px solid rgba(220,53,69,.35);

color:#ff9aa5;

padding:14px 18px;

border-radius:14px;

font-weight:700;

margin:26px 0 10px;

}

/* mobile progress: vertical */

@media(max-width:600px){

.progress-tracker{

flex-direction:column;

align-items:flex-start;

gap:22px;

}

.progress-line{

display:none;

}

.progress-step{

flex-direction:row;

align-items:center;

gap:14px;

width:100%;

}

.progress-step .dot{

margin-bottom:0;

flex-shrink:0;

}

.progress-step .label{

text-align:right;

}

.progress-step::after{

content:'';

position:absolute;

right:16px;

top:100%;

width:2px;

height:22px;

background:rgba(255,255,255,.12);

}

.progress-step.completed::after{

background:var(--gold);

}

.progress-step:last-child::after{

display:none;

}

}

/* action buttons */

.order-actions{

display:flex;

flex-wrap:wrap;

gap:14px;

margin-top:28px;

}

.order-actions a{

flex:1;

min-width:160px;

text-align:center;

padding:14px 18px;

border-radius:40px;

font-weight:800;

font-size:14.5px;

text-decoration:none;

transition:.35s;

display:flex;

align-items:center;

justify-content:center;

gap:9px;

}

.btn-view-order{

background:rgba(255,255,255,.06);

color:var(--white);

border:1px solid rgba(255,255,255,.25);

}

.btn-view-order:hover{

border-color:var(--gold);

color:var(--gold-light);

background:rgba(212,175,55,.08);

}

.btn-track{

background:rgba(74,163,224,.12);

color:#8fc7f2;

border:1px solid rgba(143,199,242,.4);

}

.btn-track:hover{

background:rgba(74,163,224,.22);

transform:translateY(-2px);

}

.btn-invoice{

background:transparent;

color:var(--gold-light);

border:1px solid var(--gold-dim);

}

.btn-invoice:hover{

background:rgba(212,175,55,.1);

border-color:var(--gold);

transform:translateY(-2px);

}

.btn-reorder{

background:linear-gradient(135deg,var(--gold-light),var(--gold) 55%,#a9812a);

color:var(--wine);

box-shadow:0 8px 20px rgba(212,175,55,.25);

}

.btn-reorder:hover{

filter:brightness(1.08);

transform:translateY(-2px);

box-shadow:0 12px 26px rgba(212,175,55,.4);

}

/* ===== EMPTY STATE ===== */

.empty-state{

text-align:center;

padding:100px 20px;

max-width:520px;

margin:0 auto;

animation:fadeUp .7s ease both;

}

.empty-state .icon-wrap{

width:120px;

height:120px;

margin:0 auto 28px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

background:radial-gradient(circle, rgba(212,175,55,.15), transparent 70%);

border:1px solid var(--gold-dim);

}

.empty-state .icon-wrap i{

font-size:50px;

color:var(--gold);

}

.empty-state h2{

font-size:30px;

font-weight:900;

margin-bottom:14px;

}

.empty-state p{

color:var(--cream);

margin-bottom:30px;

font-size:16px;

line-height:1.8;

}

.empty-shop-btn{

display:inline-flex;

align-items:center;

gap:10px;

background:linear-gradient(135deg,var(--gold-light),var(--gold) 55%,#a9812a);

color:var(--wine);

padding:16px 44px;

border-radius:40px;

text-decoration:none;

font-weight:800;

font-size:16px;

box-shadow:0 10px 25px rgba(212,175,55,.3);

transition:.35s;

}

.empty-shop-btn:hover{

transform:translateY(-3px);

box-shadow:0 16px 34px rgba(212,175,55,.45);

}

/* ===== RESPONSIVE ===== */

@media(max-width:992px){

.stats-row{

grid-template-columns:repeat(5,1fr);

overflow-x:auto;

scroll-snap-type:x mandatory;

padding-bottom:8px;

}

.stat-card{

min-width:150px;

scroll-snap-align:start;

}

.orders-hero h1{

font-size:36px;

}

}

@media(max-width:600px){

.orders-hero{

padding:60px 6% 36px;

}

.orders-hero h1{

font-size:30px;

}

.orders-hero p{

font-size:14.5px;

}

.stats-wrap{

padding:0 5% 6px;

}

.stats-row{

display:flex;

gap:14px;

grid-template-columns:none;

}

.stat-card{

min-width:130px;

padding:18px 14px;

}

.stat-card .stat-num{

font-size:24px;

}

.orders-wrap{

padding:40px 5% 10px;

}

.order-card{

padding:22px 18px;

border-radius:20px;

}

.order-card-header h2{

font-size:18px;

}

.order-actions a{

flex:1 1 100%;

min-width:100%;

}

.info-item .info-value{

font-size:14.5px;

}

}

@media(max-width:360px){

.orders-hero h1{

font-size:26px;

}

}

</style>

</head>


<body>

<section class="orders-hero">

<span class="eyebrow">TOOB SUDAN</span>

<h1>📦 طلباتي</h1>

<p>تابع جميع طلباتك وحالة الشحن في مكان واحد.</p>

<div class="gold-divider"></div>

</section>

<?php if($total_orders_count > 0){ ?>

<div class="stats-wrap">

<div class="stats-row">

<div class="stat-card stat-total" style="animation-delay:.05s;">

<i class="fa-solid fa-boxes-stacked"></i>

<span class="stat-num"><?php echo $total_orders_count; ?></span>

<span class="stat-label">إجمالي الطلبات</span>

</div>

<div class="stat-card stat-pending" style="animation-delay:.1s;">

<i class="fa-regular fa-clock"></i>

<span class="stat-num"><?php echo $pending_count; ?></span>

<span class="stat-label">قيد المراجعة</span>

</div>

<div class="stat-card stat-processing" style="animation-delay:.15s;">

<i class="fa-solid fa-gear"></i>

<span class="stat-num"><?php echo $processing_count; ?></span>

<span class="stat-label">قيد المعالجة</span>

</div>

<div class="stat-card stat-shipping" style="animation-delay:.2s;">

<i class="fa-solid fa-truck-fast"></i>

<span class="stat-num"><?php echo $shipping_count; ?></span>

<span class="stat-label">قيد الشحن</span>

</div>

<div class="stat-card stat-delivered" style="animation-delay:.25s;">

<i class="fa-solid fa-circle-check"></i>

<span class="stat-num"><?php echo $delivered_count; ?></span>

<span class="stat-label">تم التوصيل</span>

</div>

</div>

</div>

<div class="orders-wrap">

<?php $card_i = 0; foreach($orders_list as $row){ $card_i++;

$norm_status = toobsudan_normalize_status($row['status']);

$status_labels = array(
    'pending'    => '🟡 قيد الانتظار',
    'processing' => '🔵 المعالجة',
    'shipping'   => '🟠 الشحن',
    'delivered'  => '🟢 تم التسليم',
    'cancelled'  => '🔴 تم الإلغاء'
);

$status_classes = array(
    'pending'    => 'status-pending',
    'processing' => 'status-processing',
    'shipping'   => 'status-shipping',
    'delivered'  => 'status-delivered',
    'cancelled'  => 'status-cancelled'
);

$step_order = array('pending','processing','shipping','delivered');
$current_step_index = array_search($norm_status, $step_order);

?>

<div class="order-card" style="animation-delay: <?php echo min($card_i * 0.07, 0.5); ?>s;">

<div class="order-card-header">

<h2>طلب رقم <span>#<?php echo $row['id']; ?></span></h2>

<span class="status-badge <?php echo $status_classes[$norm_status]; ?>">

<?php echo $status_labels[$norm_status]; ?>

</span>

</div>

<div class="order-info-grid">

<div class="info-item">

<span class="info-label">الإجمالي</span>

<span class="info-value"><?php echo $row['total']; ?> جنيه</span>

</div>

<div class="info-item">

<span class="info-label">طريقة الدفع</span>

<span class="info-value"><?php echo $row['payment_method']; ?></span>

</div>

<?php if(isset($row['shipping_method'])){ ?>

<div class="info-item">

<span class="info-label">طريقة الشحن</span>

<span class="info-value"><?php echo $row['shipping_method']; ?></span>

</div>

<?php } ?>

<?php if(isset($row['items_count'])){ ?>

<div class="info-item">

<span class="info-label">عدد المنتجات</span>

<span class="info-value"><?php echo $row['items_count']; ?></span>

</div>

<?php } ?>

<div class="info-item">

<span class="info-label">التاريخ</span>

<span class="info-value"><?php echo $row['created_at']; ?></span>

</div>

</div>

<?php if($norm_status === 'cancelled'){ ?>

<div class="progress-cancelled-note">

<i class="fa-solid fa-circle-exclamation"></i>

هذا الطلب تم إلغاؤه ولن يتم شحنه.

</div>

<?php }else{

$fill_percent = ($current_step_index / (count($step_order) - 1)) * 100;

?>

<div class="progress-tracker">

<div class="progress-line">

<div class="progress-line-fill" style="width: <?php echo $fill_percent; ?>%;"></div>

</div>

<?php

$step_labels = array(
    'pending'    => 'تم الاستلام',
    'processing' => 'المعالجة',
    'shipping'   => 'الشحن',
    'delivered'  => 'تم التسليم'
);

foreach($step_order as $step_i => $step_key){

$step_class = '';

if($step_i < $current_step_index) $step_class = 'completed';

elseif($step_i === $current_step_index) $step_class = 'current';

$step_icon = 'fa-check';

if($step_key === 'pending') $step_icon = 'fa-box';

elseif($step_key === 'processing') $step_icon = 'fa-gear';

elseif($step_key === 'shipping') $step_icon = 'fa-truck';

elseif($step_key === 'delivered') $step_icon = 'fa-house';

?>

<div class="progress-step <?php echo $step_class; ?>">

<div class="dot"><i class="fa-solid <?php echo $step_icon; ?>"></i></div>

<div class="label"><?php echo $step_labels[$step_key]; ?></div>

</div>

<?php } ?>

</div>

<?php } ?>

<div class="order-actions">

<a class="btn-view-order" href="order_details.php?id=<?php echo $row['id']; ?>">

<i class="fa-regular fa-eye"></i> ترتيب العرض

</a>

<?php if($norm_status !== 'cancelled'){ ?>

<a class="btn-track" href="track_order.php?id=<?php echo $row['id']; ?>">

<i class="fa-solid fa-location-crosshairs"></i> تتبع الشحنة

</a>

<?php } ?>

<a class="btn-invoice" href="invoice.php?id=<?php echo $row['id']; ?>">

<i class="fa-solid fa-file-invoice"></i> تحميل الفاتورة

</a>

<a class="btn-reorder" href="reorder.php?id=<?php echo $row['id']; ?>">

<i class="fa-solid fa-rotate"></i> اشتر من جديد

</a>

</div>

</div>

<?php } ?>

</div>

<?php }else{ ?>

<div class="empty-state">

<div class="icon-wrap">

<i class="fa-solid fa-box-open"></i>

</div>

<h2>لا توجد طلبات بعد</h2>

<p>لم تقم بإجراء أي طلب حتى الآن. تصفح مجموعتنا الفاخرة من الثياب السودانية وابدأ رحلتك.</p>

<a href="products.php" class="empty-shop-btn">

<i class="fa-solid fa-bag-shopping"></i> ابدأ التسوق

</a>

</div>

<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>