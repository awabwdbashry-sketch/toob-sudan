<?php

session_start();


if(!isset($_SESSION['admin_id'])){

    header("Location: ../login.php");
    exit;

}


include "../includes/db.php";



// جلب إعدادات المتجر

$result = mysqli_query($conn,"
SELECT *
FROM settings
LIMIT 1
");


$settings = mysqli_fetch_assoc($result);



// حفظ الإعدادات

if(isset($_POST['save'])){


    $store_name = trim($_POST['store_name']);

    $description = trim($_POST['description']);

    // Whitelist store_status so only 'open' or 'closed' can ever be saved
    $store_status = ($_POST['store_status'] === 'closed') ? 'closed' : 'open';

    $phone = trim($_POST['phone']);

    $email = trim($_POST['email']);

    $address = trim($_POST['address']);

    $facebook = trim($_POST['facebook']);

    $instagram = trim($_POST['instagram']);

    $whatsapp = trim($_POST['whatsapp']);

    $close_message = trim($_POST['close_message']);

    $logo = $settings['logo'];



    // رفع الشعار

    if(isset($_FILES['logo']) && $_FILES['logo']['name'] != "" && $_FILES['logo']['error'] === UPLOAD_ERR_OK){


        $logo_name = time() . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $_FILES['logo']['name']);


        if(move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            "../uploads/".$logo_name
        )){
            $logo = $logo_name;
        }


    }


    // استخدام Prepared Statement لمنع SQL Injection
    $stmt = mysqli_prepare($conn, "
        UPDATE settings SET
            store_name = ?,
            description = ?,
            store_status = ?,
            close_message = ?,
            phone = ?,
            email = ?,
            address = ?,
            facebook = ?,
            instagram = ?,
            whatsapp = ?,
            logo = ?
        WHERE id = ?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssssi",
        $store_name,
        $description,
        $store_status,
        $close_message,
        $phone,
        $email,
        $address,
        $facebook,
        $instagram,
        $whatsapp,
        $logo,
        $settings['id']
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


    header("Location: settings.php");

    exit;


}


// قيم مشتقة للعرض فقط (لا تغيّر أي منطق أو استعلام)

$channels_filled = 0;
foreach(['facebook','instagram','whatsapp'] as $ch){
    if(!empty($settings[$ch])) $channels_filled++;
}

$is_open = (isset($settings['store_status']) && $settings['store_status'] == 'open');

?>
<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">


<title>
إعدادات المتجر
</title>


<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">



<style>

:root{

--primary:#5B1028;
--primary-light:#7a1c3a;
--gold:#D4AF37;
--gold-soft:rgba(212,175,55,.14);
--bg:#F7F7F7;
--white:#FFFFFF;
--border:#ECECEC;
--text:#222222;
--muted:#777777;
--sidebar-w:270px;
--radius:20px;
--shadow:0 10px 30px rgba(91,16,40,.06);
--shadow-lift:0 18px 40px rgba(91,16,40,.12);

}


*{

margin:0;
padding:0;
box-sizing:border-box;
font-family:'Cairo',sans-serif;

}


html{ scroll-behavior:smooth; }


body{

background:var(--bg);
color:var(--text);

}


a{ color:inherit; text-decoration:none; }


::selection{ background:var(--gold-soft); }


button:focus-visible,
a:focus-visible,
input:focus-visible,
textarea:focus-visible,
select:focus-visible,
[tabindex]:focus-visible{

outline:2px solid var(--gold);
outline-offset:2px;

}


@media (prefers-reduced-motion:reduce){

*{ animation-duration:.001ms !important; transition-duration:.001ms !important; }

}



/* ===================== LAYOUT SHELL ===================== */

.shell{

display:flex;
min-height:100vh;

}



/* ---------- Sidebar ---------- */

