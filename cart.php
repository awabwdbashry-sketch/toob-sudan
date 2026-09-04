<?php

session_start();

include 'includes/db.php';



if(isset($_POST['add_cart'])){


$product_id = $_POST['product_id'];

$quantity = $_POST['quantity'];



$_SESSION['cart'][$product_id] = $quantity;



header("Location: cart.php");

exit;

}




$cart = $_SESSION['cart'] ?? [];



?>


<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>السلة | توب سودان</title>


<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Aref+Ruqaa:wght@700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<style>

.checkout-btn{

display:inline-flex;
align-items:center;
justify-content:center;
gap:10px;

width:100%;

padding:16px 22px;

background:linear-gradient(135deg,#6b1128,#8b1836);

color:#fff;

font-size:17px;

font-weight:700;

text-decoration:none;

border:none;

border-radius:16px;

box-shadow:
0 12px 30px rgba(107,17,40,.35),
0 0 0 1px rgba(212,175,55,.25);

transition:.35s ease;

position:relative;

overflow:hidden;

cursor:pointer;

margin-top:20px;

}

.checkout-btn::before{

content:"";

position:absolute;

top:0;

left:-120%;

width:70%;

height:100%;

background:linear-gradient(
90deg,
transparent,
rgba(255,255,255,.35),
transparent
);

transition:.7s;

}

.checkout-btn:hover{

transform:translateY(-4px);

background:linear-gradient(135deg,#7c1730,#a01f42);

box-shadow:
0 18px 40px rgba(107,17,40,.45),
0 0 18px rgba(212,175,55,.35);

color:#fff;

}

.checkout-btn:hover::before{

left:130%;

}

.checkout-btn:active{

transform:scale(.98);

}

.checkout-btn i{

font-size:18px;

}

@media(max-width:768px){

.checkout-btn{

width:100%;

padding:15px;

font-size:16px;

border-radius:14px;

}

}
/* =========================
   DESIGN TOKENS
========================= */
:root{
--burgundy:#5A0F23;
--wine:#2E0812;
--wine-soft:#3A0D18;
--black:#0E0206;
--gold:#D4AF37;
--gold-soft:#E8D5C0;
--white:#ffffff;
--danger:#C1443B;
--shadow-sm:0 6px 18px rgba(0,0,0,.25);
--shadow-md:0 18px 42px rgba(0,0,0,.4);
--shadow-lg:0 26px 60px rgba(0,0,0,.5);
--gold-glow:0 0 0 4px rgba(212,175,55,.15), 0 12px 30px rgba(212,175,55,.25);
--ease:cubic-bezier(.25,.8,.25,1);
--display:'Aref Ruqaa','Cairo',serif;
--radius-lg:26px;
--radius-md:18px;
--radius-sm:12px;
}


/* =========================
   GLOBAL
========================= */

*{

box-sizing:border-box;
font-family:'Cairo',sans-serif;
-webkit-font-smoothing:antialiased;

}


html{
scroll-behavior:smooth;
}


body{

background:
radial-gradient(circle at 12% 8%, rgba(212,175,55,.08), transparent 40%),
radial-gradient(circle at 90% 85%, rgba(212,175,55,.06), transparent 45%),
var(--burgundy);

color:white;

padding:clamp(20px,5vw,50px);

min-height:100vh;

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
from{opacity:0;transform:translateY(26px);}
to{opacity:1;transform:translateY(0);}
}

@keyframes fadeIn{
from{opacity:0;}
to{opacity:1;}
}

@keyframes pulseHeart{
0%{transform:scale(1);}
35%{transform:scale(1.35);}
60%{transform:scale(.92);}
100%{transform:scale(1);}
}

@keyframes shakeOut{
0%{transform:translateX(0);opacity:1;}
20%{transform:translateX(-6px);}
40%{transform:translateX(6px);}
60%{transform:translateX(-4px);}
100%{transform:translateX(40px);opacity:0;height:0;margin:0;padding:0;border:0;}
}

@keyframes rippleAnim{
to{
transform:scale(3.2);
opacity:0;
}
}

.reveal{
animation:fadeUp .8s var(--ease) both;
}


/* =========================
   HEADER
========================= */

.page-header{

max-width:1200px;

margin:0 auto 10px;

text-align:center;

animation:fadeUp .7s var(--ease) both;

}

.page-header .eyebrow{

color:var(--gold);

font-size:13px;

font-weight:700;

letter-spacing:4px;

text-transform:uppercase;

}

h1{

font-family:var(--display);

font-size:clamp(30px,4.4vw,46px);

font-weight:700;

color:white;

margin:12px 0 6px;

}

.page-header p{

color:var(--gold-soft);

font-size:15px;

}


/* =========================
   LAYOUT
========================= */

.cart-layout{

max-width:1200px;

margin:40px auto 0;

display:grid;

grid-template-columns:1.7fr 1fr;

gap:34px;

align-items:start;

}

.cart-items{

display:flex;

flex-direction:column;

gap:20px;

}


/* =========================
   CART ITEM CARD
========================= */

.cart-item{

position:relative;

display:grid;

grid-template-columns:140px 1fr auto;

gap:24px;

align-items:center;

background:linear-gradient(155deg, rgba(58,13,24,.85), rgba(46,8,18,.9));

border:1px solid rgba(212,175,55,.25);

border-radius:var(--radius-lg);

padding:20px;

box-shadow:var(--shadow-sm);

backdrop-filter:blur(10px);

-webkit-backdrop-filter:blur(10px);

transition:transform .4s var(--ease), box-shadow .4s var(--ease), border-color .4s var(--ease);

}

.cart-item:hover{

transform:translateY(-6px);

box-shadow:var(--shadow-md), 0 0 0 1px rgba(212,175,55,.2);

border-color:rgba(212,175,55,.55);

}

.cart-item.removing{

animation:shakeOut .5s var(--ease) forwards;

pointer-events:none;

}


/* image */

.item-media{

position:relative;

width:140px;

height:140px;

border-radius:var(--radius-md);

overflow:hidden;

box-shadow:var(--shadow-sm);

border:1px solid rgba(212,175,55,.3);

flex-shrink:0;

}

.item-media img{

width:100%;

height:100%;

object-fit:cover;

display:block;

transition:transform .6s var(--ease);

}

.cart-item:hover .item-media img{

transform:scale(1.12);

}

.item-badge{

position:absolute;

bottom:8px;

right:8px;

left:8px;

background:rgba(14,2,6,.72);

border:1px solid rgba(212,175,55,.4);

color:var(--gold);

font-size:10.5px;

font-weight:700;

text-align:center;

padding:4px 6px;

border-radius:30px;

letter-spacing:.2px;

backdrop-filter:blur(4px);

}


/* body */

.item-body{

display:flex;

flex-direction:column;

gap:10px;

min-width:0;

}

.item-top{

display:flex;

align-items:flex-start;

justify-content:space-between;

gap:10px;

}

.item-category{

display:inline-block;

color:var(--gold);

font-size:11.5px;

font-weight:700;

letter-spacing:1.5px;

text-transform:uppercase;

margin-bottom:6px;

}

.item-name{

font-size:19px;

font-weight:700;

color:white;

line-height:1.4;

}

.wishlist-btn{

flex-shrink:0;

width:42px;

height:42px;

border-radius:50%;

background:rgba(212,175,55,.08);

border:1px solid rgba(212,175,55,.3);

color:var(--gold-soft);

display:flex;

align-items:center;

justify-content:center;

font-size:17px;

cursor:pointer;

transition:transform .35s var(--ease), background .35s var(--ease), color .35s var(--ease), border-color .35s var(--ease);

}

.wishlist-btn:hover{

transform:translateY(-3px);

border-color:var(--gold);

color:var(--gold);

}

.wishlist-btn.active{

background:var(--gold);

color:var(--wine);

border-color:var(--gold);

}

.wishlist-btn.active i{

animation:pulseHeart .5s var(--ease);

}

.item-price-row{

display:flex;

align-items:baseline;

gap:12px;

}

.item-price{

color:var(--gold);

font-size:21px;

font-weight:800;

}

.item-old-price{

color:var(--gold-soft);

opacity:.6;

font-size:14px;

text-decoration:line-through;

}

.item-actions{

display:flex;

align-items:center;

gap:14px;

flex-wrap:wrap;

margin-top:4px;

}

.qty-form{

display:flex;

align-items:center;

gap:10px;

flex-wrap:wrap;

}

.qty-control{

display:flex;

align-items:center;

background:rgba(0,0,0,.22);

border:1px solid rgba(212,175,55,.3);

border-radius:40px;

overflow:hidden;

}

.qty-btn{

width:36px;

height:36px;

border:none;

background:transparent;

color:var(--gold-soft);

font-size:18px;

font-weight:700;

cursor:pointer;

display:flex;

align-items:center;

justify-content:center;

transition:background .3s var(--ease), color .3s var(--ease);

}

.qty-btn:hover{

background:var(--gold);

color:var(--wine);

}

.qty-input{

width:44px;

text-align:center;

background:transparent;

border:none;

color:white;

font-size:15.5px;

font-weight:700;

-moz-appearance:textfield;

}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button{

-webkit-appearance:none;

margin:0;

}

.qty-update-btn{

border:1px solid rgba(212,175,55,.4);

background:transparent;

color:var(--gold);

font-size:12.5px;

font-weight:700;

padding:9px 16px;

border-radius:40px;

cursor:pointer;

opacity:0;

pointer-events:none;

transform:translateX(6px);

transition:opacity .3s var(--ease), transform .3s var(--ease), background .3s var(--ease), color .3s var(--ease);

white-space:nowrap;

}

.qty-form.dirty .qty-update-btn{

opacity:1;

pointer-events:auto;

transform:translateX(0);

}

.qty-update-btn:hover{

background:var(--gold);

color:var(--wine);

}

.remove-btn{

position:relative;

overflow:hidden;

display:flex;

align-items:center;

gap:6px;

background:rgba(193,68,59,.1);

border:1px solid rgba(193,68,59,.4);

color:#F0A79F;

font-size:13px;

font-weight:700;

padding:9px 16px;

border-radius:40px;

cursor:pointer;

transition:background .3s var(--ease), color .3s var(--ease), transform .3s var(--ease);

}

.remove-btn:hover{

background:var(--danger);

color:white;

transform:translateY(-2px);

}

.ripple{

position:absolute;

border-radius:50%;

background:rgba(255,255,255,.5);

transform:scale(0);

pointer-events:none;

animation:rippleAnim .6s var(--ease);

}


/* subtotal */

.item-subtotal{

display:flex;

flex-direction:column;

align-items:center;

justify-content:center;

gap:4px;

padding:14px 18px;

border-radius:var(--radius-md);

background:rgba(212,175,55,.06);

border:1px solid rgba(212,175,55,.25);

min-width:120px;

text-align:center;

}

.subtotal-label{

color:var(--gold-soft);

font-size:11.5px;

letter-spacing:.3px;

}

.subtotal-value{

color:var(--gold);

font-size:19px;

font-weight:800;

}


/* =========================
   SUMMARY CARD
========================= */

.cart-summary{

position:sticky;

top:24px;

background:rgba(46,8,18,.55);

backdrop-filter:blur(16px);

-webkit-backdrop-filter:blur(16px);

border:1px solid rgba(212,175,55,.3);

border-radius:var(--radius-lg);

padding:clamp(26px,3vw,34px);

box-shadow:var(--shadow-md);

}

.cart-summary h2{

font-family:var(--display);

font-size:22px;

font-weight:700;

margin-bottom:22px;

color:white;

}

.summary-row{

display:flex;

align-items:center;

justify-content:space-between;

padding:10px 0;

font-size:15px;

color:var(--gold-soft);

}

.summary-row span:last-child{

color:white;

font-weight:600;

}

.summary-row.discount span:last-child{

color:#8FD19E;

}

.summary-divider{

height:1px;

background:linear-gradient(90deg, transparent, rgba(212,175,55,.5), transparent);

margin:14px 0;

}

.summary-total{

display:flex;

align-items:center;

justify-content:space-between;

margin:6px 0 26px;

}

.summary-total span:first-child{

font-size:16px;

color:white;

font-weight:700;

}

.summary-total .grand-total{

font-family:var(--display);

font-size:28px;

color:var(--gold);

font-weight:700;

}


/* coupon */

.coupon-box{

margin:22px 0 26px;

}

.coupon-label{

display:block;

color:var(--gold-soft);

font-size:12.5px;

letter-spacing:.3px;

margin-bottom:10px;

}

.coupon-fields{

display:flex;

gap:10px;

}

.coupon-fields input{

flex:1;

min-width:0;

background:rgba(0,0,0,.25);

border:1.5px solid rgba(212,175,55,.35);

border-radius:14px;

padding:13px 16px;

color:white;

font-size:14px;

transition:border-color .3s var(--ease), box-shadow .3s var(--ease);

}

.coupon-fields input::placeholder{

color:var(--gold-soft);

opacity:.6;

}

.coupon-fields input:focus{

outline:none;

border-color:var(--gold);

box-shadow:var(--gold-glow);

}

.coupon-apply{

flex-shrink:0;

background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);

color:var(--wine);

border:none;

font-weight:800;

font-size:13.5px;

padding:0 20px;

border-radius:14px;

cursor:pointer;

transition:transform .3s var(--ease), box-shadow .3s var(--ease);

}

.coupon-apply:hover{

transform:translateY(-2px);

box-shadow:0 10px 22px rgba(0,0,0,.35);

}

.coupon-note{

margin-top:8px;

font-size:11.5px;

color:var(--gold-soft);

opacity:.7;

}

.coupon-msg{

margin-top:10px;

font-size:12.5px;

color:#8FD19E;

display:none;

}

.coupon-msg.show{

display:block;

animation:fadeIn .4s var(--ease) both;

}


/* buttons */

.btn-checkout{

position:relative;

overflow:hidden;

display:block;

text-align:center;

background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);

color:#090909;

font-weight:800;

font-size:17px;

padding:18px;

border-radius:50px;

text-decoration:none;

letter-spacing:.3px;

box-shadow:0 14px 30px rgba(0,0,0,.4);

transition:transform .35s var(--ease), box-shadow .35s var(--ease);

}

