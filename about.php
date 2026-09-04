<?php
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>من نحن | توب سودان</title>


<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">





</head>
<style> 

/* =====================================================
   TOOB SUDAN — ABOUT PAGE
   Luxury Fashion Redesign — LV / Dior / Gucci inspired
   Structure & CSS only. PHP untouched.
===================================================== */

:root{
  --burgundy:#5B1628;
  --burgundy-dark:#3A0D18;
  --burgundy-darkest:#240711;
  --gold:#D4AF37;
  --gold-soft:#E8D5C0;
  --cream:#F8F4EE;
  --ink:#090909;
  --white:#ffffff;

  --shadow-sm:0 6px 18px rgba(0,0,0,.22);
  --shadow-md:0 18px 42px rgba(0,0,0,.35);
  --shadow-lg:0 28px 60px rgba(0,0,0,.45);
  --gold-glow:0 0 0 4px rgba(212,175,55,.15), 0 12px 30px rgba(212,175,55,.25);
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
  background:var(--burgundy);
  overflow-x:hidden;
}

a{
  text-decoration:none;
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

/* =====================================================
   NAVBAR
===================================================== */

.navbar{
  background:var(--burgundy-dark);
  padding:18px 0;
  border-bottom:1px solid rgba(212,175,55,.3);
  transition:padding .35s var(--ease), box-shadow .35s var(--ease);
}

.navbar-brand{
  display:flex;
  align-items:center;
  gap:12px;
  color:var(--white)!important;
  font-size:clamp(21px,2.2vw,26px);
  font-weight:800;
  letter-spacing:.3px;
}

.logo{
  width:56px;
  height:56px;
  object-fit:contain;
  transition:transform .35s var(--ease);
}

.navbar-brand:hover .logo{
  transform:scale(1.06) rotate(-1deg);
}

.nav-link{
  position:relative;
  color:var(--white)!important;
  margin:0 12px;
  padding-bottom:4px!important;
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
  gap:22px;
  align-items:center;
}

.right-icons a{
  color:var(--white);
  font-size:20px;
  transition:color .3s var(--ease), transform .3s var(--ease);
}

.right-icons a:hover{
  color:var(--gold);
  transform:translateY(-3px);
}

/* =====================================================
   HERO
===================================================== */

.about-hero{
  position:relative;
  min-height:min(60vw,650px);

  background:
    linear-gradient(180deg, rgba(20,4,9,.55), rgba(58,13,24,.65) 55%, rgba(58,13,24,.94)),
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

.about-hero::before{
  content:"";
  position:absolute;
  inset:0;
  background:radial-gradient(ellipse at 50% 0%, rgba(212,175,55,.15), transparent 60%);
  pointer-events:none;
}

.floating-deco{
  position:absolute;
  color:var(--gold);
  opacity:.3;
  pointer-events:none;
  z-index:1;
}

.deco-1{ top:16%; left:9%; font-size:32px; animation:floatY 7.5s ease-in-out infinite; }
.deco-2{ top:24%; right:11%; font-size:24px; animation:floatY2 9s ease-in-out infinite; }
.deco-3{ bottom:20%; left:15%; font-size:20px; animation:floatY2 6.5s ease-in-out infinite; }
.deco-4{ bottom:26%; right:8%; font-size:28px; animation:floatY 8s ease-in-out infinite; }

.about-content{
  max-width:720px;
  position:relative;
  z-index:2;
  animation:fadeUp .9s var(--ease) both;
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:16px;
}

.breadcrumb-lux{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:13px;
  color:rgba(248,244,238,.75);
}

.breadcrumb-lux a{
  color:rgba(248,244,238,.75);
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
}

.about-content span.eyebrow{
  color:var(--gold);
  font-size:clamp(15px,1.8vw,20px);
  letter-spacing:4px;
  font-weight:700;
  text-transform:uppercase;
}

.about-content h1{
  color:var(--white);
  font-size:clamp(34px,6vw,60px);
  font-weight:900;
  line-height:1.25;
  text-shadow:0 12px 30px rgba(0,0,0,.4);
}

.hero-divider{
  width:70px;
  height:2px;
  background:linear-gradient(90deg, transparent, var(--gold), transparent);
}

.about-content p{
  color:var(--gold-soft);
  font-size:clamp(16px,1.8vw,22px);
  line-height:1.8;
}

/* =====================================================
   STORY
===================================================== */

.about-story{
  background:var(--burgundy);
  padding:clamp(60px,9vw,110px) 6%;
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:clamp(36px,6vw,70px);
  align-items:center;
  max-width:1400px;
  margin:0 auto;
}

.story-image{
  border-radius:30px;
  overflow:hidden;
  box-shadow:var(--shadow-lg);
  border:1px solid rgba(212,175,55,.3);
  animation:fadeUp .8s var(--ease) both;
}

.story-image img{
  width:100%;
  height:500px;
  object-fit:cover;
  display:block;
  transition:transform .8s var(--ease);
}

.story-image:hover img{
  transform:scale(1.06);
}

.story-text{
  animation:fadeUp .9s var(--ease) .1s both;
}

.story-text span.eyebrow{
  color:var(--gold);
  font-size:15px;
  letter-spacing:3px;
  font-weight:700;
  text-transform:uppercase;
}

.story-text h2{
  color:var(--white);
  font-size:clamp(28px,4.4vw,45px);
  margin:18px 0 22px;
  font-weight:800;
  line-height:1.3;
  position:relative;
  padding-bottom:22px;
}

.story-text h2::after{
  content:"";
  position:absolute;
  right:0;
  bottom:0;
  width:70px;
  height:3px;
  background:var(--gold);
  border-radius:3px;
}

.story-text p{
  color:var(--gold-soft);
  font-size:17.5px;
  line-height:2;
  max-width:60ch;
  white-space:pre-line;
}

/* =====================================================
   MISSION & VISION
===================================================== */

.mission-vision{
  background:var(--burgundy-dark);
  padding:clamp(60px,8vw,90px) 6%;
}

.mv-grid{
  max-width:1200px;
  margin:0 auto;
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:30px;
}

.mv-card{
  background:var(--burgundy);
  padding:48px 38px;
  border-radius:26px;
  text-align:center;
  border:1px solid rgba(212,175,55,.28);
  box-shadow:var(--shadow-sm);
  transition:transform .5s var(--ease), box-shadow .5s var(--ease), border-color .5s var(--ease);
  animation:fadeUp .8s var(--ease) both;
}

.mv-card:hover{
  transform:translateY(-10px);
  box-shadow:var(--shadow-md);
  border-color:rgba(212,175,55,.7);
}

.mv-card i{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:84px;
  height:84px;
  font-size:34px;
  color:var(--gold);
  margin-bottom:26px;
  border-radius:50%;
  background:rgba(212,175,55,.1);
  border:1px solid rgba(212,175,55,.35);
  transition:transform .4s var(--ease), background .4s var(--ease), color .4s var(--ease);
}

.mv-card:hover i{
  transform:scale(1.08) rotate(-4deg);
  background:var(--gold);
  color:var(--burgundy-dark);
}

.mv-card h3{
  color:var(--white);
  font-size:24px;
  font-weight:800;
  margin-bottom:14px;
}

.mv-card p{
  color:var(--gold-soft);
  font-size:16px;
  line-height:1.9;
  opacity:.92;
}

/* =====================================================
   WHY CHOOSE US
===================================================== */

.why-us{
  background:var(--burgundy);
  padding:clamp(60px,8vw,100px) 6%;
}

.section-heading{
  text-align:center;
  margin-bottom:56px;
  animation:fadeUp .8s var(--ease) both;
}

.section-heading span{
  color:var(--gold);
  font-size:13px;
  letter-spacing:4px;
  font-weight:700;
  text-transform:uppercase;
  opacity:.85;
}

.section-heading h2{
  color:var(--white);
  font-size:clamp(28px,4vw,42px);
  font-weight:900;
  margin-top:12px;
  position:relative;
  display:inline-block;
}

.section-heading h2::after{
  content:"";
  display:block;
  width:64px;
  height:3px;
  background:var(--gold);
  margin:16px auto 0;
  border-radius:3px;
}

.why-grid{
  max-width:1300px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:28px;
}

.why-card{
  background:var(--burgundy-dark);
  padding:42px 30px;
  border-radius:24px;
  text-align:center;
  border:1px solid rgba(212,175,55,.25);
  box-shadow:var(--shadow-sm);
  transition:transform .5s var(--ease), box-shadow .5s var(--ease), border-color .5s var(--ease);
  animation:fadeUp .7s var(--ease) both;
}

.why-card:hover{
  transform:translateY(-10px);
  box-shadow:var(--shadow-md);
  border-color:rgba(212,175,55,.65);
}

.why-card i{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:70px;
  height:70px;
  font-size:28px;
  color:var(--gold);
  margin-bottom:20px;
  border-radius:50%;
  background:rgba(212,175,55,.1);
  border:1px solid rgba(212,175,55,.35);
  transition:transform .4s var(--ease), background .4s var(--ease), color .4s var(--ease);
}

.why-card:hover i{
  transform:scale(1.08) rotate(-4deg);
  background:var(--gold);
  color:var(--burgundy-dark);
}

.why-card h3{
  color:var(--white);
  font-size:20px;
  font-weight:800;
  margin-bottom:12px;
}

.why-card p{
  color:var(--gold-soft);
  font-size:15px;
  line-height:1.85;
  opacity:.9;
}

/* =====================================================
   STATISTICS
===================================================== */

.stats-section{
  position:relative;
  background:
    linear-gradient(180deg, rgba(20,4,9,.75), rgba(58,13,24,.9)),
    url('assets/images/hero.jpg.png');
  background-size:cover;
  background-position:center;
  padding:clamp(60px,8vw,90px) 6%;
}

.stats-grid{
  max-width:1200px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:24px;
}

.stat-card{
  background:rgba(255,255,255,.06);
  backdrop-filter:blur(14px) saturate(140%);
  -webkit-backdrop-filter:blur(14px) saturate(140%);
  border:1px solid rgba(212,175,55,.3);
  border-radius:22px;
  padding:38px 20px;
  text-align:center;
  transition:transform .45s var(--ease), border-color .45s var(--ease);
  animation:fadeUp .8s var(--ease) both;
}

.stat-card:hover{
  transform:translateY(-8px);
  border-color:rgba(212,175,55,.7);
}

.stat-card i{
  font-size:26px;
  color:var(--gold);
  margin-bottom:14px;
}

.stat-number{
  display:block;
  color:var(--white);
  font-size:clamp(28px,3.6vw,38px);
  font-weight:900;
}

.stat-label{
  display:block;
  color:var(--gold-soft);
  font-size:13.5px;
  letter-spacing:.3px;
  margin-top:8px;
  opacity:.9;
}

/* =====================================================
   BRAND VALUES
===================================================== */

.about-values{
  background:var(--burgundy-dark);
  padding:clamp(60px,8vw,90px) 6%;
}

.values-grid{
  max-width:1300px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
}

.value-box{
  background:var(--burgundy);
  padding:44px 34px;
  border-radius:24px;
  text-align:center;
  border:1px solid rgba(212,175,55,.3);
  box-shadow:var(--shadow-sm);
  transition:transform .5s var(--ease), box-shadow .5s var(--ease), border-color .5s var(--ease);
  cursor:pointer;
  animation:fadeUp .7s var(--ease) both;
}

.value-box:hover{
  transform:translateY(-12px);
  box-shadow:var(--shadow-md);
  border-color:rgba(212,175,55,.65);
}

.value-box h3{
  color:var(--gold);
  font-size:26px;
  font-weight:800;
  margin-bottom:18px;
  letter-spacing:.3px;
}

.value-box p{
  color:var(--white);
  font-size:17px;
  line-height:1.9;
  opacity:.92;
}

/* =====================================================
   CTA
===================================================== */

.about-cta{
  position:relative;
  background:linear-gradient(180deg, var(--burgundy), #4A0F1C);
  padding:clamp(70px,9vw,100px) 6%;
  text-align:center;
  overflow:hidden;
}

.about-cta::before{
  content:"";
  position:absolute;
  inset:0;
  background:radial-gradient(ellipse at 50% 50%, rgba(212,175,55,.1), transparent 65%);
  pointer-events:none;
}

.about-cta > *{
  position:relative;
  z-index:2;
}

.about-cta span.eyebrow{
  display:block;
  color:var(--gold);
  font-size:13px;
  letter-spacing:4px;
  font-weight:700;
  text-transform:uppercase;
  margin-bottom:16px;
  opacity:.9;
}

.about-cta h2{
  color:var(--white);
  font-size:clamp(28px,4.4vw,45px);
  margin-bottom:36px;
  font-weight:800;
}

.about-cta a{
  display:inline-block;
  background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);
  color:var(--burgundy-dark);
  padding:17px 56px;
  border-radius:50px;
  font-weight:800;
  font-size:17px;
  letter-spacing:.3px;
  box-shadow:0 14px 32px rgba(0,0,0,.35);
  transition:transform .4s var(--ease), box-shadow .4s var(--ease);
}

.about-cta a:hover{
  transform:translateY(-4px) scale(1.05);
  box-shadow:0 18px 40px rgba(0,0,0,.45), var(--gold-glow);
}

/* =====================================================
   FOOTER
===================================================== */

.footer{
  background:var(--burgundy-darkest);
  padding:70px 6% 20px;
  border-top:1px solid rgba(212,175,55,.3);
}

.footer-container{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:44px;
  max-width:1300px;
  margin:0 auto;
}

.footer img{
  width:120px;
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.35));
}

