<?php
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>التصنيفات | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<style>

/* =====================================================
   TOOB SUDAN — CATEGORIES PAGE
   Luxury Fashion Redesign — LV / Dior / Gucci inspired
   Structure & CSS only. Backend untouched.
===================================================== */

:root{
  --burgundy:#5B1628;
  --burgundy-dark:#3A0D18;
  --gold:#D4AF37;
  --gold-soft:#E8D5C0;
  --cream:#F8F4EE;
  --gold-mist:#FBF6EA;
  --ink:#090909;
  --white:#ffffff;

  --shadow-sm:0 6px 18px rgba(0,0,0,.2);
  --shadow-md:0 18px 42px rgba(0,0,0,.32);
  --shadow-lg:0 30px 70px rgba(0,0,0,.45);
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
  background:var(--white);
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
  from{opacity:0;transform:translateY(30px);}
  to{opacity:1;transform:translateY(0);}
}

/* ============= NAVBAR ============= */

.navbar{
  background:var(--burgundy);
  padding:18px 0;
  border-bottom:1px solid var(--gold);
}

.navbar-brand{
  display:flex;
  align-items:center;
  gap:12px;
  color:var(--white)!important;
  font-size:clamp(20px,2.2vw,25px);
  font-weight:900;
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

/* ============= HERO ============= */

.categories-hero{
  position:relative;
  min-height:min(58vw,520px);

  background:
    linear-gradient(180deg, rgba(20,4,9,.5), rgba(58,13,24,.7) 55%, rgba(58,13,24,.92)),
    url('assets/images/hero.jpg.png');

  background-size:cover;
  background-position:center;

  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  padding:70px 6% 60px;
  overflow:hidden;
}

.categories-hero::before{
  content:"";
  position:absolute;
  inset:0;
  background:radial-gradient(ellipse at 50% 0%, rgba(212,175,55,.14), transparent 60%);
  pointer-events:none;
}

.hero-inner{
  position:relative;
  z-index:2;
  animation:fadeUp 1s var(--ease) both;
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:14px;
}

.hero-inner span{
  color:var(--gold);
  font-size:clamp(12px,1.4vw,15px);
  letter-spacing:6px;
  font-weight:700;
  text-transform:uppercase;
}

.hero-inner h1{
  font-size:clamp(36px,6vw,62px);
  font-weight:900;
  color:var(--white);
  text-shadow:0 14px 34px rgba(0,0,0,.45);
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

.breadcrumb-lux{
  display:flex;
  align-items:center;
  gap:10px;
  margin-top:6px;
  font-size:13px;
  color:rgba(248,244,238,.75);
}

.breadcrumb-lux a{
  color:rgba(248,244,238,.75);
  text-decoration:none;
  transition:color .3s var(--ease);
}

.breadcrumb-lux a:hover{
  color:var(--gold);
}

.breadcrumb-lux i{
  font-size:10px;
  color:var(--gold);
}

.breadcrumb-lux span{
  color:var(--gold);
}

/* ============= STATS STRIP ============= */

.stats-strip{
  background:var(--gold-mist);
  border-bottom:1px solid rgba(212,175,55,.25);
  padding:34px 6%;
}

.stats-inner{
  max-width:1200px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:20px;
  text-align:center;
}

.stat-item{
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:6px;
  padding:10px;
  position:relative;
}

.stat-item:not(:last-child)::after{
  content:"";
  position:absolute;
  left:0;
  top:50%;
  transform:translateY(-50%);
  height:60%;
  width:1px;
  background:rgba(91,22,40,.15);
}

.stat-item i{
  font-size:22px;
  color:var(--gold);
  margin-bottom:4px;
}

.stat-number{
  font-size:clamp(22px,2.6vw,30px);
  font-weight:900;
  color:var(--burgundy-dark);
}

.stat-label{
  font-size:13px;
  color:#8a7267;
  letter-spacing:.4px;
  font-weight:700;
}

/* ============= CATEGORIES SECTION ============= */

.categories-section{
  padding:clamp(60px,8vw,110px) 6%;
  background:var(--cream);
}

.section-title{
  text-align:center;
  margin-bottom:56px;
  animation:fadeUp .8s var(--ease) both;
}

.section-title span{
  color:var(--burgundy);
  font-size:12.5px;
  letter-spacing:5px;
  font-weight:700;
  text-transform:uppercase;
  opacity:.75;
}

.section-title h2{
  font-size:clamp(28px,3.6vw,42px);
  font-weight:900;
  color:var(--burgundy-dark);
  margin-top:12px;
  position:relative;
  display:inline-block;
}

.section-title h2::after{
  content:"";
  display:block;
  width:64px;
  height:3px;
  background:var(--gold);
  margin:16px auto 0;
  border-radius:3px;
}

.category-grid{
  max-width:1400px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:32px;
}

/* ============= CATEGORY CARD ============= */

.category-card{
  position:relative;
  height:480px;
  border-radius:26px;
  overflow:hidden;
  cursor:pointer;
  background:var(--white);
  border:1px solid rgba(212,175,55,.28);
  box-shadow:var(--shadow-sm);
  transition:transform .55s var(--ease), box-shadow .55s var(--ease), border-color .55s var(--ease);
  animation:fadeUp .8s var(--ease) both;
}

.category-card:hover{
  transform:translateY(-12px);
  box-shadow:var(--shadow-lg);
  border-color:rgba(212,175,55,.75);
}

.category-card::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius:26px;
  border:1.5px solid transparent;
  background:linear-gradient(135deg, rgba(212,175,55,.9), transparent 40%, transparent 60%, rgba(212,175,55,.9)) border-box;
  -webkit-mask:linear-gradient(#000 0 0) padding-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor;
  mask-composite:exclude;
  opacity:0;
  transition:opacity .5s var(--ease);
  z-index:3;
  pointer-events:none;
}

.category-card:hover::before{
  opacity:1;
}

.category-card img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
  transition:transform .8s var(--ease);
}

.category-card:hover img{
  transform:scale(1.1);
}

.category-overlay{
  position:absolute;
  inset:0;
  z-index:2;

  background:linear-gradient(180deg, transparent 35%, rgba(9,9,9,.35) 65%, rgba(9,9,9,.88) 100%);

  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:flex-end;
  text-align:center;

  padding:40px 30px 34px;
}

.category-kicker{
  color:var(--gold);
  font-size:11.5px;
  letter-spacing:3px;
  text-transform:uppercase;
  font-weight:700;
  margin-bottom:10px;
  opacity:.9;
}

.category-overlay h3{
  font-size:clamp(24px,2.6vw,30px);
  color:var(--white);
  font-weight:900;
  margin-bottom:10px;
}

.category-desc{
  color:var(--gold-soft);
  font-size:14px;
  line-height:1.8;
  max-width:34ch;
  opacity:.9;
  margin-bottom:22px;
}

.category-overlay a{
  display:inline-flex;
  align-items:center;
  gap:10px;
  background:linear-gradient(135deg,#F1D98A,var(--gold) 55%,#B4902B);
  color:var(--burgundy-dark);
  padding:13px 28px;
  border-radius:50px;
  width:max-content;
  font-weight:800;
  font-size:14.5px;
  letter-spacing:.3px;
  text-decoration:none;
  box-shadow:0 12px 26px rgba(0,0,0,.3);
  transition:transform .4s var(--ease), box-shadow .4s var(--ease), gap .4s var(--ease);
}

.category-overlay a i{
  transition:transform .4s var(--ease);
}

.category-card:hover .category-overlay a{
  transform:translateY(-3px);
  box-shadow:0 16px 32px rgba(0,0,0,.38), var(--gold-glow);
}

.category-card:hover .category-overlay a i{
  transform:translateX(-5px);
}

/* ============= FOOTER ============= */
.footer{
    background:#4b0d1d;
    color:#fff;
    padding:70px 0 25px;
}

.footer-container{
    max-width:1300px;
    margin:auto;
    padding:0 25px;

    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:60px;

    align-items:start;
}

.footer-container img{
    width:90px;
    margin-bottom:20px;
}

.footer-container h3{
    color:#D4AF37;
    margin-bottom:18px;
}

.footer-container a,
.footer-container p{
    display:block;
    color:#ddd;
    text-decoration:none;
    margin-bottom:12px;
}

.footer-container a:hover{
    color:#D4AF37;
}

.footer-bottom{
    margin-top:50px;
    border-top:1px solid rgba(255,255,255,.12);
    padding-top:20px;
    text-align:center;
    color:#ccc;
}

@media(max-width:768px){

.footer-container{
    grid-template-columns:1fr;
    text-align:center;
}

}/* =====================================================
   RESPONSIVE
   Desktop 3 · Tablet 2 · Mobile swipe carousel
===================================================== */

@media(max-width:1199px){

  .category-grid{
    grid-template-columns:repeat(2,1fr);
    gap:26px;
  }

}

@media(max-width:900px){

  .stats-inner{
    grid-template-columns:repeat(3,1fr);
    gap:10px;
  }

  .stat-item i{
    font-size:18px;
  }

  .footer-container{
    grid-template-columns:1fr;
    text-align:center;
  }

  .categories-hero{
    min-height:340px;
    padding:60px 6%;
  }

  .category-card{
    height:400px;
  }

}

@media(max-width:480px){

  .categories-section{
    padding:60px 5% 80px;
  }

  .category-grid{
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

  .category-grid::-webkit-scrollbar{
    display:none;
  }

  .category-card{
    flex:0 0 80%;
    max-width:280px;
    height:420px;
    scroll-snap-align:start;
  }

  .stats-strip{
    padding:26px 5%;
  }

  .stat-item i{
    font-size:16px;
  }

  .stat-number{
    font-size:20px;
  }

  .stat-label{
    font-size:11.5px;
  }

  .hero-inner h1{
    font-size:32px;
  }

}

@media(max-width:360px){

  .navbar-brand{
    font-size:18px;
  }

  .category-overlay a{
    padding:12px 22px;
    font-size:13.5px;
  }

}

</style>


</head>


<body>



<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg">


<div class="container">


<a class="navbar-brand" href="index.php">


<img src="assets/images/logo.png" class="logo">


<span>
توب سودان
</span>


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
<a class="nav-link active" href="categories.php">التصنيفات</a>
</li>


<li class="nav-item">
<a class="nav-link" href="offers.php">العروض</a>
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




<section class="categories-hero">

<div class="hero-inner">

<span>
TOOB SUDAN
</span>


<h1>
اكتشف مجموعاتنا
</h1>

<div class="hero-divider"></div>

<p class="hero-sub">
تشكيلة متكاملة من الأثواب السودانية الفاخرة، منظمة في تصنيفات مختارة لتناسب كل مناسبة
</p>

<div class="breadcrumb-lux">
<a href="index.php">الرئيسية</a>
<i class="fa-solid fa-angle-left"></i>
<span>التصنيفات</span>
</div>


</div>


</section>



<section class="stats-strip">

<div class="stats-inner">

<div class="stat-item">
<i class="fa-solid fa-layer-group"></i>
<span class="stat-number">3</span>
<span class="stat-label">تصنيفات مميزة</span>
</div>

<div class="stat-item">
<i class="fa-solid fa-gem"></i>
<span class="stat-number">100%</span>
<span class="stat-label">تشكيلات فاخرة</span>
</div>

<div class="stat-item">
<i class="fa-solid fa-star"></i>
<span class="stat-number">جديد</span>
<span class="stat-label">وصل حديثاً</span>
</div>

</div>

</section>




<section class="categories-section">


<div class="section-title">

<span>
تصنيفاتنا
</span>

<h2>
اختر إطلالتك
</h2>


</div>



<div class="category-grid">



<div class="category-card">


<img src="assets/images/categories/wedding.jpg">


<div class="category-overlay">

<span class="category-kicker">Bridal Edit</span>

<h3>
ثياب الأعراس
</h3>

<p class="category-desc">
تصاميم فاخرة تليق بأجمل لحظاتك
</p>

<a href="products.php">
تصفح الآن
<i class="fa-solid fa-arrow-left"></i>
</a>


</div>


</div>





<div class="category-card">


<img src="assets/images/categories/events.jpg">


<div class="category-overlay">

<span class="category-kicker">Occasion Wear</span>

<h3>
ثياب المناسبات
</h3>

<p class="category-desc">
إطلالات أنيقة لكل حدث مميز
</p>

<a href="products.php">
تصفح الآن
<i class="fa-solid fa-arrow-left"></i>
</a>


</div>


</div>





<div class="category-card">


<img src="assets/images/categories/daily.jpg">


<div class="category-overlay">

<span class="category-kicker">Everyday Luxury</span>

<h3>
الثياب اليومية
</h3>

<p class="category-desc">
راحة وأناقة في تفاصيل يومك
</p>

<a href="products.php">
تصفح الآن
<i class="fa-solid fa-arrow-left"></i>
</a>


</div>


</div>



</div>


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