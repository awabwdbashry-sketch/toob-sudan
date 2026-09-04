<?php

include 'includes/db.php';


// البحث
$search = "";

if(isset($_GET['search'])){

    $search = $_GET['search'];

}


// التصنيف

$category = "";

if(isset($_GET['category'])){

    $category = $_GET['category'];

}



$sql = "
SELECT products.*, categories.name AS category_name

FROM products

LEFT JOIN categories

ON products.category_id = categories.id

WHERE 1
";



if($search != ""){

$sql .= " AND products.name LIKE '%$search%' ";

}



if($category != ""){

$sql .= " AND products.category_id = '$category' ";

}


$sql .= " ORDER BY products.created_at DESC";



$products = mysqli_query($conn,$sql);

if(!$products){
    die(mysqli_error($conn));
}




$categories = mysqli_query($conn,

"SELECT * FROM categories WHERE status = 1"

);


?>



<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
المتجر | توب سودان
</title>



<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">



<style>

/* =====================================================
   TOOB SUDAN — PRODUCTS PAGE
   Luxury Fashion Redesign — LV / Dior / Gucci inspired
   Brand tokens preserved. Structure & CSS only.
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
  from{opacity:0;transform:translateY(30px);}
  to{opacity:1;transform:translateY(0);}
}

@keyframes fadeIn{
  from{opacity:0;}
  to{opacity:1;}
}

@keyframes shimmer{
  0%{background-position:-200% 0;}
  100%{background-position:200% 0;}
}

/* ============= NAVBAR ============= */

.navbar{
  background:var(--burgundy);
  padding:18px 0;
  border-bottom:1px solid var(--gold);
  transition:padding .35s var(--ease), box-shadow .35s var(--ease);
}

.navbar-brand{
  color:var(--white)!important;
  font-size:clamp(21px,2.2vw,25px);
  font-weight:900;
  letter-spacing:.3px;
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
  transform-origin:center;
  transition:transform .35s var(--ease);
}

.nav-link:hover{
  color:var(--gold)!important;
}

.nav-link:hover::after{
  transform:scaleX(1);
}

.right-icons a{
  color:var(--white);
  font-size:19px;
  margin-right:15px;
  transition:color .3s var(--ease), transform .3s var(--ease);
}

.right-icons a:hover{
  color:var(--gold);
  transform:translateY(-3px);
}

/* ============= STORE HERO ============= */

.store-hero{
  position:relative;
  min-height:min(56vw,480px);
  height:auto;

  background:
    linear-gradient(180deg, rgba(20,4,9,.55), rgba(58,13,24,.72) 60%, rgba(58,13,24,.94)),
    url('assets/images/hero.jpg.png');

  background-size:cover;
  background-position:center;

  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  text-align:center;

  padding:70px 6% 60px;
  overflow:hidden;
}

.store-hero::before{
  content:"";
  position:absolute;
  inset:0;
  background:
    radial-gradient(ellipse at 50% 0%, rgba(212,175,55,.14), transparent 60%);
  pointer-events:none;
}

.hero-inner{
  position:relative;
  z-index:2;
  animation:fadeUp 1s var(--ease) both;
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:16px;
}

.hero-eyebrow{
  color:var(--gold);
  font-size:clamp(12px,1.4vw,15px);
  letter-spacing:6px;
  font-weight:700;
  text-transform:uppercase;
}

.hero-inner h1{
  font-size:clamp(38px,6vw,64px);
  font-weight:900;
  color:var(--white);
  letter-spacing:1px;
  text-shadow:0 14px 34px rgba(0,0,0,.45);
}

.hero-divider{
  width:70px;
  height:2px;
  background:linear-gradient(90deg, transparent, var(--gold), transparent);
  margin-top:4px;
}

.hero-sub{
  max-width:520px;
  color:var(--gold-soft);
  font-size:clamp(13px,1.4vw,15.5px);
  line-height:1.9;
  opacity:.9;
}

