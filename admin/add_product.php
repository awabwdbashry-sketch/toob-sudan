<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

$success = "";
$error = "";

if (isset($_POST['add'])) {

    $name        = mysqli_real_escape_string($conn,$_POST['name']);
    $category    = (int)$_POST['category'];
    $description = mysqli_real_escape_string($conn,$_POST['description']);

    $price       = $_POST['price'];
    $old_price = empty($_POST['old_price']) ? NULL : (float)$_POST['old_price'];
    $quantity    = $_POST['quantity'];

    $color       = mysqli_real_escape_string($conn,$_POST['color']);
    $fabric      = mysqli_real_escape_string($conn,$_POST['fabric']);
    $size        = mysqli_real_escape_string($conn,$_POST['size']);
    $gender      = mysqli_real_escape_string($conn,$_POST['gender']);
    $country     = mysqli_real_escape_string($conn,$_POST['country']);
    $pattern     = mysqli_real_escape_string($conn,$_POST['pattern']);
    $occasion    = mysqli_real_escape_string($conn,$_POST['occasion']);
    $care        = mysqli_real_escape_string($conn,$_POST['care']);

    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new      = isset($_POST['is_new']) ? 1 : 0;

    $image_name = "";

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

    if(!is_dir("../uploads/products")){
        mkdir("../uploads/products",0777,true);
    }

    $image_name = time() . "_" . basename($_FILES['image']['name']);

    if(move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/".$image_name)){

        // تم رفع الصورة

    }else{

        die("فشل رفع الصورة.<br>خطأ رقم: ".$_FILES['image']['error']);

    }

}else{

    die("لم يتم استلام الصورة.");

}

    if($error==""){

        $sql="INSERT INTO products(

        category_id,
        name,
        description,
        price,
        old_price,
        quantity,
        color,
        fabric,
        size,
        gender,
        country,
        pattern,
        occasion,
        care,
        image,
        is_featured,
        is_new

        )VALUES(

        '$category',
        '$name',
        '$description',
        '$price',
        '$old_price',
        '$quantity',
        '$color',
        '$fabric',
        '$size',
        '$gender',
        '$country',
        '$pattern',
        '$occasion',
        '$care',
        '$image_name',
        '$is_featured',
        '$is_new'

        )";

        if(mysqli_query($conn,$sql)){

            header("Location: products.php?success=1");
            exit;

        }else{

            $error=mysqli_error($conn);

        }

    }

}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>إضافة منتج | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*========================================================
  TOOB SUDAN — ADD PRODUCT · PREMIUM ADMIN UI
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
  --red:#C83232;
  --grey-lux:#6b6470;

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

/* faint gold weave, purely decorative */
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

  max-width:1180px;
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
  margin-bottom:26px;
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
  margin-bottom:10px;

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

.back-btn{

  display:inline-flex;
  align-items:center;
  gap:10px;
  background:rgba(255,255,255,.08);
  border:1px solid rgba(212,175,55,.4);
  color:#fff;
  padding:13px 24px;
  border-radius:14px;
  text-decoration:none;
  font-weight:700;
  font-size:14px;
  transition:.35s var(--ease);
  backdrop-filter:blur(6px);

}

.back-btn:hover{

  background:var(--gold);
  color:var(--burgundy-darker);
  transform:translateX(-4px);
  box-shadow:var(--shadow-gold);

}

/*======================
Layout / Cards
=======================*/

form{position:relative;z-index:1;}

.section-card{

  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(255,255,255,.9));
  backdrop-filter:blur(10px);
  border-radius:var(--radius-lg);
  border:1px solid rgba(212,175,55,.16);
  box-shadow:var(--shadow-soft);
  padding:30px 32px;
  margin-bottom:24px;
  opacity:0;
  transform:translateY(18px);
  animation:fadeSlide .6s var(--ease) forwards;

}

.section-card:nth-of-type(1){animation-delay:.05s;}
.section-card:nth-of-type(2){animation-delay:.12s;}
.section-card:nth-of-type(3){animation-delay:.19s;}
.section-card:nth-of-type(4){animation-delay:.26s;}

@keyframes fadeSlide{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}

.section-head{

  display:flex;
  align-items:center;
  gap:14px;
  margin-bottom:24px;
  padding-bottom:18px;
  border-bottom:1px dashed var(--line);

}