.footer p{
  color:var(--gold-soft);
  margin-top:15px;
  line-height:1.8;
}

.footer h3{
  color:var(--gold);
  margin-bottom:24px;
  font-size:19px;
  font-weight:700;
  letter-spacing:.3px;
}

.footer a{
  display:block;
  color:var(--white);
  margin-bottom:14px;
  transition:color .3s var(--ease), transform .3s var(--ease);
}

.footer a:hover{
  color:var(--gold);
  transform:translateX(-4px);
}

.footer-bottom{
  border-top:1px solid rgba(255,255,255,.1);
  margin-top:50px;
  padding-top:20px;
  text-align:center;
  color:var(--gold-soft);
  font-size:14px;
  letter-spacing:.2px;
}

/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:1200px){

  .about-story{
    gap:44px;
  }

  .stats-grid{
    grid-template-columns:repeat(2,1fr);
  }

}

@media(max-width:992px){

  .about-story{
    grid-template-columns:1fr;
  }

  .story-image img{
    height:400px;
  }

  .mv-grid{
    grid-template-columns:1fr;
  }

  .why-grid,
  .values-grid{
    grid-template-columns:1fr;
  }

  .footer-container{
    grid-template-columns:1fr;
    text-align:center;
    gap:36px;
  }

  .footer a:hover{
    transform:none;
  }

  .about-content h1{
    font-size:38px;
  }

}

