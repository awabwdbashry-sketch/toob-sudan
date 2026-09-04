<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";


// جلب التقييمات

$reviews = mysqli_query($conn,"
SELECT 

reviews.*,

products.name AS product_name,

users.name AS user_name


FROM reviews


LEFT JOIN products

ON products.id = reviews.product_id


LEFT JOIN users

ON users.id = reviews.user_id


ORDER BY reviews.id DESC

");

/* ============================================================
   Presentation-only additions below.
   The SQL query above, the session check, and the include are
   completely untouched. To power the stat cards and the table
   at the same time, the result set is simply collected into an
   array once here — same rows, same fields, same order — then
   looped below instead of streamed row-by-row via mysqli.
   ============================================================ */

$all_reviews = [];

while($review = mysqli_fetch_assoc($reviews)){
    $all_reviews[] = $review;
}

$total_reviews   = count($all_reviews);
$rating_sum      = 0;
$five_star_count = 0;

// Optional 'status' column support — only counted if the column
// actually exists on the reviews table (SELECT * would include it).
$approved_count = 0;
$pending_count  = 0;
$hidden_count   = 0;
$has_status_col = false;

foreach($all_reviews as $r){

    $rating_sum += (int)$r['rating'];

    if((int)$r['rating'] == 5){
        $five_star_count++;
    }

    if(array_key_exists('status', $r)){
        $has_status_col = true;

        if($r['status'] == 'approved'){
            $approved_count++;
        } elseif($r['status'] == 'hidden'){
            $hidden_count++;
        } else {
            $pending_count++;
        }
    }
}

$average_rating = $total_reviews > 0 ? round($rating_sum / $total_reviews, 1) : 0;

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
إدارة التقييمات
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
    --sidebar-w:270px;
    --header-h:78px;
    --radius-lg:22px;
    --radius-md:16px;
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
        radial-gradient(circle at 100% 0%, rgba(212,175,55,.05), transparent 40%),
        var(--bg);
    color:var(--text);
    min-height:100vh;
}

a{ color:inherit; }

/* ================= SIDEBAR ================= */
.sidebar{
    position:fixed;
    top:0;
    right:0;
    width:var(--sidebar-w);
    height:100vh;
    background:linear-gradient(180deg, var(--primary-dark), var(--primary));
    color:#fff;
    display:flex;
    flex-direction:column;
    z-index:100;
    transition:transform .35s ease;
    box-shadow:-2px 0 30px rgba(0,0,0,.08);
}

.sidebar-brand{
    display:flex;
    align-items:center;
    gap:12px;
    padding:26px 22px;
    border-bottom:1px solid rgba(255,255,255,.1);
}

.sidebar-brand .logo{
    width:44px;
    height:44px;
    border-radius:12px;
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--primary-dark);
    font-size:19px;
    flex-shrink:0;
}

.sidebar-brand .name{
    font-size:18px;
    font-weight:800;
}

.sidebar-brand .name small{
    display:block;
    font-size:11px;
    font-weight:500;
    color:rgba(255,255,255,.6);
}

.sidebar-nav{
    flex:1;
    overflow-y:auto;
    padding:18px 14px;
    display:flex;
    flex-direction:column;
    gap:4px;
}

.sidebar-nav .nav-label{
    font-size:11px;
    color:rgba(255,255,255,.4);
    font-weight:700;
    letter-spacing:.5px;
    padding:14px 12px 8px;
}

.sidebar-nav a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:13px 14px;
    border-radius:12px;
    text-decoration:none;
    color:rgba(255,255,255,.8);
    font-weight:600;
    font-size:14.5px;
    transition:.25s;
}

.sidebar-nav a i{
    width:20px;
    text-align:center;
    font-size:16px;
}

.sidebar-nav a:hover{
    background:rgba(255,255,255,.08);
    color:#fff;
    transform:translateX(-3px);
}

