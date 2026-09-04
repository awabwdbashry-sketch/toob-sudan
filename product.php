<?php
session_start();

include 'includes/db.php';


if(!isset($_GET['id'])){

    header("Location: products.php");
    exit;

}


$id = $_GET['id'];



$product_query = mysqli_query($conn,

"
SELECT products.*, categories.name AS category_name

FROM products

LEFT JOIN categories

ON products.category_id = categories.id

WHERE products.id='$id'

"

);



$product = mysqli_fetch_assoc($product_query);



if(!$product){

header("Location: products.php");
exit;

}


?>


<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

<?php echo $product['name']; ?> | توب سودان

</title>



<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">



<style>

/* =====================================================
   TOOB SUDAN — PRODUCT DETAILS PAGE
   Luxury Fashion Redesign — LV / Dior / Gucci inspired
   Structure & CSS only. All PHP untouched.
===================================================== */

:root{
  --burgundy:#5B1628;
  --burgundy-dark:#3A0D18;
  --gold:#D4AF37;
  --gold-soft:#E8D5C0;
  --cream:#F8F4EE;
  --ink:#090909;
  --white:#ffffff;

  --shadow-sm:0 6px 18px rgba(0,0,0,.22);
  --shadow-md:0 18px 42px rgba(0,0,0,.35);
  --shadow-lg:0 30px 70px rgba(0,0,0,.5);
  --gold-glow:0 0 0 4px rgba(212,175,55,.15), 0 10px 26px rgba(212,175,55,.22);
  --ease:cubic-bezier(.25,.8,.25,1);
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Cairo',sans-serif;
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
}

html{
  scroll-behavior:smooth;
}

body{
  background:var(--cream);
  color:var(--ink);
  overflow-x:hidden;
}

@media (prefers-reduced-motion: reduce){
  *{
    animation-duration:.001ms !important;
    animation-iteration-count:1 !important;
    transition-duration:.001ms !important;
    scroll-behavior:auto !important;
  }
}

@keyframes fadeUp{
  from{opacity:0;transform:translateY(28px);}
  to{opacity:1;transform:translateY(0);}
}

@keyframes fadeIn{
  from{opacity:0;}
  to{opacity:1;}
}

/* ============= NAVBAR ============= */

.navbar{
  background:var(--burgundy);
  padding:18px 0;
  border-bottom:1px solid var(--gold);
}

.navbar-brand{
  color:var(--white)!important;
  font-size:clamp(20px,2.2vw,25px);
  font-weight:900;
  display:flex;
  align-items:center;
  gap:12px;
}

.logo{
  width:56px;
  transition:transform .35s var(--ease);
}

.navbar-brand:hover .logo{
  transform:scale(1.06) rotate(-1deg);
}

.nav-link{
  position:relative;
  color:var(--white)!important;
  margin:0 10px;
  padding-bottom:4px;
  transition:color .3s var(--ease);
}

.nav-link::after{
  content:"";
  position:absolute;
  right:0;
  left:0;
  bottom:-2px;
  height:2px;
  background:var(--gold);
  border-radius:2px;
  transform:scaleX(0);
  transition:transform .35s var(--ease);
}

.nav-link:hover{
  color:var(--gold)!important;
}

.nav-link:hover::after{
  transform:scaleX(1);
}

/* ============= BREADCRUMB ============= */

.breadcrumb-strip{
  background:var(--burgundy-dark);
  padding:16px 7%;
  border-bottom:1px solid rgba(212,175,55,.25);
}

.breadcrumb-lux{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:13px;
  color:rgba(248,244,238,.7);
  max-width:1400px;
  margin:0 auto;
  flex-wrap:wrap;
}

.breadcrumb-lux a{
  color:rgba(248,244,238,.7);
  text-decoration:none;
  transition:color .3s var(--ease);
}

.breadcrumb-lux a:hover{
  color:var(--gold);
}

.breadcrumb-lux i{
  font-size:9px;
  color:var(--gold);
}

.breadcrumb-lux span{
  color:var(--gold);
  font-weight:700;
}

/* ============= PRODUCT PAGE LAYOUT ============= */