.section-head .num{

  width:42px;
  height:42px;
  min-width:42px;
  border-radius:13px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(145deg,var(--burgundy),var(--burgundy-deep));
  color:var(--gold-soft);
  font-size:17px;
  box-shadow:0 8px 18px rgba(91,16,40,.25);

}

.section-head h2{

  font-size:19px;
  font-weight:800;
  color:var(--burgundy);
  margin-bottom:3px;

}

.section-head p{

  font-size:12.5px;
  color:var(--muted);
  font-weight:600;

}

.grid{

  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:20px;

}

.grid.grid-3{grid-template-columns:repeat(3,1fr);}

.full{grid-column:1/-1;}

/*======================
Fields
=======================*/

.field{

  display:flex;
  flex-direction:column;
  position:relative;

}

.field label{

  font-weight:700;
  margin-bottom:9px;
  color:var(--burgundy);
  font-size:14px;
  display:flex;
  align-items:center;
  gap:8px;

}

.field label i{

  width:26px;
  height:26px;
  border-radius:8px;
  background:var(--gold-mist);
  color:var(--burgundy);
  display:inline-flex;
  align-items:center;
  justify-content:center;
  font-size:12px;

}

.input-shell{position:relative;}

.field input[type="text"],
.field input[type="number"],
.field select,
.field textarea{

  width:100%;
  padding:15px 18px;
  border:1.6px solid var(--line);
  border-radius:var(--radius-sm);
  font-size:14.5px;
  font-weight:600;
  font-family:inherit;
  color:var(--ink);
  background:#fbfbfb;
  transition:.3s var(--ease);
  outline:none;

}

.field textarea{

  min-height:150px;
  resize:vertical;
  line-height:1.8;

}

.field select{cursor:pointer;}

.field input:hover,
.field select:hover,
.field textarea:hover{

  border-color:rgba(212,175,55,.55);
  box-shadow:0 4px 14px rgba(91,16,40,.06);

}

.field input:focus,
.field select:focus,
.field textarea:focus{

  border-color:var(--gold);
  background:#fff;
  box-shadow:0 0 0 4px rgba(212,175,55,.16);
  transform:translateY(-1px);

}

.field.invalid input,
.field.invalid select,
.field.invalid textarea{

  border-color:var(--red);
  background:#fdf4f4;
  box-shadow:0 0 0 4px rgba(200,50,50,.1);

}

.field-msg{

  font-size:12px;
  font-weight:700;
  color:var(--red);
  margin-top:7px;
  min-height:14px;
  display:none;
  align-items:center;
  gap:5px;

}

.field.invalid .field-msg{display:flex;}

/*======================
Image upload
=======================*/

.upload-zone{

  border:2px dashed rgba(212,175,55,.55);
  border-radius:var(--radius-lg);
  padding:38px 24px;
  text-align:center;
  cursor:pointer;
  transition:.35s var(--ease);
  background:linear-gradient(180deg,rgba(247,239,217,.4),rgba(255,255,255,.6));
  display:block;
  position:relative;

}

.upload-zone:hover,
.upload-zone.drag-over{

  background:var(--gold-mist);
  border-color:var(--gold);
  transform:translateY(-2px);
  box-shadow:var(--shadow-soft);

}

.upload-zone .upload-icon{

  width:74px;
  height:74px;
  border-radius:50%;
  margin:0 auto 18px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(145deg,var(--gold-soft),var(--gold));
  color:var(--burgundy-darker);
  font-size:28px;
  box-shadow:var(--shadow-gold);
  transition:.4s var(--ease);

}

.upload-zone:hover .upload-icon{transform:translateY(-4px) scale(1.05);}

.upload-zone h3{

  font-size:18px;
  color:var(--burgundy);
  font-weight:800;
  margin-bottom:6px;

}

.upload-zone p{color:var(--muted);font-weight:600;font-size:13px;}

.upload-zone p span{color:var(--gold);text-decoration:underline;}

#preview{

  width:220px;
  height:220px;
  object-fit:cover;
  border-radius:18px;
  margin:0 auto 18px;
  display:none;
  border:3px solid #fff;
  box-shadow:var(--shadow-lift);

}

.upload-zone.has-image{padding:24px;}

/*======================
Checkboxes
=======================*/

.checks{

  display:flex;
  gap:16px;
  flex-wrap:wrap;

}

.check-pill{

  display:flex;
  align-items:center;
  gap:10px;
  padding:14px 22px;
  border:1.6px solid var(--line);
  border-radius:30px;
  font-weight:700;
  font-size:14px;
  color:var(--ink);
  cursor:pointer;
  transition:.3s var(--ease);
  background:#fbfbfb;

}