.sidebar-nav a.active{
    background:linear-gradient(135deg, var(--gold), #B8862C);
    color:var(--primary-dark);
    box-shadow:0 8px 20px rgba(212,175,55,.3);
}

.sidebar-foot{
    padding:18px 22px;
    border-top:1px solid rgba(255,255,255,.1);
}

.sidebar-foot a{
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    color:rgba(255,255,255,.7);
    font-weight:600;
    font-size:14px;
    transition:.25s;
}

.sidebar-foot a:hover{
    color:#fff;
}

/* ================= OVERLAY (mobile drawer) ================= */
.overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.45);
    z-index:99;
    opacity:0;
    pointer-events:none;
    transition:opacity .3s;
}

.overlay.show{
    opacity:1;
    pointer-events:auto;
}

/* ================= MAIN WRAP ================= */
.main-wrap{
    margin-right:var(--sidebar-w);
    min-height:100vh;
    display:flex;
    flex-direction:column;
    transition:margin .35s ease;
}

/* ================= TOP HEADER ================= */
.topbar{
    height:var(--header-h);
    background:var(--white);
    border-bottom:1px solid var(--border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 28px;
    position:sticky;
    top:0;
    z-index:60;
}

.topbar-left{
    display:flex;
    align-items:center;
    gap:16px;
}

.burger{
    display:none;
    width:42px;
    height:42px;
    border-radius:12px;
    background:var(--bg);
    border:none;
    align-items:center;
    justify-content:center;
    font-size:17px;
    color:var(--primary);
    cursor:pointer;
}

.topbar-search{
    display:flex;
    align-items:center;
    gap:10px;
    background:var(--bg);
    border:1px solid var(--border);
    border-radius:30px;
    padding:10px 18px;
    width:280px;
    max-width:100%;
}

.topbar-search input{
    border:none;
    background:transparent;
    outline:none;
    font-family:'Cairo',sans-serif;
    font-size:14px;
    width:100%;
}

.topbar-search i{
    color:var(--muted);
}

.topbar-right{
    display:flex;
    align-items:center;
    gap:10px;
}

.icon-btn{
    position:relative;
    width:44px;
    height:44px;
    border-radius:14px;
    background:var(--bg);
    border:none;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    color:var(--primary);
    cursor:pointer;
    transition:.25s;
}

.icon-btn:hover{
    background:rgba(212,175,55,.15);
    color:var(--primary-dark);
    transform:translateY(-2px);
}

.icon-btn .dot{
    position:absolute;
    top:8px;
    left:8px;
    width:8px;
    height:8px;
    border-radius:50%;
    background:var(--gold);
    border:2px solid var(--white);
}

.admin-chip{
    display:flex;
    align-items:center;
    gap:10px;
    padding:6px 14px 6px 6px;
    background:var(--bg);
    border-radius:30px;
    margin-right:6px;
}

.admin-chip .av{
    width:34px;
    height:34px;
    border-radius:50%;
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:13px;
}

.admin-chip .info{
    font-size:13px;
    font-weight:700;
    line-height:1.3;
}

.admin-chip .info small{
    display:block;
    font-weight:500;
    color:var(--muted);
    font-size:11px;
}

/* ================= PAGE CONTENT ================= */
.page{
    padding:28px;
}

.breadcrumb{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    color:var(--muted);
    font-weight:600;
    margin-bottom:14px;
}

.breadcrumb i{
    font-size:10px;
    color:var(--gold);
}

.breadcrumb .current{
    color:var(--primary);
}

.page-head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:14px;
    margin-bottom:26px;
}

.page-head h1{
    display:flex;
    align-items:center;
    gap:12px;
    font-size:28px;
    font-weight:800;
    color:var(--primary);
}

.page-head h1 .ic{
    width:48px;
    height:48px;
    border-radius:14px;
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    color:var(--primary-dark);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:19px;
}

.page-head p{
    color:var(--muted);
    font-size:14px;
    margin-top:6px;
    margin-right:60px;
}

/* ================= STATS ================= */
.stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:24px;
}