.breadcrumb-lux{
  display:flex;
  align-items:center;
  gap:10px;
  margin-top:6px;
  font-size:13px;
  color:rgba(248,244,238,.75);
  letter-spacing:.3px;
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

/* ============= FILTER PANEL ============= */

.filter-wrap{
  position:relative;
  z-index:20;
  padding:0 6%;
  margin-top:-42px;
}

.filter-box{
  max-width:1180px;
  margin:0 auto;

  background:rgba(58,13,24,.72);
  backdrop-filter:blur(18px) saturate(140%);
  -webkit-backdrop-filter:blur(18px) saturate(140%);

  border:1px solid rgba(212,175,55,.35);
  border-radius:24px;
  box-shadow:var(--shadow-md);

  padding:22px 26px;

  display:flex;
  align-items:center;
  gap:16px;
  flex-wrap:wrap;

  animation:fadeUp .9s var(--ease) .15s both;
}

.filter-box.is-sticky{
  position:sticky;
  top:14px;
}

.field-group{
  position:relative;
  flex:1 1 240px;
}

.field-label{
  position:absolute;
  top:-9px;
  right:18px;
  background:var(--burgundy-dark);
  padding:0 8px;
  font-size:10.5px;
  letter-spacing:2px;
  color:var(--gold);
  text-transform:uppercase;
  font-weight:700;
  border-radius:6px;
}

.filter-box input,
.filter-box select{
  width:100%;
  background:rgba(9,9,9,.28);
  border:1.5px solid rgba(212,175,55,.4);
  color:var(--white);
  padding:16px 22px;
  border-radius:16px;
  font-size:15px;
  transition:border-color .3s var(--ease), box-shadow .3s var(--ease), background .3s var(--ease);
}

.filter-box input{
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23D4AF37' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
  background-repeat:no-repeat;
  background-position:left 20px center;
  background-size:17px;
  padding-left:48px;
}

.filter-box select{
  appearance:none;
  -webkit-appearance:none;
  -moz-appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23D4AF37' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat:no-repeat;
  background-position:left 18px center;
  background-size:16px;
  padding-left:44px;
  cursor:pointer;
}

.filter-box input::placeholder{
  color:var(--gold-soft);
  opacity:.7;
}

.filter-box input:focus,
.filter-box select:focus{
  outline:none;
  border-color:var(--gold);
  background:rgba(9,9,9,.42);
  box-shadow:var(--gold-glow);
}

.filter-box select option{
  background:var(--burgundy-dark);
  color:var(--white);
}

.filter-box button{
  flex:0 0 auto;
  background:linear-gradient(135deg,#F1D98A,var(--gold) 55%,#B4902B);
  color:var(--burgundy-dark);
  border:none;
  padding:16px 40px;
  border-radius:16px;
  font-weight:800;
  font-size:15px;
  letter-spacing:.4px;
  box-shadow:0 12px 26px rgba(0,0,0,.32);
  cursor:pointer;
  transition:transform .35s var(--ease), box-shadow .35s var(--ease);
  display:flex;
  align-items:center;
  gap:8px;
}

.filter-box button:hover{
  transform:translateY(-3px) scale(1.02);
  box-shadow:0 16px 32px rgba(0,0,0,.4), var(--gold-glow);
}

/* ============= PRODUCTS SECTION ============= */

.products-section{
  padding:80px 6% 110px;
  max-width:1500px;
  margin:0 auto;
}

.section-heading{
  text-align:center;
  margin-bottom:52px;
  animation:fadeUp .8s var(--ease) both;
}

.section-heading .kicker{
  color:var(--burgundy);
  font-size:12.5px;
  letter-spacing:5px;
  font-weight:700;
  text-transform:uppercase;
  opacity:.75;
}

.section-heading h2{
  font-size:clamp(26px,3.4vw,38px);
  font-weight:900;
  color:var(--burgundy-dark);
  margin-top:10px;
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

.products-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:34px;
}

/* ============= PRODUCT CARD ============= */

.product-card{
  position:relative;
  background:var(--white);
  border-radius:26px;
  overflow:hidden;
  border:1px solid rgba(91,22,40,.08);
  box-shadow:var(--shadow-sm);
  transition:transform .55s var(--ease), box-shadow .55s var(--ease), border-color .55s var(--ease);
  animation:fadeUp .7s var(--ease) both;
  display:flex;
  flex-direction:column;
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

.card-media img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
  transition:transform .8s var(--ease);
}

.product-card:hover .card-media img{
  transform:scale(1.09);
}

.card-media::after{
  content:"";
  position:absolute;
  inset:0;
  background:linear-gradient(180deg, transparent 60%, rgba(9,9,9,.35));
  opacity:0;
  transition:opacity .5s var(--ease);
}

.product-card:hover .card-media::after{
  opacity:1;
}

/* wishlist floating button — decorative, CSS only */
.wishlist-btn{
  position:absolute;
  top:16px;
  left:16px;
  width:42px;
  height:42px;
  border-radius:50%;
  background:rgba(255,255,255,.85);
  backdrop-filter:blur(6px);
  border:1px solid rgba(91,22,40,.12);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:17px;
  color:var(--burgundy);
  z-index:4;
  cursor:pointer;
  transition:transform .35s var(--ease), background .35s var(--ease), color .35s var(--ease);
}

.wishlist-btn:hover{
  background:var(--gold);
  color:var(--burgundy-dark);
  transform:scale(1.1);
}

/* luxury sale badge — shown only when an old price exists, CSS-only via :has() */
.card-media .badge-sale{
  display:none;
}

.product-card:has(.old-price) .card-media .badge-sale{
  display:inline-flex;
}

.badge-sale{
  position:absolute;
  top:16px;
  right:16px;
  align-items:center;
  gap:6px;
  background:var(--burgundy-dark);
  color:var(--gold);
  border:1px solid rgba(212,175,55,.5);
  padding:8px 16px;
  border-radius:20px;
  font-size:11.5px;
  font-weight:800;
  letter-spacing:1.2px;
  text-transform:uppercase;
  z-index:4;
  box-shadow:0 8px 18px rgba(0,0,0,.28);
}

/* quick view — reveals on hover over the image */
.quick-view{
  position:absolute;
  left:16px;
  right:16px;
  bottom:16px;
  z-index:4;
  text-align:center;
  background:rgba(248,244,238,.94);
  color:var(--burgundy-dark);
  padding:13px;
  border-radius:14px;
  font-size:13.5px;
  font-weight:800;
  letter-spacing:.4px;
  text-decoration:none;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  opacity:0;
  transform:translateY(14px);
  transition:opacity .45s var(--ease), transform .45s var(--ease);
}

.product-card:hover .quick-view{
  opacity:1;
  transform:translateY(0);
}

.card-body{
  padding:26px 24px 28px;
  text-align:center;
  display:flex;
  flex-direction:column;
  flex:1;
}

.card-category{
  color:var(--gold);
  background:rgba(212,175,55,.12);
  display:inline-block;
  margin:0 auto 12px;
  padding:5px 16px;
  border-radius:20px;
  font-size:11px;
  letter-spacing:2px;
  text-transform:uppercase;
  font-weight:800;
}

.card-body h3{
  font-size:19px;
  font-weight:700;
  color:var(--burgundy-dark);
  margin:2px 0 14px;
  letter-spacing:.2px;
  line-height:1.5;
}

.price-row{
  display:flex;
  align-items:baseline;
  justify-content:center;
  gap:12px;
  margin-bottom:22px;
}

.old-price{
  text-decoration:line-through;
  color:#b6a698;
  font-size:14.5px;
}

.price{
  color:var(--burgundy);
  font-size:23px;
  font-weight:900;
}

.price::after{
  content:" جنيه";
  font-size:13px;
  font-weight:700;
  opacity:.7;
}

.card-actions{
  margin-top:auto;
  display:flex;
  gap:10px;
}

.btn-outline-lux{
  flex:1;
  background:transparent;
  border:1.5px solid var(--burgundy);
  color:var(--burgundy);
  padding:13px 10px;
  border-radius:30px;
  font-weight:800;
  font-size:13.5px;
  letter-spacing:.2px;
  cursor:pointer;
  transition:all .35s var(--ease);
  display:flex;
  align-items:center;
  justify-content:center;
  gap:6px;
}

.btn-outline-lux:hover{
  background:var(--burgundy);
  color:var(--white);
}

.product-btn{
  flex:1;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:6px;
  background:linear-gradient(135deg,#F1D98A,var(--gold) 55%,#B4902B);
  color:var(--burgundy-dark);
  padding:13px 10px;
  border-radius:30px;
  text-decoration:none;
  font-weight:800;
  font-size:13.5px;
  letter-spacing:.2px;
  box-shadow:0 10px 22px rgba(0,0,0,.18);
  transition:transform .35s var(--ease), box-shadow .35s var(--ease);
}

.product-btn:hover{
  transform:translateY(-3px) scale(1.02);
  box-shadow:0 14px 28px rgba(0,0,0,.28), var(--gold-glow);
}

/* empty state (only relevant if the query legitimately returns nothing) */
.empty-state{
  grid-column:1/-1;
  text-align:center;
  padding:80px 20px;
  color:var(--burgundy-dark);
}

.empty-state i{
  font-size:44px;
  color:var(--gold);
  margin-bottom:18px;
}

/* ============= FOOTER ============= */

.footer{
  background:var(--burgundy-dark);
  padding:60px 6%;
  text-align:center;
  border-top:1px solid var(--gold);
  font-size:15px;
  color:var(--gold-soft);
  letter-spacing:.2px;
}

/* =====================================================
   RESPONSIVE
   Desktop 4 · Tablet 2 · Mobile swipe carousel
===================================================== */

@media(max-width:1199px){
  .products-grid{
    grid-template-columns:repeat(3,1fr);
    gap:26px;
  }
}

@media(max-width:900px){
  .products-grid{
    grid-template-columns:repeat(2,1fr);
    gap:20px;
  }

  .filter-box{
    gap:14px;
    padding:20px;
  }

  .filter-box.is-sticky{
    position:static;
  }

  .field-group{
    flex:1 1 100%;
  }

  .filter-box button{
    flex:1 1 100%;
    justify-content:center;
  }

  .store-hero{
    min-height:340px;
    padding:60px 6% 60px;
  }
}

@media(max-width:480px){
  .products-section{
    padding:60px 5% 80px;
  }

  .products-grid{
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

  .products-grid::-webkit-scrollbar{
    display:none;
  }

  .product-card{
    flex:0 0 78%;
    max-width:280px;
    scroll-snap-align:start;
  }

  .filter-wrap{
    margin-top:-30px;
  }

  .filter-box{
    padding:18px;
    border-radius:20px;
  }

  .store-hero h1{
    font-size:32px;
  }

  .hero-sub{
    font-size:12.5px;
  }
}

@media(max-width:360px){
  .navbar-brand{
    font-size:18px;
  }

  .card-actions{
    flex-direction:column;
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


<a class="nav-link d-inline" href="about.php">
من نحن
</a>


<a class="nav-link d-inline" href="contact.php">
تواصل معنا
</a>


</div>


</div>

</nav>



<section class="store-hero">

<div class="hero-inner">

<span class="hero-eyebrow">TOOB SUDAN — COLLECTION</span>

<h1>المتجر</h1>

<div class="hero-divider"></div>

<p class="hero-sub">
تشكيلة فاخرة من الأثواب السودانية الأصيلة، مصممة بعناية لتعكس الفخامة والأناقة في كل تفصيلة
</p>

<div class="breadcrumb-lux">
<a href="index.php">الرئيسية</a>
<i class="fa-solid fa-angle-left"></i>
<span>المتجر</span>
</div>

</div>

</section>



<div class="filter-wrap">

<form class="filter-box is-sticky">


<div class="field-group">

<span class="field-label">البحث</span>

<input 
type="text"
name="search"
placeholder="ابحث عن ثوب..."
value="<?php echo $search; ?>"
>

</div>



<div class="field-group">

<span class="field-label">التصنيف</span>

<select name="category">


<option value="">
كل التصنيفات
</option>



<?php while($cat=mysqli_fetch_assoc($categories)){ ?>

<option value="<?php echo $cat['id']; ?>">

<?php echo $cat['name']; ?>

</option>


<?php } ?>


</select>

</div>



<button type="submit">

<i class="fa-solid fa-magnifying-glass"></i>

بحث

</button>


</form>

</div>




<section class="products-section">

<div class="section-heading">
<span class="kicker">Handpicked for you</span>
<h2>أحدث المنتجات</h2>
</div>


<div class="products-grid">



<?php while($product=mysqli_fetch_assoc($products)){ ?>



<div class="product-card">


<div class="card-media">

<img src="assets/images/products/<?php echo $product['image']; ?>" loading="lazy">

<a href="add_to_wishlist.php?id=<?php echo $product['id']; ?>" class="wishlist-btn">
    <i class="fa-regular fa-heart"></i>
</a>

<span class="badge-sale">
<i class="fa-solid fa-tag"></i>
تخفيض
</span>

<a class="quick-view" href="product.php?id=<?php echo $product['id']; ?>">
<i class="fa-regular fa-eye"></i>
عرض سريع
</a>

</div>


<div class="card-body">


<span class="card-category">

<?php echo $product['category_name']; ?>

</span>



<h3>

<?php echo $product['name']; ?>

</h3>



<div class="price-row">

<?php if($product['old_price']){ ?>

<span class="old-price">

<?php echo $product['old_price']; ?> جنيه

</span>

<?php } ?>


<span class="price">

<?php echo $product['price']; ?>

</span>

</div>



<div class="card-actions">

<a href="cart.php?id=<?php echo $product['id']; ?>" class="btn-outline-lux">
    <i class="fa-solid fa-bag-shopping"></i>
    أضف للسلة
</a>

<a class="product-btn" href="product.php?id=<?php echo $product['id']; ?>">

عرض المنتج

</a>

</div>


</div>


</div>




<?php } ?>



</div>


</section>





<footer class="footer">

© 2026 توب سودان

</footer>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>


</html>