@media(max-width:480px){

  .about-hero{
    min-height:520px;
    padding:50px 6%;
  }

  .story-image img{
    height:300px;
  }

  .value-box,
  .why-card,
  .mv-card{
    padding:36px 24px;
  }

  .stats-grid{
    grid-template-columns:1fr 1fr;
    gap:14px;
  }

  .stat-card{
    padding:26px 14px;
  }

  .about-cta{
    padding:60px 6%;
  }

  .about-cta a{
    padding:16px 42px;
  }

  .floating-deco{
    display:none;
  }

}

@media(max-width:360px){

  .about-content h1{
    font-size:30px;
  }

  .navbar-brand span{
    font-size:18px;
  }

}

</style>

<body>



<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg">


<div class="container">


<a class="navbar-brand" href="index.php">

<img src="assets/images/logo.png" class="logo">

<span>توب سودان</span>

</a>



<button class="navbar-toggler bg-white"

type="button"

data-bs-toggle="collapse"

data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>




<div class="collapse navbar-collapse" id="menu">


<ul class="navbar-nav mx-auto">


<li class="nav-item">
<a class="nav-link" href="index.php">
الرئيسية
</a>
</li>



<li class="nav-item">
<a class="nav-link" href="products.php">
المتجر
</a>
</li>



<li class="nav-item">
<a class="nav-link" href="#">
التصنيفات
</a>
</li>



