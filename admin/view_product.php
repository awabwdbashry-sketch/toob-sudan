<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: products.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = mysqli_query($conn,"
SELECT
products.*,
categories.name AS category_name
FROM products
LEFT JOIN categories
ON products.category_id = categories.id
WHERE products.id='$id'
");

if(mysqli_num_rows($sql)==0){
    die("المنتج غير موجود");
}

$product = mysqli_fetch_assoc($sql);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
<?php echo $product['name']; ?>
</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*========================================================
  TOOB SUDAN — VIEW PRODUCT · PREMIUM ADMIN UI
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

  max-width:1180px;
  margin:0 auto;
  position:relative;
  z-index:1;

}

/*======================
Breadcrumb bar
=======================*/

.crumb-bar{

  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:14px;
  margin-bottom:22px;
  opacity:0;
  animation:fadeSlide .55s var(--ease) forwards;

}

.breadcrumb{

  display:flex;
  align-items:center;
  gap:8px;
  font-size:13.5px;
  font-weight:700;
  color:var(--muted);
  flex-wrap:wrap;

}

.breadcrumb a{

  color:var(--muted);
  text-decoration:none;
  transition:.25s var(--ease);

}

.breadcrumb a:hover{color:var(--burgundy);}

.breadcrumb i{font-size:9px;color:var(--gold);opacity:.9;}

.breadcrumb .current{color:var(--burgundy);}

.back-btn{

  display:inline-flex;
  align-items:center;
  gap:10px;
  background:#fff;
  border:1.5px solid rgba(91,16,40,.16);
  color:var(--burgundy);
  padding:12px 22px;
  border-radius:14px;
  text-decoration:none;
  font-weight:700;
  font-size:13.5px;
  transition:.35s var(--ease);
  box-shadow:var(--shadow-soft);

}

.back-btn:hover{

  background:var(--burgundy);
  color:#fff;
  transform:translateX(-4px);

}

