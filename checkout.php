<?php

session_start();
if(!isset($_SESSION['user_id'])){
    $_SESSION['redirect_after_login'] = "checkout.php";
    header("Location: login.php?message=login_required");
    exit;
}
include 'includes/db.php';


$cart = $_SESSION['cart'] ?? [];


if(empty($cart)){

    header("Location: cart.php");
    exit;

}



$total = 0;
$products = [];



foreach($cart as $id=>$qty){


$result = mysqli_query($conn,

"SELECT * FROM products WHERE id='$id'"

);


$product = mysqli_fetch_assoc($result);


$subtotal = $product['price'] * $qty;


$total += $subtotal;


$products[] = [

"data"=>$product,
"qty"=>$qty

];


}




if(isset($_POST['confirm_order'])){


$name = $_POST['name'];

$phone = $_POST['phone'];

$address = $_POST['address'];

$payment_method = $_POST['payment_method'];



$user_id = $_SESSION['user_id'];

$order_sql = "

INSERT INTO orders

(user_id,name,phone,address,payment_method,total)

VALUES

('$user_id','$name','$phone','$address','$payment_method','$total')

";


mysqli_query($conn,$order_sql);



$order_id = mysqli_insert_id($conn);




foreach($products as $item){



$product_id = $item['data']['id'];

$qty = $item['qty'];

$price = $item['data']['price'];



mysqli_query($conn,

"

INSERT INTO order_items

(order_id,product_id,quantity,price)

VALUES

('$order_id','$product_id','$qty','$price')

"

);



}




unset($_SESSION['cart']);



echo "

<script>

alert('تم إرسال طلبك بنجاح 🎉');

window.location='index.php';

</script>

";



}



?>



<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>إتمام الطلب | توب سودان</title>


<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Aref+Ruqaa:wght@700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">



<style>