.btn-checkout:hover{

transform:translateY(-3px) scale(1.015);

box-shadow:0 18px 40px rgba(0,0,0,.5), var(--gold-glow);

color:#090909;

}

.btn-continue{

display:block;

text-align:center;

background:transparent;

color:var(--gold-soft);

border:1.5px solid rgba(212,175,55,.35);

font-weight:700;

font-size:14.5px;

padding:15px;

border-radius:50px;

text-decoration:none;

margin-top:14px;

transition:border-color .3s var(--ease), color .3s var(--ease), background .3s var(--ease);

}

.btn-continue:hover{

border-color:var(--gold);

color:var(--gold);

background:rgba(212,175,55,.06);

}


/* =========================
   EMPTY CART
========================= */

.empty-cart{

max-width:520px;

margin:60px auto;

text-align:center;

background:rgba(46,8,18,.5);

border:1px solid rgba(212,175,55,.25);

border-radius:var(--radius-lg);

padding:clamp(50px,7vw,70px) clamp(24px,5vw,50px);

box-shadow:var(--shadow-md);

}

.empty-icon{

width:110px;

height:110px;

margin:0 auto 26px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

font-size:48px;

background:rgba(212,175,55,.1);

border:1px solid rgba(212,175,55,.35);

color:var(--gold);

}

