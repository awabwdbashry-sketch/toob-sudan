<?php

include 'includes/db.php';


if(isset($_POST['send'])){


$name = $_POST['name'];

$email = $_POST['email'];

$phone = $_POST['phone'];

$message = $_POST['message'];



$sql = "INSERT INTO contact_messages
(name,email,phone,message)
VALUES
('$name','$email','$phone','$message')";



if(mysqli_query($conn,$sql)){

echo "<script>alert('تم إرسال الرسالة بنجاح');</script>";

}else{

echo mysqli_error($conn);

}


}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>تواصل معنا | توب سودان</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Aref+Ruqaa:wght@700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<style>

/* =========================
   DESIGN TOKENS — same brand palette
========================= */
:root{
--burgundy:#5B1628;
--burgundy-dark:#3A0D18;
--gold:#D4AF37;
--gold-soft:#E8D5C0;
--cream:#F8F4EE;
--white:#ffffff;
--ink:#090909;
--shadow-sm:0 6px 18px rgba(0,0,0,.22);
--shadow-md:0 18px 42px rgba(0,0,0,.35);
--shadow-lg:0 26px 55px rgba(0,0,0,.45);
--gold-glow:0 0 0 4px rgba(212,175,55,.15), 0 10px 26px rgba(212,175,55,.22);
--ease:cubic-bezier(.25,.8,.25,1);
--display:'Aref Ruqaa', 'Cairo', serif;
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
    -moz-osx-font-smoothing:grayscale;
}

html{

    scroll-behavior:smooth;

}

