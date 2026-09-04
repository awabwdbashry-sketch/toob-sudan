<?php
// ==========================================
// Toob Sudan - Admin Reports Page
// ==========================================
require_once __DIR__ . "/../includes/db.php";

/* ------------------------------------------
   Handle actions (delete / mark as read / mark as resolved)
   POST + redirect (PRG pattern) to avoid resubmission on refresh
------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id     = (int) $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'mark_read') {
        $stmt = $conn->prepare("UPDATE reports SET status = 'read' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'mark_resolved') {
        $stmt = $conn->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // preserve search/filter/page in the redirect
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    header("Location: reports.php" . ($qs !== '' ? "?$qs" : ''));
    exit;
}

/* ------------------------------------------
   Stats (totals per status)
------------------------------------------ */
$stats = ['total' => 0, 'new' => 0, 'read' => 0, 'resolved' => 0];
$res = $conn->query("SELECT status, COUNT(*) AS c FROM reports GROUP BY status");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $stats[$row['status']] = (int) $row['c'];
    }
}
$stats['total'] = $stats['new'] + $stats['read'] + $stats['resolved'];

/* ------------------------------------------
   Search + Filter
------------------------------------------ */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$allowedStatus = ['new', 'read', 'resolved'];

$where  = [];
$params = [];
$types  = '';