.empty-cart h2{

font-family:var(--display);

font-size:28px;

font-weight:700;

margin-bottom:12px;

color:white;

}

.empty-cart p{

color:var(--gold-soft);

font-size:15px;

line-height:1.9;

max-width:360px;

margin:0 auto 30px;

}

.empty-cart .btn-checkout{

display:inline-block;

padding:16px 46px;

}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:1024px){

.cart-layout{

grid-template-columns:1fr;

}

.cart-summary{

position:static;

}

}

@media(max-width:768px){

body{

padding:24px 16px;

}

.cart-item{

grid-template-columns:1fr;

text-align:center;

}

.item-media{

width:100%;

height:220px;

margin:0 auto;

}

.item-top{

flex-direction:column;

align-items:center;

}

.wishlist-btn{

position:absolute;

top:16px;

left:16px;

}

.item-actions{

justify-content:center;

}

.item-subtotal{

width:100%;

}

}

@media(max-width:430px){

.cart-item{

padding:16px;

border-radius:20px;

}

.item-media{

height:190px;

border-radius:16px;

}

.item-name{

font-size:17px;

}

.qty-control{

flex:1;

justify-content:space-between;

}

.qty-form{

width:100%;

}

.item-actions{

flex-direction:column;

align-items:stretch;

}

.remove-btn{

justify-content:center;

}

.cart-summary{

padding:22px;

border-radius:20px;

}

.coupon-fields{

flex-direction:column;

}

.coupon-apply{

padding:13px;

}

.btn-checkout,
.btn-continue{

font-size:15.5px;

padding:16px;

}

}