.product-page{
  padding:clamp(40px,6vw,90px) 7% 30px;
  max-width:1400px;
  margin:0 auto;

  display:grid;
  grid-template-columns:1.05fr 1fr;
  gap:clamp(30px,5vw,70px);
  align-items:start;
}

/* ============= GALLERY ============= */

.product-gallery{
  position:sticky;
  top:24px;
  animation:fadeUp .8s var(--ease) both;
}

.gallery-frame{
  position:relative;
  border-radius:28px;
  overflow:hidden;
  border:1px solid rgba(212,175,55,.4);
  box-shadow:var(--shadow-lg);
  background:linear-gradient(135deg,var(--gold-soft),var(--cream));
}

.gallery-frame img{
  width:100%;
  height:clamp(360px,52vw,640px);
  object-fit:cover;
  display:block;
  transition:transform .8s var(--ease);
}

.gallery-frame:hover img{
  transform:scale(1.06);
}

.gallery-frame .zoom-hint{
  position:absolute;
  bottom:18px;
  left:18px;
  background:rgba(9,9,9,.55);
  backdrop-filter:blur(6px);
  color:var(--gold);
  border:1px solid rgba(212,175,55,.4);
  padding:9px 16px;
  border-radius:20px;
  font-size:12px;
  letter-spacing:.5px;
  display:flex;
  align-items:center;
  gap:8px;
  opacity:0;
  transform:translateY(8px);
  transition:opacity .4s var(--ease), transform .4s var(--ease);
}

.gallery-frame:hover .zoom-hint{
  opacity:1;
  transform:translateY(0);
}

/* ============= PRODUCT INFO ============= */

.product-info{
  animation:fadeUp .9s var(--ease) .1s both;
}

.badges-row{
  display:flex;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;
  margin-bottom:18px;
}

.category{
  color:var(--burgundy);
  background:rgba(91,22,40,.08);
  padding:7px 18px;
  border-radius:20px;
  font-size:12.5px;
  font-weight:800;
  letter-spacing:1.5px;
  text-transform:uppercase;
}