if ($search !== '') {
    $where[] = "(title LIKE ? OR description LIKE ? OR report_type LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}

if ($status !== '' && in_array($status, $allowedStatus, true)) {
    $where[] = "status = ?";
    $params[] = $status;
    $types .= 's';
}

$sql = "SELECT id, report_type, title, description, status, created_at FROM reports";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>التقارير | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
/*========================================================
  TOOB SUDAN — LUXURY ADMIN DASHBOARD
  Design tokens (reused from dashboard.php — unchanged)
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

*{margin:0;padding:0;box-sizing:border-box;font-family:"Cairo",sans-serif;}
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
  position:absolute;inset:0;pointer-events:none;opacity:.07;
  background-image:
    repeating-linear-gradient(45deg, var(--gold) 0 1px, transparent 1px 26px),
    repeating-linear-gradient(-45deg, var(--gold) 0 1px, transparent 1px 26px);
}

/*======================
Sidebar (identical to dashboard.php)
=======================*/
.sidebar{
  width:280px;
  background:linear-gradient(165deg,var(--burgundy) 0%,var(--burgundy-deep) 60%,var(--burgundy-darker) 100%);
  height:100vh;position:fixed;top:0;right:0;padding:34px 22px;
  box-shadow:-18px 0 40px rgba(0,0,0,.22);
  display:flex;flex-direction:column;z-index:200;
  transition:transform .45s var(--ease);overflow:hidden;
}
.sidebar-inner{position:relative;z-index:2;display:flex;flex-direction:column;height:100%;}
.logo{text-align:center;margin-bottom:36px;padding-bottom:28px;border-bottom:1px solid rgba(212,175,55,.25);}
.logo-badge{
  width:92px;height:92px;margin:0 auto 16px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  background:radial-gradient(circle at 30% 30%,rgba(212,175,55,.35),rgba(212,175,55,0) 70%);
  border:1px solid rgba(212,175,55,.5);
  box-shadow:0 0 0 6px rgba(212,175,55,.06), 0 10px 25px rgba(0,0,0,.25);
}
.logo img{width:56px;filter:drop-shadow(0 2px 6px rgba(0,0,0,.35));}
.logo h2{color:#fff;font-size:24px;font-weight:800;letter-spacing:.3px;}
.logo .tag{display:block;margin-top:6px;color:var(--gold-soft);font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;opacity:.85;}

.menu{list-style:none;flex:1;overflow-y:auto;}
.menu li{margin-bottom:8px;}
.menu a{
  position:relative;display:flex;align-items:center;gap:14px;padding:14px 16px;
  text-decoration:none;color:rgba(255,255,255,.78);border-radius:16px;
  transition:.3s var(--ease);font-weight:600;font-size:15px;overflow:hidden;
}
.menu a::before{
  content:"";position:absolute;inset:0;background:rgba(255,255,255,.06);
  transform:translateX(110%);transition:transform .35s var(--ease);border-radius:16px;
}
.menu a:hover::before,.menu a.active::before{transform:translateX(0);}
.menu a:hover,.menu a.active{color:#fff;padding-right:20px;}
.menu a.active{background:rgba(212,175,55,.14);box-shadow:inset 0 0 0 1px rgba(212,175,55,.4);}
.menu a.active .menu-icon{background:var(--gold);color:var(--burgundy-darker);box-shadow:0 4px 14px rgba(212,175,55,.4);}
.menu-icon{
  width:36px;height:36px;min-width:36px;border-radius:11px;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,.08);transition:.3s var(--ease);font-size:15px;
}
.menu a:hover .menu-icon{background:var(--gold);color:var(--burgundy-darker);}
.sidebar-foot{padding-top:18px;margin-top:10px;border-top:1px solid rgba(212,175,55,.2);text-align:center;color:rgba(255,255,255,.4);font-size:11px;letter-spacing:1px;}

.menu-toggle{
  display:none;position:fixed;top:20px;right:20px;z-index:300;
  width:50px;height:50px;border-radius:15px;
  background:linear-gradient(160deg,var(--burgundy),var(--burgundy-deep));
  color:#fff;border:1px solid rgba(212,175,55,.4);box-shadow:var(--shadow-soft);
  align-items:center;justify-content:center;font-size:19px;cursor:pointer;
}
.sidebar-overlay{
  display:none;position:fixed;inset:0;background:rgba(17,17,17,.55);
  backdrop-filter:blur(2px);z-index:190;opacity:0;transition:opacity .35s var(--ease);
}
.sidebar-overlay.show{display:block;opacity:1;}

/*======================
Content / Topbar (identical component classes to dashboard.php)
=======================*/
.content{width:calc(100% - 280px);margin-right:280px;padding:32px;}

.topbar{
  background:rgba(255,255,255,.75);
  backdrop-filter:blur(16px) saturate(160%);
  -webkit-backdrop-filter:blur(16px) saturate(160%);
  padding:22px 32px;border-radius:var(--radius-lg);
  display:flex;justify-content:space-between;align-items:center;
  box-shadow:var(--shadow-soft);border:1px solid rgba(212,175,55,.18);
  margin-bottom:30px;
}
.topbar-title .eyebrow{display:block;color:var(--gold);font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:4px;}
.topbar h1{font-size:28px;color:var(--burgundy);font-weight:800;}
.topbar-actions{display:flex;align-items:center;gap:14px;}
.topbar-icon{
  position:relative;width:50px;height:50px;min-width:50px;border-radius:16px;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,.55);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
  border:1px solid rgba(212,175,55,.35);box-shadow:var(--shadow-soft);
  color:var(--burgundy);font-size:18px;text-decoration:none;transition:.35s var(--ease);
}
.topbar-icon:hover{
  transform:translateY(-4px);
  background:linear-gradient(160deg,var(--burgundy),var(--burgundy-deep));
  color:var(--gold-soft);box-shadow:var(--shadow-lift);border-color:transparent;
}
.topbar-icon i{transition:.35s var(--ease);}
.topbar-icon:hover i{transform:rotate(-8deg) scale(1.05);}

@media(max-width:700px){.topbar-actions{width:100%;justify-content:flex-start;} .topbar{flex-direction:column;gap:16px;align-items:flex-start;}}

/*======================
Cards Area (identical to dashboard.php)
=======================*/
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
.card{
  position:relative;
  background:linear-gradient(180deg,rgba(255,255,255,.9),rgba(255,255,255,.7));
  backdrop-filter:blur(10px);padding:26px;border-radius:var(--radius-md);
  box-shadow:var(--shadow-soft);border:1px solid rgba(212,175,55,.15);
  transition:.4s var(--ease);cursor:pointer;overflow:hidden;
  opacity:0;transform:translateY(18px);animation:cardIn .6s var(--ease) forwards;
}
.card::after{
  content:"";position:absolute;top:0;left:0;right:0;height:4px;
  background:linear-gradient(90deg,var(--gold),var(--burgundy));
  transform:scaleX(0);transform-origin:right;transition:transform .45s var(--ease);
}
.card:hover::after{transform:scaleX(1);}
.card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lift);border-color:rgba(212,175,55,.4);}
@keyframes cardIn{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}
.cards .card:nth-child(1){animation-delay:.05s;}
.cards .card:nth-child(2){animation-delay:.12s;}
.cards .card:nth-child(3){animation-delay:.19s;}
.cards .card:nth-child(4){animation-delay:.26s;}
.card .icon{
  width:60px;height:60px;background:linear-gradient(145deg,var(--burgundy),var(--burgundy-deep));
  border-radius:16px;display:flex;justify-content:center;align-items:center;
  color:var(--gold-soft);font-size:24px;margin-bottom:22px;box-shadow:0 10px 22px rgba(91,16,40,.3);
}
.card h2{font-size:34px;color:var(--burgundy);margin-bottom:6px;font-weight:800;letter-spacing:.3px;}
.card p{color:var(--muted);font-weight:700;font-size:14px;}