body{
    background:var(--burgundy);
    color:white;
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

.reveal{
animation:fadeUp .9s var(--ease) both;
}


/* =========================
 NAVBAR
========================= */

.navbar{

    background:var(--burgundy);

    padding:18px 0;

    border-bottom:1px solid rgba(212,175,55,.5);

    transition:padding .35s var(--ease), box-shadow .35s var(--ease);

}



.navbar-brand{

    display:flex;
    align-items:center;
    gap:12px;

    color:white!important;

    font-size:clamp(20px,2.2vw,25px);
    font-weight:900;
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

    color:white!important;

    margin:0 10px;

    padding-bottom:4px !important;

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

    color:white;

    font-size:20px;

    transition:color .3s var(--ease), transform .3s var(--ease);

}


.right-icons a:hover{

    color:var(--gold);

    transform:translateY(-3px);

}




/* =========================
 HERO
========================= */

.contact-hero{

background:

linear-gradient(
180deg,
rgba(35,0,12,.68),
rgba(91,22,40,.6) 55%,
rgba(58,13,24,.94)
),

url('assets/images/hero.jpg.png');


background-size:cover;

background-position:center;

min-height:56vh;

padding:70px 6% 40px;


display:flex;

flex-direction:column;

align-items:center;

justify-content:center;

text-align:center;

position:relative;

}

.contact-hero::before{

content:"”";
position:absolute;
top:6%;
left:50%;
transform:translateX(-50%);
font-family:var(--display);
font-size:220px;
line-height:1;
color:rgba(212,175,55,.08);
pointer-events:none;
z-index:1;
user-select:none;

}



.contact-hero > div.hero-inner{

    position:relative;
    z-index:2;
    animation:fadeUp .9s var(--ease) both;
    max-width:640px;

}



.contact-hero span.eyebrow{

color:var(--gold);

font-size:clamp(13px,1.6vw,17px);

font-weight:700;

letter-spacing:5px;

text-transform:uppercase;

}



.contact-hero h1{

font-family:var(--display);

font-size:clamp(38px,6.4vw,64px);

font-weight:700;

margin:18px 0;

text-shadow:0 12px 30px rgba(0,0,0,.4);

}



.contact-hero p{

color:var(--gold-soft);

font-size:clamp(16px,1.8vw,22px);

line-height:1.8;

}

.breadcrumb-bar{

position:relative;
z-index:2;
display:flex;
align-items:center;
gap:10px;
margin-top:34px;
padding:10px 22px;
border-radius:40px;
background:rgba(255,255,255,.06);
border:1px solid rgba(212,175,55,.3);
backdrop-filter:blur(6px);
animation:fadeIn 1.1s var(--ease) both;
animation-delay:.15s;

}

.breadcrumb-bar a{

color:var(--gold-soft);
text-decoration:none;
font-size:14px;
letter-spacing:.3px;
transition:color .3s var(--ease);

}

.breadcrumb-bar a:hover{
color:var(--gold);
}

.breadcrumb-bar i{

color:var(--gold);
font-size:11px;

}

.breadcrumb-bar span{

color:var(--white);
font-size:14px;
font-weight:600;

}


/* =========================
 SECTION SHELL
========================= */

.section-eyebrow{

color:var(--gold);

font-size:14px;

font-weight:700;

letter-spacing:3px;

text-transform:uppercase;

display:inline-block;

}

.section-eyebrow::before{

content:"";
display:inline-block;
width:26px;
height:1px;
background:var(--gold);
margin-left:10px;
vertical-align:middle;

}

.section-title{

font-family:var(--display);

font-size:clamp(28px,4vw,44px);

margin:14px 0 20px;

font-weight:700;

}


/* =========================
 CONTACT
========================= */


.contact-section{

padding:clamp(60px,9vw,110px) 6% clamp(40px,6vw,70px);


display:grid;

grid-template-columns:1fr 1.05fr;

gap:70px;

align-items:start;


}



.contact-description{

color:var(--gold-soft);

font-size:17px;

line-height:2;

max-width:480px;

}


/* info grid */

.info-grid{

display:grid;

grid-template-columns:1fr 1fr;

gap:18px;

margin-top:34px;

}


.contact-box{


background:var(--burgundy-dark);

padding:26px 24px;


border-radius:20px;


border:1px solid rgba(212,175,55,.28);


box-shadow:var(--shadow-sm);


transition:transform .4s var(--ease), box-shadow .4s var(--ease), border-color .4s var(--ease);


cursor:default;


}



.contact-box:hover{

transform:translateY(-8px);

box-shadow:var(--shadow-md);

border-color:rgba(212,175,55,.6);

}



.contact-box i{

display:inline-flex;

align-items:center;

justify-content:center;

width:56px;

height:56px;

font-size:22px;

color:var(--gold);

margin-bottom:16px;

border-radius:50%;

background:rgba(212,175,55,.1);

border:1px solid rgba(212,175,55,.35);

transition:transform .4s var(--ease), background .4s var(--ease), color .4s var(--ease);

}



.contact-box:hover i{

transform:scale(1.08) rotate(-4deg);

background:var(--gold);

color:var(--burgundy-dark);

}



.contact-box h3{

color:white;

font-size:18px;

font-weight:700;

margin-bottom:6px;

}



.contact-box p{

color:var(--gold-soft);

font-size:14.5px;

line-height:1.6;

letter-spacing:.2px;

}

.contact-box.full{

grid-column:1 / -1;

display:flex;

align-items:center;

gap:20px;

}

.contact-box.full i{

margin-bottom:0;

flex-shrink:0;

}


/* social row */

.social-row{

display:flex;

align-items:center;

gap:16px;

margin-top:34px;

flex-wrap:wrap;

}

.social-label{

color:var(--gold-soft);

font-size:14px;

letter-spacing:.3px;

margin-left:6px;

}

.social-circle{

width:50px;

height:50px;

border-radius:50%;

display:inline-flex;

align-items:center;

justify-content:center;

background:var(--burgundy-dark);

border:1px solid rgba(212,175,55,.35);

color:var(--gold);

font-size:18px;

text-decoration:none;

transition:transform .4s var(--ease), box-shadow .4s var(--ease), background .4s var(--ease), color .4s var(--ease);

}

.social-circle:hover{

transform:translateY(-5px) scale(1.08);

background:var(--gold);

color:var(--burgundy-dark);

box-shadow:var(--gold-glow);

}


/* FORM */


.contact-form{


background:rgba(58,13,24,.55);


backdrop-filter:blur(14px);

-webkit-backdrop-filter:blur(14px);


padding:clamp(32px,4vw,48px);


border-radius:28px;


border:1px solid rgba(212,175,55,.3);


box-shadow:var(--shadow-md);


animation:fadeUp .9s var(--ease) both;


position:relative;


}

.contact-form::before{

content:"";
position:absolute;
inset:0;
border-radius:28px;
padding:1px;
background:linear-gradient(135deg, rgba(212,175,55,.5), transparent 40%, transparent 60%, rgba(212,175,55,.35));
-webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
-webkit-mask-composite:xor;
mask-composite:exclude;
pointer-events:none;

}


.contact-form h2{

font-family:var(--display);

font-size:clamp(24px,3vw,32px);

margin-bottom:6px;

font-weight:700;

}

.contact-form .form-sub{

color:var(--gold-soft);

font-size:14.5px;

margin-bottom:28px;

line-height:1.7;

}

.field{

position:relative;

margin-bottom:20px;

}

.field label{

display:block;

color:var(--gold-soft);

font-size:13px;

letter-spacing:.4px;

margin-bottom:8px;

font-weight:600;

}


.contact-form input,
.contact-form textarea{


width:100%;


background:rgba(91,22,40,.6);


border:1.5px solid rgba(212,175,55,.35);


padding:16px 20px;


border-radius:14px;


color:white;


font-size:15.5px;


transition:border-color .3s var(--ease), box-shadow .3s var(--ease), transform .2s var(--ease);


}



.contact-form input:focus,
.contact-form textarea:focus{

outline:none;

border-color:var(--gold);

box-shadow:var(--gold-glow);

}



.contact-form input::placeholder,
.contact-form textarea::placeholder{

color:var(--gold-soft);

opacity:.65;

font-weight:400;

}



.contact-form textarea{

height:150px;

resize:none;

line-height:1.7;

}



.contact-form button{


width:100%;


background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);


color:#090909;


border:none;


padding:18px;


border-radius:50px;


font-size:17px;


font-weight:800;


letter-spacing:.4px;


box-shadow:0 14px 30px rgba(0,0,0,.35);


transition:transform .35s var(--ease), box-shadow .35s var(--ease);


cursor:pointer;


margin-top:6px;


position:relative;


overflow:hidden;


}