<li class="nav-item">
<a class="nav-link" href="#">
العروض
</a>
</li>



<li class="nav-item">
<a class="nav-link active" href="about.php">
من نحن
</a>
</li>



<li class="nav-item">
<a class="nav-link" href="#">
تواصل معنا
</a>
</li>



</ul>




<div class="right-icons">


<a href="wishlist.php">
<i class="fa-regular fa-heart"></i>
</a>


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







<!-- HERO -->

<section class="about-hero">

<i class="fa-solid fa-gem floating-deco deco-1"></i>
<i class="fa-solid fa-star floating-deco deco-2"></i>
<i class="fa-solid fa-gem floating-deco deco-3"></i>
<i class="fa-solid fa-star floating-deco deco-4"></i>

<div class="about-content">


<div class="breadcrumb-lux">
<a href="index.php">الرئيسية</a>
<i class="fa-solid fa-angle-left"></i>
<span>من نحن</span>
</div>


<span class="eyebrow">
TOOB SUDAN
</span>


<h1>
من قلب السودان... إلى العالم
</h1>

<div class="hero-divider"></div>

<p>
نقدم جمال الثوب السوداني بروح أصيلة وتصاميم فاخرة
</p>


</div>


</section>







<!-- STORY -->


<section class="about-story">


<div class="story-image">


