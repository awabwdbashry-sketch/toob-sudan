<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT products.*
FROM wishlist
INNER JOIN products
ON wishlist.product_id = products.id
WHERE wishlist.user_id='$user_id'
ORDER BY wishlist.created_at DESC";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>المفضلة | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

:root{
  --burgundy:#5B1628;
  --wine:#3A0D18;
  --wine-light:#4a1220;
  --gold:#D4AF37;
  --gold-light:#f0d878;
  --gold-dim:rgba(212,175,55,.35);
  --cream:#E8D5C0;
  --black:#1a0509;
  --white:#ffffff;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Cairo',sans-serif;
}

html{
scroll-behavior:smooth;
}

body{
background:
radial-gradient(circle at 15% 0%, rgba(212,175,55,.08), transparent 45%),
radial-gradient(circle at 85% 20%, rgba(212,175,55,.06), transparent 40%),
linear-gradient(180deg,var(--black) 0%,var(--wine) 45%,var(--black) 100%);
background-attachment:fixed;
color:#fff;
overflow-x:hidden;
}

/* ===== NAVBAR ===== */

.navbar{

background:rgba(58,13,24,.75);

backdrop-filter:blur(16px);

-webkit-backdrop-filter:blur(16px);

padding:18px 0;

border-bottom:1px solid var(--gold-dim);

position:sticky;

top:0;

z-index:999;

}

.navbar-brand{

display:flex;

align-items:center;

gap:12px;

font-size:26px;

font-weight:900;

color:#fff !important;

letter-spacing:.5px;

}

.logo{

width:60px;

filter:drop-shadow(0 0 8px rgba(212,175,55,.4));

}

.nav-link{

color:#fff !important;

margin:0 10px;

font-weight:bold;

position:relative;

transition:.3s;

}

.nav-link::after{

content:'';

position:absolute;

right:0;

bottom:-6px;

width:0;

height:2px;

background:var(--gold);

transition:.35s;

}

.nav-link:hover::after,
.nav-link.active::after{

width:100%;

}

.nav-link:hover,
.nav-link.active{

color:var(--gold) !important;

}

.right-icons{

display:flex;

gap:18px;

}

.right-icons a{

color:#fff;

font-size:20px;

transition:.3s;

}

.right-icons a:hover{

color:var(--gold);

transform:translateY(-2px);

}

/* ===== HERO ===== */

.hero{

padding:110px 0 90px;

text-align:center;

position:relative;

background:
linear-gradient(rgba(58,13,24,.85),rgba(26,5,9,.92)),
url('assets/images/banner.jpg');

background-size:cover;

background-position:center;

overflow:hidden;

}

.hero::before{

content:'';

position:absolute;

inset:0;

background:radial-gradient(ellipse at center, rgba(212,175,55,.12), transparent 60%);

pointer-events:none;

}

.hero span.eyebrow{

color:var(--gold);

font-size:16px;

letter-spacing:6px;

font-weight:700;

display:inline-block;

animation:fadeUp .8s ease both;

}

.hero h1{

font-size:56px;

font-weight:900;

margin-top:18px;

animation:fadeUp .8s ease .1s both;

text-shadow:0 4px 30px rgba(212,175,55,.25);

}

.hero p.subtitle{

margin-top:16px;

color:var(--cream);

font-size:18px;

max-width:560px;

margin-left:auto;

margin-right:auto;

animation:fadeUp .8s ease .2s both;

}

.gold-divider{

width:90px;

height:3px;

margin:26px auto 0;

background:linear-gradient(90deg,transparent,var(--gold),transparent);

border-radius:10px;

animation:fadeUp .8s ease .3s both;

}

@keyframes fadeUp{

from{opacity:0; transform:translateY(24px);}

to{opacity:1; transform:translateY(0);}

}

/* ===== PRODUCTS ===== */

.products{

padding:80px 7% 100px;

max-width:1500px;

margin:0 auto;

}

.products-grid{

display:grid;

grid-template-columns:repeat(4,1fr);

gap:30px;

}

.product-card{

background:linear-gradient(165deg, rgba(58,13,24,.9), rgba(26,5,9,.95));

border:1px solid var(--gold-dim);

border-radius:24px;

overflow:hidden;

position:relative;

transition:transform .45s cubic-bezier(.2,.8,.2,1), box-shadow .45s, border-color .45s;

opacity:0;

transform:translateY(30px);

animation:cardIn .7s ease forwards;

}

@keyframes cardIn{

to{opacity:1; transform:translateY(0);}

}

.product-card:hover{

transform:translateY(-12px);

border-color:var(--gold);

box-shadow:0 25px 50px rgba(0,0,0,.5), 0 0 35px rgba(212,175,55,.25);

}