.contact-form button:hover{

transform:translateY(-3px) scale(1.02);

box-shadow:0 18px 38px rgba(0,0,0,.45), var(--gold-glow);

}

.contact-form button:active{

transform:translateY(-1px) scale(1.0);

}

.contact-form button:active::after{

content:"";
position:absolute;
inset:0;
border-radius:inherit;
border:2px solid rgba(9,9,9,.35);
border-top-color:transparent;
animation:spin .6s linear infinite;

}

@keyframes spin{
to{transform:rotate(360deg);}
}

.form-note{

display:flex;
align-items:center;
gap:8px;
margin-top:16px;
color:var(--gold-soft);
font-size:12.5px;
opacity:.8;

}


/* =========================
 MAP
========================= */

.map-section{

padding:0 6% clamp(60px,9vw,100px);

}

.map-head{

text-align:center;

max-width:620px;

margin:0 auto 30px;

}

.map-head h3{

font-family:var(--display);

font-size:clamp(22px,3vw,30px);

font-weight:700;

margin-bottom:10px;

color:var(--white);

}

.map-head p{

color:var(--gold-soft);

font-size:15px;

line-height:1.8;

}

.map-top{

display:flex;

align-items:center;

justify-content:space-between;

gap:18px;

flex-wrap:wrap;

background:var(--burgundy-dark);

border:1px solid rgba(212,175,55,.3);

border-radius:18px;

padding:18px 22px;

margin-bottom:22px;

box-shadow:var(--shadow-sm);

}

.map-card{

display:flex;

align-items:center;

gap:14px;

}

.map-card i{

width:46px;

height:46px;

flex-shrink:0;

display:flex;

align-items:center;

justify-content:center;

border-radius:50%;

background:rgba(212,175,55,.12);

border:1px solid rgba(212,175,55,.35);

color:var(--gold);

font-size:18px;

}

.map-card span{

color:var(--white);

font-size:15.5px;

font-weight:700;

letter-spacing:.2px;

}

.map-btn{

display:inline-flex;

align-items:center;

gap:10px;

background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);

color:#090909;

font-weight:800;

font-size:14.5px;

padding:13px 26px;

border-radius:50px;

text-decoration:none;

letter-spacing:.3px;

box-shadow:0 12px 26px rgba(0,0,0,.32);

transition:transform .35s var(--ease), box-shadow .35s var(--ease);

white-space:nowrap;

}

.map-btn:hover{

transform:translateY(-3px) scale(1.02);

box-shadow:0 16px 34px rgba(0,0,0,.4), var(--gold-glow);

color:#090909;

}

.map-frame{

position:relative;

width:100%;

border-radius:26px;

overflow:hidden;

border:1px solid rgba(212,175,55,.35);

box-shadow:var(--shadow-md);

height:500px;

transition:transform .5s var(--ease), box-shadow .5s var(--ease), border-color .5s var(--ease);

}

.map-frame:hover{

transform:scale(1.012);

box-shadow:var(--shadow-lg), var(--gold-glow);

border-color:rgba(212,175,55,.6);

}