.stat-card{
    background:var(--white);
    border-radius:var(--radius-lg);
    padding:22px;
    box-shadow:var(--shadow-soft);
    display:flex;
    align-items:center;
    gap:16px;
    transition:.3s;
    animation:slideUp .5s ease both;
    position:relative;
    overflow:hidden;
}

.stat-card:hover{
    transform:translateY(-5px);
    box-shadow:var(--shadow-hover);
}

.stat-card::before{
    content:"";
    position:absolute;
    inset:0;
    opacity:.06;
    background:var(--grad);
}

.stat-card .ic{
    width:54px;
    height:54px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:21px;
    color:#fff;
    background:var(--grad);
    flex-shrink:0;
    position:relative;
    z-index:1;
}

.stat-card .num{
    font-size:26px;
    font-weight:800;
    color:var(--text);
    position:relative;
    z-index:1;
}

.stat-card .lbl{
    font-size:13px;
    color:var(--muted);
    font-weight:700;
    margin-top:2px;
    position:relative;
    z-index:1;
}

.stat-total .ic, .stat-total{ --grad:linear-gradient(135deg,#5B1028,#7A1C3B); }
.stat-avg .ic, .stat-avg{ --grad:linear-gradient(135deg,#D4AF37,#B8862C); }
.stat-five .ic, .stat-five{ --grad:linear-gradient(135deg,#198754,#14603c); }
.stat-pending .ic, .stat-pending{ --grad:linear-gradient(135deg,#fd7e14,#c85f0c); }
.stat-hidden .ic, .stat-hidden{ --grad:linear-gradient(135deg,#6c757d,#4c5257); }
.stat-approved .ic, .stat-approved{ --grad:linear-gradient(135deg,#198754,#14603c); }

/* ================= TOOLBAR / SEARCH ================= */
.toolbar{
    background:var(--white);
    border-radius:var(--radius-lg);
    padding:20px 22px;
    box-shadow:var(--shadow-soft);
    margin-bottom:22px;
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    align-items:center;
    animation:slideUp .55s ease both;
}

.field{
    display:flex;
    align-items:center;
    gap:10px;
    background:var(--bg);
    border:1.5px solid var(--border);
    border-radius:14px;
    padding:12px 16px;
    flex:1;
    min-width:180px;
    transition:.25s;
}

.field:focus-within{
    border-color:var(--gold);
    box-shadow:0 0 0 4px rgba(212,175,55,.14);
    background:var(--white);
}

.field i{
    color:var(--gold);
    font-size:14px;
}

.field input, .field select{
    border:none;
    background:transparent;
    outline:none;
    font-family:'Cairo',sans-serif;
    font-size:14px;
    width:100%;
    color:var(--text);
    font-weight:600;
}

.field.select-field{
    flex:0 0 170px;
}

/* ================= TABLE CARD ================= */
.card{
    background:var(--white);
    border-radius:var(--radius-lg);
    box-shadow:var(--shadow-soft);
    overflow:hidden;
    animation:slideUp .6s ease both;
}

.table-scroll{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:920px;
}

thead th{
    background:var(--primary);
    color:#fff;
    padding:16px;
    font-size:13.5px;
    font-weight:700;
    position:sticky;
    top:0;
    z-index:2;
    white-space:nowrap;
}

tbody tr{
    transition:background .2s;
}

tbody tr:nth-child(even){
    background:#FBFBFB;
}

tbody tr:hover{
    background:rgba(212,175,55,.08);
}

td{
    padding:16px;
    text-align:center;
    border-bottom:1px solid var(--border);
    font-size:14px;
    vertical-align:middle;
}

.idx-cell{
    color:var(--muted);
    font-weight:700;
    font-size:13px;
}

.cust-cell{
    display:flex;
    align-items:center;
    gap:10px;
    text-align:right;
    white-space:normal;
}

.avatar{
    width:38px;
    height:38px;
    border-radius:50%;
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:14px;
    flex-shrink:0;
    box-shadow:0 4px 10px rgba(91,16,40,.25);
}

.product-cell{
    font-weight:700;
    color:var(--text);
    white-space:normal;
    text-align:right;
}

.stars{
    color:var(--gold);
    font-size:19px;
    letter-spacing:2px;
    white-space:nowrap;
}

.stars .rating-num{
    display:block;
    color:var(--muted);
    font-size:11.5px;
    letter-spacing:0;
    font-weight:700;
    margin-top:2px;
}

.comment{
    max-width:280px;
    text-align:right;
    white-space:normal;
}

.comment .comment-text{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
    font-size:13.5px;
    color:var(--text);
    line-height:1.6;
}

.comment.expanded .comment-text{
    -webkit-line-clamp:unset;
}

.comment .read-more{
    display:inline-block;
    margin-top:6px;
    font-size:12px;
    font-weight:700;
    color:var(--gold);
    cursor:pointer;
    background:none;
    border:none;
    font-family:'Cairo',sans-serif;
    padding:0;
}

.date-cell{
    color:var(--muted);
    font-size:13px;
    white-space:nowrap;
}

.status{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 15px;
    border-radius:30px;
    font-size:12.5px;
    font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
    white-space:nowrap;
}

.approved{ background:#d4edda; color:#155724; }
.pending{ background:#fff3cd; color:#856404; }
.hidden-status{ background:#e2e3e5; color:#41464b; }
.rejected{ background:#f8d7da; color:#721c24; }

.actions-cell{
    display:flex;
    gap:8px;
    justify-content:center;
    flex-wrap:wrap;
}

.action{
    display:inline-flex;
    align-items:center;
    gap:7px;
    padding:10px 16px;
    border-radius:11px;
    text-decoration:none;
    font-weight:700;
    font-size:13px;
    color:#fff;
    box-shadow:0 6px 14px rgba(0,0,0,.1);
    transition:transform .2s, box-shadow .2s;
    border:none;
    cursor:pointer;
    font-family:'Cairo',sans-serif;
}

.action:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,.16);
}

.delete{
    background:linear-gradient(135deg,#dc3545,#a32734);
}

/* ================= EMPTY STATE ================= */
.empty-state{
    padding:70px 20px;
    text-align:center;
}

.empty-state .ic{
    width:96px;
    height:96px;
    border-radius:50%;
    background:var(--bg);
    color:var(--gold);
    font-size:38px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 20px;
}

.empty-state h3{
    color:var(--primary);
    font-size:20px;
    margin-bottom:8px;
}

.empty-state p{
    color:var(--muted);
    font-size:14px;
    margin-bottom:22px;
}

.empty-state button{
    border:none;
    cursor:pointer;
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    color:#fff;
    padding:13px 26px;
    border-radius:12px;
    font-weight:700;
    font-family:'Cairo',sans-serif;
    display:inline-flex;
    align-items:center;
    gap:8px;
    box-shadow:0 10px 22px rgba(91,16,40,.25);
    transition:.25s;
}

.empty-state button:hover{
    transform:translateY(-3px);
}

/* ================= ANIMATIONS ================= */
@keyframes fadeIn{
    from{opacity:0;} to{opacity:1;}
}
@keyframes slideUp{
    from{opacity:0; transform:translateY(16px);}
    to{opacity:1; transform:translateY(0);}
}

/* ================= RESPONSIVE ================= */
@media(max-width:1150px){
    .stats{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:900px){
    .sidebar{
        transform:translateX(100%);
    }
    .sidebar.open{
        transform:translateX(0);
    }
    .main-wrap{
        margin-right:0;
    }
    .burger{
        display:flex;
    }
    .topbar-search{
        display:none;
    }
}

@media(max-width:720px){
    .page{
        padding:18px;
    }
    .page-head h1{
        font-size:22px;
    }
    .page-head p{
        margin-right:0;
    }
    .stats{
        grid-template-columns:none;
        grid-auto-flow:column;
        grid-auto-columns:78%;
        overflow-x:auto;
        padding-bottom:6px;
        scroll-snap-type:x mandatory;
    }
    .stat-card{
        scroll-snap-align:start;
    }
    .field.select-field{
        flex:1 1 100%;
    }
    .admin-chip .info{
        display:none;
    }
}

</style>


</head>


<body>

<div class="overlay" id="overlay" onclick="toobToggleDrawer()"></div>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <div class="logo"><i class="fa-solid fa-gem"></i></div>
        <div class="name">
            توب سودان
            <small>لوحة التحكم</small>
        </div>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-label">الرئيسية</div>

        <a href="dashboard.php">
            <i class="fa-solid fa-gauge-high"></i>
            الرئيسية
        </a>

        <a href="orders.php">
            <i class="fa-solid fa-cart-shopping"></i>
            الطلبات
        </a>

        <a href="products.php">
            <i class="fa-solid fa-shirt"></i>
            المنتجات
        </a>

        <div class="nav-label">الإدارة</div>

        <a href="users.php">
            <i class="fa-solid fa-users"></i>
            العملاء
        </a>

        <a href="reviews.php" class="active">
            <i class="fa-solid fa-star"></i>
            التقييمات
        </a>

        <a href="settings.php">
            <i class="fa-solid fa-gear"></i>
            الإعدادات
        </a>

    </nav>

    <div class="sidebar-foot">
        <a href="../logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            تسجيل الخروج
        </a>
    </div>

</aside>

<!-- ================= MAIN ================= -->
<div class="main-wrap">

    <header class="topbar">

        <div class="topbar-left">
            <button class="burger" onclick="toobToggleDrawer()">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="topbar-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="بحث سريع...">
            </div>
        </div>

        <div class="topbar-right">
            <button class="icon-btn" title="الإشعارات">
                <i class="fa-regular fa-bell"></i>
                <span class="dot"></span>
            </button>

            <button class="icon-btn" title="الرسائل">
                <i class="fa-regular fa-envelope"></i>
            </button>

            <button class="icon-btn" title="الإعدادات">
                <i class="fa-solid fa-gear"></i>
            </button>

            <div class="admin-chip">
                <div class="av"><i class="fa-solid fa-user-tie"></i></div>
                <div class="info">
                    المدير
                    <small>لوحة الإدارة</small>
                </div>
            </div>
        </div>

    </header>

    <main class="page">

        <div class="breadcrumb">
            <span>لوحة التحكم</span>
            <i class="fa-solid fa-chevron-left"></i>
            <span class="current">التقييمات</span>
        </div>

        <div class="page-head">
            <div>
                <h1>
                    <span class="ic"><i class="fa-solid fa-star"></i></span>
                    إدارة التقييمات
                </h1>
                <p>راقب آراء عملائك وتقييمات منتجاتك في مكان واحد.</p>
            </div>
        </div>

        <!-- ================= STATS ================= -->
        <div class="stats">

            <div class="stat-card stat-total">
                <div class="ic"><i class="fa-solid fa-star-half-stroke"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $total_reviews; ?>">0</div>
                    <div class="lbl">إجمالي التقييمات</div>
                </div>
            </div>

            <div class="stat-card stat-avg">
                <div class="ic"><i class="fa-solid fa-chart-line"></i></div>
                <div>
                    <div class="num"><?php echo $average_rating; ?> / 5</div>
                    <div class="lbl">متوسط التقييم</div>
                </div>
            </div>

            <div class="stat-card stat-five">
                <div class="ic"><i class="fa-solid fa-star"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $five_star_count; ?>">0</div>
                    <div class="lbl">تقييمات 5 نجوم</div>
                </div>
            </div>

            <?php if($has_status_col){ ?>

            <div class="stat-card stat-pending">
                <div class="ic"><i class="fa-solid fa-hourglass-half"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $pending_count; ?>">0</div>
                    <div class="lbl">قيد المراجعة</div>
                </div>
            </div>

            <?php } else { ?>

            <div class="stat-card stat-pending">
                <div class="ic"><i class="fa-solid fa-comments"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $total_reviews - $five_star_count; ?>">0</div>
                    <div class="lbl">تقييمات أقل من 5 نجوم</div>
                </div>
            </div>

            <?php } ?>

        </div>

        <!-- ================= TOOLBAR ================= -->
        <div class="toolbar">

            <div class="field">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="searchCustomer" placeholder="بحث بالعميل" oninput="toobFilterTable()">
            </div>

            <div class="field">
                <i class="fa-solid fa-shirt"></i>
                <input type="text" id="searchProduct" placeholder="بحث بالمنتج" oninput="toobFilterTable()">
            </div>

            <div class="field">
                <i class="fa-solid fa-comment"></i>
                <input type="text" id="searchComment" placeholder="بحث في التعليق" oninput="toobFilterTable()">
            </div>

            <div class="field select-field">
                <i class="fa-solid fa-filter"></i>
                <select id="filterRating" onchange="toobFilterTable()">
                    <option value="">كل التقييمات</option>
                    <option value="5">5 نجوم</option>
                    <option value="4">4 نجوم</option>
                    <option value="3">3 نجوم</option>
                    <option value="2">2 نجوم</option>
                    <option value="1">نجمة واحدة</option>
                </select>
            </div>

            <div class="field select-field">
                <i class="fa-solid fa-arrow-down-wide-short"></i>
                <select id="sortOrder" onchange="toobSortTable()">
                    <option value="default">الأحدث</option>
                    <option value="oldest">الأقدم</option>
                    <option value="rating_desc">الأعلى تقييمًا</option>
                    <option value="rating_asc">الأقل تقييمًا</option>
                </select>
            </div>

        </div>

        <!-- ================= TABLE ================= -->
        <div class="card">

            <?php if($total_reviews == 0){ ?>

            <div class="empty-state">
                <div class="ic"><i class="fa-solid fa-star-half-stroke"></i></div>
                <h3>لا توجد تقييمات بعد</h3>
                <p>بمجرد أن يقيّم العملاء منتجاتك، ستظهر تقييماتهم هنا.</p>
                <button onclick="location.reload()">
                    <i class="fa-solid fa-rotate"></i>
                    تحديث الصفحة
                </button>
            </div>

            <?php } else { ?>

            <div class="table-scroll">
            <table id="reviewsTable">

            <thead>
            <tr>

                <th>#</th>
                <th>العميل</th>
                <th>المنتج</th>
                <th>التقييم</th>
                <th>التعليق</th>
                <th>التاريخ</th>
                <?php if($has_status_col){ ?><th>الحالة</th><?php } ?>
                <th>إجراء</th>

            </tr>
            </thead>

            <tbody>
            <?php

            $row_num = 0;

            foreach($all_reviews as $review){

                $row_num++;

                $status_val = toob_field($review, 'status');
                $status_class = 'pending';
                $status_label = 'قيد المراجعة';

                if($status_val == 'approved'){
                    $status_class = 'approved';
                    $status_label = 'معتمد';
                } elseif($status_val == 'hidden'){
                    $status_class = 'hidden-status';
                    $status_label = 'مخفي';
                } elseif($status_val == 'rejected'){
                    $status_class = 'rejected';
                    $status_label = 'مرفوض';
                }

                $customer_display = $review['user_name'] ?? "زائر";

            ?>

            <tr
                data-customer="<?php echo mb_strtolower($customer_display, 'UTF-8'); ?>"
                data-product="<?php echo mb_strtolower((string)$review['product_name'], 'UTF-8'); ?>"
                data-comment="<?php echo mb_strtolower((string)$review['comment'], 'UTF-8'); ?>"
                data-rating="<?php echo (int)$review['rating']; ?>"
                data-order="<?php echo (int)$review['id']; ?>"
            >

                <td class="idx-cell">#<?php echo $row_num; ?></td>

                <td>
                    <div class="cust-cell">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span><?php echo $customer_display; ?></span>
                    </div>
                </td>

                <td class="product-cell">
                    <?php echo $review['product_name']; ?>
                </td>

                <td>
                    <div class="stars">
                        <?php

                        for($i=1;$i<=5;$i++){

                        if($i <= $review['rating']){

                        echo "★";

                        }else{

                        echo "☆";

                        }

                        }

                        ?>
                        <span class="rating-num"><?php echo (int)$review['rating']; ?>/5</span>
                    </div>
                </td>

                <td>
                    <div class="comment">
                        <div class="comment-text"><?php echo $review['comment']; ?></div>
                        <button type="button" class="read-more" onclick="toobToggleComment(this)">
                            قراءة المزيد
                        </button>
                    </div>
                </td>

                <td class="date-cell">
                    <?php echo $review['created_at']; ?>
                </td>

                <?php if($has_status_col){ ?>
                <td>
                    <span class="status <?php echo $status_class; ?>">
                        <?php echo $status_label; ?>
                    </span>
                </td>
                <?php } ?>

                <td>
                    <div class="actions-cell">

                        <a href="delete_review.php?id=<?php echo $review['id']; ?>"
                        class="action delete"
                        onclick="return confirm('هل تريد حذف هذا التقييم؟');">
                            <i class="fa-solid fa-trash"></i>
                            حذف
                        </a>

                    </div>
                </td>

            </tr>

            <?php } ?>

            </tbody>

            </table>
            </div>

            <?php } ?>

        </div>

    </main>

</div>

<script>
// Mobile drawer toggle
function toobToggleDrawer(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}

// Client-side search & filter (visual only — no backend calls,
// works purely on the rows already rendered by PHP above)
function toobFilterTable(){
    var customer = document.getElementById('searchCustomer').value.trim().toLowerCase();
    var product = document.getElementById('searchProduct').value.trim().toLowerCase();
    var comment = document.getElementById('searchComment').value.trim().toLowerCase();
    var rating = document.getElementById('filterRating').value;

    var rows = document.querySelectorAll('#reviewsTable tbody tr');

    rows.forEach(function(row){
        var matches = true;

        if(customer && row.dataset.customer.indexOf(customer) === -1) matches = false;
        if(product && row.dataset.product.indexOf(product) === -1) matches = false;
        if(comment && row.dataset.comment.indexOf(comment) === -1) matches = false;
        if(rating && row.dataset.rating !== rating) matches = false;

        row.style.display = matches ? '' : 'none';
    });
}

// Client-side sort (visual only)
function toobSortTable(){
    var value = document.getElementById('sortOrder').value;
    var tbody = document.querySelector('#reviewsTable tbody');
    if(!tbody) return;

    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));

    if(value === 'oldest'){
        rows.sort(function(a,b){
            return parseInt(a.dataset.order) - parseInt(b.dataset.order);
        });
    } else if(value === 'rating_desc'){
        rows.sort(function(a,b){
            return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
        });
    } else if(value === 'rating_asc'){
        rows.sort(function(a,b){
            return parseInt(a.dataset.rating) - parseInt(b.dataset.rating);
        });
    } else {
        rows.sort(function(a,b){
            return parseInt(b.dataset.order) - parseInt(a.dataset.order);
        });
    }

    rows.forEach(function(row){ tbody.appendChild(row); });
}

// Expand / collapse long comments
function toobToggleComment(btn){
    var wrap = btn.closest('.comment');
    wrap.classList.toggle('expanded');
    btn.textContent = wrap.classList.contains('expanded') ? 'عرض أقل' : 'قراءة المزيد';
}

// Animated counters for the stat cards
document.querySelectorAll('.num[data-count]').forEach(function(el){
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var current = 0;
    var step = Math.max(1, Math.ceil(target / 40));

    var timer = setInterval(function(){
        current += step;
        if(current >= target){
            current = target;
            clearInterval(timer);
        }
        el.textContent = current;
    }, 20);
});
</script>

</body>


</html>