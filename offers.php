<?php
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>العروض | توب سودان</title>


<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">



<style>

/* =====================================================
   TOOB SUDAN — OFFERS PAGE
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

@keyframes floatY{
  0%,100%{transform:translateY(0) rotate(0deg);}
  50%{transform:translateY(-16px) rotate(6deg);}
}

@keyframes floatY2{
  0%,100%{transform:translateY(0) rotate(0deg);}
  50%{transform:translateY(14px) rotate(-8deg);}
}

/* ===== NAVBAR ===== */

.navbar{
  background:var(--burgundy);
  padding:18px 0;
  border-bottom:1px solid var(--gold);
  transition:padding .35s var(--ease), box-shadow .35s var(--ease);
}

.navbar-brand{
  display:flex;
  align-items:center;
  gap:12px;
  color:var(--white)!important;
  font-size:clamp(21px,2.2vw,25px);
  font-weight:900;
  letter-spacing:.3px;
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
  transform-origin:center;
  transition:transform .35s var(--ease);
}

.nav-link:hover,
.nav-link.active{
  color:var(--gold)!important;
}

.nav-link:hover::after,
.nav-link.active::after{
  transform:scaleX(1);
}

.right-icons{
  display:flex;
  gap:20px;
  align-items:center;
}

.right-icons a{
  color:var(--white);
  font-size:19px;
  transition:color .3s var(--ease), transform .3s var(--ease);
}

.right-icons a:hover{
  color:var(--gold);
  transform:translateY(-3px);
}

/* ===== HERO ===== */

.offers-hero{
  position:relative;
  min-height:min(58vw,540px);

  background:
    linear-gradient(180deg, rgba(20,4,9,.55), rgba(58,13,24,.6) 55%, rgba(58,13,24,.94)),
    url('assets/images/hero.jpg.png');

  background-size:cover;
  background-position:center;

  display:flex;
  justify-content:center;
  align-items:center;
  text-align:center;
  padding:70px 6% 60px;
  overflow:hidden;
}

.offers-hero::before{
  content:"";
  position:absolute;
  inset:0;
  background:radial-gradient(ellipse at 50% 0%, rgba(212,175,55,.16), transparent 60%);
  pointer-events:none;
}

/* floating golden decorations — CSS only */
.floating-deco{
  position:absolute;
  color:var(--gold);
  opacity:.35;
  pointer-events:none;
  z-index:1;
}

.deco-1{ top:14%; left:10%; font-size:34px; animation:floatY 7s ease-in-out infinite; }
.deco-2{ top:22%; right:12%; font-size:26px; animation:floatY2 8.5s ease-in-out infinite; }
.deco-3{ bottom:18%; left:16%; font-size:22px; animation:floatY2 6.5s ease-in-out infinite; }
.deco-4{ bottom:24%; right:9%; font-size:30px; animation:floatY 9s ease-in-out infinite; }

.offers-hero > .hero-inner{
  position:relative;
  z-index:2;
  animation:fadeUp .9s var(--ease) both;
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:14px;
}

.offers-hero span.eyebrow{
  color:var(--gold);
  font-size:clamp(14px,1.7vw,18px);
  letter-spacing:4px;
  font-weight:700;
  text-transform:uppercase;
}

.offers-hero h1{
  font-size:clamp(34px,6vw,60px);
  font-weight:900;
  color:var(--white);
  line-height:1.25;
  text-shadow:0 12px 30px rgba(0,0,0,.4);
}

.hero-divider{
  width:70px;
  height:2px;
  background:linear-gradient(90deg, transparent, var(--gold), transparent);
}

.hero-sub{
  max-width:520px;
  color:var(--gold-soft);
  font-size:clamp(13px,1.4vw,15.5px);
  line-height:1.9;
  opacity:.92;
}

/* ===== SECTION HEADINGS ===== */

.title{
  text-align:center;
  margin-bottom:clamp(40px,5vw,54px);
  animation:fadeUp .8s var(--ease) both;
}

.title span{
  color:var(--burgundy);
  font-size:12.5px;
  letter-spacing:4px;
  font-weight:700;
  text-transform:uppercase;
  opacity:.8;
}

.title h2{
  font-size:clamp(26px,4vw,42px);
  font-weight:900;
  color:var(--burgundy-dark);
  margin-top:12px;
  position:relative;
  display:inline-block;
}

.title h2::after{
  content:"";
  display:block;
  position:absolute;
  left:50%;
  bottom:-16px;
  transform:translateX(-50%);
  width:64px;
  height:3px;
  background:var(--gold);
  border-radius:3px;
}

/* ===== DISCOUNTED PRODUCTS ===== */

.products-offers{
  padding:clamp(60px,8vw,90px) 6% 20px;
  max-width:1500px;
  margin:0 auto;
}