.map-frame iframe{

position:absolute;

inset:0;

width:100%;

height:100%;

border:0;

filter:grayscale(.15) contrast(1.05) saturate(1.05);

}

@media(max-width:480px){

.map-frame{

height:320px;

border-radius:20px;

}

.map-top{

flex-direction:column;

align-items:stretch;

text-align:center;

}

.map-btn{

justify-content:center;

}

}


/* =========================
 FAQ
========================= */

.faq-section{

padding:clamp(20px,4vw,40px) 6% clamp(70px,9vw,110px);

}

.faq-head{

text-align:center;

max-width:620px;

margin:0 auto 46px;

}

.faq-head .contact-description{

margin:0 auto;

text-align:center;

}

.faq-list{

max-width:820px;

margin:0 auto;

display:flex;

flex-direction:column;

gap:14px;

}

.faq-item{

background:var(--burgundy-dark);

border:1px solid rgba(212,175,55,.25);

border-radius:16px;

overflow:hidden;

transition:border-color .35s var(--ease), box-shadow .35s var(--ease);

}

.faq-item[open]{

border-color:rgba(212,175,55,.55);

box-shadow:var(--shadow-sm);

}

.faq-item summary{

list-style:none;

cursor:pointer;

padding:20px 24px;

display:flex;

align-items:center;

justify-content:space-between;

gap:16px;

font-size:16.5px;

font-weight:700;

color:var(--white);

}

.faq-item summary::-webkit-details-marker{

display:none;

}

.faq-item summary .faq-icon{

width:34px;

height:34px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

background:rgba(212,175,55,.12);

border:1px solid rgba(212,175,55,.35);

color:var(--gold);

flex-shrink:0;

transition:transform .35s var(--ease), background .35s var(--ease);

}

.faq-item[open] summary .faq-icon{

transform:rotate(45deg);

background:var(--gold);

color:var(--burgundy-dark);

}

.faq-answer{

padding:0 24px 22px;

color:var(--gold-soft);

font-size:15px;

line-height:1.9;

animation:fadeIn .35s var(--ease) both;

}


/* =========================
 CTA
========================= */

.cta-section{

margin:0 6% clamp(70px,9vw,110px);

border-radius:30px;

padding:clamp(50px,7vw,80px) 6%;

text-align:center;

position:relative;

overflow:hidden;

background:
linear-gradient(135deg, var(--burgundy-dark), var(--burgundy) 60%, var(--burgundy-dark)),
radial-gradient(circle at 20% 20%, rgba(212,175,55,.14), transparent 45%);

border:1px solid rgba(212,175,55,.3);

}

.cta-section h2{

font-family:var(--display);

font-size:clamp(26px,4vw,42px);

font-weight:700;

margin-bottom:14px;

}

.cta-section p{

color:var(--gold-soft);

font-size:16px;

max-width:520px;

margin:0 auto 30px;

line-height:1.8;

}

