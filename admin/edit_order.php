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


// جلب بيانات الطلب

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



// تحديث الحالة

if(isset($_POST['update'])){


    $status = $_POST['status'];


    mysqli_query($conn,"
    UPDATE orders
    SET status='$status'
    WHERE id='$id'
    ");


    header("Location: view_order.php?id=$id");
    exit;


}

/* ============================================================
   Presentation-only helpers below.
   No SQL, sessions, includes, form action/method, POST field
   names, or business logic were changed. These simply derive
   display values from data already fetched above.
   ============================================================ */

$status_map = [
    "جديد"        => ["class" => "new",        "icon" => "fa-solid fa-star"],
    "قيد التجهيز" => ["class" => "processing", "icon" => "fa-solid fa-box-open"],
    "قيد الشحن"   => ["class" => "shipping",   "icon" => "fa-solid fa-truck-fast"],
    "مكتمل"       => ["class" => "completed",  "icon" => "fa-solid fa-circle-check"],
    "ملغي"        => ["class" => "cancelled",  "icon" => "fa-solid fa-circle-xmark"],
];

$current_class = $status_map[$order['status']]['class'] ?? "new";
$current_icon  = $status_map[$order['status']]['icon'] ?? "fa-solid fa-circle";

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
تعديل حالة الطلب #<?php echo $order['id']; ?>
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
    max-width:720px;
    margin:0 auto;
    padding:44px 20px 60px;
}

/* ---------- Header ---------- */
.page-head{
    text-align:center;
    margin-bottom:26px;
    animation:fadeIn .5s ease both;
}

.eyebrow{
    display:inline-flex;
    align-items:center;
    gap:8px;
    color:var(--gold);
    font-weight:700;
    font-size:13px;
    letter-spacing:.5px;
    margin-bottom:10px;
}

.title{
    font-size:27px;
    font-weight:800;
    color:var(--primary);
}

.title span{
    color:var(--gold);
}

.head-meta{
    display:flex;
    justify-content:center;
    flex-wrap:wrap;
    gap:10px 22px;
    margin-top:14px;
    font-size:13.5px;
    color:var(--muted);
    font-weight:600;
}

.head-meta span{
    display:inline-flex;
    align-items:center;
    gap:7px;
}

.head-meta i{
    color:var(--gold);
}

/* ---------- Cards ---------- */
.card{
    background:var(--white);
    border-radius:var(--radius-lg);
    padding:30px;
    box-shadow:var(--shadow-soft);
    margin-bottom:22px;
    animation:slideUp .55s ease both;
    transition:box-shadow .3s;
}

.card:hover{
    box-shadow:var(--shadow-hover);
}

.card h3{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:17px;
    font-weight:800;
    color:var(--primary);
    margin-bottom:18px;
    padding-bottom:14px;
    border-bottom:2px solid var(--border);
}

.card h3 i{
    width:34px;
    height:34px;
    border-radius:10px;
    background:linear-gradient(135deg, rgba(91,16,40,.08), rgba(212,175,55,.12));
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
}

/* ---------- Order info rows ---------- */
.info{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:12px 0;
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
    font-weight:700;
    text-align:left;
    word-break:break-word;
}

/* ---------- Status badges ---------- */
.status{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:9px 18px;
    border-radius:30px;
    font-size:13.5px;
    font-weight:700;
    box-shadow:0 6px 14px rgba(0,0,0,.08);
}