/* =========================
   DESIGN TOKENS
========================= */
:root{
--burgundy:#5B1628;
--wine:#3A0D18;
--black:#0E0206;
--gold:#D4AF37;
--gold-soft:#E8D5C0;
--white:#ffffff;
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
   GLOBAL
========================= */

*{

margin:0;
padding:0;
box-sizing:border-box;
font-family:'Cairo',sans-serif;
-webkit-font-smoothing:antialiased;

}


html{
scroll-behavior:smooth;
}


body{

background:
radial-gradient(circle at 10% 5%, rgba(212,175,55,.08), transparent 40%),
radial-gradient(circle at 92% 90%, rgba(212,175,55,.06), transparent 45%),
var(--burgundy);

color:white;

padding:clamp(20px,5vw,50px);

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

@keyframes rippleAnim{
to{transform:scale(3.2);opacity:0;}
}

.reveal{
animation:fadeUp .8s var(--ease) both;
}


/* =========================
   HEADER
========================= */

.page-header{

max-width:1180px;

margin:0 auto 8px;

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

font-size:clamp(30px,4.6vw,48px);

font-weight:700;

color:white;

margin:12px 0 10px;

}

.page-header p{

color:var(--gold-soft);

font-size:15.5px;

max-width:520px;

margin:0 auto;

line-height:1.9;

}


/* =========================
   LAYOUT
========================= */

.checkout{

max-width:1180px;

margin:44px auto 0;

display:grid;

grid-template-columns:1.55fr 1fr;

gap:32px;

align-items:start;

}

.checkout-left{

display:flex;

flex-direction:column;

gap:24px;

}


/* =========================
   GLASS CARD
========================= */

.box{

background:rgba(58,13,24,.6);

backdrop-filter:blur(14px);

-webkit-backdrop-filter:blur(14px);

padding:clamp(26px,3.4vw,38px);

border-radius:var(--radius-lg);

border:1px solid rgba(212,175,55,.28);

box-shadow:var(--shadow-sm);

transition:box-shadow .4s var(--ease), border-color .4s var(--ease);

}

.box:hover{

box-shadow:var(--shadow-md);

border-color:rgba(212,175,55,.45);

}

.box h2{

font-family:var(--display);

color:var(--gold);

font-size:22px;

font-weight:700;

margin-bottom:24px;

display:flex;

align-items:center;

gap:10px;

}

.box h2 i{

font-size:19px;

}

.box h3{

color:var(--gold-soft);

font-size:14.5px;

font-weight:700;

letter-spacing:.3px;

margin:26px 0 14px;

display:flex;

align-items:center;

gap:8px;

}

.box h3:first-of-type{

margin-top:0;

}


/* =========================
   FIELDS
========================= */

.field{

position:relative;

margin-bottom:20px;

}

.field label{

display:block;

color:var(--gold-soft);

font-size:12.5px;

letter-spacing:.3px;

margin-bottom:8px;

font-weight:600;

}

.field .field-icon{

position:absolute;

top:41px;

right:18px;

color:var(--gold);

opacity:.75;

font-size:15px;

pointer-events:none;

}

.field-row{

display:grid;

grid-template-columns:1fr 1fr;

gap:16px;

}


input,
textarea{

width:100%;

background:rgba(91,22,40,.55);

border:1.5px solid rgba(212,175,55,.35);

color:white;

padding:15px 44px 15px 18px;

border-radius:14px;

font-size:15px;

transition:border-color .3s var(--ease), box-shadow .3s var(--ease);

}

.field:not(.field-plain) input,
.field:not(.field-plain) textarea{

padding-right:44px;

}

.field-plain input,
.field-plain textarea{

padding:15px 18px;

}

input::placeholder,
textarea::placeholder{

color:var(--gold-soft);

opacity:.6;

}

input:focus,
textarea:focus{

outline:none;

border-color:var(--gold);

box-shadow:var(--gold-glow);

}

textarea{

height:110px;

resize:none;

line-height:1.7;

}


/* =========================
   DELIVERY / PAYMENT CARDS
========================= */

.option-grid{

display:grid;

grid-template-columns:repeat(3,1fr);

gap:14px;

margin-bottom:6px;

}

.option-card{

position:relative;

cursor:pointer;

display:block;

}

.option-card input{

position:absolute;

opacity:0;

width:0;

height:0;

}

.option-card .card-inner{

display:flex;

flex-direction:column;

align-items:center;

text-align:center;

gap:10px;

padding:20px 12px;

border-radius:var(--radius-md);

background:rgba(91,22,40,.4);

border:1.5px solid rgba(212,175,55,.28);

transition:transform .35s var(--ease), border-color .35s var(--ease), box-shadow .35s var(--ease), background .35s var(--ease);

}

.option-card .card-inner i{

font-size:24px;

color:var(--gold);

width:52px;

height:52px;

display:flex;

align-items:center;

justify-content:center;

border-radius:50%;

background:rgba(212,175,55,.1);

border:1px solid rgba(212,175,55,.3);

transition:transform .35s var(--ease), background .35s var(--ease), color .35s var(--ease);

}

.option-card .card-title{

font-size:14px;

font-weight:700;

color:white;

}

.option-card .card-desc{

font-size:11.5px;

color:var(--gold-soft);

opacity:.8;

line-height:1.5;

}

.option-card:hover .card-inner{

transform:translateY(-4px);

border-color:rgba(212,175,55,.5);

}

.option-card input:checked + .card-inner{

border-color:var(--gold);

box-shadow:var(--gold-glow);

background:rgba(212,175,55,.08);

}

.option-card input:checked + .card-inner i{

background:var(--gold);

color:var(--wine);

transform:scale(1.06);

}

.option-card input:focus-visible + .card-inner{

outline:2px solid var(--gold);

outline-offset:2px;

}


/* payment cards can be 3-col too but slightly larger desc */
.payment-box .option-grid{

grid-template-columns:repeat(3,1fr);

}


/* =========================
   NOTES / COUPON
========================= */

.notes-note,
.coupon-note{

font-size:11.5px;

color:var(--gold-soft);

opacity:.65;

margin-top:-10px;

margin-bottom:6px;

}

.coupon-fields{

display:flex;

gap:10px;

}

.coupon-fields input{

flex:1;

min-width:0;

padding:14px 18px;

}

.coupon-apply{

position:relative;

overflow:hidden;

flex-shrink:0;

background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);

color:var(--wine);

border:none;

font-weight:800;

font-size:13.5px;

padding:0 22px;

border-radius:14px;

cursor:pointer;

transition:transform .3s var(--ease), box-shadow .3s var(--ease);

}

.coupon-apply:hover{

transform:translateY(-2px);

box-shadow:0 10px 22px rgba(0,0,0,.35);

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


/* =========================
   MAIN SUBMIT BUTTON
========================= */

button[name="confirm_order"]{

position:relative;

overflow:hidden;

width:100%;

background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);

color:var(--wine);

border:none;

padding:19px;

border-radius:50px;

font-size:18px;

font-weight:800;

letter-spacing:.3px;

box-shadow:0 14px 30px rgba(0,0,0,.4);

cursor:pointer;

margin-top:10px;

transition:transform .35s var(--ease), box-shadow .35s var(--ease);

}

button[name="confirm_order"]:hover{

transform:translateY(-3px) scale(1.015);

box-shadow:0 18px 40px rgba(0,0,0,.5), var(--gold-glow);

}

.ripple{

position:absolute;

border-radius:50%;

background:rgba(255,255,255,.5);

transform:scale(0);

pointer-events:none;

animation:rippleAnim .6s var(--ease);

}

.btn-continue{

display:block;

text-align:center;

background:transparent;

color:var(--gold-soft);

border:1.5px solid rgba(212,175,55,.4);

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
   ORDER SUMMARY
========================= */

.summary-box{

position:sticky;

top:24px;

}

.summary-items{

display:flex;

flex-direction:column;

gap:14px;

margin-bottom:20px;

max-height:340px;

overflow-y:auto;

padding-right:4px;

}

.summary-items::-webkit-scrollbar{

width:5px;

}

.summary-items::-webkit-scrollbar-thumb{

background:rgba(212,175,55,.35);

border-radius:10px;

}

.product{

display:flex;

align-items:center;

gap:14px;

border-bottom:1px solid rgba(255,255,255,.08);

padding-bottom:14px;

}

.product-media{

position:relative;

width:64px;

height:64px;

flex-shrink:0;

border-radius:14px;

overflow:hidden;

border:1px solid rgba(212,175,55,.3);

box-shadow:var(--shadow-sm);

}

.product-media img{

width:100%;

height:100%;

object-fit:cover;

display:block;

}

.product-media .qty-badge{

position:absolute;

top:-6px;

left:-6px;

background:var(--gold);

color:var(--wine);

font-size:11px;

font-weight:800;

width:22px;

height:22px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

box-shadow:var(--shadow-sm);

}

.product-info{

flex:1;

min-width:0;

}

.product-info .p-name{

font-size:14.5px;

font-weight:700;

color:white;

white-space:nowrap;

overflow:hidden;

text-overflow:ellipsis;

}

.product-info .p-qty{

font-size:12px;

color:var(--gold-soft);

opacity:.8;

margin-top:2px;

}

.price{

color:var(--gold);

font-weight:800;

font-size:14.5px;

white-space:nowrap;

}

.summary-row{

display:flex;

align-items:center;

justify-content:space-between;

padding:9px 0;

font-size:14.5px;

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

margin:12px 0 18px;

}

.total{

display:flex;

align-items:center;

justify-content:space-between;

margin-bottom:26px;

}

.total .total-label{

font-size:16px;

color:white;

font-weight:700;

}

.total .grand-total{

font-family:var(--display);

font-size:30px;

color:var(--gold);

font-weight:700;

}

.secure-note{

display:flex;

align-items:center;

justify-content:center;

gap:8px;

margin-top:16px;

font-size:12px;

color:var(--gold-soft);

opacity:.75;

}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:1024px){

.checkout{

grid-template-columns:1fr;

}

.summary-box{

position:static;

}

}

@media(max-width:768px){

body{

padding:24px 16px;

}

.field-row{

grid-template-columns:1fr;

}

.option-grid{

grid-template-columns:1fr 1fr;

}

}

@media(max-width:430px){

.box{

padding:22px 18px;

border-radius:20px;

}

.option-grid{

grid-template-columns:1fr;

}

.coupon-fields{

flex-direction:column;

}

.coupon-apply{

padding:13px;

}

button[name="confirm_order"],
.btn-continue{

font-size:16px;

padding:16px;

}

h1{

font-size:26px;

}

}

@media(max-width:360px){

.product-info .p-name{

max-width:120px;

}

}



</style>


</head>



<body>


<div class="page-header">

<span class="eyebrow">TOOB SUDAN</span>

<h1>إتمام الطلب 🛍️</h1>

<p>أنت على بعد خطوة واحدة من امتلاك أجمل الثياب السودانية.</p>

</div>


<div class="checkout">


<div class="checkout-left">


<div class="box reveal">


<h2><i class="fa-solid fa-user"></i> بيانات العميل</h2>


<form method="POST" id="checkoutForm">


<div class="field">

<label for="name">الاسم الكامل</label>

<input 
type="text"
name="name"
id="name"
placeholder="اكتب اسمك الكامل"
required>

<i class="fa-solid fa-user field-icon"></i>

</div>


<div class="field-row">

<div class="field">

<label for="phone">رقم الهاتف</label>

<input 
type="text"
name="phone"
id="phone"
placeholder="09XXXXXXXX"
required>

<i class="fa-solid fa-phone field-icon"></i>

</div>


<div class="field">

<label for="email_ui">البريد الإلكتروني</label>

<input 
type="email"
id="email_ui"
placeholder="example@email.com">

<i class="fa-solid fa-envelope field-icon"></i>

</div>

</div>


<h3><i class="fa-solid fa-location-dot"></i> عنوان الشحن</h3>


<div class="field-row">

<div class="field">

<label for="city_ui">المدينة</label>

<input 
type="text"
id="city_ui"
placeholder="مثال: الخرطوم">

<i class="fa-solid fa-city field-icon"></i>

</div>


<div class="field">

<label for="district_ui">الحي</label>

<input 
type="text"
id="district_ui"
placeholder="اسم الحي">

<i class="fa-solid fa-map field-icon"></i>

</div>

</div>


<div class="field-row">

<div class="field" style="grid-column:1 / -1;">

<label for="street_ui">العنوان بالتفصيل</label>

<textarea
id="street_ui"
placeholder="اسم الشارع، رقم المنزل، أقرب علامة مميزة"></textarea>

<i class="fa-solid fa-house field-icon" style="top:41px;"></i>

</div>

</div>


<div class="field">

<label for="postal_ui">الرمز البريدي (اختياري)</label>

<input 
type="text"
id="postal_ui"
placeholder="الرمز البريدي">

<i class="fa-solid fa-mailbox field-icon"></i>

</div>


<textarea
name="address"
id="address"
placeholder="العنوان"
required
style="display:none;"></textarea>


<h3><i class="fa-solid fa-truck-fast"></i> طريقة التوصيل</h3>

<div class="option-grid delivery-box">

<label class="option-card">
<input type="radio" name="delivery_method" value="توصيل منزلي" checked>
<span class="card-inner">
<i class="fa-solid fa-house-chimney"></i>
<span class="card-title">توصيل منزلي</span>
<span class="card-desc">2-4 أيام عمل</span>
</span>
</label>

<label class="option-card">
<input type="radio" name="delivery_method" value="توصيل سريع">
<span class="card-inner">
<i class="fa-solid fa-bolt"></i>
<span class="card-title">توصيل سريع</span>
<span class="card-desc">خلال 24 ساعة</span>
</span>
</label>

<label class="option-card">
<input type="radio" name="delivery_method" value="استلام من المتجر">
<span class="card-inner">
<i class="fa-solid fa-store"></i>
<span class="card-title">استلام من المتجر</span>
<span class="card-desc">جاهز خلال ساعات</span>
</span>
</label>

</div>

<p class="notes-note">يُحدد وقت التوصيل النهائي عند التأكيد الهاتفي للطلب</p>


<div class="payment-box">

<h3><i class="fa-solid fa-credit-card"></i> طريقة الدفع</h3>


<div class="option-grid">

<label class="option-card">
<input 
type="radio"
name="payment_method"
value="الدفع عند الاستلام"
checked>
<span class="card-inner">
<i class="fa-solid fa-money-bill-wave"></i>
<span class="card-title">الدفع عند الاستلام</span>
<span class="card-desc">ادفع نقداً عند وصول طلبك</span>
</span>
</label>


<label class="option-card">
<input 
type="radio"
name="payment_method"
value="تحويل بنكي">
<span class="card-inner">
<i class="fa-solid fa-building-columns"></i>
<span class="card-title">تحويل بنكي</span>
<span class="card-desc">حوّل إلى حسابنا البنكي</span>
</span>
</label>


<label class="option-card">
<input 
type="radio"
name="payment_method"
value="محفظة إلكترونية">
<span class="card-inner">
<i class="fa-solid fa-mobile-screen-button"></i>
<span class="card-title">محفظة إلكترونية</span>
<span class="card-desc">ادفع عبر محفظتك الرقمية</span>
</span>
</label>

</div>


</div>


<h3><i class="fa-solid fa-note-sticky"></i> ملاحظات الطلب (اختياري)</h3>

<div class="field field-plain">

<textarea id="order_notes_ui" placeholder="أي تعليمات إضافية لطلبك أو للتوصيل"></textarea>

</div>


<button name="confirm_order" type="submit">

تأكيد الطلب

</button>


<a href="products.php" class="btn-continue">مواصلة التسوق</a>


</form>


</div>


<div class="box reveal coupon-card">

<h2><i class="fa-solid fa-tag"></i> كود الخصم</h2>

<div class="coupon-fields">

<input type="text" id="coupon" placeholder="أدخل كود الكوبون">

<button type="button" class="coupon-apply">تطبيق</button>

</div>

<p class="coupon-note">سيتم تفعيل الكوبون بشكل نهائي عند تأكيد الطلب</p>

<p class="coupon-msg"></p>

</div>


</div>




<div class="box summary-box reveal">


<h2><i class="fa-solid fa-bag-shopping"></i> ملخص الطلب</h2>


<div class="summary-items">

<?php foreach($products as $item){ ?>


<div class="product">

<div class="product-media">

<?php if(!empty($item['data']['image'])): ?>

<img src="assets/images/products/<?php echo $item['data']['image']; ?>" alt="<?php echo $item['data']['name']; ?>">

<?php endif; ?>

<span class="qty-badge"><?php echo $item['qty']; ?></span>

</div>


<div class="product-info">

<div class="p-name"><?php echo $item['data']['name']; ?></div>

<div class="p-qty">الكمية: <?php echo $item['qty']; ?></div>

</div>


<span class="price">

<?php echo $item['data']['price'] * $item['qty']; ?>

 جنيه

</span>


</div>


<?php } ?>

</div>


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


<div class="total">

<span class="total-label">الإجمالي:</span>

<span class="grand-total js-grand-total"><?php echo $total; ?> جنيه</span>

</div>


<p class="secure-note"><i class="fa-solid fa-lock"></i> عملية دفع آمنة ومشفّرة بالكامل</p>


</div>



</div>



<script>

(function(){

// ---- keep the real "address" field (required by PHP) in sync with the visual fields ----
var addressField = document.getElementById('address');
var city = document.getElementById('city_ui');
var district = document.getElementById('district_ui');
var street = document.getElementById('street_ui');
var postal = document.getElementById('postal_ui');

function syncAddress(){
var parts = [];
if(street.value.trim()) parts.push(street.value.trim());
if(district.value.trim()) parts.push('حي ' + district.value.trim());
if(city.value.trim()) parts.push(city.value.trim());
if(postal.value.trim()) parts.push('الرمز البريدي: ' + postal.value.trim());
addressField.value = parts.join('، ');
}

[city, district, street, postal].forEach(function(el){
el.addEventListener('input', syncAddress);
});

var form = document.getElementById('checkoutForm');
form.addEventListener('submit', function(e){
syncAddress();
if(!addressField.value.trim()){
e.preventDefault();
street.focus();
street.style.borderColor = '#C1443B';
}
});


// ---- ripple effect ----
function addRipple(e){
var btn = e.currentTarget;
var circle = document.createElement('span');
var rect = btn.getBoundingClientRect();
var size = Math.max(rect.width, rect.height);
circle.style.width = circle.style.height = size + 'px';
circle.style.left = (e.clientX - rect.left - size/2) + 'px';
circle.style.top = (e.clientY - rect.top - size/2) + 'px';
circle.classList.add('ripple');
btn.style.position = btn.style.position || 'relative';
btn.style.overflow = 'hidden';
btn.appendChild(circle);
setTimeout(function(){ circle.remove(); }, 650);
}

document.querySelectorAll('button[name="confirm_order"], .coupon-apply').forEach(function(btn){
btn.addEventListener('click', addRipple);
});


// ---- coupon demo (front-end preview only, no coupon backend provided) ----
var couponBtn = document.querySelector('.coupon-apply');
if(couponBtn){
couponBtn.addEventListener('click', function(){
var input = document.getElementById('coupon');
var msg = document.querySelector('.coupon-msg');
var discountRow = document.querySelector('.js-discount-row');
var discountEl = document.querySelector('.js-discount');
var subtotalEl = document.querySelector('.js-subtotal');
var grandEl = document.querySelector('.js-grand-total');

if(!input.value.trim()){
msg.textContent = 'يرجى إدخال كود الكوبون';
msg.style.color = '#F0A79F';
msg.classList.add('show');
return;
}

var subtotal = parseFloat(subtotalEl.textContent) || 0;
var discount = Math.round(subtotal * 0.1);

discountEl.textContent = discount + ' جنيه-';
discountRow.style.display = 'flex';
grandEl.textContent = Math.max(subtotal - discount, 0) + ' جنيه';

msg.style.color = '#8FD19E';
msg.textContent = 'تم تطبيق الكوبون كمعاينة، سيتم تأكيده عند تأكيد الطلب';
msg.classList.add('show');
});
}

})();

</script>


</body>


</html>