@media(max-width:360px){

h1{

font-size:26px;

}

.item-media{

height:170px;

}

.empty-icon{

width:88px;

height:88px;

font-size:38px;

}

}

</style>

</head>



<body>


<div class="page-header">

<span class="eyebrow">TOOB SUDAN</span>

<h1>🛍 سلة توب سودان</h1>

<p>راجع تشكيلتك الفاخرة قبل إتمام الطلب</p>

</div>




<?php


$total = 0;



if(empty($cart)){


echo '

<div class="empty-cart reveal">

<div class="empty-icon">🛍️</div>

<h2>سلتك فارغة</h2>

<p>لم تقم بإضافة أي منتجات إلى سلتك بعد. تصفح تشكيلتنا الفاخرة الجديدة وابدأ تجربة تسوق تليق بذوقك.</p>

<a href="products.php" class="btn-checkout">ابدأ التسوق</a>

</div>

';


}else{



?>

<div class="cart-layout">

<div class="cart-items">

<?php



foreach($cart as $id=>$qty){



$result=mysqli_query($conn,

"SELECT * FROM products WHERE id='$id'"

);


$product=mysqli_fetch_assoc($result);



$subtotal = $product['price'] * $qty;


$total += $subtotal;


$item_old_price = $product['old_price'] ?? null;

$item_category  = $product['category'] ?? null;



?>



<div class="cart-item reveal" data-price="<?php echo $product['price']; ?>" data-subtotal="<?php echo $subtotal; ?>">


<div class="item-media">

<img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">

<span class="item-badge">✓ منتج أصلي</span>

</div>



<div class="item-body">


<div class="item-top">

<div>

<?php if(!empty($item_category)): ?>

<span class="item-category"><?php echo $item_category; ?></span>

<?php endif; ?>

<h3 class="item-name"><?php echo $product['name']; ?></h3>

</div>


<button type="button" class="wishlist-btn" aria-label="أضف إلى المفضلة">

<i class="fa-regular fa-heart"></i>

</button>

</div>


<div class="item-price-row">

<span class="item-price"><?php echo $product['price']; ?> جنيه</span>

<?php if(!empty($item_old_price) && $item_old_price > $product['price']): ?>

<span class="item-old-price"><?php echo $item_old_price; ?> جنيه</span>

<?php endif; ?>

</div>


<div class="item-actions">

<form method="POST" class="qty-form">

<input type="hidden" name="product_id" value="<?php echo $id; ?>">

<div class="qty-control">

<button type="button" class="qty-btn qty-minus" aria-label="تقليل الكمية">−</button>

<input type="number" name="quantity" class="qty-input" value="<?php echo $qty; ?>" min="1" readonly>

<button type="button" class="qty-btn qty-plus" aria-label="زيادة الكمية">+</button>

</div>

<button type="submit" name="add_cart" class="qty-update-btn">تحديث</button>

</form>


<button type="button" class="remove-btn">

<i class="fa-solid fa-trash-can"></i>

إزالة

</button>


</div>


</div>



<div class="item-subtotal">

<span class="subtotal-label">الإجمالي الفرعي</span>

<span class="subtotal-value"><?php echo $subtotal; ?> جنيه</span>

</div>


</div>



<?php } ?>


</div>


<aside class="cart-summary reveal">

<h2>ملخص الطلب</h2>

<div class="summary-row">
<span>المجموع الفرعي</span>
<span class="js-subtotal"><?php echo $total; ?> جنيه</span>
</div>

<div class="summary-row">
<span>الشحن</span>
<span>يُحسب عند الدفع</span>
</div>

<div class="summary-row discount js-discount-row" style="display:none;">
<span>الخصم</span>
<span class="js-discount">0 جنيه</span>
</div>

<div class="summary-divider"></div>

<div class="summary-total">
<span>الإجمالي الكلي</span>
<span class="grand-total js-grand-total"><?php echo $total; ?> جنيه</span>
</div>

<div class="coupon-box">

<label class="coupon-label" for="coupon">هل لديك كود خصم؟</label>

<div class="coupon-fields">

<input type="text" id="coupon" class="coupon-input" placeholder="أدخل كود الكوبون">

<button type="button" class="coupon-apply">تطبيق</button>

</div>

<p class="coupon-note">سيتم تفعيل الكوبون بشكل نهائي عند إتمام الطلب</p>

<p class="coupon-msg"></p>

</div>

<?php if(isset($_SESSION['user_id'])){ ?>

<a href="checkout.php" class="checkout-btn">
إتمام الطلب
</a>

<?php }else{ ?>

<a href="login.php?redirect=checkout" class="checkout-btn">
سجل الدخول لإتمام الطلب
</a>

<?php } ?>

<a class="btn-continue" href="products.php">مواصلة التسوق</a>

</aside>

</div>


<?php } ?>