.product-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:32px;
}

.product-card{
  position:relative;
  background:var(--white);
  border-radius:26px;
  overflow:hidden;
  border:1px solid rgba(91,22,40,.08);
  box-shadow:var(--shadow-sm);
  transition:transform .55s var(--ease), box-shadow .55s var(--ease), border-color .55s var(--ease);
  animation:fadeUp .7s var(--ease) both;
  text-align:center;
}

.product-card:hover{
  transform:translateY(-12px);
  box-shadow:var(--shadow-lg);
  border-color:rgba(212,175,55,.55);
}

.card-media{
  position:relative;
  overflow:hidden;
  aspect-ratio:3/4;
  background:linear-gradient(135deg,var(--gold-soft),var(--cream));
}

.card-media > img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
  transition:transform .8s var(--ease);
}

.product-card:hover .card-media > img{
  transform:scale(1.09);
}

/* floating discount badge — glass effect */
.discount{
  position:absolute;
  top:16px;
  right:16px;
  z-index:3;
  background:rgba(58,13,24,.55);
  backdrop-filter:blur(8px);
  -webkit-backdrop-filter:blur(8px);
  color:var(--gold);
  border:1px solid rgba(212,175,55,.55);
  padding:8px 18px;
  border-radius:20px;
  display:inline-flex;
  align-items:center;
  gap:6px;
  font-size:12.5px;
  font-weight:800;
  letter-spacing:.4px;
  box-shadow:0 8px 18px rgba(0,0,0,.28);
}

.product-info{
  padding:26px 24px 28px;
}

.product-info h3{
  font-size:19.5px;
  font-weight:700;
  color:var(--burgundy-dark);
  margin:2px 0 16px;
  line-height:1.5;
}

.price-row{
  display:flex;
  align-items:baseline;
  justify-content:center;
  gap:12px;
  margin-bottom:6px;
}

.old-price{
  color:#b6a698;
  text-decoration:line-through;
  font-weight:400;
  font-size:14.5px;
}

.new-price{
  color:var(--burgundy);
  font-size:24px;
  font-weight:900;
}

.new-price::after{
  content:" جنيه";
  font-size:13px;
  font-weight:700;
  opacity:.7;
}

.product-info a{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  margin-top:18px;
  background:linear-gradient(135deg,#F1D98A,var(--gold) 55%,#B4902B);
  color:var(--burgundy-dark);
  padding:13px 30px;
  border-radius:30px;
  text-decoration:none;
  font-weight:800;
  font-size:14.5px;
  letter-spacing:.2px;
  box-shadow:0 10px 22px rgba(0,0,0,.2);
  transition:transform .35s var(--ease), box-shadow .35s var(--ease);
}

.product-info a:hover{
  transform:translateY(-3px) scale(1.03);
  box-shadow:0 14px 28px rgba(0,0,0,.3), var(--gold-glow);
}

/* ===== SPECIAL OFFERS (4 boxes) ===== */

.offers-section{
  padding:clamp(70px,8vw,100px) 6%;
}

.offer-grid{
  max-width:1400px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:28px;
}

.offer-card{
  position:relative;
  background:rgba(255,255,255,.6);
  backdrop-filter:blur(10px) saturate(140%);
  -webkit-backdrop-filter:blur(10px) saturate(140%);
  padding:44px 26px;
  border-radius:26px;
  text-align:center;
  border:1px solid rgba(212,175,55,.35);
  box-shadow:var(--shadow-sm);
  transition:transform .45s var(--ease), box-shadow .45s var(--ease), border-color .45s var(--ease);
  cursor:pointer;
  overflow:hidden;
}

.offer-card::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius:26px;
  padding:1.5px;
  background:linear-gradient(135deg, rgba(212,175,55,.9), transparent 45%, transparent 55%, rgba(212,175,55,.9));
  -webkit-mask:linear-gradient(#000 0 0) padding-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor;
  mask-composite:exclude;
  opacity:0;
  transition:opacity .5s var(--ease);
  pointer-events:none;
}

.offer-card:hover::before{
  opacity:1;
}

.offer-card:hover{
  transform:translateY(-10px);
  box-shadow:var(--shadow-md);
  border-color:rgba(212,175,55,.7);
}

.offer-card i{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:76px;
  height:76px;
  font-size:32px;
  color:var(--gold);
  margin-bottom:22px;
  border-radius:50%;
  background:rgba(212,175,55,.12);
  border:1px solid rgba(212,175,55,.4);
  transition:transform .4s var(--ease), background .4s var(--ease), color .4s var(--ease);
}

.offer-card:hover i{
  transform:scale(1.08) rotate(-4deg);
  background:var(--gold);
  color:var(--burgundy-dark);
}

.offer-card h3{
  font-size:22px;
  font-weight:800;
  color:var(--burgundy-dark);
  margin-bottom:10px;
}

.offer-card p{
  color:#6b5548;
  font-size:15px;
  line-height:1.8;
}

/* ===== PROMOTIONAL BANNER ===== */

.offer-banner{
  position:relative;
  margin:clamp(20px,6vw,40px) 6% clamp(60px,8vw,90px);
  min-height:340px;

  background:
    linear-gradient(180deg, rgba(20,4,9,.35), rgba(58,13,24,.78)),
    url('assets/images/hero.jpg.png');

  background-size:cover;
  background-position:center;
  background-attachment:fixed;

  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  text-align:center;
  gap:24px;
  padding:50px 24px;

  border-radius:32px;
  border:1px solid rgba(212,175,55,.4);
  box-shadow:var(--shadow-lg);
  overflow:hidden;
}

@media(max-width:900px){
  .offer-banner{
    background-attachment:scroll;
  }
}

.offer-banner span.eyebrow{
  color:var(--gold);
  font-size:13px;
  letter-spacing:4px;
  font-weight:700;
  text-transform:uppercase;
}

.offer-banner h2{
  font-size:clamp(26px,4.4vw,48px);
  font-weight:900;
  color:var(--white);
  max-width:720px;
  line-height:1.3;
  text-shadow:0 10px 26px rgba(0,0,0,.4);
}

.offer-banner a{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:linear-gradient(135deg,#F1D98A,var(--gold) 55%,#B4902B);
  color:var(--burgundy-dark);
  padding:16px 40px;
  border-radius:50px;
  text-decoration:none;
  font-weight:800;
  font-size:15.5px;
  letter-spacing:.3px;
  box-shadow:0 14px 30px rgba(0,0,0,.32);
  transition:transform .35s var(--ease), box-shadow .35s var(--ease);
}

.offer-banner a:hover{
  transform:translateY(-3px) scale(1.02);
  box-shadow:0 18px 36px rgba(0,0,0,.4), var(--gold-glow);
}

/* ===== FOOTER ===== */

.footer{
  background:var(--burgundy-dark);
  padding:70px 6% 20px;
  border-top:1px solid var(--gold);
}

.footer-container{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:34px;
  max-width:1300px;
  margin:0 auto;
}

.footer img{
  width:118px;
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.35));
}