.check-pill:hover{border-color:rgba(212,175,55,.6);}

.check-pill input{

  width:18px;
  height:18px;
  accent-color:var(--burgundy);
  cursor:pointer;

}

.check-pill:has(input:checked){

  background:var(--gold-mist);
  border-color:var(--gold);
  color:var(--burgundy);

}

/*======================
Action bar
=======================*/

.action-bar{

  display:flex;
  gap:16px;
  flex-wrap:wrap;

}

.btn{

  flex:1;
  min-width:200px;
  padding:18px 26px;
  border:none;
  border-radius:16px;
  font-size:16px;
  font-weight:800;
  cursor:pointer;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  text-decoration:none;
  transition:.35s var(--ease);
  position:relative;
  overflow:hidden;

}

.btn-save{

  background:linear-gradient(120deg,var(--burgundy),var(--burgundy-deep));
  color:#fff;
  box-shadow:0 14px 28px rgba(91,16,40,.3);

}

.btn-save:hover{

  background:linear-gradient(120deg,var(--gold),var(--gold-soft));
  color:var(--burgundy-darker);
  transform:translateY(-4px);
  box-shadow:var(--shadow-gold);

}

.btn-save.is-loading{

  pointer-events:none;
  color:transparent;

}

.btn-save .spinner{

  display:none;
  position:absolute;
  width:22px;
  height:22px;
  border-radius:50%;
  border:3px solid rgba(255,255,255,.35);
  border-top-color:#fff;
  animation:spin .7s linear infinite;

}

.btn-save.is-loading .spinner{display:block;}

@keyframes spin{to{transform:rotate(360deg);}}

.btn-cancel{

  flex:0 0 200px;
  background:#eceaec;
  color:var(--grey-lux);
  border:1.6px solid var(--line);

}

.btn-cancel:hover{

  background:#e2dee2;
  color:var(--ink);
  transform:translateY(-3px);

}

.ripple-el{

  position:absolute;
  border-radius:50%;
  background:rgba(255,255,255,.55);
  transform:scale(0);
  animation:ripple .55s ease-out;
  pointer-events:none;

}

@keyframes ripple{to{transform:scale(20);opacity:0;}}

/*======================
Alerts
=======================*/

.alert{

  padding:16px 22px;
  border-radius:14px;
  font-weight:700;
  margin-bottom:22px;
  display:flex;
  align-items:center;
  gap:12px;
  position:relative;
  z-index:1;

}