.new{ background:#fff3cd; color:#856404; }
.processing{ background:#cfe2ff; color:#084298; }
.shipping{ background:#d1ecf1; color:#0c5460; }
.completed{ background:#d4edda; color:#155724; }
.cancelled{ background:#f8d7da; color:#842029; }

/* ---------- Live preview ---------- */
.preview{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:18px;
    flex-wrap:wrap;
    padding:8px 0 4px;
}

.preview .arrow{
    color:var(--gold);
    font-size:20px;
    animation:pulseArrow 1.6s ease-in-out infinite;
}

@keyframes pulseArrow{
    0%,100%{ transform:translateX(0); opacity:.6; }
    50%{ transform:translateX(-6px); opacity:1; }
}

[dir="rtl"] .arrow{
    transform:scaleX(-1);
}

/* ---------- Form ---------- */
label{
    font-weight:700;
    color:var(--text);
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:12px;
    font-size:15px;
}

label i{
    color:var(--gold);
}

select{
    width:100%;
    padding:18px 20px;
    border-radius:14px;
    border:2px solid var(--border);
    font-size:16px;
    font-weight:700;
    font-family:'Cairo',sans-serif;
    color:var(--text);
    background:var(--white);
    outline:none;
    box-shadow:0 4px 14px rgba(0,0,0,.04);
    transition:border-color .25s, box-shadow .25s, transform .15s;
    cursor:pointer;
    appearance:none;
    background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235B1028' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>");
    background-repeat:no-repeat;
    background-position:left 18px center;
    padding-left:44px;
}

select:hover{
    border-color:rgba(212,175,55,.6);
}

select:focus{
    border-color:var(--gold);
    box-shadow:0 0 0 4px rgba(212,175,55,.18);
    transform:translateY(-1px);
}

/* ---------- Warning box ---------- */
.warning-box{
    display:flex;
    align-items:flex-start;
    gap:14px;
    background:#FFF8E1;
    border:1px solid #F3E0A6;
    border-radius:var(--radius-md);
    padding:16px 18px;
    margin-top:20px;
    animation:fadeIn .6s ease both;
}

.warning-box i{
    color:var(--gold);
    font-size:20px;
    margin-top:2px;
}

.warning-box p{
    font-size:13.5px;
    color:#7a5c14;
    font-weight:600;
    line-height:1.7;
}

/* ---------- Buttons ---------- */
button{
    position:relative;
    overflow:hidden;
    margin-top:26px;
    width:100%;
    padding:18px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg, var(--primary), var(--primary-light));
    color:#fff;
    font-size:16.5px;
    font-weight:800;
    font-family:'Cairo',sans-serif;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    box-shadow:0 10px 24px rgba(91,16,40,.25);
    transition:transform .25s, box-shadow .25s;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 16px 32px rgba(91,16,40,.32);
}

button:active{
    transform:translateY(-1px);
}

button::after{
    content:"";
    position:absolute;
    inset:0;
    background:rgba(255,255,255,.22);
    transform:scale(0);
    border-radius:50%;
    opacity:0;
    transition:transform .5s ease, opacity .6s ease;
}

button:active::after{
    transform:scale(3);
    opacity:1;
    transition:0s;
}

.back{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    text-align:center;
    margin-top:16px;
    text-decoration:none;
    color:var(--muted);
    font-weight:700;
    font-size:14.5px;
    padding:12px;
    border-radius:12px;
    transition:.25s;
}

.back:hover{
    background:var(--bg);
    color:var(--primary);
    transform:translateY(-2px);
}

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
@media(max-width:600px){
    .container{
        padding:26px 14px 40px;
    }
    .title{
        font-size:22px;
    }
    .card{
        padding:22px;
    }
    .head-meta{
        flex-direction:column;
        gap:8px;
    }
    select{
        padding:16px 18px;
        padding-left:40px;
    }
}

</style>


</head>


<body>


<div class="container">

<div class="page-head">
    <div class="eyebrow">
        <i class="fa-solid fa-sparkles"></i>
        توب سودان &middot; إدارة الطلبات
    </div>
    <h1 class="title">
        تعديل حالة الطلب <span>#<?php echo $order['id']; ?></span>
    </h1>

    <div class="head-meta">
        <span><i class="fa-solid fa-user"></i> <?php echo $order['name']; ?></span>
        <span><i class="fa-regular fa-calendar"></i> <?php echo $order['created_at']; ?></span>
    </div>
</div>

<!-- ================= ORDER INFO ================= -->
<div class="card">
    <h3>
        <i class="fa-solid fa-clipboard-list"></i>
        ملخص الطلب
    </h3>

    <div class="info">
        <span class="info-label"><i class="fa-solid fa-hashtag"></i> رقم الطلب</span>
        <span>#<?php echo $order['id']; ?></span>
    </div>

    <div class="info">
        <span class="info-label"><i class="fa-solid fa-user"></i> اسم العميل</span>
        <span><?php echo $order['name']; ?></span>
    </div>

    <div class="info">
        <span class="info-label"><i class="fa-solid fa-sack-dollar"></i> إجمالي الطلب</span>
        <span><?php echo $order['total']; ?> جنيه</span>
    </div>

    <?php $pay = toob_field($order,'payment_method'); if($pay){ ?>
    <div class="info">
        <span class="info-label"><i class="fa-solid fa-credit-card"></i> طريقة الدفع</span>
        <span><?php echo $pay; ?></span>
    </div>
    <?php } ?>

    <div class="info">
        <span class="info-label"><i class="fa-regular fa-calendar"></i> تاريخ الطلب</span>
        <span><?php echo $order['created_at']; ?></span>
    </div>

    <div class="info">
        <span class="info-label"><i class="fa-solid fa-circle-info"></i> الحالة الحالية</span>
        <span class="status <?php echo $current_class; ?>">
            <i class="<?php echo $current_icon; ?>"></i>
            <?php echo $order['status']; ?>
        </span>
    </div>
</div>

<!-- ================= STATUS PREVIEW ================= -->
<div class="card">
    <h3>
        <i class="fa-solid fa-arrows-rotate"></i>
        معاينة التحديث
    </h3>

    <div class="preview">
        <span class="status <?php echo $current_class; ?>" id="statusFrom">
            <i class="<?php echo $current_icon; ?>"></i>
            <?php echo $order['status']; ?>
        </span>

        <i class="fa-solid fa-arrow-left arrow"></i>

        <span class="status <?php echo $current_class; ?>" id="statusTo">
            <i class="<?php echo $current_icon; ?>"></i>
            <?php echo $order['status']; ?>
        </span>
    </div>
</div>

<!-- ================= UPDATE FORM ================= -->
<div class="card">
    <h3>
        <i class="fa-solid fa-pen-to-square"></i>
        تحديث الحالة
    </h3>

    <form method="POST">

        <label for="status">
            <i class="fa-solid fa-truck-fast"></i>
            حالة الطلب
        </label>

        <select name="status" id="status" onchange="toobPreviewStatus(this.value)">

            <option value="جديد"
            <?php if($order['status']=="جديد") echo "selected"; ?>
            data-class="new" data-icon="fa-solid fa-star">
            جديد
            </option>

            <option value="قيد التجهيز"
            <?php if($order['status']=="قيد التجهيز") echo "selected"; ?>
            data-class="processing" data-icon="fa-solid fa-box-open">
            قيد التجهيز
            </option>

            <option value="قيد الشحن"
            <?php if($order['status']=="قيد الشحن") echo "selected"; ?>
            data-class="shipping" data-icon="fa-solid fa-truck-fast">
            قيد الشحن
            </option>

            <option value="مكتمل"
            <?php if($order['status']=="مكتمل") echo "selected"; ?>
            data-class="completed" data-icon="fa-solid fa-circle-check">
            مكتمل
            </option>

        </select>

        <div class="warning-box">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <p>
                تنبيه: تغيير حالة الطلب قد يرسل إشعارًا للعميل ويؤثر على تتبع الطلب.
                تأكد من اختيار الحالة الصحيحة قبل الحفظ.
            </p>
        </div>

        <button type="submit" name="update">
            <i class="fa-solid fa-check"></i>
            حفظ التعديل
        </button>

    </form>

    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="back">
        <i class="fa-solid fa-arrow-right"></i>
        رجوع للطلب
    </a>
</div>

</div>

<script>
function toobPreviewStatus(value){
    var select = document.getElementById('status');
    var opt = select.options[select.selectedIndex];
    var cls = opt.getAttribute('data-class');
    var icon = opt.getAttribute('data-icon');

    var target = document.getElementById('statusTo');
    target.className = 'status ' + cls;
    target.innerHTML = '<i class="' + icon + '"></i> ' + value;
}
</script>

</body>

</html>