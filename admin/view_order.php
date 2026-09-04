<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: orders.php");
    exit;
}

$id = (int)$_GET['id'];

$order = mysqli_query($conn,"
SELECT *
FROM orders
WHERE id='$id'
");

if(mysqli_num_rows($order)==0){

    header("Location: orders.php");
    exit;

}

$order = mysqli_fetch_assoc($order);

$items = mysqli_query($conn,"
SELECT

order_items.*,
products.name,
products.image

FROM order_items

INNER JOIN products
ON products.id=order_items.product_id

WHERE order_items.order_id='$id'

");

/* ============================================================
   Presentation-only helpers below.
   No SQL, sessions, includes, or business logic were changed.
   These simply derive display values from data already fetched
   above, and safely no-op if optional columns don't exist.
   ============================================================ */

// Status -> badge class (unchanged mapping/logic, just organised)
$status = $order['status'];
$class = "new";

if($status == "جديد"){
    $class = "new";
} elseif($status == "قيد التجهيز"){
    $class = "processing";
} elseif($status == "قيد الشحن"){
    $class = "shipping";
} elseif($status == "مكتمل"){
    $class = "completed";
} elseif($status == "ملغي" || $status == "ملغى"){
    $class = "cancelled";
}

// Status icon map (display only)
$status_icons = [
    "new"        => "fa-solid fa-star",
    "processing" => "fa-solid fa-box-open",
    "shipping"   => "fa-solid fa-truck-fast",
    "completed"  => "fa-solid fa-circle-check",
    "cancelled"  => "fa-solid fa-circle-xmark",
];
$status_icon = $status_icons[$class] ?? "fa-solid fa-circle";

// Simple 4-stage progress derived from the existing $status value only.
// No new queries — purely a visual read of data already loaded.
$stage_order = ["new" => 1, "processing" => 2, "shipping" => 3, "completed" => 4];
$current_stage = $stage_order[$class] ?? 0;

// Small helper so optional columns never trigger warnings if they
// don't exist in the orders/order_items tables on this install.
function toob_field($arr, $key){
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : null;
}
?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>

عرض الطلب #<?php echo $order['id']; ?>

</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

:root{
    --primary:#5B1028;
    --primary-light:#7A1C3B;
    --primary-dark:#40091C;
    --gold:#D4AF37;
    --gold-light:#E9CE7A;
    --bg:#F7F7F7;
    --white:#FFFFFF;
    --border:#ECECEC;
    --text:#222222;
    --muted:#777777;
    --radius-lg:22px;
    --radius-md:16px;
    --radius-sm:10px;
    --shadow-soft:0 10px 30px rgba(91,16,40,.06);
    --shadow-hover:0 20px 45px rgba(91,16,40,.12);
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Cairo',sans-serif;
}

body{
    background:
        radial-gradient(circle at 100% 0%, rgba(212,175,55,.06), transparent 40%),
        radial-gradient(circle at 0% 100%, rgba(91,16,40,.05), transparent 40%),
        var(--bg);
    color:var(--text);
    min-height:100vh;
}

.container{
    max-width:1300px;
    margin:0 auto;
    padding:32px 24px 60px;
}

/* ---------- Top bar ---------- */
.page-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:16px;
    margin-bottom:28px;
    animation:fadeIn .5s ease both;
}

.title-wrap .eyebrow{
    display:flex;
    align-items:center;
    gap:8px;
    color:var(--gold);
    font-weight:700;
    font-size:13px;
    letter-spacing:.5px;
    margin-bottom:6px;
}

.title{
    font-size:32px;
    font-weight:800;
    color:var(--primary);
}

.title span{
    color:var(--gold);
}

/* ---------- Summary hero ---------- */
.hero{
    background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius:var(--radius-lg);
    padding:34px;
    color:#fff;
    box-shadow:var(--shadow-soft);
    margin-bottom:26px;
    position:relative;
    overflow:hidden;
    animation:slideUp .5s ease both;
}

.hero::before{
    content:"";
    position:absolute;
    inset:0;
    background:
        radial-gradient(circle at 90% -10%, rgba(212,175,55,.35), transparent 45%),
        radial-gradient(circle at -10% 120%, rgba(212,175,55,.15), transparent 40%);
    pointer-events:none;
}

.hero-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:16px;
    position:relative;
    z-index:1;
    margin-bottom:22px;
}