<script>

(function(){

// ---- Ripple effect for buttons ----
function addRipple(e){
var btn = e.currentTarget;
var circle = document.createElement('span');
var rect = btn.getBoundingClientRect();
var size = Math.max(rect.width, rect.height);
circle.style.width = circle.style.height = size + 'px';
circle.style.left = (e.clientX - rect.left - size/2) + 'px';
circle.style.top = (e.clientY - rect.top - size/2) + 'px';
circle.classList.add('ripple');
btn.appendChild(circle);
setTimeout(function(){ circle.remove(); }, 650);
}

document.querySelectorAll('.remove-btn, .coupon-apply, .btn-checkout').forEach(function(btn){
btn.style.position = btn.style.position || 'relative';
btn.style.overflow = 'hidden';
btn.addEventListener('click', addRipple);
});


// ---- Quantity +/- : uses the existing add_cart POST flow, no backend change ----
document.querySelectorAll('.qty-form').forEach(function(form){

var input = form.querySelector('.qty-input');
var minus = form.querySelector('.qty-minus');
var plus  = form.querySelector('.qty-plus');
var startValue = input.value;

function markDirty(){
form.classList.toggle('dirty', input.value !== startValue);
}

minus.addEventListener('click', function(){
var v = parseInt(input.value || '1', 10);
if(v > 1){ input.value = v - 1; }
markDirty();
});

plus.addEventListener('click', function(){
var v = parseInt(input.value || '1', 10);
input.value = v + 1;
markDirty();
});

});


// ---- Wishlist toggle (front-end only, no backend endpoint provided) ----
document.querySelectorAll('.wishlist-btn').forEach(function(btn){
btn.addEventListener('click', function(){
btn.classList.toggle('active');
var icon = btn.querySelector('i');
if(btn.classList.contains('active')){
icon.classList.remove('fa-regular');
icon.classList.add('fa-solid');
}else{
icon.classList.remove('fa-solid');
icon.classList.add('fa-regular');
}
});
});


// ---- Remove item (front-end preview only, no backend delete endpoint provided) ----
function recalcTotals(){
var subtotal = 0;
document.querySelectorAll('.cart-item').forEach(function(item){
subtotal += parseFloat(item.getAttribute('data-subtotal')) || 0;
});

var subtotalEl = document.querySelector('.js-subtotal');
var grandEl = document.querySelector('.js-grand-total');
var discountEl = document.querySelector('.js-discount');
var discountRow = document.querySelector('.js-discount-row');

var discount = 0;
if(discountEl && discountRow && discountRow.style.display !== 'none'){
discount = parseFloat(discountEl.getAttribute('data-value')) || 0;
}

if(subtotalEl) subtotalEl.textContent = subtotal + ' جنيه';
if(grandEl) grandEl.textContent = Math.max(subtotal - discount, 0) + ' جنيه';

if(document.querySelectorAll('.cart-item').length === 0){
var itemsWrap = document.querySelector('.cart-items');
if(itemsWrap){
itemsWrap.innerHTML = '<div class="empty-cart reveal"><div class="empty-icon">🛍️</div><h2>سلتك فارغة</h2><p>لقد أزلت جميع المنتجات من هذا العرض. أعد تحميل الصفحة لمزامنة السلة الفعلية.</p><a href="products.php" class="btn-checkout">ابدأ التسوق</a></div>';
}
}
}

document.querySelectorAll('.remove-btn').forEach(function(btn){
btn.addEventListener('click', function(){
var card = btn.closest('.cart-item');
if(!card) return;
card.classList.add('removing');
setTimeout(function(){
card.remove();
recalcTotals();
}, 480);
});
});


// ---- Coupon (front-end demo only, no coupon backend provided) ----
var couponBtn = document.querySelector('.coupon-apply');
if(couponBtn){
couponBtn.addEventListener('click', function(){
var input = document.getElementById('coupon');
var msg = document.querySelector('.coupon-msg');
var discountRow = document.querySelector('.js-discount-row');
var discountEl = document.querySelector('.js-discount');

if(!input.value.trim()){
msg.textContent = 'يرجى إدخال كود الكوبون';
msg.style.color = '#F0A79F';
msg.classList.add('show');
return;
}

var subtotalText = document.querySelector('.js-subtotal').textContent;
var subtotal = parseFloat(subtotalText) || 0;
var discount = Math.round(subtotal * 0.1);

discountEl.setAttribute('data-value', discount);
discountEl.textContent = discount + ' جنيه-';
discountRow.style.display = 'flex';

msg.style.color = '#8FD19E';
msg.textContent = 'تم تطبيق الكوبون كمعاينة، سيتم تأكيده عند إتمام الطلب';
msg.classList.add('show');

recalcTotals();
});
}

})();

</script>


</body>

</html>