.cta-btn{

display:inline-block;

background:linear-gradient(135deg,#E9C767,#D4AF37 55%,#B4902B);

color:#090909;

font-weight:800;

font-size:16px;

padding:16px 44px;

border-radius:50px;

text-decoration:none;

letter-spacing:.4px;

box-shadow:0 14px 30px rgba(0,0,0,.35);

transition:transform .35s var(--ease), box-shadow .35s var(--ease);

}

.cta-btn:hover{

transform:translateY(-3px) scale(1.03);

box-shadow:0 18px 38px rgba(0,0,0,.45), var(--gold-glow);

color:#090909;

}


/* =========================
 FOOTER
========================= */


.footer{

background:var(--burgundy-dark);

padding:70px 6% 22px;

border-top:1px solid var(--gold);

}



.footer-container{


display:grid;

grid-template-columns:repeat(3,1fr);

gap:44px;


}



.footer img{

width:120px;

filter:drop-shadow(0 8px 16px rgba(0,0,0,.35));

}



.footer h3{

color:var(--gold);

margin-bottom:22px;

font-size:19px;

font-weight:700;

letter-spacing:.3px;

}



.footer p,
.footer a{


color:var(--gold-soft);

display:block;

margin-bottom:14px;

text-decoration:none;

font-size:15px;

transition:color .3s var(--ease), transform .3s var(--ease);


}


.footer a:hover{

color:var(--gold);

transform:translateX(-4px);

}



.footer-bottom{


margin-top:50px;


padding-top:20px;


border-top:1px solid #ffffff20;


text-align:center;


color:var(--gold-soft);

font-size:14px;

letter-spacing:.2px;


}




/* MOBILE */


@media(max-width:992px){

.contact-section{

gap:44px;

}

}


@media(max-width:900px){


.contact-section{

grid-template-columns:1fr;

}

.info-grid{

grid-template-columns:1fr 1fr;

}


.footer-container{

grid-template-columns:1fr;

text-align:center;

gap:36px;

}


.footer a:hover{

transform:none;

}


.social-row{

justify-content:center;

}


.contact-hero h1{

font-size:38px;

}


}


@media(max-width:480px){

.contact-hero{

min-height:44vh;

padding:56px 6% 36px;

}

.contact-hero::before{

font-size:140px;

}

.info-grid{

grid-template-columns:1fr;

}

.contact-box{

padding:22px 20px;

}

.contact-box i{

width:52px;

height:52px;

font-size:20px;

}

.contact-form{

padding:26px 20px;

border-radius:22px;

}

.contact-form input,
.contact-form textarea{

padding:15px 16px;

}

.cta-section{

margin:0 4% 60px;

border-radius:22px;

}

.faq-item summary{

padding:16px 18px;

font-size:15px;

}

.faq-answer{

padding:0 18px 18px;

}

}


@media(max-width:360px){

.contact-hero h1{

font-size:30px;

}

.navbar-brand span{

font-size:18px;

}

.breadcrumb-bar{

padding:8px 16px;

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
<a class="nav-link" href="#">التصنيفات</a>
</li>


<li class="nav-item">
<a class="nav-link" href="#">العروض</a>
</li>


<li class="nav-item">
<a class="nav-link" href="about.php">من نحن</a>
</li>


<li class="nav-item">
<a class="nav-link active" href="contact.php">تواصل معنا</a>
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

<section class="contact-hero">

<div class="hero-inner">

<span class="eyebrow">TOOB SUDAN</span>

<h1>تواصل معنا</h1>

<p>
نحن هنا لخدمتك دائماً، بأناقة تليق بك
</p>

</div>

<nav class="breadcrumb-bar" aria-label="breadcrumb">

<a href="index.php">الرئيسية</a>

<i class="fa-solid fa-chevron-left"></i>

<span>تواصل معنا</span>

</nav>

</section>


<!-- CONTACT -->

<section class="contact-section">


<div class="reveal">


<span class="section-eyebrow">
تواصل معنا
</span>


<h2 class="section-title">
يسعدنا خدمتك
</h2>


<p class="contact-description">
لديك سؤال أو استفسار؟
تواصل معنا وسنكون سعداء بمساعدتك عبر أي من الوسائل التالية.
</p>


<div class="info-grid">


<div class="contact-box">

<i class="fa-solid fa-phone"></i>

<h3>اتصل بنا</h3>

<p>
+249 XXX XXX XXX
</p>

</div>


<div class="contact-box">

<i class="fa-brands fa-whatsapp"></i>

<h3>واتساب</h3>

<p>
+249 XXX XXX XXX
</p>

</div>


<div class="contact-box">

<i class="fa-solid fa-envelope"></i>

<h3>البريد الإلكتروني</h3>

<p>
info@toobsudan.com
</p>

</div>


<div class="contact-box">

<i class="fa-solid fa-clock"></i>

<h3>ساعات العمل</h3>

<p>
يومياً: 10 صباحاً - 10 مساءً
</p>

</div>


<div class="contact-box full">

<i class="fa-solid fa-location-dot"></i>

<div>

<h3>عنوان المتجر</h3>

<p>
الخرطوم، السودان
</p>

</div>

</div>


</div>


<div class="social-row">

<span class="social-label">تابعنا</span>

<a href="#" class="social-circle" aria-label="Instagram">
<i class="fa-brands fa-instagram"></i>
</a>

<a href="#" class="social-circle" aria-label="TikTok">
<i class="fa-brands fa-tiktok"></i>
</a>

<a href="#" class="social-circle" aria-label="Facebook">
<i class="fa-brands fa-facebook-f"></i>
</a>

<a href="#" class="social-circle" aria-label="WhatsApp">
<i class="fa-brands fa-whatsapp"></i>
</a>

</div>


</div>




<div class="contact-form reveal">


<h2>
أرسل رسالة
</h2>

<p class="form-sub">
سيسعدنا الرد عليك في أقرب وقت ممكن
</p>


<form method="POST">


<div class="field">

<label for="name">الاسم</label>

<input 
type="text" 
name="name"
id="name"
placeholder="اسمك الكامل"
required>

</div>


<div class="field">

<label for="email">البريد الإلكتروني</label>

<input 
type="email"
name="email"
id="email"
placeholder="example@email.com"
required>

</div>


<div class="field">

<label for="phone">رقم الهاتف</label>

<input 
type="text"
name="phone"
id="phone"
placeholder="09XXXXXXXX">

</div>


<div class="field">

<label for="message">الرسالة</label>

<textarea 
name="message"
id="message"
placeholder="اكتب رسالتك هنا..."
required></textarea>

</div>


<button name="send">
إرسال الرسالة
</button>


<p class="form-note">

<i class="fa-solid fa-lock"></i>

بياناتك محفوظة وسرية بالكامل

</p>


</form>


</div>


</section>


<!-- MAP -->

<section class="map-section reveal">

<div class="map-head">

<h3>📍 موقع متجر توب السودان</h3>

<p>
يمكنكم زيارتنا في متجرنا أو التواصل معنا عبر الواتساب.
</p>

</div>

<div class="map-top">

<div class="map-card">

<i class="fa-solid fa-location-dot"></i>

<span>📍 الخرطوم - السودان</span>

</div>

<a href="https://www.google.com/maps?q=Khartoum,Sudan" target="_blank" rel="noopener" class="map-btn">
<i class="fa-solid fa-up-right-from-square"></i>
فتح الموقع في Google Maps
</a>

</div>

<div class="map-frame">

<iframe
src="https://www.google.com/maps?q=Khartoum,Sudan&output=embed"
loading="lazy"
referrerpolicy="no-referrer-when-downgrade"
allowfullscreen
title="موقع متجر توب السودان على الخريطة">
</iframe>

</div>

</section>


<!-- FAQ -->

<section class="faq-section">

<div class="faq-head reveal">

<span class="section-eyebrow">
الأسئلة الشائعة
</span>

<h2 class="section-title">
كل ما تحتاج معرفته
</h2>

<p class="contact-description">
إجابات سريعة على أكثر الأسئلة تكراراً حول الطلب والشحن والدفع
</p>

</div>

<div class="faq-list">

<details class="faq-item">

<summary>

<span>كم تستغرق مدة الشحن؟</span>

<span class="faq-icon"><i class="fa-solid fa-plus"></i></span>

</summary>

<div class="faq-answer">

تختلف مدة الشحن حسب المنطقة، ويتم إبلاغك بالمدة المتوقعة فور تأكيد الطلب.

</div>

</details>


<details class="faq-item">

<summary>

<span>ما هي طرق الدفع المتاحة؟</span>

<span class="faq-icon"><i class="fa-solid fa-plus"></i></span>

</summary>

<div class="faq-answer">

نوفر عدة وسائل دفع آمنة ومريحة، وسيتم عرضها لك عند إتمام عملية الشراء.

</div>

</details>


<details class="faq-item">

<summary>

<span>كيف يمكنني تتبع طلبي؟</span>

<span class="faq-icon"><i class="fa-solid fa-plus"></i></span>

</summary>

<div class="faq-answer">

بعد شحن طلبك ستصلك رسالة تحتوي على تفاصيل التتبع الخاصة به.

</div>

</details>


<details class="faq-item">

<summary>

<span>ما سياسة الإرجاع والاستبدال؟</span>

<span class="faq-icon"><i class="fa-solid fa-plus"></i></span>

</summary>

<div class="faq-answer">

يمكنك طلب الإرجاع أو الاستبدال خلال المدة المحددة بشرط أن تكون القطعة بحالتها الأصلية.

</div>

</details>


<details class="faq-item">

<summary>

<span>كيف أتواصل مع خدمة العملاء؟</span>

<span class="faq-icon"><i class="fa-solid fa-plus"></i></span>

</summary>

<div class="faq-answer">

فريق خدمة العملاء متاح عبر واتساب والبريد الإلكتروني للرد على استفساراتك يومياً.

</div>

</details>


</div>

</section>


<!-- CTA -->

<section class="cta-section reveal">

<h2>
تسوقي أحدث تشكيلاتنا الآن
</h2>

<p>
اكتشفي مجموعتنا الفاخرة الجديدة، وعيشي تجربة تسوق تليق بذوقك
</p>

<a href="products.php" class="cta-btn">
تسوق الآن
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

<p>
واتساب
</p>

<p>
Instagram
</p>

<p>
TikTok
</p>

</div>


</div>


<div class="footer-bottom">

© 2026 توب سودان

</div>


</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>