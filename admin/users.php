<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";


$users = mysqli_query($conn,"
SELECT 

users.*,

COUNT(orders.id) AS orders_count


FROM users


LEFT JOIN orders

ON orders.user_id = users.id


WHERE users.role='user'


GROUP BY users.id


ORDER BY users.id DESC

");

/* ============================================================
   Presentation-only additions below.
   The SQL query above, the session check, and the include are
   completely untouched. To power the stat cards (Total / Active
   / Blocked / New this month) the result set is simply collected
   into an array once here, then looped for the table further
   down — the exact same rows, same fields, same order, just
   read into memory instead of streamed row-by-row twice.
   ============================================================ */

$all_users = [];

while($user = mysqli_fetch_assoc($users)){
    $all_users[] = $user;
}

$total_customers   = count($all_users);
$active_customers  = 0;
$blocked_customers = 0;
$new_this_month    = 0;

foreach($all_users as $u){

    if($u['status'] == "active"){
        $active_customers++;
    } else {
        $blocked_customers++;
    }

    if(!empty($u['created_at'])){
        $created_ts = strtotime($u['created_at']);
        if($created_ts && date('Y-m', $created_ts) == date('Y-m')){
            $new_this_month++;
        }
    }
}

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
العملاء
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
.stat-active .ic, .stat-active{ --grad:linear-gradient(135deg,#198754,#14603c); }
.stat-blocked .ic, .stat-blocked{ --grad:linear-gradient(135deg,#dc3545,#a32734); }
.stat-new .ic, .stat-new{ --grad:linear-gradient(135deg,#D4AF37,#B8862C); }

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
    min-width:820px;
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
    white-space:nowrap;
}

.cust-cell{
    display:flex;
    align-items:center;
    gap:12px;
    text-align:right;
    white-space:normal;
}

.avatar{
    width:42px;
    height:42px;
    border-radius:50%;
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:15px;
    flex-shrink:0;
    box-shadow:0 4px 10px rgba(91,16,40,.25);
}

.avatar img{
    width:100%;
    height:100%;
    border-radius:50%;
    object-fit:cover;
}

.cust-name{
    font-weight:700;
}

.muted-cell{
    color:var(--muted);
}

.orders-pill{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:var(--bg);
    border-radius:30px;
    padding:6px 14px;
    font-weight:700;
    font-size:13px;
    color:var(--primary);
}

.status{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:30px;
    font-size:12.5px;
    font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
}

.active{
    background:#d4edda;
    color:#155724;
}

.blocked{
    background:#f8d7da;
    color:#721c24;
}

.actions-cell{
    display:flex;
    gap:8px;
    justify-content:center;
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
}

.action:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,.16);
}

.block{
    background:linear-gradient(135deg,#dc3545,#a32734);
}

.unblock{
    background:linear-gradient(135deg,#198754,#14603c);
}

.view{
    background:linear-gradient(135deg,var(--gold),#B8862C);
    color:var(--primary-dark) !important;
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

        <a href="users.php" class="active">
            <i class="fa-solid fa-users"></i>
            العملاء
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
            <span class="current">العملاء</span>
        </div>

        <div class="page-head">
            <div>
                <h1>
                    <span class="ic"><i class="fa-solid fa-users"></i></span>
                    إدارة العملاء
                </h1>
                <p>تابع بيانات عملائك، حالتهم، وعدد طلباتهم في مكان واحد.</p>
            </div>
        </div>

        <!-- ================= STATS ================= -->
        <div class="stats">

            <div class="stat-card stat-total">
                <div class="ic"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $total_customers; ?>">0</div>
                    <div class="lbl">إجمالي العملاء</div>
                </div>
            </div>

            <div class="stat-card stat-active">
                <div class="ic"><i class="fa-solid fa-user-check"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $active_customers; ?>">0</div>
                    <div class="lbl">عملاء نشطون</div>
                </div>
            </div>

            <div class="stat-card stat-blocked">
                <div class="ic"><i class="fa-solid fa-user-slash"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $blocked_customers; ?>">0</div>
                    <div class="lbl">عملاء موقوفون</div>
                </div>
            </div>

            <div class="stat-card stat-new">
                <div class="ic"><i class="fa-solid fa-user-plus"></i></div>
                <div>
                    <div class="num" data-count="<?php echo $new_this_month; ?>">0</div>
                    <div class="lbl">عملاء جدد هذا الشهر</div>
                </div>
            </div>

        </div>

        <!-- ================= TOOLBAR ================= -->
        <div class="toolbar">

            <div class="field">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchName" placeholder="بحث بالاسم" oninput="toobFilterTable()">
            </div>

            <div class="field">
                <i class="fa-solid fa-envelope"></i>
                <input type="text" id="searchEmail" placeholder="بحث بالإيميل" oninput="toobFilterTable()">
            </div>

            <div class="field">
                <i class="fa-solid fa-phone"></i>
                <input type="text" id="searchPhone" placeholder="بحث بالهاتف" oninput="toobFilterTable()">
            </div>

            <div class="field select-field">
                <i class="fa-solid fa-filter"></i>
                <select id="filterStatus" onchange="toobFilterTable()">
                    <option value="">كل الحالات</option>
                    <option value="active">نشط</option>
                    <option value="blocked">موقوف</option>
                </select>
            </div>

            <div class="field select-field">
                <i class="fa-solid fa-arrow-down-wide-short"></i>
                <select id="sortOrder" onchange="toobSortTable()">
                    <option value="default">الأحدث</option>
                    <option value="orders_desc">الأكثر طلبات</option>
                    <option value="name_asc">الاسم (أ-ي)</option>
                </select>
            </div>

        </div>

        <!-- ================= TABLE ================= -->
        <div class="card">

            <?php if($total_customers == 0){ ?>

            <div class="empty-state">
                <div class="ic"><i class="fa-solid fa-users-slash"></i></div>
                <h3>لا يوجد عملاء بعد</h3>
                <p>بمجرد تسجيل عملاء جدد، ستظهر بياناتهم هنا تلقائيًا.</p>
                <button onclick="location.reload()">
                    <i class="fa-solid fa-rotate"></i>
                    تحديث الصفحة
                </button>
            </div>

            <?php } else { ?>

            <div class="table-scroll">
            <table id="usersTable">

            <thead>
            <tr>

                <th>العميل</th>
                <th>الهاتف</th>
                <th>الإيميل</th>
                <th>المدينة</th>
                <th>الطلبات</th>
                <th>الحالة</th>
                <th>إجراء</th>

            </tr>
            </thead>

            <tbody>
            <?php

            foreach($all_users as $user){

                $avatar_img = toob_field($user, 'avatar');

            ?>

            <tr
                data-name="<?php echo mb_strtolower($user['name'], 'UTF-8'); ?>"
                data-email="<?php echo mb_strtolower($user['email'], 'UTF-8'); ?>"
                data-phone="<?php echo $user['phone'] ?? ''; ?>"
                data-status="<?php echo $user['status']; ?>"
                data-orders="<?php echo $user['orders_count']; ?>"
            >

                <td>
                    <div class="cust-cell">
                        <div class="avatar">
                            <?php if($avatar_img){ ?>
                            <img src="../uploads/users/<?php echo $avatar_img; ?>">
                            <?php } else { ?>
                            <?php echo toob_initial($user['name']); ?>
                            <?php } ?>
                        </div>
                        <span class="cust-name"><?php echo $user['name']; ?></span>
                    </div>
                </td>

                <td class="muted-cell">
                    <?php echo $user['phone'] ?? "لا يوجد"; ?>
                </td>

                <td class="muted-cell">
                    <?php echo $user['email']; ?>
                </td>

                <td class="muted-cell">
                    <?php echo $user['city'] ?? "لا يوجد"; ?>
                </td>

                <td>
                    <span class="orders-pill">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <?php echo $user['orders_count']; ?>
                    </span>
                </td>

                <td>
                    <span class="status <?php echo $user['status']; ?>">
                        <?php

                        if($user['status']=="active"){

                        echo "نشط";

                        }else{

                        echo "موقوف";

                        }

                        ?>
                    </span>
                </td>

                <td>
                    <div class="actions-cell">

                    <?php if($user['status']=="active"){ ?>

                    <a href="toggle_user.php?id=<?php echo $user['id']; ?>&status=blocked"
                    class="action block"
                    onclick="return confirm('إيقاف هذا العميل؟');">
                        <i class="fa-solid fa-ban"></i>
                        إيقاف
                    </a>

                    <?php }else{ ?>

                    <a href="toggle_user.php?id=<?php echo $user['id']; ?>&status=active"
                    class="action unblock">
                        <i class="fa-solid fa-check"></i>
                        تنشيط
                    </a>

                    <?php } ?>

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
    var name = document.getElementById('searchName').value.trim().toLowerCase();
    var email = document.getElementById('searchEmail').value.trim().toLowerCase();
    var phone = document.getElementById('searchPhone').value.trim();
    var status = document.getElementById('filterStatus').value;

    var rows = document.querySelectorAll('#usersTable tbody tr');

    rows.forEach(function(row){
        var matches = true;

        if(name && row.dataset.name.indexOf(name) === -1) matches = false;
        if(email && row.dataset.email.indexOf(email) === -1) matches = false;
        if(phone && row.dataset.phone.indexOf(phone) === -1) matches = false;
        if(status && row.dataset.status !== status) matches = false;

        row.style.display = matches ? '' : 'none';
    });
}

// Client-side sort (visual only)
function toobSortTable(){
    var value = document.getElementById('sortOrder').value;
    var tbody = document.querySelector('#usersTable tbody');
    if(!tbody) return;

    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));

    if(value === 'orders_desc'){
        rows.sort(function(a,b){
            return parseInt(b.dataset.orders) - parseInt(a.dataset.orders);
        });
    } else if(value === 'name_asc'){
        rows.sort(function(a,b){
            return a.dataset.name.localeCompare(b.dataset.name, 'ar');
        });
    } else {
        rows.sort(function(a,b){
            return b.dataset.orders === a.dataset.orders ? 0 : 0;
        });
        return;
    }

    rows.forEach(function(row){ tbody.appendChild(row); });
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