<img src="assets/images/logo.png.png">


</div>




<div class="story-text">


<span class="eyebrow">
قصتنا
</span>


<h2>
تراث يحكى بالخيوط
</h2>


<p>

في توب سودان نؤمن أن الثوب السوداني
ليس مجرد قطعة قماش، بل هو هوية وثقافة
وحكاية تمتد عبر الأجيال.

نختار أجود الخامات ونقدم تصاميم تجمع
بين الأصالة والفخامة الحديثة.

</p>


</div>


</section>




<!-- MISSION & VISION -->

<section class="mission-vision">

<div class="mv-grid">

<div class="mv-card">
<i class="fa-solid fa-bullseye"></i>
<h3>رسالتنا</h3>
<p>
تقديم أثواب سودانية فاخرة تعكس الهوية والتراث، بجودة عالمية تليق بكل امرأة تبحث عن الأصالة والأناقة.
</p>
</div>

<div class="mv-card">
<i class="fa-solid fa-eye"></i>
<h3>رؤيتنا</h3>
<p>
أن نكون العلامة السودانية الأولى عالمياً في عالم الأزياء الفاخرة، ونحمل جمال الثوب السوداني إلى كل بيت حول العالم.
</p>
</div>

</div>

</section>




<!-- WHY CHOOSE US -->

<section class="why-us">

<div class="section-heading">
<span>مميزاتنا</span>
<h2>لماذا توب سودان</h2>
</div>