.badge{

position:absolute;

top:16px;

right:16px;

z-index:3;

background:linear-gradient(135deg,var(--gold),#b8912b);

color:var(--wine);

font-weight:800;

font-size:12px;

padding:7px 14px;

border-radius:30px;

box-shadow:0 6px 16px rgba(0,0,0,.35);

letter-spacing:.5px;

}

.stock-badge{

position:absolute;

top:16px;

left:16px;

z-index:3;

font-size:12px;

font-weight:700;

padding:6px 13px;

border-radius:30px;

backdrop-filter:blur(6px);

}

.stock-in{

background:rgba(46,160,90,.25);

color:#7ee0a3;

border:1px solid rgba(126,224,163,.4);

}

.stock-out{

background:rgba(220,53,69,.25);

color:#ff9aa5;

border:1px solid rgba(255,154,165,.4);

}

.product-image{

height:340px;

overflow:hidden;

position:relative;

}

.product-image::after{

content:'';

position:absolute;

inset:0;

background:linear-gradient(180deg, transparent 55%, rgba(26,5,9,.85) 100%);

opacity:0;

transition:.4s;

}

.product-card:hover .product-image::after{

opacity:1;

}

.product-image img{

width:100%;

height:100%;

object-fit:cover;

transition:transform .6s ease, filter .6s ease;

}

.product-card:hover .product-image img{

transform:scale(1.1);

filter:brightness(1.05);

}

.quick-view-overlay{

position:absolute;

bottom:-60px;

left:0;

right:0;

display:flex;

justify-content:center;

z-index:4;

transition:bottom .4s ease;

}

.product-card:hover .quick-view-overlay{

bottom:18px;

}

.btn-quickview{

background:rgba(26,5,9,.75);

backdrop-filter:blur(8px);

border:1px solid var(--gold);

color:var(--gold-light);

font-size:13px;

font-weight:700;

padding:9px 20px;

border-radius:30px;

text-decoration:none;

display:flex;

align-items:center;

gap:8px;

transition:.3s;

}

.btn-quickview:hover{

background:var(--gold);

color:var(--wine);

}

.product-info{

padding:22px 20px 24px;

text-align:center;

}

.category-tag{

color:var(--gold);

font-size:12px;

letter-spacing:2px;

font-weight:700;

text-transform:uppercase;

opacity:.85;

}

.product-info h3{

font-size:21px;

font-weight:700;

margin-top:8px;

margin-bottom:8px;

color:#fff;

}

.rating{

display:flex;

justify-content:center;

gap:3px;

margin-bottom:10px;

color:var(--gold);

font-size:13px;

}

.rating i{

filter:drop-shadow(0 0 4px rgba(212,175,55,.4));

}

.price-row{

display:flex;

justify-content:center;

align-items:baseline;

gap:10px;

margin-bottom:20px;

flex-wrap:wrap;

}

.old-price{

color:#a98d92;

font-size:15px;

text-decoration:line-through;

opacity:.75;

}

.price{

font-size:25px;

color:var(--gold);

font-weight:900;

text-shadow:0 2px 12px rgba(212,175,55,.25);

}

.price small{

font-size:14px;

font-weight:600;

margin-right:3px;

}

.actions{

display:flex;

flex-direction:column;

gap:12px;

}

.actions a{

text-decoration:none;

padding:13px;

border-radius:40px;

font-weight:bold;

transition:.35s;

display:flex;

align-items:center;

justify-content:center;

gap:9px;

font-size:15px;

}

.btn-view{

background:rgba(255,255,255,.06);

color:#fff;

border:1px solid rgba(255,255,255,.25);

}

.btn-view:hover{

border-color:var(--gold);

color:var(--gold-light);

background:rgba(212,175,55,.08);

}

.btn-cart{

background:linear-gradient(135deg,var(--gold-light),var(--gold) 55%,#a9812a);

color:var(--wine);

box-shadow:0 8px 20px rgba(212,175,55,.25);

}

.btn-cart:hover{

filter:brightness(1.08);

transform:translateY(-2px);

box-shadow:0 12px 26px rgba(212,175,55,.4);

}

.btn-remove{

background:transparent;

color:#ff9aa5;

border:1.5px solid rgba(220,53,69,.55);

}

.btn-remove:hover{

background:#dc3545;

color:#fff;

border-color:#dc3545;

}

.btn-remove.confirming{

animation:shake .4s ease;

}

@keyframes shake{

0%,100%{transform:translateX(0);}

25%{transform:translateX(-4px);}

75%{transform:translateX(4px);}

}

/* ===== EMPTY STATE ===== */

.empty{

text-align:center;

padding:120px 20px;

max-width:560px;

margin:0 auto;

animation:fadeUp .8s ease both;

}

.empty .heart-wrap{

width:130px;

height:130px;

margin:0 auto 30px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

background:radial-gradient(circle, rgba(212,175,55,.15), transparent 70%);

border:1px solid var(--gold-dim);

}

.empty i{

font-size:56px;

color:var(--gold);

animation:pulseHeart 2.2s ease-in-out infinite;

}

@keyframes pulseHeart{

0%,100%{transform:scale(1);}

50%{transform:scale(1.12);}

}

.empty h2{

font-size:34px;

font-weight:900;

margin-bottom:15px;

}

.empty p{

color:var(--cream);

margin-bottom:34px;

font-size:16px;

line-height:1.8;

}

.shop-btn{

display:inline-flex;

align-items:center;

gap:10px;

background:linear-gradient(135deg,var(--gold-light),var(--gold) 55%,#a9812a);

color:var(--wine);

padding:16px 44px;

border-radius:40px;

text-decoration:none;

font-weight:800;

font-size:16px;

box-shadow:0 10px 25px rgba(212,175,55,.3);

transition:.35s;

}

.shop-btn:hover{

transform:translateY(-3px);

box-shadow:0 16px 34px rgba(212,175,55,.45);

}

/* ===== FOOTER ===== */

.footer{

background:var(--black);

padding:70px 7% 20px;

margin-top:70px;

border-top:1px solid var(--gold-dim);

}

.footer-bottom{

text-align:center;

border-top:1px solid rgba(255,255,255,.1);

padding-top:20px;

color:var(--cream);

font-size:14px;

}

/* ===== CONFIRM MODAL ===== */

.confirm-backdrop{

position:fixed;

inset:0;

background:rgba(0,0,0,.65);

backdrop-filter:blur(4px);

display:none;

align-items:center;

justify-content:center;

z-index:2000;

opacity:0;

transition:opacity .3s ease;

}

.confirm-backdrop.show{

display:flex;

opacity:1;

}

.confirm-box{

background:linear-gradient(160deg,var(--wine),var(--black));

border:1px solid var(--gold-dim);

border-radius:22px;

padding:36px 34px;

max-width:360px;

width:90%;

text-align:center;

transform:scale(.85);

transition:transform .3s ease;

box-shadow:0 25px 60px rgba(0,0,0,.5);

}

.confirm-backdrop.show .confirm-box{

transform:scale(1);

}

.confirm-box i{

font-size:42px;

color:#ff9aa5;

margin-bottom:16px;

}

.confirm-box h4{

font-size:20px;

margin-bottom:10px;

}

.confirm-box p{

color:var(--cream);

font-size:14px;

margin-bottom:26px;

}

.confirm-actions{

display:flex;

gap:12px;

}

.confirm-actions button, .confirm-actions a{

flex:1;

padding:11px;

border-radius:30px;

border:none;

font-weight:700;

cursor:pointer;

text-decoration:none;

font-size:14px;

transition:.3s;

}

.confirm-cancel{

background:rgba(255,255,255,.08);

color:#fff;

}

.confirm-cancel:hover{

background:rgba(255,255,255,.18);

}

.confirm-ok{

background:#dc3545;

color:#fff;

display:inline-block;

}

.confirm-ok:hover{

background:#c82333;

}

/* ===== RESPONSIVE ===== */

@media(max-width:1200px){

.products-grid{

grid-template-columns:repeat(3,1fr);

}

}

@media(max-width:992px){

.products-grid{

grid-template-columns:repeat(2,1fr);

gap:22px;

}

.hero h1{

font-size:44px;

}

}

@media(max-width:600px){

.products{

padding:60px 5% 80px;

}

.products-grid{

grid-template-columns:1fr;

gap:22px;

}

.hero{

padding:80px 0 60px;

}

.hero h1{

font-size:34px;

}

.hero p.subtitle{

font-size:15px;

padding:0 12px;

}

.product-image{

height:300px;

}

.actions a{

padding:14px;

font-size:15px;

}

}

@media(max-width:360px){

.hero h1{

font-size:28px;

}

.product-info h3{

font-size:18px;

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
type="button"
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
<a class="nav-link active" href="wishlist.php">المفضلة</a>
</li>

<li class="nav-item">
<a class="nav-link" href="orders.php">طلباتي</a>
</li>

<li class="nav-item">
<a class="nav-link" href="contact.php">تواصل معنا</a>
</li>

</ul>

<div class="right-icons">

<a href="cart.php">
<i class="fa-solid fa-cart-shopping"></i>
</a>

<a href="logout.php">
<i class="fa-solid fa-right-from-bracket"></i>
</a>

</div>

</div>

</div>

</nav>

<section class="hero">

<span class="eyebrow">TOOB SUDAN</span>

<h1>❤️ المفضلة</h1>

<p class="subtitle">احتفظ بأجمل الثياب السودانية في قائمتك الخاصة.</p>

<div class="gold-divider"></div>

</section>

<section class="products">

<?php if(mysqli_num_rows($result)>0){ ?>

<div class="products-grid">

<?php $card_index = 0; while($product=mysqli_fetch_assoc($result)){ $card_index++; ?>

<div class="product-card" style="animation-delay: <?php echo min($card_index * 0.08, 0.6); ?>s;">

<?php if(isset($product['old_price']) && $product['old_price'] > $product['price']){
    $discount_percent = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
?>
<div class="badge">خصم <?php echo $discount_percent; ?>%</div>
<?php } ?>

<?php if(isset($product['stock'])){ ?>
<?php if($product['stock'] > 0){ ?>
<div class="stock-badge stock-in">متوفر</div>
<?php }else{ ?>
<div class="stock-badge stock-out">غير متوفر</div>
<?php } } ?>

<div class="product-image">

<img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">

<div class="quick-view-overlay">

<a class="btn-quickview" href="product.php?id=<?php echo $product['id']; ?>">

<i class="fa-regular fa-eye"></i> عرض سريع

</a>

</div>

</div>

<div class="product-info">

<?php if(isset($product['category'])){ ?>

<div class="category-tag"><?php echo $product['category']; ?></div>

<?php } ?>

<h3><?php echo $product['name']; ?></h3>

<div class="rating">

<i class="fa-solid fa-star"></i>

<i class="fa-solid fa-star"></i>

<i class="fa-solid fa-star"></i>

<i class="fa-solid fa-star"></i>

<i class="fa-regular fa-star"></i>

</div>

<div class="price-row">

<?php if(isset($product['old_price']) && $product['old_price'] > $product['price']){ ?>

<span class="old-price"><?php echo number_format($product['old_price']); ?> جنيه</span>

<?php } ?>

<div class="price"><?php echo number_format($product['price']); ?> <small>جنيه</small></div>

</div>

<div class="actions">

<a class="btn-view"
href="product.php?id=<?php echo $product['id']; ?>">

<i class="fa-regular fa-eye"></i> عرض المنتج

</a>

<a class="btn-cart"
href="add_to_cart.php?id=<?php echo $product['id']; ?>">

<i class="fa-solid fa-bag-shopping"></i> إضافة للسلة

</a>

<a class="btn-remove js-remove"
href="remove_wishlist.php?id=<?php echo $product['id']; ?>">

<i class="fa-solid fa-trash"></i> حذف من المفضلة

</a>

</div>

</div>

</div>

<?php } ?>

</div>

<?php }else{ ?>

<div class="empty">

<div class="heart-wrap">

<i class="fa-regular fa-heart"></i>

</div>

<h2>لا توجد منتجات في المفضلة</h2>

<p>

لم تقم بإضافة أي ثوب إلى المفضلة حتى الآن. تصفح مجموعتنا الفاخرة واختر ما يناسب ذوقك الرفيع.

</p>

<a href="products.php" class="shop-btn">

<i class="fa-solid fa-bag-shopping"></i> ابدأ التسوق

</a>

</div>

<?php } ?>

</section>

<footer class="footer">

<div class="footer-bottom">

© 2026 Toob Sudan - جميع الحقوق محفوظة

</div>

</footer>

<!-- Remove confirmation modal (UI only — link/href unchanged) -->

<div class="confirm-backdrop" id="confirmBackdrop">

<div class="confirm-box">

<i class="fa-solid fa-trash-can"></i>

<h4>حذف من المفضلة؟</h4>

<p>سيتم إزالة هذا المنتج من قائمة المفضلة الخاصة بك.</p>

<div class="confirm-actions">

<button class="confirm-cancel" id="confirmCancel">إلغاء</button>

<a class="confirm-ok" id="confirmOk" href="#">حذف</a>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Elegant confirmation before following remove-from-wishlist links.
// Does not alter the underlying href/functionality.

document.addEventListener('DOMContentLoaded', function(){

var backdrop = document.getElementById('confirmBackdrop');

var okBtn = document.getElementById('confirmOk');

var cancelBtn = document.getElementById('confirmCancel');

var removeLinks = document.querySelectorAll('.js-remove');

var pendingHref = null;

removeLinks.forEach(function(link){

link.addEventListener('click', function(e){

e.preventDefault();

pendingHref = link.getAttribute('href');

link.classList.add('confirming');

setTimeout(function(){ link.classList.remove('confirming'); }, 400);

backdrop.classList.add('show');

});

});

okBtn.addEventListener('click', function(e){

e.preventDefault();

if(pendingHref){

window.location.href = pendingHref;

}

});

cancelBtn.addEventListener('click', function(){

backdrop.classList.remove('show');

pendingHref = null;

});

backdrop.addEventListener('click', function(e){

if(e.target === backdrop){

backdrop.classList.remove('show');

pendingHref = null;

}

});

});

</script>

</body>

</html>