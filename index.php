<?php

session_start();

// إذا المتجر مغلق والزائر ليس أدمن، سيعرض صفحة store_closed.php ويتوقف هنا
include "includes/store_status.php";

include 'includes/db.php';
?>
// store_status.php includes db.php itself and, when the store is closed
// for a non-admin visitor, renders store_closed.php and exits right here.
// It's the single source of truth for the closed-store behavior, so we
// don't duplicate that check (and don't include db.php a second time).
include "includes/store_status.php";

$settings = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM settings LIMIT 1")
);

$user_name = $_SESSION['user_name'] ?? null;

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>توب سودان | أناقة سودانية</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Reem+Kufi:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/*================================================================
  TOOB SUDAN — Luxury Design System
  A quiet-luxury Sudanese fashion identity: warm ivory & cream
  fields, deep burgundy and hairline gold used as accents rather
  than a wash, Reem Kufi for display moments, Cairo for everything
  people read and act on.
================================================================*/

:root{

--ink:#241318;
--ink-soft:#7d6670;
--ink-faint:#a9969c;

--burgundy-900:#2E0A14;
--burgundy-800:#3A0D18;
--burgundy-700:#5B1028;
--burgundy-600:#7A1936;
--burgundy-500:#96204A;

--gold:#D4AF37;
--gold-deep:#A6811F;
--gold-bright:#E9CD79;
--gold-soft:#E8D5C0;

--cream:#FBF6EE;
--cream-deep:#F4E9D6;
--white:#FFFFFF;

--shadow-sm:0 6px 18px rgba(46,10,20,.08);
--shadow-md:0 20px 44px rgba(46,10,20,.12);
--shadow-lg:0 32px 70px rgba(46,10,20,.16);
--shadow-gold:0 16px 36px rgba(212,175,55,.32);

--ease:cubic-bezier(.25,.8,.25,1);
--ease-soft:cubic-bezier(.16,1,.3,1);

--radius-xl:30px;
--radius-lg:24px;
--radius-md:16px;
--radius-sm:10px;

--font-display:'Reem Kufi','Cairo',sans-serif;
--font-body:'Cairo',sans-serif;

}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:var(--font-body);
-webkit-font-smoothing:antialiased;
-moz-osx-font-smoothing:grayscale;
}

html{
scroll-behavior:smooth;
}

body{
background:var(--white);
overflow-x:hidden;
color:var(--ink);
}

img{
max-width:100%;
display:block;
}

a{
color:inherit;
}

.container{
max-width:1280px;
}

@media (prefers-reduced-motion: reduce){
*{
animation-duration:.001ms !important;
animation-iteration-count:1 !important;
transition-duration:.001ms !important;
scroll-behavior:auto !important;
}
}

*:focus-visible{
outline:2px solid var(--gold-deep);
outline-offset:3px;
}

@keyframes fadeUp{
from{opacity:0;transform:translateY(34px);}
to{opacity:1;transform:translateY(0);}
}

@keyframes fadeIn{
from{opacity:0;}
to{opacity:1;}
}

@keyframes floatY{
0%,100%{transform:translateY(0);}
50%{transform:translateY(-10px);}
}

@keyframes shimmerLine{
0%{background-position:0% 50%;}
100%{background-position:200% 50%;}
}

@keyframes rippleAnim{
to{transform:scale(2.8);opacity:0;}
}

.ripple{
position:absolute;
border-radius:50%;
background:rgba(255,255,255,.55);
transform:scale(0);
animation:rippleAnim .65s var(--ease-soft);
pointer-events:none;
}

/*=========================
Site Header (micro-bar + nav)
=========================*/

.site-header{

position:fixed;
top:0;
right:0;
left:0;
z-index:9999;

}

.micro-bar{

background:var(--burgundy-900);
color:var(--gold-soft);
text-align:center;
font-size:12px;
font-weight:600;
letter-spacing:2px;
padding:9px 16px;
overflow:hidden;
max-height:40px;
transition:max-height .5s var(--ease-soft), padding .5s var(--ease-soft), opacity .4s var(--ease);
white-space:nowrap;
text-overflow:ellipsis;

}

.micro-bar i{

color:var(--gold);
margin-inline-end:8px;
font-size:11px;

}

.site-header.scrolled .micro-bar{

max-height:0;
padding:0;
opacity:0;

}

.navbar{

padding:24px 0;
transition:background .5s var(--ease-soft), padding .4s var(--ease-soft), box-shadow .5s var(--ease-soft), backdrop-filter .5s var(--ease-soft);
background:linear-gradient(180deg, rgba(46,10,20,.55), rgba(46,10,20,0));

}

.site-header.scrolled .navbar{

background:rgba(46,10,20,.94);
box-shadow:var(--shadow-md);
padding:14px 0;
backdrop-filter:blur(14px);
-webkit-backdrop-filter:blur(14px);

}

.logo{

height:50px;
transition:transform .4s var(--ease-soft);

}

.navbar-brand{

font-family:var(--font-display);
font-size:clamp(20px,2.2vw,26px);
font-weight:700;
color:#fff !important;
display:flex;
align-items:center;
gap:13px;
letter-spacing:.3px;

}

