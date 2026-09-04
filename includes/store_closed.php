<?php
if(!isset($message) || trim($message)==""){
    $message = "المتجر مغلق مؤقتاً، وسنعود إليكم قريباً بإذن الله.";
}

$result = mysqli_query($conn,"SELECT * FROM settings LIMIT 1");
$store = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>المتجر مغلق مؤقتاً</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

:root{

  --burgundy-deepest:#22050f;
  --burgundy-deep:#3c0b1d;
  --burgundy:#5B1028;
  --burgundy-soft:#7a1c3a;
  --gold:#D4AF37;
  --gold-bright:#F3D77A;
  --gold-soft:rgba(212,175,55,.35);
  --ivory:#F8F1E4;
  --ivory-dim:rgba(248,241,228,.7);

}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Cairo',sans-serif;
}

html,body{
  height:100%;
}

body{
  min-height:100vh;
  min-height:100dvh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:28px;
  overflow-x:hidden;
  position:relative;
  background:
    radial-gradient(circle at 18% 20%, rgba(122,28,58,.55), transparent 45%),
    radial-gradient(circle at 82% 78%, rgba(91,16,40,.6), transparent 50%),
    linear-gradient(160deg,var(--burgundy-deepest) 0%, var(--burgundy-deep) 45%, #1b0510 100%);
  background-size:220% 220%, 220% 220%, 100% 100%;
  animation:bgDrift 22s ease-in-out infinite;
}

@keyframes bgDrift{
  0%{ background-position:0% 0%, 100% 100%, 0 0; }
  50%{ background-position:60% 40%, 40% 60%, 0 0; }
  100%{ background-position:0% 0%, 100% 100%, 0 0; }
}

/* ---------- Ambient blurred glow circles ---------- */

.orb{
  position:fixed;
  border-radius:50%;
  filter:blur(70px);
  pointer-events:none;
  z-index:0;
  opacity:.55;
}

.orb-1{
  width:420px;
  height:420px;
  top:-140px;
  left:-120px;
  background:radial-gradient(circle, var(--gold) 0%, transparent 70%);
  animation:floatOrb 16s ease-in-out infinite;
}

.orb-2{
  width:520px;
  height:520px;
  bottom:-200px;
  right:-160px;
  background:radial-gradient(circle, var(--burgundy-soft) 0%, transparent 70%);
  animation:floatOrb 20s ease-in-out infinite reverse;
}

.orb-3{
  width:260px;
  height:260px;
  top:45%;
  left:50%;
  transform:translate(-50%,-50%);
  background:radial-gradient(circle, var(--gold-soft) 0%, transparent 70%);
  animation:pulseOrb 6s ease-in-out infinite;
}

@keyframes floatOrb{
  0%,100%{ transform:translate(0,0) scale(1); }
  50%{ transform:translate(40px,-30px) scale(1.08); }
}

@keyframes pulseOrb{
  0%,100%{ opacity:.25; }
  50%{ opacity:.5; }
}

/* ---------- Floating gold particles (pure CSS) ---------- */

.particles{
  position:fixed;
  inset:0;
  overflow:hidden;
  pointer-events:none;
  z-index:1;
}

.particle{
  position:absolute;
  bottom:-10px;
  width:5px;
  height:5px;
  border-radius:50%;
  background:var(--gold-bright);
  box-shadow:0 0 8px 2px rgba(243,215,122,.6);
  opacity:0;
  animation:rise linear infinite;
}

@keyframes rise{
  0%{ transform:translateY(0) translateX(0); opacity:0; }
  10%{ opacity:.9; }
  90%{ opacity:.5; }
  100%{ transform:translateY(-105vh) translateX(var(--drift,20px)); opacity:0; }
}

.particle:nth-child(1){ left:6%;  width:4px; height:4px; --drift:24px; animation-duration:14s; animation-delay:0s; }
.particle:nth-child(2){ left:16%; width:6px; height:6px; --drift:-18px; animation-duration:18s; animation-delay:2s; }
.particle:nth-child(3){ left:27%; width:3px; height:3px; --drift:14px; animation-duration:12s; animation-delay:4s; }
.particle:nth-child(4){ left:38%; width:5px; height:5px; --drift:-26px; animation-duration:20s; animation-delay:1s; }
.particle:nth-child(5){ left:49%; width:4px; height:4px; --drift:20px; animation-duration:15s; animation-delay:6s; }
.particle:nth-child(6){ left:60%; width:6px; height:6px; --drift:-16px; animation-duration:19s; animation-delay:3s; }
.particle:nth-child(7){ left:71%; width:3px; height:3px; --drift:22px; animation-duration:13s; animation-delay:5s; }
.particle:nth-child(8){ left:82%; width:5px; height:5px; --drift:-20px; animation-duration:17s; animation-delay:7s; }
.particle:nth-child(9){ left:91%; width:4px; height:4px; --drift:18px; animation-duration:16s; animation-delay:2.5s; }
.particle:nth-child(10){ left:12%; width:3px; height:3px; --drift:-14px; animation-duration:21s; animation-delay:8s; }
.particle:nth-child(11){ left:56%; width:4px; height:4px; --drift:16px; animation-duration:14s; animation-delay:9s; }
.particle:nth-child(12){ left:78%; width:3px; height:3px; --drift:-12px; animation-duration:18s; animation-delay:1.5s; }

/* ---------- Card wrap + glowing rotating border ---------- */

.card-wrap{
  position:relative;
  z-index:2;
  width:100%;
  max-width:560px;
  opacity:0;
  animation:cardEnter 1s cubic-bezier(.19,1,.22,1) .15s forwards;
}

@keyframes cardEnter{
  0%{ opacity:0; transform:translateY(36px) scale(.96); }
  100%{ opacity:1; transform:translateY(0) scale(1); }
}

.glow-ring{
  position:absolute;
  inset:-2px;
  border-radius:32px;
  padding:2px;
  background:conic-gradient(from 0deg, var(--gold) 0%, transparent 22%, transparent 50%, var(--gold-bright) 65%, transparent 85%, var(--gold) 100%);
  -webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite:xor;
  mask-composite:exclude;
  animation:spinRing 7s linear infinite;
  opacity:.85;
}

@keyframes spinRing{
  to{ transform:rotate(360deg); }
}

/* ---------- The glass card itself ---------- */

.card{
  position:relative;
  background:linear-gradient(165deg, rgba(255,255,255,.10), rgba(255,255,255,.035));
  backdrop-filter:blur(22px) saturate(140%);
  -webkit-backdrop-filter:blur(22px) saturate(140%);
  border:1px solid rgba(212,175,55,.28);
  border-radius:30px;
  padding:52px 44px 40px;
  text-align:center;
  box-shadow:
    0 40px 90px rgba(0,0,0,.45),
    0 0 0 1px rgba(255,255,255,.04) inset,
    0 1px 0 rgba(255,255,255,.12) inset;
}

.reveal{
  opacity:0;
  animation:softIn .8s ease forwards;
}
.card .logo{ animation-delay:.35s; }
.card .icon-badge{ animation-delay:.5s; }
.card h1{ animation-delay:.62s; }
.card .gold-line{ animation-delay:.72s; }
.card .message-box{ animation-delay:.82s; }
.card .actions{ animation-delay:.95s; }
.card .footer{ animation-delay:1.08s; }

@keyframes softIn{
  0%{ opacity:0; transform:translateY(14px); }
  100%{ opacity:1; transform:translateY(0); }
}

/* ---------- Logo ---------- */

.logo{
  width:118px;
  height:118px;
  margin:0 auto 26px;
  border-radius:50%;
  overflow:hidden;
  background:radial-gradient(circle, rgba(212,175,55,.14), rgba(255,255,255,.02));
  display:flex;
  align-items:center;
  justify-content:center;
  border:2.5px solid var(--gold);
  box-shadow:
    0 0 0 6px rgba(212,175,55,.08),
    0 14px 32px rgba(0,0,0,.4);
  position:relative;
}

.logo img{
  width:100%;
  height:100%;
  object-fit:cover;
}

.logo i{
  font-size:46px;
  background:linear-gradient(160deg, var(--gold-bright), var(--gold));
  -webkit-background-clip:text;
  background-clip:text;
  -webkit-text-fill-color:transparent;
}

/* ---------- Maintenance icon badge ---------- */

.icon-badge{
  width:76px;
  height:76px;
  margin:0 auto 22px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(160deg, rgba(212,175,55,.22), rgba(212,175,55,.05));
  border:1px solid rgba(212,175,55,.4);
  position:relative;
  animation:softIn .8s ease forwards, breathe 3.6s ease-in-out .8s infinite;
}

@keyframes breathe{
  0%,100%{ box-shadow:0 0 0 0 rgba(212,175,55,.25); }
  50%{ box-shadow:0 0 0 12px rgba(212,175,55,0); }
}

.icon-badge i{
  font-size:30px;
  color:var(--gold-bright);
}

/* ---------- Heading & copy ---------- */

h1{
  font-size:clamp(28px,5vw,38px);
  font-weight:800;
  letter-spacing:.2px;
  margin-bottom:10px;
  background:linear-gradient(160deg, var(--ivory) 0%, var(--gold-bright) 100%);
  -webkit-background-clip:text;
  background-clip:text;
  -webkit-text-fill-color:transparent;
}

.eyebrow{
  font-size:13px;
  font-weight:600;
  letter-spacing:2px;
  color:var(--gold-bright);
  text-transform:uppercase;
  margin-bottom:14px;
  opacity:.85;
}

.gold-line{
  width:86px;
  height:2px;
  margin:18px auto 22px;
  background:linear-gradient(90deg, transparent, var(--gold), transparent);
  position:relative;
}

.gold-line::before{
  content:"";
  position:absolute;
  left:50%;
  top:50%;
  transform:translate(-50%,-50%);
  width:7px;
  height:7px;
  border-radius:50%;
  background:var(--gold-bright);
  box-shadow:0 0 10px 2px rgba(243,215,122,.7);
}

/* ---------- Message glass box ---------- */

.message-box{
  background:rgba(255,255,255,.05);
  border:1px solid rgba(212,175,55,.22);
  border-radius:18px;
  padding:22px 24px;
  margin-bottom:30px;
}

.message{
  font-size:16.5px;
  line-height:2;
  color:var(--ivory-dim);
  font-weight:400;
}

.small-note{
  margin-top:14px;
  font-size:12.5px;
  color:rgba(212,175,55,.75);
  letter-spacing:.3px;
  font-weight:500;
}

/* ---------- Action buttons ---------- */

.actions{
  display:flex;
  justify-content:center;
  flex-wrap:wrap;
  gap:14px;
  margin-top:8px;
}

.btn{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:15px 28px;
  border-radius:14px;
  text-decoration:none;
  font-weight:700;
  font-size:15px;
  color:#fff;
  position:relative;
  overflow:hidden;
  transition:transform .3s ease, box-shadow .3s ease;
  isolation:isolate;
}

.btn::before{
  content:"";
  position:absolute;
  inset:0;
  background:linear-gradient(120deg, transparent, rgba(255,255,255,.35), transparent);
  transform:translateX(-120%);
  transition:transform .6s ease;
  z-index:1;
}

.btn:hover::before{
  transform:translateX(120%);
}

.btn i{ position:relative; z-index:2; }
.btn span{ position:relative; z-index:2; }

.whatsapp{
  background:linear-gradient(135deg,#1fae55,#25D366);
  box-shadow:0 12px 28px rgba(37,211,102,.28);
}

.phone{
  background:linear-gradient(135deg,var(--burgundy),var(--burgundy-soft));
  border:1px solid rgba(212,175,55,.5);
  box-shadow:0 12px 28px rgba(91,16,40,.4);
}

.btn:hover{
  transform:translateY(-4px);
}

.whatsapp:hover{ box-shadow:0 18px 34px rgba(37,211,102,.4); }
.phone:hover{ box-shadow:0 18px 34px rgba(212,175,55,.28); }

/* ---------- Footer ---------- */

.footer{
  margin-top:34px;
  padding-top:22px;
  border-top:1px solid rgba(212,175,55,.18);
  font-size:13px;
  color:rgba(248,241,228,.5);
  letter-spacing:.3px;
}

.footer strong{
  color:var(--gold-bright);
  font-weight:700;
}

/* ---------- Responsive ---------- */

@media (max-width:520px){

  .card{ padding:40px 24px 32px; border-radius:24px; }
  .logo{ width:98px; height:98px; }
  h1{ font-size:26px; }
  .message-box{ padding:18px 18px; }
  .btn{ width:100%; justify-content:center; padding:15px 20px; }
  .actions{ flex-direction:column; }

}

@media (prefers-reduced-motion:reduce){
  *{ animation:none !important; transition:none !important; }
}

</style>

</head>

<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="particles">
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
  <span class="particle"></span>
</div>

<div class="card-wrap">

<div class="glow-ring"></div>

<div class="card">

<div class="logo reveal">

<?php if(!empty($store['logo'])){ ?>

<img src="uploads/<?php echo $store['logo']; ?>">

<?php }else{ ?>

<i class="fa-solid fa-store"></i>

<?php } ?>

</div>

<div class="icon-badge reveal">

<i class="fa-solid fa-store-slash"></i>

</div>

<div class="eyebrow reveal" style="animation-delay:.58s;">إشعار مؤقت</div>

<h1 class="reveal">

المتجر مغلق مؤقتاً

</h1>

<div class="gold-line reveal"></div>

<div class="message-box reveal">

<div class="message">

<?php echo nl2br(htmlspecialchars($message)); ?>

</div>

<div class="small-note">سنعود إليكم قريباً بإذن الله</div>

</div>

<div class="actions reveal">

<?php if(!empty($store['whatsapp'])){ ?>

<a
class="btn whatsapp"
href="https://wa.me/<?php echo preg_replace('/[^0-9]/','',$store['whatsapp']); ?>">

<i class="fa-brands fa-whatsapp"></i>

<span>واتساب</span>

</a>

<?php } ?>

<?php if(!empty($store['phone'])){ ?>

<a
class="btn phone"
href="tel:<?php echo $store['phone']; ?>">

<i class="fa-solid fa-phone"></i>

<span>اتصل بنا</span>

</a>

<?php } ?>

</div>

<div class="footer reveal">

© <?php echo date("Y"); ?> <strong><?php echo htmlspecialchars($store['store_name']); ?></strong>

</div>

</div>

</div>

</body>

</html>