.brand-badge{
  color:var(--burgundy-dark);
  background:linear-gradient(135deg,#F1D98A,var(--gold) 60%,#B4902B);
  padding:7px 18px;
  border-radius:20px;
  font-size:12.5px;
  font-weight:800;
  letter-spacing:.5px;
  display:flex;
  align-items:center;
  gap:6px;
}

.availability-badge{
  color:#2f6b3f;
  background:rgba(60,140,80,.1);
  padding:7px 16px;
  border-radius:20px;
  font-size:12.5px;
  font-weight:800;
  display:flex;
  align-items:center;
  gap:6px;
}

.product-info h1{
  font-size:clamp(28px,3.4vw,44px);
  font-weight:900;
  color:var(--burgundy-dark);
  line-height:1.3;
  margin-bottom:12px;
}

.rating-row{
  display:flex;
  align-items:center;
  gap:6px;
  color:var(--gold);
  font-size:15px;
  margin-bottom:22px;
}

.description{
  color:#6b5548;
  line-height:2;
  font-size:16.5px;
  margin:0 0 28px;
  max-width:56ch;
}

.price-block{
  display:flex;
  align-items:baseline;
  gap:16px;
  margin-bottom:26px;
  flex-wrap:wrap;
}

.old-price{
  text-decoration:line-through;
  color:#b6a698;
  font-size:19px;
}

.price{
  color:var(--burgundy);
  font-size:clamp(30px,3.6vw,40px);
  font-weight:900;
}

.price::after{
  content:" جنيه";
  font-size:16px;
  font-weight:700;
  opacity:.65;
  margin-right:2px;
}

/* details cards */

.details{
  background:var(--white);
  border:1px solid rgba(91,22,40,.08);
  padding:8px 22px;
  border-radius:22px;
  margin:0 0 30px;
  box-shadow:var(--shadow-sm);
}

.details p{
  margin:0;
  padding:16px 0;
  display:flex;
  align-items:center;
  gap:12px;
  font-size:15px;
  color:var(--ink);
  border-bottom:1px solid rgba(91,22,40,.07);
}

.details p:last-child{
  border-bottom:none;
}

.details p i{
  width:34px;
  height:34px;
  flex:0 0 34px;
  border-radius:50%;
  background:rgba(212,175,55,.12);
  color:var(--gold);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:14px;
}

.details p b{
  color:#8a7267;
  font-weight:700;
  margin-left:auto;
}

.details span{
  color:var(--burgundy);
  font-weight:800;
}

/* ============= PURCHASE AREA ============= */

.purchase-row{
  display:flex;
  align-items:stretch;
  gap:14px;
  margin-bottom:16px;
  flex-wrap:wrap;
}

.quantity{
  width:110px;
  padding:14px;
  background:var(--white);
  border:1.5px solid rgba(91,22,40,.2);
  color:var(--burgundy-dark);
  border-radius:20px;
  text-align:center;
  font-size:16px;
  font-weight:700;
  transition:border-color .3s var(--ease), box-shadow .3s var(--ease);
}

.quantity:focus{
  outline:none;
  border-color:var(--gold);
  box-shadow:var(--gold-glow);
}

.cart-form{
  display:flex;
  gap:14px;
  flex:1;
  flex-wrap:wrap;
}

.cart-btn{
  flex:1;
  min-width:200px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  background:linear-gradient(135deg,#F1D98A,var(--gold) 55%,#B4902B);
  color:var(--burgundy-dark);
  text-align:center;
  padding:18px;
  border-radius:50px;
  border:none;
  text-decoration:none;
  font-weight:800;
  font-size:16.5px;
  letter-spacing:.3px;
  cursor:pointer;
  box-shadow:0 14px 30px rgba(0,0,0,.22);
  transition:transform .35s var(--ease), box-shadow .35s var(--ease);
}

.cart-btn:hover{
  transform:translateY(-3px) scale(1.01);
  box-shadow:0 18px 36px rgba(0,0,0,.3), var(--gold-glow);
}

.secondary-actions{
  display:flex;
  gap:14px;
  flex-wrap:wrap;
}

.btn-wishlist,
.btn-buynow{
  flex:1;
  min-width:180px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  padding:16px;
  border-radius:50px;
  font-weight:800;
  font-size:15px;
  letter-spacing:.2px;
  cursor:pointer;
  transition:all .35s var(--ease);
  text-decoration:none;
}

.btn-wishlist{
  background:transparent;
  border:1.5px solid var(--burgundy);
  color:var(--burgundy);
}

.btn-wishlist:hover{
  background:var(--burgundy);
  color:var(--white);
}

.btn-buynow{
  background:var(--burgundy-dark);
  border:1.5px solid var(--burgundy-dark);
  color:var(--gold-soft);
}

.btn-buynow:hover{
  background:var(--ink);
  border-color:var(--gold);
  color:var(--gold);
  transform:translateY(-3px);
}

.trust-row{
  display:flex;
  gap:22px;
  margin-top:28px;
  flex-wrap:wrap;
  color:#8a7267;
  font-size:13px;
}

.trust-row div{
  display:flex;
  align-items:center;
  gap:8px;
}

.trust-row i{
  color:var(--gold);
}

/* ============= TABS (CSS-only) ============= */

.tabs-section{
  max-width:1400px;
  margin:20px auto clamp(50px,6vw,90px);
  padding:0 7%;
  animation:fadeUp .8s var(--ease) both;
}

.tabs-nav{
  display:flex;
  gap:6px;
  border-bottom:2px solid rgba(91,22,40,.1);
  margin-bottom:34px;
  overflow-x:auto;
  scrollbar-width:none;
}

.tabs-nav::-webkit-scrollbar{
  display:none;
}

.tabs-nav label{
  padding:16px 26px;
  font-weight:800;
  font-size:15px;
  color:#8a7267;
  cursor:pointer;
  white-space:nowrap;
  border-bottom:3px solid transparent;
  transition:color .3s var(--ease), border-color .3s var(--ease);
}

.tab-panel{
  display:none;
  color:#6b5548;
  line-height:2;
  font-size:16px;
  animation:fadeIn .5s var(--ease) both;
}

.tab-panel h4{
  color:var(--burgundy-dark);
  font-size:19px;
  margin-bottom:14px;
}

.tab-panel ul{
  list-style:none;
  display:flex;
  flex-direction:column;
  gap:10px;
  margin-top:8px;
}

.tab-panel ul li{
  display:flex;
  align-items:center;
  gap:10px;
}

.tab-panel ul li i{
  color:var(--gold);
}

#tab-1:checked ~ .tabs-nav label[for="tab-1"],
#tab-2:checked ~ .tabs-nav label[for="tab-2"],
#tab-3:checked ~ .tabs-nav label[for="tab-3"],
#tab-4:checked ~ .tabs-nav label[for="tab-4"],
#tab-5:checked ~ .tabs-nav label[for="tab-5"]{
  color:var(--burgundy);
  border-bottom-color:var(--gold);
}

#tab-1:checked ~ .tab-content #panel-1,
#tab-2:checked ~ .tab-content #panel-2,
#tab-3:checked ~ .tab-content #panel-3,
#tab-4:checked ~ .tab-content #panel-4,
#tab-5:checked ~ .tab-content #panel-5{
  display:block;
}