.footer h3{
  color:var(--gold);
  font-size:19px;
  font-weight:700;
  letter-spacing:.3px;
  margin-bottom:6px;
}

.footer p,
.footer a{
  color:var(--gold-soft);
  display:block;
  margin:15px 0;
  font-size:15px;
  text-decoration:none;
  transition:color .3s var(--ease), transform .3s var(--ease);
}

.footer a:hover{
  color:var(--gold);
  transform:translateX(-4px);
}

.footer-bottom{
  text-align:center;
  margin-top:40px;
  border-top:1px solid #ffffff20;
  padding-top:20px;
  color:var(--gold-soft);
  font-size:14px;
  letter-spacing:.2px;
}

/* =====================================================
   RESPONSIVE
   Desktop 4 · Tablet 2 · Mobile swipe carousel
===================================================== */

@media(max-width:1199px){

  .product-grid,
  .offer-grid{
    grid-template-columns:repeat(2,1fr);
    gap:26px;
  }

}

@media(max-width:900px){

  .footer-container{
    grid-template-columns:1fr;
    text-align:center;
  }

  .footer a:hover{
    transform:none;
  }

  .offers-hero h1{
    font-size:38px;
  }

  .offer-banner{
    min-height:280px;
  }

}

@media(max-width:480px){

  .products-offers,
  .offers-section{
    padding-left:5%;
    padding-right:5%;
  }

  .product-grid,
  .offer-grid{
    display:flex;
    grid-template-columns:unset;
    overflow-x:auto;
    overflow-y:hidden;
    gap:16px;
    padding:6px 4px 18px;
    scroll-snap-type:x mandatory;
    -webkit-overflow-scrolling:touch;
    scrollbar-width:none;
  }

  .product-grid::-webkit-scrollbar,
  .offer-grid::-webkit-scrollbar{
    display:none;
  }

  .product-card,
  .offer-card{
    flex:0 0 80%;
    max-width:280px;
    scroll-snap-align:start;
  }

  .offer-card{
    padding:36px 22px;
  }

  .offer-card i{
    width:64px;
    height:64px;
    font-size:26px;
  }

  .offer-banner{
    margin:24px 5% 60px;
    min-height:260px;
    border-radius:24px;
  }

  .deco-1,.deco-2,.deco-3,.deco-4{
    display:none;
  }

}

@media(max-width:360px){

  .offers-hero h1{
    font-size:30px;
  }

  .navbar-brand span{
    font-size:18px;
  }

  .offer-banner a{
    padding:14px 30px;
    font-size:14px;
  }

}