@media(max-width:1200px){.cards{grid-template-columns:repeat(2,1fr);}}
@media(max-width:900px){
  .sidebar{transform:translateX(105%);width:280px;}
  .sidebar.open{transform:translateX(0);}
  .menu-toggle{display:flex;}
  .content{margin-right:0;width:100%;padding:90px 18px 30px;}
  .cards{grid-template-columns:repeat(4,minmax(180px,1fr));grid-auto-flow:column;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x mandatory;padding-bottom:10px;-ms-overflow-style:none;scrollbar-width:none;}
  .cards::-webkit-scrollbar{display:none;}
  .card{scroll-snap-align:start;min-width:190px;}
}

/*======================
Panel — shared card style for every content section (identical to dashboard.php)
=======================*/
.panel{background:#fff;padding:26px;border-radius:var(--radius-lg);box-shadow:var(--shadow-soft);border:1px solid rgba(212,175,55,.12);position:relative;}
.panel h2{color:var(--burgundy);margin-bottom:20px;font-size:22px;font-weight:800;display:flex;align-items:center;gap:10px;}
.panel h2::before{content:"";width:5px;height:22px;border-radius:4px;background:linear-gradient(var(--gold),var(--burgundy));display:inline-block;}
.table-box{margin-top:32px;}
.table-scroll{overflow-x:auto;border-radius:var(--radius-sm);}

table{width:100%;border-collapse:collapse;min-width:760px;}
table th{background:linear-gradient(90deg,var(--burgundy),var(--burgundy-deep));color:#fff;padding:15px;font-size:14px;font-weight:700;white-space:nowrap;}
table th:first-child{border-radius:12px 0 0 12px;}
table th:last-child{border-radius:0 12px 12px 0;}
table td{padding:16px 15px;border-bottom:1px solid var(--line);font-weight:600;font-size:14.5px;color:var(--ink);}
table tr:hover td{background:var(--gold-mist);}

.badge{display:inline-block;padding:6px 16px;border-radius:30px;font-size:12.5px;font-weight:700;background:var(--line);color:var(--muted);}
/* Report status colours — reuse the exact hues dashboard already uses for st-* order badges */
.badge.badge-new{background:rgba(212,175,55,.16);color:#9c7a17;}
.badge.badge-read{background:rgba(91,16,40,.10);color:var(--burgundy);}
.badge.badge-resolved{background:rgba(34,150,90,.12);color:#22965A;}
.badge.badge-type{background:var(--line);color:var(--muted);white-space:nowrap;}

.footer{margin-top:36px;padding:22px;text-align:center;background:#fff;border-radius:var(--radius-lg);color:var(--muted);font-weight:600;box-shadow:var(--shadow-soft);border:1px solid rgba(212,175,55,.12);font-size:13.5px;}
.footer strong{color:var(--burgundy);}

/*========================================================
  NEW — Reports-specific components
  (built with the same tokens/classes above, nothing new
  introduced outside this design language)
========================================================*/

/* Filters bar */
.filters-bar{display:flex;flex-wrap:wrap;gap:14px;margin-bottom:26px;}
.filter-search{flex:2;min-width:240px;position:relative;}
.filter-search i{position:absolute;top:50%;right:18px;transform:translateY(-50%);color:var(--muted);font-size:14px;}
.filter-search input{
  width:100%;padding:13px 46px 13px 16px;border-radius:var(--radius-sm);
  border:1px solid var(--line);background:var(--bg);font-family:"Cairo",sans-serif;
  font-size:14px;font-weight:600;color:var(--ink);transition:.3s var(--ease);
}
.filter-select{flex:1;min-width:170px;}
.filter-select select{
  width:100%;padding:13px 16px;border-radius:var(--radius-sm);
  border:1px solid var(--line);background:var(--bg);font-family:"Cairo",sans-serif;
  font-size:14px;font-weight:600;color:var(--ink);cursor:pointer;transition:.3s var(--ease);
}
.filter-search input:focus,.filter-select select:focus{outline:none;border-color:var(--gold);background:#fff;box-shadow:0 0 0 4px rgba(212,175,55,.12);}
.filter-actions{display:flex;gap:10px;}
.btn-filter{
  background:linear-gradient(135deg,var(--gold),var(--burgundy));color:#fff;border:none;
  padding:0 24px;border-radius:var(--radius-sm);font-weight:800;font-size:14px;cursor:pointer;
  transition:.3s var(--ease);box-shadow:var(--shadow-gold);display:inline-flex;align-items:center;gap:8px;
  font-family:"Cairo",sans-serif;
}
.btn-filter:hover{transform:translateY(-3px);box-shadow:var(--shadow-lift);}
.btn-reset{
  background:#fff;border:1px solid var(--line);color:var(--muted);width:48px;border-radius:var(--radius-sm);
  cursor:pointer;transition:.3s var(--ease);display:flex;align-items:center;justify-content:center;font-size:15px;
  text-decoration:none;
}
.btn-reset:hover{background:var(--gold-mist);color:var(--burgundy);border-color:rgba(212,175,55,.3);}

/* Row action buttons */
.row-actions{display:flex;gap:8px;justify-content:flex-end;}
.icon-btn{
  width:38px;height:38px;min-width:38px;border-radius:12px;border:1px solid var(--line);
  background:#fff;color:var(--muted);display:inline-flex;align-items:center;justify-content:center;
  font-size:14px;cursor:pointer;transition:.3s var(--ease);text-decoration:none;
}
.icon-btn:hover{transform:translateY(-3px);box-shadow:var(--shadow-soft);}
.icon-btn.view:hover{background:linear-gradient(160deg,var(--burgundy),var(--burgundy-deep));color:var(--gold-soft);border-color:transparent;}
.icon-btn.read:hover{background:rgba(91,16,40,.08);color:var(--burgundy);border-color:rgba(91,16,40,.25);}
.icon-btn.resolve:hover{background:rgba(34,150,90,.12);color:#22965A;border-color:rgba(34,150,90,.3);}
.icon-btn.delete:hover{background:rgba(200,50,50,.12);color:#c83232;border-color:rgba(200,50,50,.3);}
.actions-form{display:inline-flex;}

/* Empty state */
.empty-state{text-align:center;padding:70px 20px;}
.empty-state .empty-icon{
  width:110px;height:110px;margin:0 auto 22px;border-radius:50%;
  background:radial-gradient(circle at 30% 30%,rgba(212,175,55,.18),rgba(212,175,55,0) 70%);
  border:1px solid rgba(212,175,55,.35);display:flex;align-items:center;justify-content:center;
  font-size:42px;color:var(--gold);
}
.empty-state h3{color:var(--burgundy);font-weight:800;font-size:19px;margin-bottom:8px;}
.empty-state p{color:var(--muted);font-weight:600;font-size:14px;}

/* View modal */
.modal-overlay{
  position:fixed;inset:0;background:rgba(17,17,17,.55);backdrop-filter:blur(2px);
  z-index:400;display:none;align-items:center;justify-content:center;padding:20px;
  opacity:0;transition:opacity .3s var(--ease);
}
.modal-overlay.show{display:flex;opacity:1;}
.modal-box{
  background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-lift);
  max-width:520px;width:100%;overflow:hidden;transform:translateY(20px);
  transition:transform .35s var(--ease);border:1px solid rgba(212,175,55,.18);
}
.modal-overlay.show .modal-box{transform:translateY(0);}
.modal-head{background:linear-gradient(90deg,var(--burgundy),var(--burgundy-deep));padding:20px 26px;display:flex;justify-content:space-between;align-items:center;}
.modal-head h3{color:#fff;font-size:18px;font-weight:800;display:flex;align-items:center;gap:10px;}
.modal-head h3 i{color:var(--gold-soft);}
.modal-close-btn{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.12);border:none;color:#fff;font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.3s var(--ease);}
.modal-close-btn:hover{background:rgba(255,255,255,.25);}
.modal-body{padding:26px;max-height:60vh;overflow-y:auto;}
.detail-row{margin-bottom:18px;}
.detail-row:last-child{margin-bottom:0;}
.detail-label{color:var(--gold);font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;}
.detail-value{color:var(--ink);font-weight:600;font-size:14.5px;line-height:1.8;}
.modal-foot{padding:18px 26px;border-top:1px solid var(--line);display:flex;justify-content:flex-start;}
.btn-ghost-modal{background:#fff;border:1px solid var(--line);color:var(--muted);padding:11px 22px;border-radius:var(--radius-sm);font-weight:700;cursor:pointer;transition:.3s var(--ease);font-family:"Cairo",sans-serif;font-size:14px;}
.btn-ghost-modal:hover{background:var(--gold-mist);color:var(--burgundy);border-color:rgba(212,175,55,.3);}

@media(max-width:700px){
  .filters-bar{flex-direction:column;}
  .filter-actions{width:100%;}
  .btn-filter{flex:1;justify-content:center;}
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
<li><a href="Reports.php"><span class="menu-icon"><i class="fa-solid fa-gear"></i></span><span>التقارير</span></a></li>
<li><a href="../logout.php"><span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span><span>تسجيل الخروج</span></a></li>
</ul>

<div class="sidebar-foot">TOOB SUDAN ADMIN</div>

</div>
</div>

<div class="content">

<div class="topbar">
<div class="topbar-title">
<span class="eyebrow">إدارة النظام</span>
<h1>التقارير والشكاوى</h1>
</div>
<div class="topbar-actions">
<a href="dashboard.php" class="topbar-icon" title="لوحة التحكم">
<i class="fa-solid fa-house"></i>
</a>
<a href="notifications.php" class="topbar-icon" title="الإشعارات">
<i class="fa-solid fa-bell"></i>
</a>
<a href="messages.php" class="topbar-icon" title="الرسائل">
<i class="fa-solid fa-comment-dots"></i>
</a>
<a href="settings.php" class="topbar-icon" title="الإعدادات">
<i class="fa-solid fa-gear"></i>
</a>
</div>
</div>

<div class="cards">
<div class="card">
<div class="icon"><i class="fa-solid fa-layer-group"></i></div>
<h2><?= (int)$stats['total'] ?></h2>
<p>إجمالي التقارير</p>
</div>

<div class="card">
<div class="icon"><i class="fa-solid fa-star"></i></div>
<h2><?= (int)$stats['new'] ?></h2>
<p>تقارير جديدة</p>
</div>

<div class="card">
<div class="icon"><i class="fa-solid fa-envelope-open-text"></i></div>
<h2><?= (int)$stats['read'] ?></h2>
<p>تقارير مقروءة</p>
</div>

<div class="card">
<div class="icon"><i class="fa-solid fa-circle-check"></i></div>
<h2><?= (int)$stats['resolved'] ?></h2>
<p>تقارير محلولة</p>
</div>
</div>

<div class="panel table-box">
<h2>كل التقارير</h2>

<form method="GET" class="filters-bar">
<div class="filter-search">
<i class="fa-solid fa-magnifying-glass"></i>
<input type="text" name="search" placeholder="ابحث بالعنوان أو الوصف أو النوع..." value="<?= e($search) ?>">
</div>
<div class="filter-select">
<select name="status">
<option value="">كل الحالات</option>
<option value="new" <?= $status === 'new' ? 'selected' : '' ?>>جديد</option>
<option value="read" <?= $status === 'read' ? 'selected' : '' ?>>مقروء</option>
<option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>تم الحل</option>
</select>
</div>
<div class="filter-actions">
<button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> فلترة</button>
<a href="reports.php" class="btn-reset" title="إعادة تعيين"><i class="fa-solid fa-rotate-left"></i></a>
</div>
</form>

<?php
    // query string to preserve current search/filter through action forms
    $qsKeep = ($search !== '' || $status !== '')
        ? ('?' . http_build_query(['search' => $search, 'status' => $status]))
        : '';

    $statusLabel = ['new' => 'جديد', 'read' => 'مقروء', 'resolved' => 'تم الحل'];
    $statusIcon  = ['new' => 'fa-star', 'read' => 'fa-envelope-open', 'resolved' => 'fa-circle-check'];
    $statusClass = ['new' => 'badge-new', 'read' => 'badge-read', 'resolved' => 'badge-resolved'];
?>

<?php if (count($reports) > 0): ?>
<div class="table-scroll">
<table>
<thead>
<tr>
<th>#</th>
<th>النوع</th>
<th>العنوان</th>
<th>الوصف</th>
<th>الحالة</th>
<th>تاريخ الإنشاء</th>
<th>الإجراءات</th>
</tr>
</thead>
<tbody>
<?php foreach ($reports as $r): ?>
<tr>
<td>#<?= (int)$r['id'] ?></td>
<td><span class="badge badge-type"><?= e($r['report_type']) ?></span></td>
<td><strong><?= e($r['title']) ?></strong></td>
<td><?= e(mb_strimwidth($r['description'], 0, 60, '...')) ?></td>
<td>
<span class="badge <?= $statusClass[$r['status']] ?>">
<i class="fa-solid <?= $statusIcon[$r['status']] ?>"></i> <?= e($statusLabel[$r['status']]) ?>
</span>
</td>
<td><?= e(date('d M Y - h:i A', strtotime($r['created_at']))) ?></td>
<td>
<div class="row-actions">
<button type="button" class="icon-btn view"
        data-id="<?= (int)$r['id'] ?>"
        data-type="<?= e($r['report_type']) ?>"
        data-title="<?= e($r['title']) ?>"
        data-desc="<?= e($r['description']) ?>"
        data-status="<?= e($statusLabel[$r['status']]) ?>"
        data-status-class="<?= $statusClass[$r['status']] ?>"
        data-status-icon="<?= $statusIcon[$r['status']] ?>"
        data-date="<?= e(date('d M Y - h:i A', strtotime($r['created_at']))) ?>"
        title="عرض">
<i class="fa-solid fa-eye"></i>
</button>

<?php if ($r['status'] !== 'read' && $r['status'] !== 'resolved'): ?>
<form action="reports.php<?= $qsKeep ?>" method="POST" class="actions-form">
<input type="hidden" name="action" value="mark_read">
<input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
<button type="submit" class="icon-btn read" title="تحديد كمقروء">
<i class="fa-solid fa-envelope-open-text"></i>
</button>
</form>
<?php endif; ?>

<?php if ($r['status'] !== 'resolved'): ?>
<form action="reports.php<?= $qsKeep ?>" method="POST" class="actions-form">
<input type="hidden" name="action" value="mark_resolved">
<input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
<button type="submit" class="icon-btn resolve" title="تحديد كمحلول">
<i class="fa-solid fa-circle-check"></i>
</button>
</form>
<?php endif; ?>

<form action="reports.php<?= $qsKeep ?>" method="POST" class="actions-form"
      onsubmit="return confirm('هل تريد حذف التقرير رقم #<?= (int)$r['id'] ?>؟ لا يمكن التراجع عن هذا الإجراء.');">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
<button type="submit" class="icon-btn delete" title="حذف">
<i class="fa-solid fa-trash"></i>
</button>
</form>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="empty-state">
<div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
<h3>لا توجد تقارير</h3>
<p>جرّب تعديل كلمة البحث أو الفلترة، أو تحقق مرة أخرى لاحقاً.</p>
</div>
<?php endif; ?>

</div>

<div class="footer">
© <?= date("Y") ?> <strong>Toob Sudan</strong> Dashboard
</div>

</div>

<!-- View modal -->
<div class="modal-overlay" id="viewModal">
<div class="modal-box">
<div class="modal-head">
<h3><i class="fa-solid fa-file-lines"></i> تفاصيل التقرير</h3>
<button type="button" class="modal-close-btn" id="modalCloseBtn"><i class="fa-solid fa-xmark"></i></button>
</div>
<div class="modal-body">
<div class="detail-row">
<div class="detail-label">رقم التقرير</div>
<div class="detail-value" id="mv-id"></div>
</div>
<div class="detail-row">
<div class="detail-label">النوع</div>
<div class="detail-value" id="mv-type"></div>
</div>
<div class="detail-row">
<div class="detail-label">العنوان</div>
<div class="detail-value" id="mv-title"></div>
</div>
<div class="detail-row">
<div class="detail-label">الوصف</div>
<div class="detail-value" id="mv-desc"></div>
</div>
<div class="detail-row">
<div class="detail-label">الحالة</div>
<div class="detail-value" id="mv-status"></div>
</div>
<div class="detail-row">
<div class="detail-label">تاريخ الإنشاء</div>
<div class="detail-value" id="mv-date"></div>
</div>
</div>
<div class="modal-foot">
<button type="button" class="btn-ghost-modal" id="modalCloseBtn2">إغلاق</button>
</div>
</div>
</div>

<script>
/* Mobile off-canvas sidebar — identical behaviour to dashboard.php */
const sidebar=document.getElementById('sidebar');
const menuToggle=document.getElementById('menuToggle');
const overlay=document.getElementById('sidebarOverlay');
function openSidebar(){sidebar.classList.add('open');overlay.classList.add('show');document.body.style.overflow='hidden';}
function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('show');document.body.style.overflow='';}
menuToggle?.addEventListener('click',()=>{sidebar.classList.contains('open')?closeSidebar():openSidebar();});
overlay?.addEventListener('click',closeSidebar);

/* Active menu link based on current file — identical to dashboard.php */
const currentPage=location.pathname.split('/').pop() || 'dashboard.php';
document.querySelectorAll('.menu a').forEach(a=>{
  const href=a.getAttribute('href').split('/').pop();
  if(href.toLowerCase()===currentPage.toLowerCase()){a.classList.add('active');}
  a.addEventListener('click',closeSidebar);
});

/* Card hover lift — identical to dashboard.php */
document.querySelectorAll('.card').forEach(card=>{
  card.addEventListener('mouseenter',()=>{card.style.transform='translateY(-8px)';});
  card.addEventListener('mouseleave',()=>{card.style.transform='translateY(0)';});
});

/* View modal — reads data attributes already rendered by PHP, no extra queries */
const viewModal=document.getElementById('viewModal');
document.querySelectorAll('.icon-btn.view').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.getElementById('mv-id').textContent='#'+btn.dataset.id;
    document.getElementById('mv-type').textContent=btn.dataset.type;
    document.getElementById('mv-title').textContent=btn.dataset.title;
    document.getElementById('mv-desc').textContent=btn.dataset.desc;
    document.getElementById('mv-date').textContent=btn.dataset.date;
    document.getElementById('mv-status').innerHTML=
      '<span class="badge '+btn.dataset.statusClass+'"><i class="fa-solid '+btn.dataset.statusIcon+'"></i> '+btn.dataset.status+'</span>';
    viewModal.classList.add('show');
  });
});
function closeModal(){viewModal.classList.remove('show');}
document.getElementById('modalCloseBtn').addEventListener('click',closeModal);
document.getElementById('modalCloseBtn2').addEventListener('click',closeModal);
viewModal.addEventListener('click',(e)=>{if(e.target===viewModal){closeModal();}});
document.addEventListener('keydown',(e)=>{if(e.key==='Escape'){closeModal();}});
</script>

</body>
</html>