.alert.success{background:#e4f6ea;color:#1c7a49;border:1px solid rgba(34,150,90,.3);}
.alert.error{background:#fbe9e9;color:#a12727;border:1px solid rgba(200,50,50,.3);}

/*======================
Responsive
=======================*/

@media(max-width:900px){

  .grid,.grid.grid-3{grid-template-columns:1fr;}

  .top-bar{padding:24px;}

  .section-card{padding:24px 20px;}

}

@media(max-width:640px){

  body{padding:18px 12px 40px;}

  .top-bar-row{flex-direction:column;align-items:flex-start;}

  .back-btn{width:100%;justify-content:center;}

  .action-bar{flex-direction:column;}

  .btn-cancel{flex:1;}

  #preview{width:170px;height:170px;}

}

</style>
</head>

<body>

<div class="page-wrap">

  <div class="top-bar">
    <div class="top-bar-row">
      <div class="top-bar-titles">
        <h1><i class="fa-solid fa-box-open"></i> إضافة منتج جديد</h1>
        <nav class="breadcrumb">
          <a href="dashboard.php">الرئيسية</a>
          <i class="fa-solid fa-chevron-left"></i>
          <a href="products.php">المنتجات</a>
          <i class="fa-solid fa-chevron-left"></i>
          <span class="current">إضافة منتج</span>
        </nav>
      </div>
      <a href="products.php" class="back-btn"><i class="fa-solid fa-arrow-right"></i> رجوع للمنتجات</a>
    </div>
  </div>

  <?php if($success!=""){ ?>
  <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
  <?php } ?>

  <?php if($error!=""){ ?>
  <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
  <?php } ?>

  <form method="POST" enctype="multipart/form-data" id="addProductForm" novalidate>

    <!-- المعلومات الأساسية -->
    <div class="section-card">
      <div class="section-head">
        <div class="num"><i class="fa-solid fa-star"></i></div>
        <div>
          <h2>المعلومات الأساسية</h2>
          <p>البيانات الرئيسية التي تُعرّف المنتج</p>
        </div>
      </div>

      <div class="grid">

        <div class="field">
          <label><i class="fa-solid fa-tag"></i> اسم المنتج</label>
          <input type="text" name="name" required>
          <span class="field-msg"><i class="fa-solid fa-circle-exclamation"></i> هذا الحقل مطلوب</span>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-layer-group"></i> التصنيف</label>
          <select name="category" required>
            <?php
            $cats=mysqli_query($conn,"SELECT * FROM categories");
            while($cat=mysqli_fetch_assoc($cats)){
            ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
            <?php } ?>
          </select>
          <span class="field-msg"><i class="fa-solid fa-circle-exclamation"></i> الرجاء اختيار تصنيف</span>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-sack-dollar"></i> السعر</label>
          <input type="number" name="price" required>
          <span class="field-msg"><i class="fa-solid fa-circle-exclamation"></i> الرجاء إدخال السعر</span>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-percent"></i> السعر قبل الخصم</label>
          <input type="number" name="old_price">
          <span class="field-msg"><i class="fa-solid fa-circle-exclamation"></i> قيمة غير صحيحة</span>
        </div>

      </div>
    </div>

    <!-- تفاصيل المنتج -->
    <div class="section-card">
      <div class="section-head">
        <div class="num"><i class="fa-solid fa-ruler-combined"></i></div>
        <div>
          <h2>تفاصيل المنتج</h2>
          <p>المواصفات الدقيقة والخامات</p>
        </div>
      </div>

      <div class="grid grid-3">

        <div class="field">
          <label><i class="fa-solid fa-cubes-stacked"></i> الكمية</label>
          <input type="number" name="quantity" required>
          <span class="field-msg"><i class="fa-solid fa-circle-exclamation"></i> الرجاء إدخال الكمية</span>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-ruler"></i> المقاس</label>
          <select name="size">
            <option value="XS">XS</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
            <option value="Free Size" selected>Free Size</option>
          </select>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-venus-mars"></i> الفئة</label>
          <select name="gender">
            <option value="نساء">نساء</option>
            <option value="رجال">رجال</option>
            <option value="أطفال">أطفال</option>
            <option value="للجميع">للجميع</option>
          </select>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-palette"></i> اللون</label>
          <input type="text" name="color">
        </div>

        <div class="field">
          <label><i class="fa-solid fa-scroll"></i> الخامة</label>
          <input type="text" name="fabric">
        </div>

        <div class="field">
          <label><i class="fa-solid fa-earth-africa"></i> بلد الصنع</label>
          <input type="text" name="country">
        </div>

        <div class="field">
          <label><i class="fa-solid fa-brush"></i> النقشة</label>
          <input type="text" name="pattern">
        </div>

        <div class="field">
          <label><i class="fa-solid fa-calendar-star"></i> المناسبة</label>
          <input type="text" name="occasion">
        </div>

        <div class="field">
          <label><i class="fa-solid fa-soap"></i> تعليمات الغسيل</label>
          <input type="text" name="care">
        </div>

      </div>
    </div>

    <!-- الوصف -->
    <div class="section-card">
      <div class="section-head">
        <div class="num"><i class="fa-solid fa-align-right"></i></div>
        <div>
          <h2>وصف المنتج</h2>
          <p>نص تعريفي مفصّل يظهر للعميل في صفحة المنتج</p>
        </div>
      </div>

      <div class="field full">
        <textarea name="description" placeholder="اكتب وصفاً جذاباً للمنتج..."></textarea>
      </div>
    </div>

    <!-- الصورة -->
    <div class="section-card">
      <div class="section-head">
        <div class="num"><i class="fa-solid fa-image"></i></div>
        <div>
          <h2>صورة المنتج</h2>
          <p>JPG - PNG - WEBP</p>
        </div>
      </div>

      <div class="field full" id="imageField">
        <label class="upload-zone" id="uploadZone">

          <img id="preview">

          <div id="uploadText">
            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
            <h3>اسحب الصورة هنا</h3>
            <p>أو <span>اضغط للاختيار</span> — JPG · PNG · WEBP</p>
          </div>

          <input
            type="file"
            name="image"
            id="imageInput"
            accept="image/*"
            hidden
            required
            onchange="previewImage(event)">

        </label>
        <span class="field-msg"><i class="fa-solid fa-circle-exclamation"></i> الرجاء اختيار صورة للمنتج</span>
      </div>

      <div class="field full" style="margin-top:22px;">
        <div class="checks">

          <label class="check-pill">
            <input type="checkbox" name="is_featured">
            <span>⭐ منتج مميز</span>
          </label>

          <label class="check-pill">
            <input type="checkbox" name="is_new" checked>
            <span>🆕 منتج جديد</span>
          </label>

        </div>
      </div>
    </div>

    <!-- الأزرار -->
    <div class="action-bar">

      <a href="products.php" class="btn btn-cancel">
        <i class="fa-solid fa-xmark"></i> إلغاء
      </a>

      <button class="btn btn-save" name="add" id="saveBtn" type="submit">
        <span class="spinner"></span>
        <span class="btn-label"><i class="fa-solid fa-floppy-disk"></i> حفظ المنتج</span>
      </button>

    </div>

  </form>

</div>

<script>

/* ---------- Image preview + drag & drop (UI only) ---------- */

const uploadZone  = document.getElementById('uploadZone');
const imageInput  = document.getElementById('imageInput');
const previewImg  = document.getElementById('preview');
const uploadText  = document.getElementById('uploadText');

function previewImage(event){

  const file = event.target.files[0];
  if(!file) return;

  const reader = new FileReader();

  reader.onload = function(){
    previewImg.src = reader.result;
    previewImg.style.display = "block";
    uploadText.style.display = "none";
    uploadZone.classList.add('has-image');
    clearFieldError(document.getElementById('imageField'));
  };

  reader.readAsDataURL(file);

}

['dragover','dragenter'].forEach(evt=>{
  uploadZone.addEventListener(evt, e=>{
    e.preventDefault();
    uploadZone.classList.add('drag-over');
  });
});

['dragleave','dragend'].forEach(evt=>{
  uploadZone.addEventListener(evt, e=>{
    uploadZone.classList.remove('drag-over');
  });
});

uploadZone.addEventListener('drop', e=>{

  e.preventDefault();
  uploadZone.classList.remove('drag-over');

  const files = e.dataTransfer.files;

  if(files && files.length){
    imageInput.files = files;
    previewImage({ target:{ files } });
  }

});

/* ---------- Lightweight front-end validation (UI only) ---------- */

function fieldWrap(el){
  return el.closest('.field');
}

function clearFieldError(wrap){
  wrap.classList.remove('invalid');
}

function markFieldError(wrap){
  wrap.classList.add('invalid');
}

const form = document.getElementById('addProductForm');
const requiredEls = form.querySelectorAll('[required]');

requiredEls.forEach(el=>{

  const events = (el.tagName === 'SELECT') ? ['change'] : ['input','change'];

  events.forEach(evt=>{
    el.addEventListener(evt, ()=>{

      const wrap = fieldWrap(el) || document.getElementById('imageField');
      if(!wrap) return;

      if(el.type === 'file'){
        if(el.files && el.files.length){ clearFieldError(wrap); }
      }else if(el.value.trim() !== ''){
        clearFieldError(wrap);
      }

    });
  });

});

form.addEventListener('submit', function(e){

  let hasError = false;

  requiredEls.forEach(el=>{

    const wrap = fieldWrap(el) || document.getElementById('imageField');
    if(!wrap) return;

    const empty = (el.type === 'file')
      ? !(el.files && el.files.length)
      : el.value.trim() === '';

    if(empty){
      markFieldError(wrap);
      hasError = true;
    }else{
      clearFieldError(wrap);
    }

  });

  if(hasError){
    e.preventDefault();
    const firstInvalid = form.querySelector('.invalid');
    if(firstInvalid){
      firstInvalid.scrollIntoView({behavior:'smooth', block:'center'});
    }
    return;
  }

  /* valid — show loading state, let the form submit normally to PHP */
  const saveBtn = document.getElementById('saveBtn');
  saveBtn.classList.add('is-loading');

});

/* ---------- Ripple feedback on buttons ---------- */

document.querySelectorAll('.btn').forEach(btn=>{

  btn.addEventListener('click', function(e){

    const r = document.createElement('span');
    r.className = 'ripple-el';
    r.style.left = (e.offsetX - 10) + 'px';
    r.style.top  = (e.offsetY - 10) + 'px';
    r.style.width = r.style.height = '20px';

    this.style.position = 'relative';
    this.style.overflow = 'hidden';
    this.appendChild(r);

    setTimeout(()=>r.remove(), 550);

  });

});

</script>

</body>
</html>