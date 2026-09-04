<?php

session_start();


if(!isset($_SESSION['admin_id'])){

    header("Location: ../login.php");
    exit;

}


include "../includes/db.php";



// جلب الرسائل

$messages = mysqli_query($conn,"
SELECT *

FROM contact_messages

ORDER BY id DESC

");

/* ============================================================
   Presentation-only additions below.
   The SQL query above, the session check, and the include are
   completely untouched. To power the stat cards and the message
   feed at the same time, the result set is simply collected into
   an array once here — same rows, same fields, same order — then
   looped below instead of streamed row-by-row via mysqli.
   ============================================================ */

$all_messages = [];

while($msg = mysqli_fetch_assoc($messages)){
    $all_messages[] = $msg;
}

$total_messages = count($all_messages);
$this_month     = 0;
$unique_senders = [];

// Optional 'status'/'read' column support — only counted if the
// column actually exists on contact_messages (SELECT * includes it).
$unread_count  = 0;
$has_read_col  = false;

foreach($all_messages as $m){

    if(!empty($m['created_at'])){
        $created_ts = strtotime($m['created_at']);
        if($created_ts && date('Y-m', $created_ts) == date('Y-m')){
            $this_month++;
        }
    }

    if(!empty($m['email'])){
        $unique_senders[$m['email']] = true;
    }

    if(array_key_exists('is_read', $m)){
        $has_read_col = true;
        if(!$m['is_read']){
            $unread_count++;
        }
    } elseif(array_key_exists('status', $m)){
        $has_read_col = true;
        if($m['status'] == 'unread'){
            $unread_count++;
        }
    }
}

$unique_sender_count = count($unique_senders);

function toob_field($arr, $key){
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : null;
}

function toob_initial($name){
    $name = trim((string)$name);
    if($name === '') return '؟';
    return mb_substr($name, 0, 1, 'UTF-8');
}
?>


<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">


<title>
رسائل العملاء
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

.sidebar-nav a .badge{
    margin-right:auto;
    background:rgba(255,255,255,.18);
    color:#fff;
    font-size:11px;
    font-weight:800;
    padding:2px 8px;
    border-radius:20px;
}

.sidebar-nav a.active .badge{
    background:rgba(64,9,28,.25);
    color:var(--primary-dark);
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
    grid-template-columns:repeat(3,1fr);
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
.stat-month .ic, .stat-month{ --grad:linear-gradient(135deg,#D4AF37,#B8862C); }
.stat-senders .ic, .stat-senders{ --grad:linear-gradient(135deg,#198754,#14603c); }
.stat-unread .ic, .stat-unread{ --grad:linear-gradient(135deg,#fd7e14,#c85f0c); }

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

/* ================= MESSAGE FEED ================= */
.feed{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.msg-card{
    background:var(--white);
    border-radius:var(--radius-lg);
    padding:26px;
    box-shadow:var(--shadow-soft);
    transition:box-shadow .3s, transform .25s;
    animation:slideUp .55s ease both;
    border-right:4px solid transparent;
}

.msg-card:hover{
    box-shadow:var(--shadow-hover);
    transform:translateY(-3px);
    border-right-color:var(--gold);
}

.msg-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
    margin-bottom:18px;
    padding-bottom:16px;
    border-bottom:1px solid var(--border);
}

.msg-sender{
    display:flex;
    align-items:center;
    gap:12px;
}

.avatar{
    width:46px;
    height:46px;
    border-radius:50%;
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:16px;
    flex-shrink:0;
    box-shadow:0 4px 10px rgba(91,16,40,.25);
}

.msg-sender .sname{
    font-size:17px;
    font-weight:800;
    color:var(--primary);
}

.msg-sender .sdate{
    font-size:12.5px;
    color:var(--muted);
    font-weight:600;
    margin-top:2px;
    display:flex;
    align-items:center;
    gap:6px;
}

.msg-contact{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.msg-contact span{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:var(--bg);
    padding:9px 15px;
    border-radius:11px;
    font-size:13px;
    font-weight:600;
    color:var(--text);
}

.msg-contact i{
    color:var(--gold);
}

.msg-body{
    background:var(--bg);
    padding:20px;
    border-radius:var(--radius-md);
    line-height:2;
    color:var(--text);
    font-size:14.5px;
}

.msg-foot{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:12px;
    margin-top:18px;
}

.status{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:7px 15px;
    border-radius:30px;
    font-size:12px;
    font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
}

.read{ background:#d4edda; color:#155724; }
.unread{ background:#fff3cd; color:#856404; }

.action{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:11px 20px;
    border-radius:12px;
    text-decoration:none;
    font-weight:700;
    font-size:13.5px;
    color:#fff;
    background:linear-gradient(135deg,#dc3545,#a32734);
    box-shadow:0 6px 16px rgba(220,53,69,.25);
    transition:transform .2s, box-shadow .2s;
}

.action:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 22px rgba(220,53,69,.32);
}

/* ================= EMPTY STATE ================= */
.empty-state{
    padding:70px 20px;
    text-align:center;
    background:var(--white);
    border-radius:var(--radius-lg);
    box-shadow:var(--shadow-soft);
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
    .msg-card{
        padding:20px;
    }
    .msg-head{
        flex-direction:column;
        align-items:flex-start;
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

        <a href="reviews.php">
            <i class="fa-solid fa-star"></i>
            التقييمات
        </a>

        <a href="messages.php" class="active">
            <i class="fa-solid fa-envelope"></i>
            الرسائل
            <?php if($total_messages > 0){ ?>
            <span class="badge"><?php echo $total_messages; ?></span>
            <?php } ?>
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
            <span class="current">الرسائل</span>
        </div>

        <div class="page-head">
            <div>
                <h1>
                    <span class="ic"><i class="fa-solid fa-envelope"></i></span>
                    رسائل العملاء
                </h1>
                <p>تابع رسائل التواصل الواردة من العملاء والزوار وتفاعل معها.</p>
            </div>
        </div>

        <!-- ================= STATS ================= -->
        <div class="stats">

            <div class="stat-card stat-total">
                <div class="ic"><i class="fa-solid fa-envelope-open-text"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $total_messages; ?>">0</div>
                    <div class="lbl">إجمالي الرسائل</div>
                </div>
            </div>

            <div class="stat-card stat-month">
                <div class="ic"><i class="fa-regular fa-calendar"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $this_month; ?>">0</div>
                    <div class="lbl">هذا الشهر</div>
                </div>
            </div>

            <?php if($has_read_col){ ?>

            <div class="stat-card stat-unread">
                <div class="ic"><i class="fa-solid fa-envelope-circle-check"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $unread_count; ?>">0</div>
                    <div class="lbl">غير مقروءة</div>
                </div>
            </div>

            <?php } else { ?>

            <div class="stat-card stat-senders">
                <div class="ic"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $unique_sender_count; ?>">0</div>
                    <div class="lbl">عملاء متواصلون</div>
                </div>
            </div>

            <?php } ?>

        </div>

        <!-- ================= TOOLBAR ================= -->
        <?php if($total_messages > 0){ ?>
        <div class="toolbar">

            <div class="field">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="searchName" placeholder="بحث بالاسم" oninput="toobFilterFeed()">
            </div>

            <div class="field">
                <i class="fa-solid fa-envelope"></i>
                <input type="text" id="searchEmail" placeholder="بحث بالإيميل" oninput="toobFilterFeed()">
            </div>

            <div class="field">
                <i class="fa-solid fa-comment"></i>
                <input type="text" id="searchText" placeholder="بحث في نص الرسالة" oninput="toobFilterFeed()">
            </div>

            <div class="field select-field">
                <i class="fa-solid fa-arrow-down-wide-short"></i>
                <select id="sortOrder" onchange="toobSortFeed()">
                    <option value="default">الأحدث</option>
                    <option value="oldest">الأقدم</option>
                </select>
            </div>

        </div>
        <?php } ?>

        <!-- ================= FEED ================= -->
        <?php

        if($total_messages > 0){

        ?>

        <div class="feed" id="messagesFeed">

        <?php

        $row_order = 0;

        foreach($all_messages as $msg){

            $row_order++;

            $sender_name = $msg['name'] ?? "بدون اسم";

            $is_unread = false;
            if(array_key_exists('is_read', $msg)){
                $is_unread = !$msg['is_read'];
            } elseif(array_key_exists('status', $msg)){
                $is_unread = ($msg['status'] == 'unread');
            }

        ?>

        <div class="msg-card"
            data-name="<?php echo mb_strtolower($sender_name, 'UTF-8'); ?>"
            data-email="<?php echo mb_strtolower((string)($msg['email'] ?? ''), 'UTF-8'); ?>"
            data-text="<?php echo mb_strtolower((string)$msg['message'], 'UTF-8'); ?>"
            data-order="<?php echo $row_order; ?>"
        >

            <div class="msg-head">

                <div class="msg-sender">
                    <div class="avatar"><?php echo toob_initial($sender_name); ?></div>
                    <div>
                        <div class="sname"><?php echo $sender_name; ?></div>
                        <div class="sdate">
                            <i class="fa-regular fa-clock"></i>
                            <?php echo $msg['created_at']; ?>
                        </div>
                    </div>
                </div>

                <div class="msg-contact">
                    <span>
                        <i class="fa-solid fa-phone"></i>
                        <?php echo $msg['phone'] ?? "لا يوجد"; ?>
                    </span>

                    <span>
                        <i class="fa-solid fa-envelope"></i>
                        <?php echo $msg['email'] ?? "لا يوجد"; ?>
                    </span>
                </div>

            </div>

            <div class="msg-body">
                <?php echo $msg['message']; ?>
            </div>

            <div class="msg-foot">

                <?php if($has_read_col){ ?>
                <span class="status <?php echo $is_unread ? 'unread' : 'read'; ?>">
                    <i class="fa-solid <?php echo $is_unread ? 'fa-circle-exclamation' : 'fa-circle-check'; ?>"></i>
                    <?php echo $is_unread ? 'غير مقروءة' : 'مقروءة'; ?>
                </span>
                <?php } else { ?>
                <span></span>
                <?php } ?>

                <a href="delete_message.php?id=<?php echo $msg['id']; ?>"
                class="action"
                onclick="return confirm('هل تريد حذف هذه الرسالة؟');">
                    <i class="fa-solid fa-trash"></i>
                    حذف الرسالة
                </a>

            </div>

        </div>

        <?php } ?>

        </div>

        <?php } else { ?>

        <div class="empty-state">
            <div class="ic"><i class="fa-solid fa-envelope-open"></i></div>
            <h3>لا توجد رسائل حالياً</h3>
            <p>بمجرد أن يتواصل معك العملاء، ستظهر رسائلهم هنا.</p>
            <button onclick="location.reload()">
                <i class="fa-solid fa-rotate"></i>
                تحديث الصفحة
            </button>
        </div>

        <?php } ?>

    </main>

</div>

<script>
// Mobile drawer toggle
function toobToggleDrawer(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}

// Client-side search & filter (visual only — no backend calls,
// works purely on the cards already rendered by PHP above)
function toobFilterFeed(){
    var name = document.getElementById('searchName').value.trim().toLowerCase();
    var email = document.getElementById('searchEmail').value.trim().toLowerCase();
    var text = document.getElementById('searchText').value.trim().toLowerCase();

    var cards = document.querySelectorAll('#messagesFeed .msg-card');

    cards.forEach(function(card){
        var matches = true;

        if(name && card.dataset.name.indexOf(name) === -1) matches = false;
        if(email && card.dataset.email.indexOf(email) === -1) matches = false;
        if(text && card.dataset.text.indexOf(text) === -1) matches = false;

        card.style.display = matches ? '' : 'none';
    });
}

// Client-side sort (visual only)
function toobSortFeed(){
    var value = document.getElementById('sortOrder').value;
    var feed = document.getElementById('messagesFeed');
    if(!feed) return;

    var cards = Array.prototype.slice.call(feed.querySelectorAll('.msg-card'));

    cards.sort(function(a,b){
        var diff = parseInt(a.dataset.order) - parseInt(b.dataset.order);
        return value === 'oldest' ? -diff : diff;
    });

    cards.forEach(function(card){ feed.appendChild(card); });
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