.sidebar{

width:var(--sidebar-w);
flex-shrink:0;
background:linear-gradient(180deg,var(--primary) 0%,#42061c 100%);
color:#fff;
padding:26px 20px;
position:sticky;
top:0;
height:100vh;
overflow-y:auto;
display:flex;
flex-direction:column;
gap:26px;
z-index:40;

}


.brand{

display:flex;
align-items:center;
gap:12px;
padding:0 6px 20px;
border-bottom:1px solid rgba(255,255,255,.12);

}


.brand-mark{

width:42px;
height:42px;
border-radius:12px;
background:var(--gold);
color:var(--primary);
display:flex;
align-items:center;
justify-content:center;
font-weight:800;
font-size:18px;
flex-shrink:0;

}


.brand-name{ font-weight:800; font-size:18px; letter-spacing:.3px; }

.brand-sub{ font-size:11px; color:rgba(255,255,255,.6); margin-top:2px; }


.nav-group-label{

font-size:11px;
color:rgba(255,255,255,.45);
text-transform:uppercase;
letter-spacing:1px;
padding:0 10px;
margin-bottom:6px;
font-weight:700;

}


.nav{ display:flex; flex-direction:column; gap:4px; }


.nav-item{

display:flex;
align-items:center;
gap:12px;
padding:12px 14px;
border-radius:12px;
color:rgba(255,255,255,.8);
font-weight:600;
font-size:14.5px;
transition:background .2s ease, color .2s ease, transform .15s ease;
position:relative;

}


.nav-item i{ width:20px; text-align:center; color:rgba(255,255,255,.6); transition:color .2s ease; }


.nav-item:hover{ background:rgba(255,255,255,.08); color:#fff; transform:translateX(-2px); }

.nav-item:hover i{ color:var(--gold); }


.nav-item.active{

background:rgba(212,175,55,.16);
color:#fff;

}


.nav-item.active i{ color:var(--gold); }


.nav-item.active::before{

content:"";
position:absolute;
right:-20px;
top:8px;
bottom:8px;
width:4px;
border-radius:4px;
background:var(--gold);

}


.sidebar-foot{ margin-top:auto; padding-top:16px; border-top:1px solid rgba(255,255,255,.12); }



/* ---------- Main column ---------- */

.main{

flex:1;
min-width:0;
display:flex;
flex-direction:column;

}



/* ---------- Topbar ---------- */

.topbar{

height:72px;
background:var(--white);
border-bottom:1px solid var(--border);
display:flex;
align-items:center;
gap:16px;
padding:0 28px;
position:sticky;
top:0;
z-index:30;

}


.burger{

display:none;
width:40px;
height:40px;
border-radius:10px;
border:1px solid var(--border);
background:var(--white);
align-items:center;
justify-content:center;
cursor:pointer;
font-size:16px;
color:var(--primary);

}


.search-box{

flex:1;
max-width:380px;
display:flex;
align-items:center;
gap:10px;
background:var(--bg);
border:1px solid var(--border);
border-radius:12px;
padding:10px 14px;
color:var(--muted);
font-size:14px;

}


.search-box i{ color:var(--muted); }


.search-box input{

border:none;
background:transparent;
outline:none;
font-size:14px;
width:100%;
font-family:'Cairo',sans-serif;

}


.topbar-actions{

margin-right:auto;
display:flex;
align-items:center;
gap:10px;

}


.icon-btn{

width:42px;
height:42px;
border-radius:12px;
border:1px solid var(--border);
background:var(--white);
display:flex;
align-items:center;
justify-content:center;
position:relative;
cursor:pointer;
color:var(--primary);
font-size:16px;
transition:background .2s ease, transform .15s ease;

}


.icon-btn:hover{ background:var(--gold-soft); transform:translateY(-1px); }


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


.admin-chip{

display:flex;
align-items:center;
gap:10px;
padding:6px 8px 6px 14px;
border-radius:14px;
border:1px solid var(--border);
cursor:pointer;

}


.admin-avatar{

width:34px;
height:34px;
border-radius:50%;
background:var(--primary);
color:#fff;
display:flex;
align-items:center;
justify-content:center;
font-weight:700;
font-size:13px;

}


.admin-name{ font-size:13.5px; font-weight:700; }

.admin-role{ font-size:11px; color:var(--muted); }



/* ---------- Content ---------- */

.content{

padding:28px;
max-width:1120px;
width:100%;
margin:0 auto;

}


.breadcrumb{

font-size:13px;
color:var(--muted);
margin-bottom:14px;
display:flex;
align-items:center;
gap:8px;

}


.breadcrumb .sep{ opacity:.5; }

.breadcrumb .current{ color:var(--primary); font-weight:700; }



.page-head{

display:flex;
align-items:flex-start;
gap:18px;
margin-bottom:26px;
animation:fadeUp .5s ease both;

}


.page-head-icon{

width:58px;
height:58px;
border-radius:16px;
background:linear-gradient(135deg,var(--primary),#7a1c3a);
color:var(--gold);
display:flex;
align-items:center;
justify-content:center;
font-size:24px;
flex-shrink:0;
box-shadow:var(--shadow);

}


.page-title{ font-size:28px; font-weight:800; color:var(--primary); }

.page-sub{ font-size:14px; color:var(--muted); margin-top:4px; }



/* ---------- Stat cards ---------- */

.stats-row{

display:grid;
grid-template-columns:repeat(3,1fr);
gap:16px;
margin-bottom:28px;

}


.stat-card{

background:var(--white);
border:1px solid var(--border);
border-radius:16px;
padding:18px 20px;
display:flex;
align-items:center;
gap:14px;
box-shadow:var(--shadow);
animation:fadeUp .5s ease both;

}


.stat-icon{

width:44px;
height:44px;
border-radius:12px;
display:flex;
align-items:center;
justify-content:center;
font-size:17px;
flex-shrink:0;

}


.stat-icon.ok{ background:#e9f7ef; color:#1e9e5a; }

.stat-icon.warn{ background:#fdecec; color:#d64545; }

.stat-icon.gold{ background:var(--gold-soft); color:var(--gold); }


.stat-label{ font-size:12px; color:var(--muted); font-weight:600; }

.stat-value{ font-size:15px; font-weight:800; color:var(--text); margin-top:2px; }



/* ---------- Alerts ---------- */

.alert{

display:flex;
align-items:center;
gap:12px;
padding:14px 18px;
border-radius:14px;
font-size:14px;
font-weight:600;
margin-bottom:20px;
border:1px solid transparent;
animation:slideDown .4s ease both;

}


.alert i{ font-size:17px; }


.alert.success{ background:#eafaf1; color:#177a45; border-color:#bfe9d2; }

.alert.info{ background:var(--gold-soft); color:#8a6d1a; border-color:#e7d9a6; }



/* ---------- Cards / sections ---------- */

.grid{

display:grid;
grid-template-columns:340px 1fr;
gap:22px;
align-items:start;

}


.card{

background:var(--white);
border:1px solid var(--border);
border-radius:var(--radius);
padding:28px;
box-shadow:var(--shadow);
margin-bottom:22px;
transition:box-shadow .25s ease, transform .25s ease;
animation:fadeUp .5s ease both;

}


.card:hover{ box-shadow:var(--shadow-lift); }


.card-eyebrow{

display:inline-flex;
align-items:center;
gap:8px;
font-size:12px;
font-weight:700;
color:var(--gold);
background:var(--gold-soft);
padding:6px 12px;
border-radius:20px;
margin-bottom:14px;

}


.card-title{

font-size:19px;
font-weight:800;
color:var(--primary);
margin-bottom:4px;

}


.card-desc{ font-size:13px; color:var(--muted); margin-bottom:18px; }


.stitch{

border:none;
border-top:2px dashed var(--gold);
opacity:.5;
margin:0 0 22px;

}



.form-group{ margin-bottom:20px; }


label{

display:flex;
align-items:center;
gap:8px;
font-weight:700;
color:#444;
margin-bottom:8px;
font-size:14px;

}


label i{ color:var(--gold); width:16px; text-align:center; }


input[type=text],
input[type=email],
input[type=tel],
textarea,
select{

width:100%;
padding:14px 16px;
border:1px solid var(--border);
border-radius:12px;
font-size:14.5px;
outline:none;
background:#fcfcfc;
transition:border-color .2s ease, box-shadow .2s ease, background .2s ease;
font-family:'Cairo',sans-serif;
color:var(--text);

}


input[type=text]:focus,
input[type=email]:focus,
input[type=tel]:focus,
textarea:focus,
select:focus{

border-color:var(--gold);
background:#fff;
box-shadow:0 0 0 4px var(--gold-soft);

}


textarea{

min-height:120px;
resize:vertical;
line-height:1.7;

}


.input-icon-wrap{ position:relative; }


.input-icon-wrap i.field-icon{

position:absolute;
top:50%;
right:16px;
transform:translateY(-50%);
color:var(--muted);
font-size:14px;
pointer-events:none;

}


.input-icon-wrap input{ padding-right:42px; }



/* ---------- Status toggle ---------- */

.status-toggle-card{

display:flex;
align-items:center;
justify-content:space-between;
gap:16px;
background:#fcfcfc;
border:1px solid var(--border);
border-radius:14px;
padding:16px 18px;
margin-bottom:8px;

}


.status-text{ font-size:14px; font-weight:700; }

.status-hint{ font-size:12px; color:var(--muted); margin-top:2px; }


.toggle{

position:relative;
width:58px;
height:32px;
border-radius:20px;
background:#e2e2e2;
cursor:pointer;
transition:background .25s ease;
flex-shrink:0;

}


.toggle.on{ background:linear-gradient(90deg,#1e9e5a,#27b56a); }


.toggle-knob{

position:absolute;
top:3px;
right:3px;
width:26px;
height:26px;
border-radius:50%;
background:#fff;
box-shadow:0 2px 6px rgba(0,0,0,.2);
transition:right .25s ease;

}


.toggle.on .toggle-knob{ right:29px; }


.status-badge{

display:inline-flex;
align-items:center;
gap:8px;
font-size:12.5px;
font-weight:800;
padding:8px 14px;
border-radius:20px;
margin-top:12px;

}


.status-badge.open{ background:#e9f7ef; color:#1e9e5a; }

.status-badge.closed{ background:#fdecec; color:#d64545; }

.status-badge .blip{ width:8px; height:8px; border-radius:50%; background:currentColor; }



/* ---------- Logo upload ---------- */

.logo-card{ text-align:center; }


.logo-preview{

width:150px;
height:150px;
border-radius:24px;
margin:0 auto 18px;
border:1px solid var(--border);
overflow:hidden;
display:flex;
align-items:center;
justify-content:center;
background:linear-gradient(135deg,#fafafa,#f2f2f2);
color:var(--primary);
font-size:44px;
position:relative;

}


.logo-preview img{ width:100%; height:100%; object-fit:cover; }


.dropzone{

border:2px dashed #dcdcdc;
border-radius:16px;
padding:22px 16px;
text-align:center;
cursor:pointer;
transition:border-color .2s ease, background .2s ease;
position:relative;
background:#fcfcfc;

}


.dropzone.drag{ border-color:var(--gold); background:var(--gold-soft); }


.dropzone i{ font-size:22px; color:var(--gold); margin-bottom:8px; display:block; }


.dropzone p{ font-size:13px; color:var(--muted); font-weight:600; }


.dropzone span{ color:var(--primary); font-weight:800; }


.dropzone input[type=file]{

position:absolute;
inset:0;
opacity:0;
cursor:pointer;

}


.file-picked{ font-size:12px; color:var(--gold); font-weight:700; margin-top:8px; }



/* ---------- Save bar ---------- */

.save-bar{

position:sticky;
bottom:0;
background:linear-gradient(180deg,rgba(247,247,247,0),var(--bg) 30%);
padding:18px 0 4px;
margin-top:6px;

}


.save-btn{

width:100%;
padding:18px;
background:linear-gradient(135deg,var(--primary),#7a1c3a);
color:#fff;
border:none;
border-radius:14px;
font-size:16.5px;
font-weight:800;
cursor:pointer;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
box-shadow:0 12px 30px rgba(91,16,40,.28);
transition:transform .15s ease, box-shadow .2s ease, opacity .2s ease;
font-family:'Cairo',sans-serif;

}


.save-btn:hover{ transform:translateY(-2px); box-shadow:0 16px 36px rgba(91,16,40,.34); }


.save-btn:disabled{ opacity:.75; cursor:progress; transform:none; }


.save-btn .fa-spinner{ display:none; }


.save-btn.loading .fa-spinner{ display:inline-block; animation:spin .8s linear infinite; }

.save-btn.loading .fa-floppy-disk{ display:none; }



/* ---------- Animations ---------- */

@keyframes fadeUp{ from{ opacity:0; transform:translateY(14px);} to{ opacity:1; transform:translateY(0);} }

@keyframes slideDown{ from{ opacity:0; transform:translateY(-10px);} to{ opacity:1; transform:translateY(0);} }

@keyframes spin{ to{ transform:rotate(360deg); } }


.card:nth-child(1){ animation-delay:.02s; }

.card:nth-child(2){ animation-delay:.06s; }

.card:nth-child(3){ animation-delay:.1s; }



/* ---------- Drawer overlay (mobile) ---------- */

.overlay{

display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,.4);
z-index:35;

}


.overlay.show{ display:block; }



/* ---------- Responsive ---------- */

@media (max-width:1024px){

.grid{ grid-template-columns:1fr; }

.stats-row{ grid-template-columns:repeat(3,1fr); }

}


@media (max-width:900px){

.sidebar{

position:fixed;
right:0;
top:0;
transform:translateX(100%);
transition:transform .3s ease;
box-shadow:0 0 40px rgba(0,0,0,.25);

}


.sidebar.open{ transform:translateX(0); }


.burger{ display:flex; }


.search-box{ display:none; }

}


@media (max-width:640px){

.content{ padding:18px; }

.stats-row{

grid-template-columns:1fr;
grid-auto-flow:column;
grid-auto-columns:80%;
overflow-x:auto;
scroll-snap-type:x mandatory;
padding-bottom:6px;

}


.stat-card{ scroll-snap-align:start; }


.page-title{ font-size:22px; }

.card{ padding:20px; }

.admin-name,.admin-role{ display:none; }

.topbar{ padding:0 16px; gap:10px; }

}


@media (max-width:480px){

.page-head{ flex-direction:column; }

}

</style>


</head>


<body>


<div class="overlay" id="overlay"></div>


<div class="shell">


<!-- ===================== SIDEBAR ===================== -->

<aside class="sidebar" id="sidebar">


<div class="brand">

<div class="brand-mark">T</div>

<div>

<div class="brand-name">Toob Sudan</div>

<div class="brand-sub">لوحة تحكم المتجر</div>

</div>

</div>


<div>

<div class="nav-group-label">القائمة الرئيسية</div>

<nav class="nav">

<a class="nav-item" href="../dashboard.php"><i class="fa-solid fa-gauge"></i> لوحة التحكم</a>

<a class="nav-item" href="../orders.php"><i class="fa-solid fa-cart-shopping"></i> الطلبات</a>

<a class="nav-item" href="../products.php"><i class="fa-solid fa-box"></i> المنتجات</a>

<a class="nav-item" href="../categories.php"><i class="fa-solid fa-tags"></i> الأقسام</a>

<a class="nav-item" href="../customers.php"><i class="fa-solid fa-users"></i> العملاء</a>

</nav>

</div>


<div>

<div class="nav-group-label">النظام</div>

<nav class="nav">

<a class="nav-item active" href="settings.php"><i class="fa-solid fa-gear"></i> الإعدادات</a>

</nav>

</div>


<div class="sidebar-foot">

<a class="nav-item" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج</a>

</div>


</aside>



<!-- ===================== MAIN ===================== -->

<div class="main">


<header class="topbar">

<button class="burger" id="burgerBtn" aria-label="فتح القائمة"><i class="fa-solid fa-bars"></i></button>


<div class="search-box">

<i class="fa-solid fa-magnifying-glass"></i>

<input type="text" placeholder="بحث سريع...">

</div>


<div class="topbar-actions">

<div class="icon-btn" title="الرسائل"><i class="fa-regular fa-envelope"></i><span class="dot"></span></div>

<div class="icon-btn" title="الإشعارات"><i class="fa-regular fa-bell"></i><span class="dot"></span></div>

<a class="icon-btn" href="settings.php" title="الإعدادات"><i class="fa-solid fa-gear"></i></a>


<div class="admin-chip">

<div class="admin-avatar"><i class="fa-solid fa-user"></i></div>

<div>

<div class="admin-name">المشرف</div>

<div class="admin-role">حساب إداري</div>

</div>

<i class="fa-solid fa-chevron-down" style="font-size:11px;color:var(--muted)"></i>

</div>

</div>

</header>



<main class="content">


<div class="breadcrumb">

<span>الرئيسية</span>

<i class="fa-solid fa-chevron-left sep"></i>

<span class="current">إعدادات المتجر</span>

</div>


<div class="page-head">

<div class="page-head-icon"><i class="fa-solid fa-gear"></i></div>

<div>

<div class="page-title">إعدادات المتجر</div>

<div class="page-sub">إدارة جميع إعدادات المتجر من مكان واحد.</div>

</div>

</div>


<div class="alert success" id="successAlert" style="display:none;">

<i class="fa-solid fa-circle-check"></i>

تم حفظ الإعدادات بنجاح.

</div>


<div class="stats-row">

<div class="stat-card">

<div class="stat-icon <?php echo $is_open ? 'ok' : 'warn'; ?>">

<i class="fa-solid <?php echo $is_open ? 'fa-shop' : 'fa-store-slash'; ?>"></i>

</div>

<div>

<div class="stat-label">حالة المتجر</div>

<div class="stat-value"><?php echo $is_open ? 'مفتوح الآن' : 'مغلق حالياً'; ?></div>

</div>

</div>


<div class="stat-card">

<div class="stat-icon <?php echo !empty($settings['logo']) ? 'ok' : 'warn'; ?>">

<i class="fa-solid fa-image"></i>

</div>

<div>

<div class="stat-label">شعار المتجر</div>

<div class="stat-value"><?php echo !empty($settings['logo']) ? 'تم الرفع' : 'غير مرفوع'; ?></div>

</div>

</div>


<div class="stat-card">

<div class="stat-icon gold">

<i class="fa-solid fa-share-nodes"></i>

</div>

<div>

<div class="stat-label">قنوات التواصل</div>

<div class="stat-value"><?php echo $channels_filled; ?> من 3 مفعّلة</div>

</div>

</div>

</div>



<form method="POST" enctype="multipart/form-data" id="settingsForm">


<div class="grid">


<!-- ===== LEFT COLUMN: logo + status ===== -->

<div>


<div class="card logo-card">

<div class="card-eyebrow"><i class="fa-solid fa-image"></i> الهوية البصرية</div>

<div class="card-title">شعار المتجر</div>

<div class="card-desc">يظهر هذا الشعار في المتجر وفي لوحة التحكم.</div>


<div class="logo-preview" id="logoPreview">

<?php if(!empty($settings['logo'])){ ?>

<img src="../uploads/<?php echo htmlspecialchars($settings['logo']); ?>" id="logoImg">

<?php }else{ ?>

<i class="fa-solid fa-store" id="logoIcon"></i>

<?php } ?>

</div>


<div class="dropzone" id="dropzone">

<i class="fa-solid fa-cloud-arrow-up"></i>

<p><span>اضغط للرفع</span> أو اسحب الصورة هنا</p>

<input type="file" name="logo" id="logoInput" accept="image/*">

</div>


<div class="file-picked" id="filePicked"></div>

</div>



<div class="card">

<div class="card-eyebrow"><i class="fa-solid fa-toggle-on"></i> حالة التشغيل</div>

<div class="card-title">حالة المتجر</div>

<div class="card-desc">تحكم في ظهور المتجر للزوار.</div>

<hr class="stitch">


<div class="status-toggle-card">

<div>

<div class="status-text">المتجر مفتوح</div>

<div class="status-hint">السماح للعملاء بالتصفح والشراء</div>

</div>


<div class="toggle <?php echo $is_open ? 'on' : ''; ?>" id="statusToggle" role="switch" aria-checked="<?php echo $is_open ? 'true':'false'; ?>" tabindex="0">

<div class="toggle-knob"></div>

</div>

</div>
<div class="form-group" id="closeMessageBox"
<?php if($is_open) echo 'style="display:none;"'; ?>>

<label>
<i class="fa-solid fa-comment-dots"></i>
رسالة إغلاق المتجر
</label>

<textarea
name="close_message"
placeholder="مثال: المتجر مغلق مؤقتاً بسبب أعمال تطوير، وسنعود قريباً بإذن الله."><?php echo htmlspecialchars($settings['close_message'] ?? ''); ?></textarea>

<small>
هذه الرسالة ستظهر للزوار عند إغلاق المتجر.
</small>

</div>

<select name="store_status" id="statusSelect" style="display:none;">

<option value="open" <?php if($settings['store_status']=="open") echo "selected"; ?>>🟢 المتجر مفتوح</option>

<option value="closed" <?php if($settings['store_status']=="closed") echo "selected"; ?>>🔴 المتجر مغلق</option>

</select>


<span class="status-badge <?php echo $is_open ? 'open':'closed'; ?>" id="statusBadge">

<span class="blip"></span>

<?php echo $is_open ? 'المتجر يستقبل الطلبات الآن' : 'المتجر متوقف عن استقبال الطلبات'; ?>

</span>

</div>


</div>



<!-- ===== RIGHT COLUMN: info + contact + social ===== -->

<div>


<div class="card">

<div class="card-eyebrow"><i class="fa-solid fa-store"></i> معلومات المتجر</div>

<div class="card-title">المعلومات الأساسية</div>

<div class="card-desc">اسم المتجر ووصفه كما يظهران للعملاء.</div>

<hr class="stitch">


<div class="form-group">

<label><i class="fa-solid fa-signature"></i> اسم المتجر</label>

<input type="text" name="store_name" value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>" placeholder="اسم المتجر">

</div>


<div class="form-group">

<label><i class="fa-solid fa-align-right"></i> وصف المتجر</label>

<textarea name="description" placeholder="وصف مختصر عن المتجر"><?php echo htmlspecialchars($settings['description'] ?? ''); ?></textarea>

</div>

</div>



<div class="card">

<div class="card-eyebrow"><i class="fa-solid fa-address-card"></i> التواصل</div>

<div class="card-title">معلومات التواصل</div>

<div class="card-desc">بيانات التواصل الظاهرة للعملاء.</div>

<hr class="stitch">


<div class="form-group">

<label><i class="fa-solid fa-phone"></i> رقم الهاتف</label>

<div class="input-icon-wrap">

<input type="text" name="phone" value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>" placeholder="رقم الهاتف">

<i class="fa-solid fa-phone field-icon"></i>

</div>

</div>


<div class="form-group">

<label><i class="fa-solid fa-envelope"></i> البريد الإلكتروني</label>

<div class="input-icon-wrap">

<input type="email" name="email" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>" placeholder="example@mail.com">

<i class="fa-solid fa-envelope field-icon"></i>

</div>

</div>


<div class="form-group">

<label><i class="fa-solid fa-location-dot"></i> العنوان</label>

<textarea name="address" placeholder="عنوان المتجر"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>

</div>

</div>



<div class="card">

<div class="card-eyebrow"><i class="fa-solid fa-hashtag"></i> التواصل الاجتماعي</div>

<div class="card-title">وسائل التواصل الاجتماعي</div>

<div class="card-desc">روابط حسابات المتجر على منصات التواصل.</div>

<hr class="stitch">


<div class="form-group">

<label><i class="fa-brands fa-facebook"></i> فيسبوك</label>

<div class="input-icon-wrap">

<input type="text" name="facebook" value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>" placeholder="رابط صفحة الفيسبوك">

<i class="fa-brands fa-facebook field-icon"></i>

</div>

</div>


<div class="form-group">

<label><i class="fa-brands fa-instagram"></i> إنستجرام</label>

<div class="input-icon-wrap">

<input type="text" name="instagram" value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>" placeholder="رابط حساب إنستجرام">

<i class="fa-brands fa-instagram field-icon"></i>

</div>

</div>


<div class="form-group">

<label><i class="fa-brands fa-whatsapp"></i> واتساب</label>

<div class="input-icon-wrap">

<input type="text" name="whatsapp" value="<?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?>" placeholder="رقم واتساب">

<i class="fa-brands fa-whatsapp field-icon"></i>

</div>

</div>

</div>



<div class="save-bar">

<button type="submit" name="save" class="save-btn" id="saveBtn">

<i class="fa-solid fa-floppy-disk"></i>

<i class="fa-solid fa-spinner"></i>

حفظ الإعدادات

</button>

</div>


</div>


</div>


</form>


</main>


</div>


</div>



<script>

// ---------- Mobile drawer ----------

const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const burgerBtn = document.getElementById('burgerBtn');

function openDrawer(){ sidebar.classList.add('open'); overlay.classList.add('show'); }
function closeDrawer(){ sidebar.classList.remove('open'); overlay.classList.remove('show'); }

burgerBtn.addEventListener('click', openDrawer);
overlay.addEventListener('click', closeDrawer);


// ---------- Status toggle (kept in sync with the real <select> used by PHP) ----------

const statusToggle = document.getElementById('statusToggle');
const statusSelect = document.getElementById('statusSelect');
const statusBadge = document.getElementById('statusBadge');

function setStatus(isOpen){

    statusToggle.classList.toggle('on', isOpen);

    statusToggle.setAttribute(
        'aria-checked',
        isOpen ? 'true' : 'false'
    );

    statusSelect.value = isOpen ? 'open' : 'closed';

    statusBadge.classList.toggle('open', isOpen);
    statusBadge.classList.toggle('closed', !isOpen);

    statusBadge.innerHTML =
        '<span class="blip"></span>' +
        (isOpen
            ? 'المتجر يستقبل الطلبات الآن'
            : 'المتجر متوقف عن استقبال الطلبات');

    const messageBox = document.getElementById("closeMessageBox");

    if(isOpen){
        messageBox.style.display = "none";
    }else{
        messageBox.style.display = "block";
    }

}
statusToggle.addEventListener('click', () => {
    setStatus(!statusToggle.classList.contains('on'));
});

statusToggle.addEventListener('keydown', (e) => {
    if(e.key === 'Enter' || e.key === ' '){
        e.preventDefault();
        setStatus(!statusToggle.classList.contains('on'));
    }
});
// ---------- Logo dropzone (kept bound to the real file input) ----------

const dropzone = document.getElementById('dropzone');
const logoInput = document.getElementById('logoInput');
const logoPreview = document.getElementById('logoPreview');
const filePicked = document.getElementById('filePicked');

function previewFile(file){

if(!file) return;

filePicked.textContent = file.name;

const reader = new FileReader();

reader.onload = (e) => {

logoPreview.innerHTML = '<img src="' + e.target.result + '">';

};

reader.readAsDataURL(file);

}

logoInput.addEventListener('change', () => previewFile(logoInput.files[0]));

['dragenter','dragover'].forEach(evt => dropzone.addEventListener(evt, (e) => {

e.preventDefault();

dropzone.classList.add('drag');

}));

['dragleave','drop'].forEach(evt => dropzone.addEventListener(evt, (e) => {

e.preventDefault();

dropzone.classList.remove('drag');

}));

dropzone.addEventListener('drop', (e) => {

const file = e.dataTransfer.files[0];

if(file){

logoInput.files = e.dataTransfer.files;

previewFile(file);

}

});



// ---------- Save button loading state + success toast after redirect ----------

const form = document.getElementById('settingsForm');
const saveBtn = document.getElementById('saveBtn');
const successAlert = document.getElementById('successAlert');

form.addEventListener('submit', () => {

    saveBtn.classList.add('loading');

    setTimeout(() => {
        saveBtn.disabled = true;
    }, 0);

    try{
        localStorage.setItem('toob_settings_saved','1');
    }catch(e){}

});

window.addEventListener('DOMContentLoaded', () => {

try{

if(localStorage.getItem('toob_settings_saved') === '1'){

successAlert.style.display = 'flex';

localStorage.removeItem('toob_settings_saved');

setTimeout(() => { successAlert.style.display = 'none'; }, 4000);

}

}catch(e){}

});

</script>


</body>

</html>