<div class="why-grid">

<div class="why-card">
<i class="fa-solid fa-gem"></i>
<h3>جودة فاخرة</h3>
<p>خامات مختارة بعناية فائقة لتمنحك إحساساً استثنائياً.</p>
</div>

<div class="why-card">
<i class="fa-solid fa-crown"></i>
<h3>تصاميم أصيلة</h3>
<p>تصاميم سودانية خالصة تجمع بين التراث والحداثة.</p>
</div>

<div class="why-card">
<i class="fa-solid fa-truck-fast"></i>
<h3>توصيل سريع</h3>
<p>نوصل طلبك بأسرع وقت ممكن أينما كنت.</p>
</div>

<div class="why-card">
<i class="fa-solid fa-shield-halved"></i>
<h3>دفع آمن</h3>
<p>وسائل دفع موثوقة تحمي بياناتك بالكامل.</p>
</div>

<div class="why-card">
<i class="fa-solid fa-headset"></i>
<h3>خدمة عملاء مميزة</h3>
<p>فريق دعم جاهز لمساعدتك في أي وقت.</p>
</div>

<div class="why-card">
<i class="fa-solid fa-scroll"></i>
<h3>خامات فاخرة</h3>
<p>أقمشة منتقاة توفر لك الراحة والأناقة معاً.</p>
</div>

</div>

</section>




<!-- STATISTICS -->

<section class="stats-section">

<div class="stats-grid">

<div class="stat-card">
<i class="fa-solid fa-shirt"></i>
<span class="stat-number">+500</span>
<span class="stat-label">منتج فاخر</span>
</div>

<div class="stat-card">
<i class="fa-solid fa-face-smile"></i>
<span class="stat-number">+10,000</span>
<span class="stat-label">عميل سعيد</span>
</div>

<div class="stat-card">
<i class="fa-solid fa-box"></i>
<span class="stat-number">+15,000</span>
<span class="stat-label">طلب تم توصيله</span>
</div>

<div class="stat-card">
<i class="fa-solid fa-award"></i>
<span class="stat-number">+5</span>
<span class="stat-label">سنوات خبرة</span>
</div>

</div>

</section>




<!-- VALUES -->


<section class="about-values">

<div class="section-heading">
<span>هويتنا</span>
<h2>قيمنا</h2>
</div>

<div class="values-grid">


<div class="value-box">

<h3>
✨ الجودة
</h3>

<p>
خامات مختارة بعناية وتصاميم عالية الجودة.
</p>

</div>




<div class="value-box">

<h3>
👑 الأصالة
</h3>

<p>
نحافظ على روح الثوب السوداني وتفاصيله.
</p>

</div>




<div class="value-box">

<h3>
🌍 رؤيتنا
</h3>

<p>
إيصال جمال الأزياء السودانية لكل العالم.
</p>

</div>


</div>


</section>







<!-- CTA -->


<section class="about-cta">

<span class="eyebrow">Toob Sudan</span>

<h2>
اكتشف مجموعتنا الفاخرة
</h2>


<a href="products.php">
تسوق الآن
</a>


</section>








<!-- FOOTER -->


<footer class="footer">


<div class="footer-container">



<div class="footer-brand">


<img src="assets/images/logo.png">


<p>
من قلب السودان... إلى العالم
</p>


</div>





<div class="footer-links">


<h3>
روابط
</h3>


<a href="index.php">
الرئيسية
</a>


<a href="products.php">
المتجر
</a>


<a href="about.php">
من نحن
</a>


<a href="#">
تواصل معنا
</a>


</div>







<div class="footer-contact">


<h3>
تواصل معنا
</h3>


<p>
📞 واتساب
</p>


<p>
📷 Instagram
</p>


<p>
🎵 TikTok
</p>


</div>



</div>





<div class="footer-bottom">

© 2026 توب سودان - جميع الحقوق محفوظة

</div>



</footer>






<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>