</style>

</head>


<body>


<nav class="navbar navbar-expand-lg">

<div class="container">


<a class="navbar-brand" href="index.php">

<img src="assets/images/logo.png" class="logo">

<span>توب سودان</span>

</a>


<button class="navbar-toggler bg-white"
data-bs-toggle="collapse"
data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>


<div class="collapse navbar-collapse" id="menu">


<ul class="navbar-nav mx-auto">


<li class="nav-item">
<a class="nav-link" href="index.php">الرئيسية</a>
</li>


<li class="nav-item">
<a class="nav-link" href="products.php">المتجر</a>
</li>


<li class="nav-item">
<a class="nav-link active" href="offers.php">العروض</a>
</li>


<li class="nav-item">
<a class="nav-link" href="about.php">من نحن</a>
</li>


<li class="nav-item">
<a class="nav-link" href="contact.php">تواصل معنا</a>
</li>


</ul>



<div class="right-icons">

<a href="cart.php">
<i class="fa-solid fa-cart-shopping"></i>
</a>

<a href="login.php">
<i class="fa-regular fa-user"></i>
</a>

</div>


</div>


</div>

</nav>



<section class="offers-hero">

<i class="fa-solid fa-gem floating-deco deco-1"></i>
<i class="fa-solid fa-star floating-deco deco-2"></i>
<i class="fa-solid fa-gem floating-deco deco-3"></i>
<i class="fa-solid fa-star floating-deco deco-4"></i>

<div class="hero-inner">

<span class="eyebrow">TOOB SUDAN</span>

<h1>
عروضنا الخاصة
</h1>

<div class="hero-divider"></div>

<p class="hero-sub">
تشكيلة مختارة من أرقى الأثواب السودانية، بأسعار استثنائية لفترة محدودة
</p>

</div>

</section>

<?php

$offers_products = mysqli_query($conn,"
SELECT *
FROM products
WHERE old_price > price

ORDER BY id DESC
LIMIT 8
");

?>


<section class="products-offers">


<div class="title">

<span>عروض حصرية</span>

<h2>
منتجات عليها خصم
</h2>

</div>


<div class="product-grid">


<?php while($product = mysqli_fetch_assoc($offers_products)){ ?>


<div class="product-card">


<div class="card-media">

<img src="assets/images/products/<?php echo $product['image']; ?>">

<span class="discount">
<i class="fa-solid fa-tag"></i>
عرض خاص
</span>

</div>


<div class="product-info">


<h3>
<?php echo $product['name']; ?>
</h3>


<div class="price-row">

<span class="old-price">

<?php echo $product['old_price']; ?> جنيه

</span>


<span class="new-price">

<?php echo $product['price']; ?>

</span>

</div>



<a href="product.php?id=<?php echo $product['id']; ?>">
عرض المنتج
<i class="fa-solid fa-arrow-left"></i>
</a>


</div>


</div>



<?php } ?>


</div>


</section>

<section class="offers-section">


<div class="title">

<span>
اختيارات مميزة
</span>

<h2>
عروض توب سودان
</h2>

</div>



<div class="offer-grid">


<div class="offer-card">

<i class="fa-solid fa-percent"></i>

<h3>
خصم 20%
</h3>

<p>
على مجموعة مختارة من الثياب
</p>

</div>



<div class="offer-card">

<i class="fa-solid fa-heart"></i>

<h3>
عرض العروس
</h3>

<p>
إطلالة كاملة للمناسبات
</p>

</div>



<div class="offer-card">

<i class="fa-solid fa-truck"></i>

<h3>
شحن مجاني
</h3>

<p>
لفترة محدودة
</p>

</div>



<div class="offer-card">

<i class="fa-solid fa-crown"></i>

<h3>
مجموعة فاخرة
</h3>

<p>
أفضل التصاميم السودانية
</p>

</div>


</div>


</section>



<section class="offer-banner">

<span class="eyebrow">Toob Sudan</span>

<h2>
اكتشف أناقة الثوب السوداني
</h2>

<a href="products.php">
تسوق المجموعة
<i class="fa-solid fa-arrow-left"></i>
</a>

</section>




<footer class="footer">

<div class="footer-container">

<div>

<img src="assets/images/logo.png">

<p>
من قلب السودان... إلى العالم
</p>

</div>


<div>

<h3>
روابط
</h3>

<a href="index.php">الرئيسية</a>

<a href="products.php">المتجر</a>

<a href="about.php">من نحن</a>

</div>


<div>

<h3>
تواصل
</h3>

<p>واتساب</p>
<p>Instagram</p>
<p>TikTok</p>

</div>


</div>


<div class="footer-bottom">

© 2026 توب سودان

</div>


</footer>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>