@keyframes fadeSlide{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

/*======================
Hero card
=======================*/

.hero-card{

  background:linear-gradient(180deg,rgba(255,255,255,.97),rgba(255,255,255,.92));
  backdrop-filter:blur(10px);
  border-radius:var(--radius-lg);
  border:1px solid rgba(212,175,55,.18);
  box-shadow:var(--shadow-lift);
  overflow:hidden;
  margin-bottom:24px;
  opacity:0;
  transform:translateY(18px);
  animation:fadeSlide .6s var(--ease) forwards;
  animation-delay:.08s;

}

.hero-grid{

  display:grid;
  grid-template-columns:440px 1fr;

}

.gallery{

  background:
    radial-gradient(500px 300px at 30% 0%, rgba(212,175,55,.10), transparent 60%),
    linear-gradient(180deg,#fbfaf7,#f3f1ee);
  padding:38px;
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;

}

.frame{

  padding:10px;
  border-radius:26px;
  background:linear-gradient(135deg,var(--gold),var(--burgundy));
  box-shadow:var(--shadow-lift);
  max-width:380px;
  width:100%;

}

.frame .frame-inner{

  border-radius:20px;
  overflow:hidden;
  background:#fff;

}

.frame img{

  width:100%;
  aspect-ratio:1/1;
  object-fit:cover;
  display:block;
  transition:transform .6s var(--ease);

}

.frame:hover img{transform:scale(1.06);}

.info-side{

  padding:42px 44px;
  display:flex;
  flex-direction:column;
  justify-content:center;

}

.eyebrow-row{

  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:14px;
  flex-wrap:wrap;

}

.cat-badge{

  display:inline-flex;
  align-items:center;
  gap:7px;
  background:var(--gold-mist);
  color:var(--burgundy);
  padding:8px 16px;
  border-radius:30px;
  font-weight:800;
  font-size:12.5px;

}

.id-tag{

  color:var(--muted);
  font-weight:700;
  font-size:12.5px;

}

.info-side h1{

  font-size:32px;
  color:var(--burgundy);
  font-weight:800;
  line-height:1.3;
  margin-bottom:20px;

}

.price-row{

  display:flex;
  align-items:baseline;
  gap:14px;
  flex-wrap:wrap;
  margin-bottom:8px;

}

.price-now{

  font-size:34px;
  font-weight:900;
  color:var(--burgundy);
  display:flex;
  align-items:baseline;
  gap:6px;

}

.price-now .cur{font-size:16px;font-weight:700;color:var(--gold);}

.price-old{

  font-size:19px;
  font-weight:700;
  color:var(--muted);
  text-decoration:line-through;

}

.discount-chip{

  display:none;
  align-items:center;
  gap:6px;
  background:linear-gradient(120deg,var(--red),#a12727);
  color:#fff;
  padding:6px 14px;
  border-radius:30px;
  font-weight:800;
  font-size:12.5px;

}

.discount-chip.show{display:inline-flex;}

.badges{

  margin-top:22px;
  display:flex;
  gap:10px;
  flex-wrap:wrap;

}

.badge{

  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 18px;
  border-radius:30px;
  font-size:13.5px;
  font-weight:800;
  color:#fff;
  transition:.3s var(--ease);
  box-shadow:0 8px 18px rgba(0,0,0,.08);

}

.badge:hover{transform:translateY(-3px);}

.badge.stock-green{background:linear-gradient(120deg,var(--green),#1c7a49);}
.badge.stock-orange{background:linear-gradient(120deg,var(--orange),#a86a10);}
.badge.stock-red{background:linear-gradient(120deg,var(--red),#a12727);}
.badge.featured{background:linear-gradient(120deg,var(--gold-soft),var(--gold));color:var(--burgundy-darker);}
.badge.new{background:linear-gradient(120deg,var(--blue),#245f96);}

/*======================
Details section
=======================*/

.details-card{

  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(255,255,255,.9));
  backdrop-filter:blur(10px);
  border-radius:var(--radius-lg);
  border:1px solid rgba(212,175,55,.16);
  box-shadow:var(--shadow-soft);
  padding:34px;
  margin-bottom:24px;
  opacity:0;
  transform:translateY(18px);
  animation:fadeSlide .6s var(--ease) forwards;
  animation-delay:.16s;

}

.section-title{

  display:flex;
  align-items:center;
  gap:12px;
  margin-bottom:24px;
  padding-bottom:18px;
  border-bottom:1px dashed var(--line);

}

.section-title .num{

  width:40px;
  height:40px;
  min-width:40px;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(145deg,var(--burgundy),var(--burgundy-deep));
  color:var(--gold-soft);
  font-size:16px;
  box-shadow:0 8px 18px rgba(91,16,40,.25);

}

.section-title h2{

  font-size:18px;
  font-weight:800;
  color:var(--burgundy);

}

.spec-grid{

  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:16px;

}

.spec-card{

  background:#fbfbfb;
  border:1px solid var(--line);
  border-radius:var(--radius-md);
  padding:18px;
  transition:.3s var(--ease);
  position:relative;
  overflow:hidden;

}

.spec-card::after{

  content:"";
  position:absolute;
  top:0;left:0;right:0;
  height:3px;
  background:linear-gradient(90deg,var(--gold),var(--burgundy));
  transform:scaleX(0);
  transform-origin:right;
  transition:transform .4s var(--ease);

}

.spec-card:hover::after{transform:scaleX(1);}

.spec-card:hover{

  transform:translateY(-4px);
  box-shadow:var(--shadow-soft);
  border-color:rgba(212,175,55,.4);

}

.spec-card .spec-icon{

  width:38px;
  height:38px;
  border-radius:11px;
  background:var(--gold-mist);
  color:var(--burgundy);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:14px;
  margin-bottom:12px;

}

.spec-card h3{

  font-size:12.5px;
  color:var(--muted);
  font-weight:700;
  margin-bottom:6px;

}

.spec-card p{

  font-size:16.5px;
  font-weight:800;
  color:var(--burgundy);
  word-break:break-word;

}

.spec-card p:empty::before{content:"—";color:var(--muted);font-weight:700;}

/*======================
Description
=======================*/

.desc-box{

  margin-top:26px;
  padding:28px;
  background:linear-gradient(180deg,var(--gold-mist),#fff);
  border-radius:var(--radius-md);
  border:1px solid rgba(212,175,55,.25);
  line-height:2;
  font-size:15.5px;
  color:#4a4a4a;
  font-weight:500;

}

/*======================
Actions
=======================*/

.actions-card{

  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(255,255,255,.9));
  backdrop-filter:blur(10px);
  border-radius:var(--radius-lg);
  border:1px solid rgba(212,175,55,.16);
  box-shadow:var(--shadow-soft);
  padding:26px 34px;
  display:flex;
  gap:16px;
  flex-wrap:wrap;
  opacity:0;
  transform:translateY(18px);
  animation:fadeSlide .6s var(--ease) forwards;
  animation-delay:.24s;

}

.btn{

  flex:1;
  min-width:180px;
  padding:16px 26px;
  border:none;
  border-radius:15px;
  text-decoration:none;
  font-weight:800;
  font-size:15px;
  color:#fff;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  transition:.35s var(--ease);
  position:relative;
  overflow:hidden;
  min-height:52px;

}

.btn:hover{transform:translateY(-4px);box-shadow:0 16px 30px rgba(0,0,0,.16);}

.btn.edit{background:linear-gradient(120deg,var(--blue),#245f96);}
.btn.delete{background:linear-gradient(120deg,var(--red),#a12727);}
.btn.back-action{background:linear-gradient(120deg,var(--burgundy),var(--burgundy-deep));}

.ripple-el{

  position:absolute;
  border-radius:50%;
  background:rgba(255,255,255,.55);
  transform:scale(0);
  animation:ripple .55s ease-out;
  pointer-events:none;

}

@keyframes ripple{to{transform:scale(22);opacity:0;}}

/*======================
Responsive
=======================*/

@media(max-width:1000px){

  .hero-grid{grid-template-columns:1fr;}

  .gallery{padding:30px;}

  .info-side{padding:32px;}

  .spec-grid{grid-template-columns:repeat(3,1fr);}

}

@media(max-width:640px){

  body{padding:18px 12px 40px;}

  .crumb-bar{flex-direction:column;align-items:flex-start;}

  .back-btn{width:100%;justify-content:center;}

  .info-side{padding:26px 22px;}

  .info-side h1{font-size:25px;}

  .price-now{font-size:27px;}

  .spec-grid{grid-template-columns:repeat(2,1fr);}

  .details-card{padding:22px;}

  .actions-card{padding:20px;flex-direction:column;}

  .btn{width:100%;}

}

</style>
</head>

<body>

<div class="page-wrap">

  <div class="crumb-bar">
    <nav class="breadcrumb">
      <a href="dashboard.php">الرئيسية</a>
      <i class="fa-solid fa-chevron-left"></i>
      <a href="products.php">المنتجات</a>
      <i class="fa-solid fa-chevron-left"></i>
      <span class="current"><?php echo $product['name']; ?></span>
    </nav>
    <a href="products.php" class="back-btn"><i class="fa-solid fa-arrow-right"></i> الرجوع للمنتجات</a>
  </div>

  <div class="hero-card">
    <div class="hero-grid">

      <div class="gallery">
        <div class="frame">
          <div class="frame-inner">

            <?php if($product['image']!=""){ ?>

            <img src="../uploads/products/<?php echo $product['image']; ?>">

            <?php }else{ ?>

            <img src="../assets/images/no-image.png">

            <?php } ?>

          </div>
        </div>
      </div>

      <div class="info-side">

        <div class="eyebrow-row">
          <span class="cat-badge"><i class="fa-solid fa-layer-group"></i> <?php echo $product['category_name']; ?></span>
          <span class="id-tag">#<?php echo $product['id']; ?></span>
        </div>

        <h1><?php echo $product['name']; ?></h1>

        <div class="price-row" id="priceRow"
             data-price="<?php echo $product['price']; ?>"
             data-old-price="<?php echo $product['old_price']; ?>">

          <div class="price-now">
            <?php echo number_format($product['price'],2); ?>
            <span class="cur">ج.س</span>
          </div>

          <?php if($product['old_price']>0){ ?>

          <span class="price-old"><?php echo number_format($product['old_price'],2); ?> ج.س</span>

          <?php } ?>

          <span class="discount-chip" id="discountChip"><i class="fa-solid fa-tag"></i> <span id="discountValue"></span></span>

        </div>

        <div class="badges" id="stockBadgeWrap" data-quantity="<?php echo $product['quantity']; ?>">

          <span class="badge stock-green" id="stockBadge">
            <i class="fa-solid fa-box"></i> <?php echo $product['quantity']; ?> قطعة
          </span>

          <?php if($product['is_featured']){ ?>

          <span class="badge featured"><i class="fa-solid fa-star"></i> منتج مميز</span>

          <?php } ?>

          <?php if($product['is_new']){ ?>

          <span class="badge new"><i class="fa-solid fa-sparkles"></i> جديد</span>

          <?php } ?>

        </div>

      </div>

    </div>
  </div>

  <div class="details-card">

    <div class="section-title">
      <div class="num"><i class="fa-solid fa-list-ul"></i></div>
      <h2>مواصفات المنتج</h2>
    </div>

    <div class="spec-grid">

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-layer-group"></i></div>
        <h3>التصنيف</h3>
        <p><?php echo $product['category_name']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-cubes-stacked"></i></div>
        <h3>الكمية</h3>
        <p><?php echo $product['quantity']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-palette"></i></div>
        <h3>اللون</h3>
        <p><?php echo $product['color']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-scroll"></i></div>
        <h3>الخامة</h3>
        <p><?php echo $product['fabric']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-ruler"></i></div>
        <h3>المقاس</h3>
        <p><?php echo $product['size']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-venus-mars"></i></div>
        <h3>الفئة</h3>
        <p><?php echo $product['gender']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-earth-africa"></i></div>
        <h3>بلد الصنع</h3>
        <p><?php echo $product['country']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-brush"></i></div>
        <h3>النقشة</h3>
        <p><?php echo $product['pattern']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-calendar-star"></i></div>
        <h3>المناسبة</h3>
        <p><?php echo $product['occasion']; ?></p>
      </div>

      <div class="spec-card">
        <div class="spec-icon"><i class="fa-solid fa-soap"></i></div>
        <h3>تعليمات الغسيل</h3>
        <p><?php echo $product['care']; ?></p>
      </div>

    </div>

    <div class="desc-box">

      <h2 style="margin-bottom:15px;color:#5B1028;font-size:18px;">وصف المنتج</h2>

      <p>

      <?php
      echo !empty($product['description'])
      ? nl2br($product['description'])
      : "لا يوجد وصف لهذا المنتج.";
      ?>

      </p>

    </div>

  </div>

  <div class="actions-card">

    <a
      href="edit_product.php?id=<?php echo $product['id']; ?>"
      class="btn edit">
      <i class="fa-solid fa-pen"></i>
      تعديل المنتج
    </a>

    <a
      href="delete_product.php?id=<?php echo $product['id']; ?>"
      class="btn delete"
      onclick="return confirm('هل أنت متأكد من حذف المنتج؟')">
      <i class="fa-solid fa-trash"></i>
      حذف المنتج
    </a>

    <a
      href="products.php"
      class="btn back-action">
      <i class="fa-solid fa-arrow-right"></i>
      الرجوع للمنتجات
    </a>

  </div>

</div>

<script>

/* ---------- Discount chip (UI only, derived from PHP-rendered data attrs) ---------- */

(function(){

  const priceRow = document.getElementById('priceRow');
  const price = parseFloat(priceRow.dataset.price);
  const oldPrice = parseFloat(priceRow.dataset.oldPrice);

  if(oldPrice && oldPrice > price){

    const pct = Math.round(((oldPrice - price) / oldPrice) * 100);
    document.getElementById('discountValue').textContent = 'خصم ' + pct + '%';
    document.getElementById('discountChip').classList.add('show');

  }

})();

/* ---------- Stock badge color (UI only, derived from quantity data attr) ---------- */

(function(){

  const wrap = document.getElementById('stockBadgeWrap');
  const qty = parseInt(wrap.dataset.quantity, 10);
  const badge = document.getElementById('stockBadge');

  badge.classList.remove('stock-green','stock-orange','stock-red');

  if(qty > 5){
    badge.classList.add('stock-green');
  }else if(qty > 0){
    badge.classList.add('stock-orange');
  }else{
    badge.classList.add('stock-red');
  }

})();

/* ---------- Ripple feedback on action buttons ---------- */

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