.tabs-radio{
  display:none;
}

.empty-reviews{
  text-align:center;
  padding:40px 20px;
  color:#8a7267;
}

.empty-reviews i{
  font-size:34px;
  color:var(--gold);
  margin-bottom:14px;
}

/* ============= FOOTER ============= */

.footer{
  background:var(--burgundy-dark);
  padding:50px;
  text-align:center;
  border-top:1px solid var(--gold);
  color:var(--gold-soft);
  font-size:15px;
}

/* ============= RESPONSIVE ============= */

@media(max-width:1024px){

  .product-page{
    grid-template-columns:1fr 1fr;
    gap:36px;
  }

}

@media(max-width:900px){

  .product-page{
    grid-template-columns:1fr;
    padding-top:36px;
  }

  .product-gallery{
    position:static;
  }

  .gallery-frame img{
    height:min(78vw,440px);
  }

  .purchase-row{
    flex-direction:column;
  }

  .quantity{
    width:100%;
  }

}

@media(max-width:480px){

  .product-page{
    padding-left:5.5%;
    padding-right:5.5%;
  }

  .breadcrumb-strip{
    padding:14px 5.5%;
  }

  .badges-row{
    gap:8px;
  }

  .category,
  .brand-badge,
  .availability-badge{
    font-size:11.5px;
    padding:6px 14px;
  }

  .secondary-actions{
    flex-direction:column;
  }

  .tabs-section{
    padding:0 5.5%;
  }

  .tabs-nav label{
    padding:14px 18px;
    font-size:14px;
  }

}

@media(max-width:360px){

  .cart-btn{
    font-size:15px;
    padding:16px;
  }

}

</style>


</head>



<body>



<nav class="navbar navbar-expand-lg">

<div class="container">


<a class="navbar-brand" href="index.php">

<img src="assets/images/logo.png" class="logo">

توب سودان

</a>


<div>


<a class="nav-link d-inline" href="index.php">
الرئيسية
</a>


<a class="nav-link d-inline" href="products.php">
المتجر
</a>


</div>


</div>

</nav>



<div class="breadcrumb-strip">
<div class="breadcrumb-lux">
<a href="index.php">الرئيسية</a>
<i class="fa-solid fa-angle-left"></i>
<a href="products.php">المتجر</a>
<i class="fa-solid fa-angle-left"></i>
<span><?php echo $product['name']; ?></span>
</div>
</div>




<section class="product-page">



<div class="product-gallery">

<div class="gallery-frame">

<img src="assets/images/products/<?php echo $product['image']; ?>">

<span class="zoom-hint">
<i class="fa-solid fa-magnifying-glass-plus"></i>
مرر للتكبير
</span>

</div>

</div>





<div class="product-info">


<div class="badges-row">

<p class="category">

<?php echo $product['category_name']; ?>

</p>

<span class="brand-badge">
<i class="fa-solid fa-gem"></i>
<?php echo $product['brand']; ?>
</span>

<span class="availability-badge">
<i class="fa-solid fa-circle-check"></i>
متوفر
</span>

</div>



<h1>

<?php echo $product['name']; ?>

</h1>


<div class="rating-row">
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-regular fa-star"></i>
</div>



<p class="description">

<?php echo $product['description']; ?>