.navbar-brand:hover .logo{

transform:scale(1.06) rotate(-1deg);

}

.navbar-toggler{

border:none;
box-shadow:none !important;
border-radius:10px;
padding:8px 11px;

}

.nav-link{

position:relative;
color:#fff !important;
font-weight:600;
margin:0 15px;
padding-bottom:6px !important;
transition:color .35s var(--ease);

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
transition:transform .4s var(--ease-soft);

}

.nav-link:hover{

color:var(--gold) !important;

}

.nav-link:hover::after{

transform:scaleX(1);

}

.right-icons{

display:flex;
align-items:center;
gap:20px;

}

.right-icons a,
.navbar-collapse > a{

color:#fff;
font-size:18px;
transition:color .35s var(--ease), transform .35s var(--ease);

}

.right-icons a:hover,
.navbar-collapse > a:hover{

color:var(--gold);
transform:translateY(-3px);

}

/* ===== USER MENU ===== */

.user-menu .dropdown-toggle{

background:rgba(255,255,255,.14);
backdrop-filter:blur(8px);
color:#ffffff;
border:1px solid rgba(255,255,255,.28);
padding:9px 16px;
border-radius:50px;
font-weight:700;
font-size:14px;
display:flex;
align-items:center;
gap:10px;
transition:background .3s var(--ease), transform .3s var(--ease);

}

.user-menu .dropdown-toggle:hover{

background:rgba(255,255,255,.24);
transform:translateY(-2px);

}

.user-menu .dropdown-toggle::after{

margin-right:8px;

}

.user-menu img{

width:32px;
height:32px;
border-radius:50%;
object-fit:cover;
border:2px solid var(--gold);

}

.user-menu .dropdown-menu{

border:none;
border-radius:18px;
padding:10px;
min-width:230px;
margin-top:14px;
box-shadow:var(--shadow-lg);
animation:fadeUp .3s var(--ease) both;

}

.user-menu .dropdown-item{

padding:12px 14px;
border-radius:12px;
font-weight:600;
color:var(--ink);
display:flex;
align-items:center;
gap:8px;
transition:background .3s var(--ease), color .3s var(--ease);

}

.user-menu .dropdown-item:hover{

background:var(--cream-deep);
color:var(--burgundy-700);

}

.user-menu .dropdown-item i{

width:22px;
color:var(--gold-deep);

}

.logout{

color:#b13434 !important;
font-weight:bold;

}

@media(max-width:992px){

.navbar{

background:var(--burgundy-800);

}

.navbar-collapse{

background:var(--burgundy-800);
margin-top:16px;
padding:22px;
border-radius:18px;
box-shadow:var(--shadow-md);

}

.right-icons{

margin-top:14px;
justify-content:center;

}

}

/*=========================
Hero
=========================*/

.hero{

height:100vh;
min-height:640px;
background:url('assets/images/hero.jpg') center center/cover;
background-attachment:fixed;
position:relative;
display:flex;
align-items:center;

}

.hero::before{

content:"";
position:absolute;
inset:0;
background:linear-gradient(
180deg,
rgba(46,10,20,.72) 0%,
rgba(46,10,20,.32) 42%,
rgba(46,10,20,.42) 70%,
rgba(46,10,20,.86) 100%
);

}

.hero-content{

position:relative;
z-index:2;
color:#fff;
animation:fadeUp 1s var(--ease) both;
padding-top:64px;

}

.hero-eyebrow{

display:flex;
align-items:center;
gap:14px;
margin-bottom:22px;

}

.hero-eyebrow span{

font-size:13px;
font-weight:700;
letter-spacing:4px;
text-transform:uppercase;
color:var(--gold-bright);

}

.hero-eyebrow i{

width:34px;
height:1px;
background:linear-gradient(90deg, var(--gold), transparent);

}

.hero-content h1{

font-family:var(--font-display);
font-size:clamp(42px,7vw,84px);
font-weight:700;
margin-bottom:20px;
letter-spacing:.5px;
line-height:1.08;
text-shadow:0 14px 34px rgba(0,0,0,.4);

}

.hero-content p{

font-size:clamp(16px,1.6vw,20px);
margin-bottom:40px;
line-height:1.95;
max-width:600px;
color:rgba(255,255,255,.9);
font-weight:400;

}

.hero-actions{

display:flex;
flex-wrap:wrap;
align-items:center;
gap:18px;
margin-bottom:52px;

}