.hero-order-num{
    font-size:26px;
    font-weight:800;
}

.hero-order-num small{
    display:block;
    font-size:13px;
    font-weight:500;
    color:rgba(255,255,255,.7);
    margin-top:4px;
}

.status{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 20px;
    border-radius:30px;
    font-size:14px;
    font-weight:700;
    box-shadow:0 6px 16px rgba(0,0,0,.12);
}

.new{ background:#fff3cd; color:#856404; }
.processing{ background:#cfe2ff; color:#084298; }
.shipping{ background:#d1ecf1; color:#0c5460; }
.completed{ background:#d4edda; color:#155724; }
.cancelled{ background:#f8d7da; color:#842029; }

.hero-stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:14px;
    position:relative;
    z-index:1;
}

.hero-stat{
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.14);
    backdrop-filter:blur(6px);
    border-radius:var(--radius-md);
    padding:16px 18px;
    display:flex;
    align-items:center;
    gap:12px;
    transition:.3s;
}

.hero-stat:hover{
    background:rgba(255,255,255,.14);
    transform:translateY(-3px);
}

.hero-stat .icon{
    width:42px;
    height:42px;
    border-radius:12px;
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    color:var(--primary-dark);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    flex-shrink:0;
}

.hero-stat .label{
    font-size:12px;
    color:rgba(255,255,255,.65);
    margin-bottom:3px;
}

.hero-stat .value{
    font-size:15px;
    font-weight:700;
    word-break:break-word;
}

/* ---------- Timeline ---------- */
.timeline-card{
    background:var(--white);
    border-radius:var(--radius-lg);
    padding:26px 30px;
    box-shadow:var(--shadow-soft);
    margin-bottom:26px;
    animation:slideUp .55s ease both;
}

.timeline{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    position:relative;
    margin-top:22px;
}

.timeline::before{
    content:"";
    position:absolute;
    top:19px;
    right:5%;
    left:5%;
    height:3px;
    background:var(--border);
    z-index:0;
}

.timeline-step{
    position:relative;
    z-index:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:10px;
    flex:1;
    text-align:center;
}

.timeline-step .dot{
    width:40px;
    height:40px;
    border-radius:50%;
    background:var(--white);
    border:3px solid var(--border);
    color:var(--muted);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    transition:.3s;
}

.timeline-step.done .dot{
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    border-color:var(--primary);
    color:#fff;
}

.timeline-step.current .dot{
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    border-color:var(--gold);
    color:var(--primary-dark);
    box-shadow:0 0 0 6px rgba(212,175,55,.18);
}

.timeline-step .label{
    font-size:13px;
    font-weight:700;
    color:var(--text);
}

.timeline-step.cancelled-note{
    color:#842029;
}

/* ---------- Grid ---------- */
.grid{
    display:grid;
    grid-template-columns:380px 1fr;
    gap:22px;
    align-items:start;
}

.col-side{
    display:flex;
    flex-direction:column;
    gap:22px;
}

.card{
    background:var(--white);
    border-radius:var(--radius-lg);
    padding:26px;
    box-shadow:var(--shadow-soft);
    transition:box-shadow .3s;
    animation:slideUp .6s ease both;
}

.card:hover{
    box-shadow:var(--shadow-hover);
}

.card h3{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:19px;
    font-weight:800;
    color:var(--primary);
    margin-bottom:18px;
    padding-bottom:14px;
    border-bottom:2px solid var(--border);
}

.card h3 i{
    width:36px;
    height:36px;
    border-radius:10px;
    background:linear-gradient(135deg, rgba(91,16,40,.08), rgba(212,175,55,.12));
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
}

.info{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:13px 0;
    border-bottom:1px solid var(--border);
}

.info:last-child{
    border-bottom:none;
}

.info .info-label{
    display:flex;
    align-items:center;
    gap:9px;
    font-weight:700;
    color:var(--muted);
    font-size:14px;
    white-space:nowrap;
}

.info .info-label i{
    color:var(--gold);
    width:16px;
    text-align:center;
}

.info span:last-child{
    color:var(--text);
    font-weight:600;
    text-align:left;
    word-break:break-word;
}

/* ---------- Products ---------- */
.product-list{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.product-row{
    display:grid;
    grid-template-columns:1fr auto auto auto;
    align-items:center;
    gap:16px;
    padding:16px;
    border:1px solid var(--border);
    border-radius:var(--radius-md);
    transition:.25s;
}

.product-row:hover{
    box-shadow:var(--shadow-hover);
    transform:translateY(-2px);
    border-color:rgba(212,175,55,.4);
}

.product{
    display:flex;
    align-items:center;
    gap:14px;
    min-width:0;
}

.product img{
    width:64px;
    height:64px;
    object-fit:cover;
    border-radius:12px;
    border:1px solid var(--border);
    flex-shrink:0;
}

.product .p-name{
    font-weight:700;
    font-size:15px;
    overflow:hidden;
    text-overflow:ellipsis;
}

.product .p-category{
    font-size:12px;
    color:var(--muted);
    margin-top:3px;
}

.p-qty{
    background:var(--bg);
    border-radius:30px;
    padding:6px 14px;
    font-weight:700;
    font-size:13px;
    color:var(--primary);
    white-space:nowrap;
}

.p-price{
    font-weight:600;
    color:var(--muted);
    font-size:14px;
    white-space:nowrap;
}

.p-total{
    font-weight:800;
    color:var(--primary);
    font-size:15px;
    white-space:nowrap;
}

.col-head{
    display:grid;
    grid-template-columns:1fr auto auto auto;
    gap:16px;
    padding:0 16px 10px;
    font-size:12px;
    font-weight:700;
    color:var(--muted);
}

/* ---------- Totals ---------- */
.totals-card{
    margin-top:22px;
}

.totals-row{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    font-size:14.5px;
    color:var(--muted);
    font-weight:600;
}

.totals-row.grand{
    margin-top:8px;
    padding-top:18px;
    border-top:2px dashed var(--border);
    font-size:22px;
    font-weight:800;
    color:var(--primary);
}

.totals-row.grand .gold-amount{
    background:linear-gradient(135deg, var(--gold), #B8862C);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
}

/* ---------- Buttons ---------- */
.buttons{
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    margin-top:24px;
}

.buttons a{
    position:relative;
    overflow:hidden;
    display:inline-flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    padding:14px 26px;
    border-radius:14px;
    color:#fff;
    font-weight:700;
    font-size:14.5px;
    box-shadow:0 8px 20px rgba(0,0,0,.1);
    transition:transform .25s, box-shadow .25s;
}

.buttons a:hover{
    transform:translateY(-4px);
    box-shadow:0 14px 28px rgba(0,0,0,.16);
}

.buttons a:active{
    transform:translateY(-1px);
}

.buttons a::after{
    content:"";
    position:absolute;
    inset:0;
    background:rgba(255,255,255,.25);
    transform:scale(0);
    border-radius:50%;
    transition:transform .5s ease, opacity .6s ease;
    opacity:0;
}

.buttons a:active::after{
    transform:scale(3);
    opacity:1;
    transition:0s;
}

.print{ background:linear-gradient(135deg,#198754,#14603c); }
.edit{ background:linear-gradient(135deg, var(--gold), #B8862C); color:#3a2a05 !important; }
.back{ background:linear-gradient(135deg,#6c757d,#4c5257); }

/* ---------- Animations ---------- */
@keyframes fadeIn{
    from{opacity:0;}
    to{opacity:1;}
}

@keyframes slideUp{
    from{opacity:0; transform:translateY(16px);}
    to{opacity:1; transform:translateY(0);}
}

/* ---------- Responsive ---------- */
@media(max-width:1000px){
    .grid{
        grid-template-columns:1fr;
    }
    .hero-stats{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:720px){
    .container{
        padding:20px 14px 40px;
    }
    .title{
        font-size:24px;
    }
    .hero{
        padding:22px;
    }
    .hero-order-num{
        font-size:20px;
    }
    .card{
        padding:20px;
    }
    .product-row{
        grid-template-columns:1fr;
        gap:10px;
        text-align:center;
    }
    .product{
        flex-direction:column;
        text-align:center;
    }
    .col-head{
        display:none;
    }
    .p-qty,.p-price,.p-total{
        justify-self:center;
    }
    .timeline{
        flex-wrap:wrap;
        gap:18px 8px;
    }
    .timeline::before{
        display:none;
    }
    .buttons{
        flex-direction:column;
    }
    .buttons a{
        justify-content:center;
    }
}

@media(max-width:420px){
    .hero-stats{
        grid-template-columns:1fr 1fr;
    }
}

/* ---------- Print ---------- */
@media print{
    body{
        background:#fff;
    }
    .buttons, .timeline-card{
        display:none !important;
    }
    .hero{
        background:var(--primary) !important;
        -webkit-print-color-adjust:exact;
        print-color-adjust:exact;
    }
    .card{
        box-shadow:none;
        border:1px solid var(--border);
    }
    .grid{
        grid-template-columns:1fr;
    }
}

</style>

</head>

<body>

<div class="container">

<div class="page-head">
    <div class="title-wrap">
        <div class="eyebrow">
            <i class="fa-solid fa-sparkles"></i>
            توب سودان &middot; إدارة الطلبات
        </div>
        <h1 class="title">
            تفاصيل الطلب <span>#<?php echo $order['id']; ?></span>
        </h1>
    </div>
</div>

<!-- ================= HERO SUMMARY ================= -->
<div class="hero">
    <div class="hero-top">
        <div class="hero-order-num">
            طلب #<?php echo $order['id']; ?>
            <small>
                <i class="fa-regular fa-calendar"></i>
                <?php echo $order['created_at']; ?>
            </small>
        </div>

        <span class="status <?php echo $class; ?>">
            <i class="<?php echo $status_icon; ?>"></i>
            <?php echo $status; ?>
        </span>
    </div>

    <div class="hero-stats">
        <div class="hero-stat">
            <div class="icon"><i class="fa-solid fa-user"></i></div>
            <div>
                <div class="label">العميل</div>
                <div class="value"><?php echo $order['name']; ?></div>
            </div>
        </div>

        <div class="hero-stat">
            <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
            <div>
                <div class="label">طريقة الدفع</div>
                <div class="value"><?php echo $order['payment_method']; ?></div>
            </div>
        </div>

        <div class="hero-stat">
            <div class="icon"><i class="fa-solid fa-phone"></i></div>
            <div>
                <div class="label">الهاتف</div>
                <div class="value"><?php echo $order['phone']; ?></div>
            </div>
        </div>

        <div class="hero-stat">
            <div class="icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div>
                <div class="label">الإجمالي</div>
                <div class="value"><?php echo $order['total']; ?> جنيه</div>
            </div>
        </div>
    </div>
</div>

<!-- ================= TIMELINE ================= -->
<?php if($class != "cancelled"){ ?>
<div class="timeline-card">
    <h3 style="border:none;padding:0;margin:0;">
        <i class="fa-solid fa-route"></i>
        مسار الطلب
    </h3>

    <div class="timeline">
        <div class="timeline-step <?php echo $current_stage >= 1 ? ($current_stage == 1 ? 'current' : 'done') : ''; ?>">
            <div class="dot"><i class="fa-solid fa-star"></i></div>
            <div class="label">طلب جديد</div>
        </div>

        <div class="timeline-step <?php echo $current_stage >= 2 ? ($current_stage == 2 ? 'current' : 'done') : ''; ?>">
            <div class="dot"><i class="fa-solid fa-box-open"></i></div>
            <div class="label">قيد التجهيز</div>
        </div>

        <div class="timeline-step <?php echo $current_stage >= 3 ? ($current_stage == 3 ? 'current' : 'done') : ''; ?>">
            <div class="dot"><i class="fa-solid fa-truck-fast"></i></div>
            <div class="label">قيد الشحن</div>
        </div>

        <div class="timeline-step <?php echo $current_stage >= 4 ? 'done' : ''; ?>">
            <div class="dot"><i class="fa-solid fa-circle-check"></i></div>
            <div class="label">مكتمل</div>
        </div>
    </div>
</div>
<?php } ?>

<div class="grid">

<!-- ================= SIDE COLUMN ================= -->
<div class="col-side">

    <div class="card">
        <h3>
            <i class="fa-solid fa-user"></i>
            بيانات العميل
        </h3>

        <div class="info">
            <span class="info-label"><i class="fa-solid fa-signature"></i> الاسم</span>
            <span><?php echo $order['name']; ?></span>
        </div>

        <div class="info">
            <span class="info-label"><i class="fa-solid fa-phone"></i> الهاتف</span>
            <span><?php echo $order['phone']; ?></span>
        </div>

        <?php $email = toob_field($order,'email'); if($email){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-envelope"></i> البريد الإلكتروني</span>
            <span><?php echo $email; ?></span>
        </div>
        <?php } ?>

        <?php $city = toob_field($order,'city'); if($city){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-city"></i> المدينة</span>
            <span><?php echo $city; ?></span>
        </div>
        <?php } ?>

        <div class="info">
            <span class="info-label"><i class="fa-solid fa-location-dot"></i> العنوان</span>
            <span><?php echo $order['address']; ?></span>
        </div>

        <?php $country = toob_field($order,'country'); if($country){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-earth-africa"></i> الدولة</span>
            <span><?php echo $country; ?></span>
        </div>
        <?php } ?>

        <?php $notes = toob_field($order,'notes'); if($notes){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-note-sticky"></i> ملاحظات</span>
            <span><?php echo $notes; ?></span>
        </div>
        <?php } ?>

        <div class="info">
            <span class="info-label"><i class="fa-regular fa-calendar"></i> تاريخ الطلب</span>
            <span><?php echo $order['created_at']; ?></span>
        </div>

        <div class="info">
            <span class="info-label"><i class="fa-solid fa-circle-info"></i> حالة الطلب</span>
            <span class="status <?php echo $class; ?>">
                <i class="<?php echo $status_icon; ?>"></i>
                <?php echo $status; ?>
            </span>
        </div>
    </div>

    <div class="card">
        <h3>
            <i class="fa-solid fa-credit-card"></i>
            معلومات الدفع
        </h3>

        <div class="info">
            <span class="info-label"><i class="fa-solid fa-money-bill-wave"></i> طريقة الدفع</span>
            <span><?php echo $order['payment_method']; ?></span>
        </div>

        <?php $pstatus = toob_field($order,'payment_status'); if($pstatus){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-circle-check"></i> حالة الدفع</span>
            <span><?php echo $pstatus; ?></span>
        </div>
        <?php } ?>

        <?php $txn = toob_field($order,'transaction_id'); if($txn){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-hashtag"></i> رقم العملية</span>
            <span><?php echo $txn; ?></span>
        </div>
        <?php } ?>

        <?php $pdate = toob_field($order,'payment_date'); if($pdate){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-regular fa-calendar-check"></i> تاريخ الدفع</span>
            <span><?php echo $pdate; ?></span>
        </div>
        <?php } ?>
    </div>

    <?php
        $ship_receiver = toob_field($order,'receiver_name');
        $ship_company  = toob_field($order,'shipping_company');
        $ship_tracking = toob_field($order,'tracking_number');
        $ship_eta      = toob_field($order,'estimated_delivery');

        if($ship_receiver || $ship_company || $ship_tracking || $ship_eta){
    ?>
    <div class="card">
        <h3>
            <i class="fa-solid fa-truck-fast"></i>
            معلومات الشحن
        </h3>

        <?php if($ship_receiver){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-user-tag"></i> اسم المستلم</span>
            <span><?php echo $ship_receiver; ?></span>
        </div>
        <?php } ?>

        <?php if($ship_company){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-truck"></i> شركة الشحن</span>
            <span><?php echo $ship_company; ?></span>
        </div>
        <?php } ?>

        <?php if($ship_tracking){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-solid fa-barcode"></i> رقم التتبع</span>
            <span><?php echo $ship_tracking; ?></span>
        </div>
        <?php } ?>

        <?php if($ship_eta){ ?>
        <div class="info">
            <span class="info-label"><i class="fa-regular fa-clock"></i> موعد التسليم المتوقع</span>
            <span><?php echo $ship_eta; ?></span>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

</div>

<!-- ================= MAIN COLUMN ================= -->
<div>

    <div class="card">
        <h3>
            <i class="fa-solid fa-cart-shopping"></i>
            المنتجات
        </h3>

        <div class="col-head">
            <span>المنتج</span>
            <span>الكمية</span>
            <span>السعر</span>
            <span>الإجمالي</span>
        </div>

        <div class="product-list">

        <?php

        $total = 0;

        while($item=mysqli_fetch_assoc($items)){

            $item_total = $item['price'] * $item['quantity'];

            $total += $item_total;

            $category = toob_field($item,'category');

        ?>

        <div class="product-row">

            <div class="product">
                <img src="../uploads/products/<?php echo $item['image']; ?>">
                <div>
                    <div class="p-name"><?php echo $item['name']; ?></div>
                    <?php if($category){ ?>
                    <div class="p-category"><?php echo $category; ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="p-qty">x <?php echo $item['quantity']; ?></div>

            <div class="p-price"><?php echo $item['price']; ?> جنيه</div>

            <div class="p-total"><?php echo $item_total; ?> جنيه</div>

        </div>

        <?php } ?>

        </div>

        <div class="totals-card">

            <?php $subtotal_field = toob_field($order,'subtotal'); if($subtotal_field){ ?>
            <div class="totals-row">
                <span>المجموع الفرعي</span>
                <span><?php echo $subtotal_field; ?> جنيه</span>
            </div>
            <?php } ?>

            <?php $shipping_fee = toob_field($order,'shipping_fee'); if($shipping_fee){ ?>
            <div class="totals-row">
                <span>الشحن</span>
                <span><?php echo $shipping_fee; ?> جنيه</span>
            </div>
            <?php } ?>

            <?php $discount = toob_field($order,'discount'); if($discount){ ?>
            <div class="totals-row">
                <span>الخصم</span>
                <span>- <?php echo $discount; ?> جنيه</span>
            </div>
            <?php } ?>

            <?php $tax = toob_field($order,'tax'); if($tax){ ?>
            <div class="totals-row">
                <span>الضريبة</span>
                <span><?php echo $tax; ?> جنيه</span>
            </div>
            <?php } ?>

            <div class="totals-row grand">
                <span>الإجمالي الكلي</span>
                <span class="gold-amount"><?php echo $order['total']; ?> جنيه</span>
            </div>

        </div>

        <div class="buttons">

            <a href="print_order.php?id=<?php echo $order['id']; ?>" class="print">
                <i class="fa-solid fa-print"></i>
                طباعة الطلب
            </a>

            <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="edit">
                <i class="fa-solid fa-pen"></i>
                تعديل الحالة
            </a>

            <a href="orders.php" class="back">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع
            </a>

        </div>

    </div>

</div>

</div>

</div>

</body>

</html>