</p>



<div class="price-block">

<?php if($product['old_price']){ ?>

<p class="old-price">

<?php echo $product['old_price']; ?> جنيه

</p>

<?php } ?>



<p class="price">

<?php echo $product['price']; ?>

</p>

</div>




<div class="details">


<p>

<i class="fa-solid fa-scroll"></i>

المادة

<span><?php echo $product['fabric']; ?></span>

</p>


<p>

<i class="fa-solid fa-palette"></i>

اللون

<span><?php echo $product['color']; ?></span>

</p>


<p>

<i class="fa-solid fa-ruler"></i>

الحجم

<span><?php echo $product['size']; ?></span>

</p>


<p>

<i class="fa-solid fa-gem"></i>

العلامة

<span><?php echo $product['brand']; ?></span>

</p>


</div>




<div class="purchase-row">

<form class="cart-form" action="cart.php" method="POST">

<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">


<input 
class="quantity" 
type="number" 
name="quantity"
value="1"
min="1">


<button class="cart-btn" name="add_cart">

<i class="fa-solid fa-cart-shopping"></i>

أضف للسلة

</button>


</form>

</div>


<div class="secondary-actions">

<a href="add_to_wishlist.php?id=<?php echo $product['id']; ?>" class="btn-wishlist">
    <i class="fa-regular fa-heart"></i>
    أضف للمفضلة
</a>

<a href="cart.php" class="btn-buynow">
    <i class="fa-solid fa-bolt"></i>
    اشترِ الآن
</a>
</div>


<div class="trust-row">
<div><i class="fa-solid fa-truck-fast"></i> توصيل سريع</div>
<div><i class="fa-solid fa-rotate-left"></i> إمكانية الإرجاع</div>
<div><i class="fa-solid fa-shield-halved"></i> جودة مضمونة</div>
</div>


</div>



</section>




<section class="tabs-section">

<input type="radio" class="tabs-radio" name="tabs" id="tab-1" checked>
<input type="radio" class="tabs-radio" name="tabs" id="tab-2">
<input type="radio" class="tabs-radio" name="tabs" id="tab-3">
<input type="radio" class="tabs-radio" name="tabs" id="tab-4">
<input type="radio" class="tabs-radio" name="tabs" id="tab-5">

<div class="tabs-nav">
<label for="tab-1">الوصف</label>
<label for="tab-2">المواصفات</label>
<label for="tab-3">الشحن</label>
<label for="tab-4">الإرجاع</label>
<label for="tab-5">التقييمات</label>
</div>

<div class="tab-content">

<div class="tab-panel" id="panel-1">
<h4>وصف المنتج</h4>
<p><?php echo $product['description']; ?></p>
</div>

<div class="tab-panel" id="panel-2">
<h4>المواصفات</h4>
<ul>
<li><i class="fa-solid fa-scroll"></i> المادة: <?php echo $product['fabric']; ?></li>
<li><i class="fa-solid fa-palette"></i> اللون: <?php echo $product['color']; ?></li>
<li><i class="fa-solid fa-ruler"></i> الحجم: <?php echo $product['size']; ?></li>
<li><i class="fa-solid fa-gem"></i> العلامة: <?php echo $product['brand']; ?></li>
</ul>
</div>

<div class="tab-panel" id="panel-3">
<h4>الشحن والتوصيل</h4>
<p>يتم شحن الطلبات خلال 2-5 أيام عمل داخل السودان. سيتم التواصل معك لتأكيد الطلب وتفاصيل التوصيل بعد إتمام عملية الشراء.</p>
</div>

<div class="tab-panel" id="panel-4">
<h4>سياسة الإرجاع</h4>
<p>يمكنك طلب استبدال أو إرجاع المنتج خلال فترة محددة من تاريخ الاستلام، بشرط أن يكون المنتج بحالته الأصلية دون استخدام.</p>
</div>

<div class="tab-panel" id="panel-5">
<div class="empty-reviews">
<i class="fa-regular fa-comment-dots"></i>
<p>لا توجد تقييمات على هذا المنتج بعد</p>
</div>
</div>

</div>

</section>




<footer class="footer">

© 2026 توب سودان

</footer>




</body>

</html>