.btn-main{

position:relative;
overflow:hidden;
background:linear-gradient(135deg,#7A1936,#5B1028 55%,#3A0D18);
color:#fff;
padding:18px 50px;
border-radius:50px;
font-weight:700;
font-size:15px;
text-decoration:none;
display:inline-block;
transition:transform .4s var(--ease-soft), box-shadow .4s var(--ease-soft), background-position .5s var(--ease);
background-size:200% 200%;
background-position:0% 50%;
border:1px solid rgba(212,175,55,.5);
letter-spacing:.3px;
box-shadow:var(--shadow-sm);

}

.btn-main:active{

transform:translateY(-1px) scale(.98);

}

.btn-main:hover{

background-position:100% 50%;
color:#fff;
transform:translateY(-4px);
box-shadow:var(--shadow-md), var(--shadow-gold);

}

.btn-light2{

position:relative;
overflow:hidden;
background:rgba(255,255,255,.06);
backdrop-filter:blur(6px);
border:1.5px solid rgba(255,255,255,.7);
padding:18px 50px;
border-radius:50px;
color:#fff;
text-decoration:none;
font-weight:700;
font-size:15px;
transition:all .4s var(--ease-soft);
letter-spacing:.3px;
display:inline-block;

}

.btn-light2:active{

transform:translateY(-1px) scale(.98);

}

.btn-light2:hover{

background:#fff;
color:var(--burgundy-700);
border-color:#fff;
transform:translateY(-4px);
box-shadow:var(--shadow-md);

}

.view-all a:active,
.btn-main:active,
.btn-light2:active,
.product-actions a:active{

transform:scale(.97);

}

.hero-trust{

display:flex;
flex-wrap:wrap;
gap:34px;

}

.hero-trust li{

display:flex;
align-items:center;
gap:12px;
list-style:none;
color:rgba(255,255,255,.88);
font-size:14px;
font-weight:600;

}

.hero-trust i{

width:38px;
height:38px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
background:rgba(255,255,255,.1);
border:1px solid rgba(212,175,55,.45);
color:var(--gold-bright);
font-size:15px;

}

.scroll-down{

position:absolute;
bottom:38px;
left:50%;
transform:translateX(-50%);
color:#fff;
font-size:24px;
animation:updown 1.8s ease-in-out infinite;
z-index:2;
opacity:.85;

}

@keyframes updown{

0%,100%{

transform:translate(-50%,0);

}

50%{

transform:translate(-50%,12px);

}

}

@media(max-width:992px){

.hero{

text-align:center;
background-attachment:scroll;

}

.hero-eyebrow{

justify-content:center;

}

.hero-trust{

justify-content:center;

}

}

@media(max-width:480px){

.hero{

min-height:600px;

}

.btn-main,
.btn-light2{

padding:15px 34px;
font-size:14px;

}

.hero-content{

padding-top:40px;

}

}

/*=========================
Category Band
=========================*/

.category-band{

background:var(--white);
padding:clamp(56px,8vw,96px) 6% clamp(20px,4vw,40px);

}

.category-grid{

display:grid;
grid-template-columns:repeat(4,1fr);
gap:24px;
margin-top:clamp(30px,5vw,48px);

}

.category-tile{

position:relative;
display:flex;
flex-direction:column;
align-items:center;
text-align:center;
gap:14px;
padding:38px 20px;
border-radius:var(--radius-lg);
background:linear-gradient(160deg, var(--cream) 0%, var(--cream-deep) 100%);
border:1px solid rgba(212,175,55,.25);
text-decoration:none;
color:var(--ink);
transition:transform .5s var(--ease-soft), box-shadow .5s var(--ease-soft), border-color .5s var(--ease-soft);

}

.category-tile:hover{

transform:translateY(-10px);
box-shadow:var(--shadow-md);
border-color:rgba(212,175,55,.6);

}

.category-icon{

width:64px;
height:64px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
background:linear-gradient(135deg,var(--burgundy-700),var(--burgundy-900));
color:var(--gold-bright);
font-size:24px;
box-shadow:var(--shadow-sm);
transition:transform .5s var(--ease-soft);

}

.category-tile:hover .category-icon{

transform:scale(1.08) rotate(-4deg);

}

.category-tile h4{

font-family:var(--font-display);
font-size:18px;
font-weight:600;

}

.category-tile span{

font-size:12.5px;
letter-spacing:1px;
color:var(--gold-deep);
font-weight:700;
text-transform:uppercase;

}

@media(max-width:900px){

.category-grid{

grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:420px){

.category-tile{

padding:28px 14px;

}

}

/*=========================
Section shell
=========================*/

.section{

padding:clamp(70px,9vw,130px) 6%;

}

.section-cream{

background:linear-gradient(180deg, var(--cream) 0%, var(--cream-deep) 100%);

}

.section-light{

background:var(--white);

}

.section-header{

text-align:center;
margin-bottom:clamp(44px,6vw,68px);
max-width:640px;
margin-left:auto;
margin-right:auto;

}

.section-header .ornament{

width:10px;
height:10px;
margin:0 auto 18px;
background:var(--gold);
transform:rotate(45deg);
box-shadow:var(--shadow-gold);

}

.section-header span.eyebrow{

color:var(--gold-deep);
font-size:13px;
letter-spacing:3px;
text-transform:uppercase;
font-weight:700;

}

.section-header h2{

font-family:var(--font-display);
color:var(--ink);
font-size:clamp(30px,4.4vw,48px);
margin:16px 0;
font-weight:700;
position:relative;

}

.section-light .section-header h2,
.section-light .section-header span.eyebrow,
.section-cream .section-header h2,
.section-cream .section-header span.eyebrow{

color:var(--ink);

}

.section-header h2::after{

content:"";
display:block;
width:70px;
height:2px;
background:linear-gradient(90deg, transparent, var(--gold), transparent);
margin:22px auto 0;
border-radius:3px;

}

.section-header p{

color:var(--ink-soft);
font-size:clamp(15px,1.4vw,17.5px);
max-width:520px;
margin:0 auto;
line-height:1.8;

}

/*=========================
Products grid + card
=========================*/

.products-grid{

display:grid;
grid-template-columns:repeat(4,1fr);
gap:32px;

}

.product-card{

position:relative;
background:var(--white);
border-radius:var(--radius-xl);
padding:14px 14px 8px;
border:1px solid rgba(212,175,55,.18);
box-shadow:var(--shadow-sm);
transition:transform .55s var(--ease-soft), box-shadow .55s var(--ease-soft), border-color .55s var(--ease-soft), opacity .8s var(--ease), translate .8s var(--ease);

}

.product-card.reveal{

opacity:0;
translate:0 24px;

}

.product-card.reveal.in-view{

opacity:1;
translate:0 0;

}

.product-card:hover{

transform:translateY(-12px);
box-shadow:var(--shadow-lg);
border-color:rgba(212,175,55,.55);

}

.product-image{

height:440px;
position:relative;
overflow:hidden;
border-radius:var(--radius-lg);
background:var(--cream);

}

.product-image::after{

content:"";
position:absolute;
inset:0;
background:linear-gradient(180deg, rgba(46,10,20,0) 45%, rgba(46,10,20,.62) 100%);
opacity:0;
transition:opacity .5s var(--ease);
pointer-events:none;

}

.product-card:hover .product-image::after{

opacity:1;

}

.product-image::before{

content:"";
position:absolute;
top:0;
left:-160%;
width:55%;
height:100%;
background:linear-gradient(115deg, transparent, rgba(255,255,255,.4), transparent);
transform:skewX(-18deg);
transition:left .9s var(--ease-soft);
pointer-events:none;
z-index:2;

}

.product-card:hover .product-image::before{

left:160%;

}

.product-image img{

width:100%;
height:100%;
object-fit:cover;
transition:transform .8s var(--ease-soft);

}

.product-card:hover .product-image img{

transform:scale(1.09);

}

.card-badge{

position:absolute;
top:16px;
right:16px;
background:rgba(255,255,255,.16);
backdrop-filter:blur(8px);
-webkit-backdrop-filter:blur(8px);
color:#fff;
border:1px solid rgba(212,175,55,.7);
padding:7px 16px;
border-radius:30px;
font-size:11.5px;
font-weight:700;
letter-spacing:1px;
z-index:4;

}

.vip-badge{

position:absolute;
top:16px;
right:16px;
background:linear-gradient(135deg,#E7C766,var(--gold) 60%,#B8912B);
color:var(--burgundy-900);
padding:8px 17px;
border-radius:30px;
font-size:12px;
font-weight:800;
letter-spacing:.2px;
box-shadow:0 8px 20px rgba(0,0,0,.25), var(--shadow-gold);
z-index:4;
display:inline-flex;
align-items:center;
gap:6px;

}

.wishlist-btn{

position:absolute;
top:16px;
left:16px;
width:42px;
height:42px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
background:rgba(255,255,255,.16);
backdrop-filter:blur(10px);
-webkit-backdrop-filter:blur(10px);
color:#fff;
border:1px solid rgba(255,255,255,.35);
font-size:17px;
cursor:pointer;
z-index:4;
transition:background .35s var(--ease), color .35s var(--ease), transform .35s var(--ease), border-color .35s var(--ease), box-shadow .35s var(--ease);

}

.wishlist-btn:hover{

background:var(--gold);
color:var(--burgundy-900);
border-color:var(--gold);
transform:translateY(-3px) scale(1.07);
box-shadow:var(--shadow-gold);

}

.product-actions{

position:absolute;
bottom:-70px;
left:0;
width:100%;
padding:16px 0;
display:flex;
justify-content:center;
gap:14px;
background:rgba(46,10,20,.22);
backdrop-filter:blur(8px);
-webkit-backdrop-filter:blur(8px);
opacity:0;
transition:bottom .5s var(--ease-soft), opacity .45s var(--ease);
z-index:3;

}

.product-card:hover .product-actions{

bottom:16px;
opacity:1;

}

.product-actions a{

position:relative;
overflow:hidden;
background:linear-gradient(135deg,#E7C766,var(--gold) 60%,#B8912B);
color:var(--burgundy-900);
padding:13px 30px;
border-radius:30px;
border:none;
font-weight:700;
font-size:14.5px;
text-decoration:none;
box-shadow:0 10px 24px rgba(0,0,0,.28);
transition:transform .3s var(--ease), box-shadow .3s var(--ease), filter .3s var(--ease);

}

.product-actions a:hover{

transform:translateY(-3px);
filter:brightness(1.06);
box-shadow:0 14px 28px rgba(0,0,0,.32);

}

.product-info{

padding:24px 14px 28px;
text-align:center;

}

.product-info h3{

font-family:var(--font-display);
color:var(--ink);
font-size:19.5px;
font-weight:600;
letter-spacing:.1px;

}

.stars{

color:var(--gold);
margin:12px 0;
font-size:13px;
letter-spacing:3px;

}

.price{

display:inline-flex;
align-items:baseline;
gap:6px;
padding-top:12px;
margin-top:2px;
border-top:1px solid rgba(212,175,55,.3);

}

.price .amount{

color:var(--ink);
font-size:23px;
font-weight:800;
letter-spacing:.2px;

}

.price .currency{

color:var(--gold-deep);
font-size:13.5px;
font-weight:700;

}

.view-all{

text-align:center;
margin-top:58px;

}

.view-all a{

position:relative;
overflow:hidden;
border:1.5px solid var(--gold-deep);
color:var(--gold-deep);
padding:16px 48px;
border-radius:40px;
display:inline-block;
text-decoration:none;
font-weight:700;
letter-spacing:.3px;
transition:all .4s var(--ease-soft);

}

.view-all a:hover{

background:linear-gradient(135deg,#E7C766,var(--gold) 60%,#B8912B);
color:var(--burgundy-900);
border-color:transparent;
transform:translateY(-3px);
box-shadow:var(--shadow-gold);

}

/* Mobile: swipe carousel */

@media(max-width:1200px){

.products-grid{

grid-template-columns:repeat(3,1fr);

}

}

@media(max-width:900px){

.products-grid{

grid-template-columns:repeat(2,1fr);
gap:22px;

}

}

@media(max-width:768px){

.products-grid{

display:flex;
grid-template-columns:unset;
overflow-x:auto;
overflow-y:hidden;
gap:18px;
padding:6px 4px 20px;
scroll-snap-type:x mandatory;
-webkit-overflow-scrolling:touch;
scrollbar-width:none;

}

.products-grid::-webkit-scrollbar{

display:none;

}

.product-card{

flex:0 0 280px;
width:280px;
scroll-snap-align:start;

}

.product-image{

height:360px;

}

.product-actions{

bottom:16px;
opacity:1;

}

.section-header h2{

font-size:30px;

}

}

@media(max-width:480px){

.product-image{

height:320px;

}

}

/*=========================
Luxury Banner
=========================*/

.luxury-banner{

min-height:560px;
background-image:
linear-gradient(180deg, rgba(46,10,20,.72), rgba(58,13,24,.86)),
url('assets/images/banner.jpg');
background-size:cover;
background-position:center;
background-attachment:fixed;
display:flex;
align-items:center;
justify-content:center;
text-align:center;
position:relative;
padding:60px 6%;

}

.banner-overlay{

max-width:720px;
animation:fadeUp .9s var(--ease) both;

}

.banner-overlay span{

color:var(--gold-bright);
font-size:14px;
letter-spacing:4px;
text-transform:uppercase;
font-weight:700;

}

.banner-overlay h2{

font-family:var(--font-display);
color:#fff;
font-size:clamp(30px,5.4vw,54px);
line-height:1.35;
margin:24px 0;
font-weight:700;
text-shadow:0 12px 28px rgba(0,0,0,.35);

}

.banner-overlay p{

color:rgba(255,255,255,.88);
font-size:clamp(16px,1.7vw,19px);
margin-bottom:44px;

}

.banner-overlay a{

position:relative;
overflow:hidden;
display:inline-block;
background:linear-gradient(135deg,#E7C766,var(--gold) 60%,#B8912B);
color:var(--burgundy-900);
padding:18px 56px;
border-radius:50px;
text-decoration:none;
font-weight:800;
letter-spacing:.3px;
transition:transform .4s var(--ease-soft), box-shadow .4s var(--ease-soft);
box-shadow:0 16px 34px rgba(0,0,0,.28);

}

.banner-overlay a:hover{

transform:translateY(-4px) scale(1.03);
box-shadow:0 20px 42px rgba(0,0,0,.36);

}

@media(max-width:768px){

.luxury-banner{

background-attachment:scroll;
min-height:460px;

}

}

/*=========================
Reviews
=========================*/

.reviews-grid{

display:grid;
grid-template-columns:repeat(3,1fr);
gap:28px;

}

.review-card{

background:var(--white);
border:1px solid rgba(212,175,55,.25);
padding:42px 34px;
border-radius:var(--radius-lg);
text-align:center;
transition:transform .5s var(--ease-soft), box-shadow .5s var(--ease-soft), border-color .5s var(--ease-soft);
box-shadow:var(--shadow-sm);
position:relative;

}

.review-card::before{

content:"\201C";
position:absolute;
top:12px;
right:26px;
font-size:60px;
font-family:Georgia, serif;
color:rgba(212,175,55,.3);
line-height:1;

}

.review-card:hover{

transform:translateY(-10px);
box-shadow:var(--shadow-md);
border-color:rgba(212,175,55,.55);

}

.review-card .stars{

color:var(--gold);
font-size:18px;
letter-spacing:3px;
margin-bottom:20px;

}

.review-card p{

color:var(--ink-soft);
font-size:16.5px;
line-height:1.9;

}

.review-card h4{

font-family:var(--font-display);
color:var(--burgundy-700);
margin-top:24px;
font-weight:600;
font-size:16px;
letter-spacing:.2px;

}

@media(max-width:900px){

.reviews-grid{

grid-template-columns:1fr;
gap:22px;

}

}

/*=========================
Footer
=========================*/

.footer{

background:linear-gradient(180deg, var(--burgundy-900), #200710);
padding:82px 6% 24px;
border-top:1px solid rgba(212,175,55,.3);
position:relative;
color:#fff;

}

.footer-container{

display:grid;
grid-template-columns:1.3fr 1fr 1fr;
gap:48px;

}

.footer-brand img{

width:132px;
filter:drop-shadow(0 8px 16px rgba(0,0,0,.35)) brightness(0) invert(1);
opacity:.95;

}

.footer-brand p{

color:var(--gold-soft);
margin-top:22px;
font-size:15.5px;
line-height:1.9;
max-width:280px;

}

.footer h3{

font-family:var(--font-display);
color:var(--gold);
margin-bottom:26px;
font-size:18px;
font-weight:600;
letter-spacing:.3px;

}

.footer-links a{

display:flex;
align-items:center;
gap:8px;
color:rgba(255,255,255,.85);
text-decoration:none;
margin-bottom:15px;
font-size:15px;
transition:color .35s var(--ease), transform .35s var(--ease);

}

.footer-links a i{

color:var(--gold);
font-size:12px;

}

.footer-links a:hover{

color:var(--gold);
transform:translateX(-5px);

}

.footer-contact-list{

display:flex;
flex-direction:column;
gap:16px;

}

.footer-contact-list a{

display:flex;
align-items:center;
gap:12px;
color:rgba(255,255,255,.9);
text-decoration:none;
font-size:15px;
letter-spacing:.2px;
transition:color .3s var(--ease), transform .3s var(--ease);

}

.footer-contact-list a:hover{

color:var(--gold);
transform:translateX(-5px);

}

.footer-contact-list i{

width:38px;
height:38px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
background:rgba(255,255,255,.08);
border:1px solid rgba(212,175,55,.4);
color:var(--gold);
font-size:15px;
flex-shrink:0;

}

.footer-bottom{

margin-top:58px;
padding-top:24px;
border-top:1px solid rgba(255,255,255,.1);
text-align:center;
color:var(--gold-soft);
font-size:13.5px;
letter-spacing:.2px;

}

@media(max-width:900px){

.footer-container{

grid-template-columns:1fr;
text-align:center;
gap:40px;

}

.footer-brand p{

margin-left:auto;
margin-right:auto;

}

.footer-links a,
.footer-contact-list a{

justify-content:center;

}

.footer-links a:hover,
.footer-contact-list a:hover{

transform:none;

}

}

/*=========================
Fine-tuned responsive polish (320 → 1600)
=========================*/

@media(max-width:375px){

.hero-content h1{

font-size:34px;

}

.section-header p{

padding:0 8px;

}

.category-grid{

gap:16px;

}

}

@media(min-width:1400px){

.section,
.category-band{

padding-left:9%;
padding-right:9%;

}

}

@media(min-width:1600px){

.container{

max-width:1360px;

}

}

</style>

</head>

<body>

<header class="site-header" id="siteHeader">

<div class="micro-bar">
<i class="fa-solid fa-truck-fast"></i>
توصيل لجميع ولايات السودان &nbsp;•&nbsp; صنع بفخر سوداني &nbsp;•&nbsp; دفع آمن 100%
</div>

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
<a class="nav-link" href="#">الرئيسية</a>
</li>

<li class="nav-item">
<a class="nav-link" href="products.php">المتجر</a>
</li>

<li class="nav-item">
<a class="nav-link" href="categories.php">التصنيفات</a>
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

<a href="wishlist.php">
    <i class="fa-regular fa-heart"></i>
</a>

<a href="cart.php">
    <i class="fa-solid fa-cart-shopping"></i>
</a>

<?php if(isset($_SESSION['user_id'])): ?>

<div class="dropdown user-menu">

<button class="btn dropdown-toggle"
type="button"
data-bs-toggle="dropdown"
aria-expanded="false">



<?php echo $_SESSION['user_name']; ?>

</button>

<ul class="dropdown-menu">

<li>
<a class="dropdown-item" href="cart.php">
<i class="fa-solid fa-cart-shopping"></i>
السلة
</a>
</li>

<li>
<a class="dropdown-item" href="orders.php">
<i class="fa-solid fa-box"></i>
طلباتي
</a>
</li>

<li>
<a class="dropdown-item" href="messages.php">
<i class="fa-solid fa-comments"></i>
الرسائل
</a>
</li>

<li><hr class="dropdown-divider"></li>

<li>
<a class="dropdown-item logout" href="logout.php">
<i class="fa-solid fa-right-from-bracket"></i>
تسجيل الخروج
</a>
</li>

</ul>

</div>

<?php else: ?>

<a href="login.php">
<i class="fa-regular fa-user"></i>
</a>

<?php endif; ?>

</div>



</div>

</div>

</nav>

</header>

<section class="hero">

<div class="container">

<div class="hero-content">

<div class="hero-eyebrow">
<i></i>
<span>Toob Sudan — مجموعة 2026</span>
</div>

<h1>توب سودان</h1>

<p>

اكتشف مجموعة مختارة من أجمل الثياب السودانية
بتصاميم أصيلة وجودة عالية تناسب كل المناسبات.

</p>

<div class="hero-actions">

<a href="products.php" class="btn-main">

تسوق الآن

</a>

<a href="#" class="btn-light2">

اكتشف المجموعة

</a>

</div>

<ul class="hero-trust">

<li>
<i class="fa-solid fa-truck-fast"></i>
شحن سريع لكل الولايات
</li>

<li>
<i class="fa-solid fa-gem"></i>
جودة أصلية 100%
</li>

<li>
<i class="fa-solid fa-lock"></i>
دفع آمن وموثوق
</li>

</ul>

</div>

</div>

<div class="scroll-down">

<i class="fa-solid fa-angles-down"></i>

</div>

</section>

<section class="category-band">

<div class="section-header">

<div class="ornament"></div>
<span class="eyebrow">تسوقي بذوقك</span>

<h2>تسوق حسب الفئة</h2>

<p>
اختر الفئة التي تناسبك من مجموعتنا المتكاملة
</p>

</div>

<div class="category-grid">

<a class="category-tile" href="products.php">
<div class="category-icon"><i class="fa-solid fa-shirt"></i></div>
<h4>ثياب نسائية</h4>
<span>تسوقي الآن</span>
</a>

<a class="category-tile" href="products.php">
<div class="category-icon"><i class="fa-solid fa-user-tie"></i></div>
<h4>أزياء رجالية</h4>
<span>تسوق الآن</span>
</a>

<a class="category-tile" href="categories.php">
<div class="category-icon"><i class="fa-solid fa-gem"></i></div>
<h4>إكسسوارات فاخرة</h4>
<span>اكتشف المزيد</span>
</a>

<a class="category-tile" href="offers.php">
<div class="category-icon"><i class="fa-solid fa-tags"></i></div>
<h4>عروض حصرية</h4>
<span>لا تفوتها</span>
</a>

</div>

</section>

<section class="section section-light" id="featured">

<div class="section-header">

<div class="ornament"></div>
<span class="eyebrow">اختياراتنا الخاصة</span>

<h2>
المجموعة المميزة
</h2>

<p>
قطع سودانية فاخرة تم اختيارها بعناية
</p>

</div>



<div class="products-grid">


<?php

$query = "SELECT * FROM products 
WHERE is_featured = 1 
ORDER BY created_at DESC 
LIMIT 8";

$result = mysqli_query($conn,$query);


while($product = mysqli_fetch_assoc($result)):

?>


<div class="product-card">


<div class="product-image">


<img src="uploads/products/<?php echo $product['image']; ?>">

<span class="card-badge">مميز</span>

<button class="wishlist-btn" type="button" aria-label="أضف للمفضلة">
♡
</button>

<div class="product-actions">

<a href="product.php?id=<?php echo $product['id']; ?>">
عرض
</a>


</div>


</div>



<div class="product-info">


<h3>
<?php echo $product['name']; ?>
</h3>


<div class="stars">
★★★★★
</div>


<div class="price">
<span class="amount"><?php echo $product['price']; ?></span>
<span class="currency">جنيه</span>
</div>



</div>


</div>



<?php endwhile; ?>


</div>


<div class="view-all">

<a href="products.php">
عرض كل المنتجات
</a>

</div>


</section>

<section class="section section-cream" id="new-arrivals">


<div class="section-header">

<div class="ornament"></div>
<span class="eyebrow">وصل حديثاً</span>

<h2>
أحدث المنتجات
</h2>

<p>
اكتشف آخر تصاميم الثياب السودانية
</p>

</div>



<div class="products-grid">


<?php

$new_query = "SELECT * FROM products 
ORDER BY created_at DESC
LIMIT 8";


$new_result = mysqli_query($conn,$new_query);



while($product = mysqli_fetch_assoc($new_result)):

?>


<div class="product-card">


<div class="product-image">


<img src="uploads/products/<?php echo $product['image']; ?>">

<span class="card-badge">جديد</span>

<button class="wishlist-btn" type="button" aria-label="أضف للمفضلة">
♡
</button>

<div class="product-actions">

<a href="product.php?id=<?php echo $product['id']; ?>">
اكتشف الثوب
</a>


</div>


</div>



<div class="product-info">


<h3>
<?php echo $product['name']; ?>
</h3>


<div class="stars">
★★★★★
</div>


<div class="price">
<span class="amount"><?php echo $product['price']; ?></span>
<span class="currency">جنيه</span>
</div>


</div>


</div>


<?php endwhile; ?>


</div>



<div class="view-all">

<a href="products.php">
عرض المجموعة كاملة
</a>

</div>


</section>

<section class="section section-light" id="bestsellers">


<div class="section-header">

<div class="ornament"></div>
<span class="eyebrow">الأكثر طلباً</span>

<h2>
الأكثر مبيعاً
</h2>

<p>
اختيارات عملائنا المفضلة
</p>

</div>



<div class="products-grid">


<?php
$best_query = "SELECT * FROM products
ORDER BY created_at DESC
LIMIT 8";


$best_result = mysqli_query($conn,$best_query);



while($product = mysqli_fetch_assoc($best_result)):

?>


<div class="product-card">


<div class="product-image">


<img src="uploads/products/<?php echo $product['image']; ?>">



<div class="vip-badge">
<i class="fa-solid fa-fire"></i>
الأكثر طلباً
</div>

<button class="wishlist-btn" type="button" aria-label="أضف للمفضلة">
♡
</button>

<div class="product-actions">


<a href="product.php?id=<?php echo $product['id']; ?>">

اكتشف الثوب

</a>


</div>


</div>



<div class="product-info">


<h3>

<?php echo $product['name']; ?>

</h3>


<div class="stars">

★★★★★

</div>


<div class="price">
<span class="amount"><?php echo $product['price']; ?></span>
<span class="currency">جنيه</span>
</div>


</div>


</div>



<?php endwhile; ?>


</div>


<div class="view-all">

<a href="products.php">

كل المنتجات

</a>

</div>


</section>

<section class="luxury-banner">


<div class="banner-overlay">


<span>
مجموعة 2026
</span>


<h2>
من قلب السودان...
إلى العالم
</h2>


<p>
ثياب سودانية بتصميم فاخر وجودة استثنائية
</p>


<a href="products.php">
تسوق الآن
</a>


</div>


</section>
<section class="section section-cream reviews-section">


<div class="section-header">

<div class="ornament"></div>
<span class="eyebrow">تجارب الزبائن</span>

<h2>
آراء العملاء
</h2>

<p>
رضاكم هو فخرنا
</p>

</div>



<div class="reviews-grid">



<div class="review-card">


<div class="stars">
★★★★★
</div>


<p>
الثوب أجمل من الصور، الخامة ممتازة جداً.
</p>


<h4>
سارة محمد
</h4>


</div>




<div class="review-card">


<div class="stars">
★★★★★
</div>


<p>
التوصيل سريع والتعامل راقي جداً.
</p>


<h4>
آمنة علي
</h4>


</div>





<div class="review-card">


<div class="stars">
★★★★★
</div>


<p>
تصميم فاخر وجودة تستحق السعر.
</p>


<h4>
هبة أحمد
</h4>


</div>



</div>


</section>
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

<a href="index.php"><i class="fa-solid fa-angle-left"></i> الرئيسية</a>

<a href="products.php"><i class="fa-solid fa-angle-left"></i> المتجر</a>

<a href="#"><i class="fa-solid fa-angle-left"></i> عن المتجر</a>


<a href="#"><i class="fa-solid fa-angle-left"></i> تواصل معنا</a>

</div>




<div class="footer-contact">

<h3>
تواصل معنا
</h3>

<div class="footer-contact-list">

<a href="#"><i class="fa-brands fa-whatsapp"></i> واتساب</a>

<a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a>

<a href="#"><i class="fa-brands fa-tiktok"></i> TikTok</a>

</div>

</div>



</div>



<div class="footer-bottom">

© 2026 Toob Sudan - جميع الحقوق محفوظة

</div>



</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
var header = document.getElementById('siteHeader');
if(!header) return;
function onScroll(){
if(window.scrollY > 40){ header.classList.add('scrolled'); }
else{ header.classList.remove('scrolled'); }
}
window.addEventListener('scroll', onScroll, {passive:true});
onScroll();
})();

/* Luxury button ripple */
(function(){
function addRipple(e){
var btn = e.currentTarget;
var rect = btn.getBoundingClientRect();
var size = Math.max(rect.width, rect.height);
var circle = document.createElement('span');
circle.className = 'ripple';
circle.style.width = circle.style.height = size + 'px';
circle.style.left = (e.clientX - rect.left - size/2) + 'px';
circle.style.top = (e.clientY - rect.top - size/2) + 'px';
var old = btn.querySelector('.ripple');
if(old){ old.remove(); }
btn.appendChild(circle);
circle.addEventListener('animationend', function(){ circle.remove(); });
}
var targets = document.querySelectorAll('.btn-main, .btn-light2, .view-all a, .product-actions a, .banner-overlay a, .wishlist-btn, .category-tile');
targets.forEach(function(btn){
btn.style.position = btn.style.position || 'relative';
btn.style.overflow = btn.style.overflow || 'hidden';
btn.addEventListener('click', addRipple);
});
})();

/* Staggered fade-up for product cards as they enter view (progressive enhancement) */
(function(){
if(!('IntersectionObserver' in window)){ return; }
var cards = document.querySelectorAll('.product-card');
cards.forEach(function(card, i){
card.classList.add('reveal');
card.style.transitionDelay = (Math.min(i % 4, 4) * 0.08) + 's';
});
var io = new IntersectionObserver(function(entries){
entries.forEach(function(entry){
if(entry.isIntersecting){
entry.target.classList.add('in-view');
io.unobserve(entry.target);
}
});
},{threshold:.12});
cards.forEach(function(card){ io.observe(card); });
})();
</script>

</body>

</html>