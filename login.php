<?php

session_start();

include 'includes/db.php';
include "includes/store_status.php";
if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    // أولاً: نفحص جدول الأدمن
    $admin_result = mysqli_query($conn,"SELECT * FROM admins WHERE email='$email'");

    if(mysqli_num_rows($admin_result) > 0){

        $admin = mysqli_fetch_assoc($admin_result);

        if(password_verify($password,$admin['password'])){

            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email']= $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];

            header("Location: admin/dashboard.php");
            exit;

        }else{

            $error = "كلمة المرور غير صحيحة";

        }

    }else{

        // إذا لم يكن أدمن، نفحص جدول المستخدمين
        $result = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

        if(mysqli_num_rows($result) > 0){

            $user = mysqli_fetch_assoc($result);

            if(password_verify($password,$user['password'])){

                if($user['status'] == 'blocked'){

                    $error = "حسابك موقوف";

                }else{
                
                $_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];

// إذا كان المستخدم جاي من صفحة تتطلب تسجيل دخول
if(isset($_SESSION['redirect_after_login'])){

    $redirect = $_SESSION['redirect_after_login'];

    unset($_SESSION['redirect_after_login']);

    header("Location: ".$redirect);
    exit;
}

// إذا ما كان جاي من أي صفحة
header("Location: index.php");
exit;

                }

            }else{

                $error = "كلمة المرور غير صحيحة";

            }

        }else{

            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة";

        }

    }

}

?>


<!DOCTYPE html>

<html lang="ar" dir="rtl">


<head>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>تسجيل الدخول | توب سودان</title>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">



<style>

:root{
  --burgundy-deep:   #2A0810;
  --burgundy:        #5B1628;
  --burgundy-light:  #7D2038;
  --gold:            #D4AF37;
  --gold-light:      #F0D78C;
  --gold-dim:        #9C812C;
  --cream:           #F5EDE0;
  --rose-muted:      #C79A8E;
  --error-bg:        #7d1d30;
  --radius-lg: 28px;
  --radius-md: 18px;
  --radius-sm: 12px;
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

html, body{
  height:100%;
}

body{
  font-family:'Cairo', sans-serif;
  color:var(--cream);
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:24px;
  position:relative;
  overflow-x:hidden;
  background:
    radial-gradient(circle at 20% 15%, rgba(212,175,55,0.10), transparent 45%),
    radial-gradient(circle at 85% 85%, rgba(212,175,55,0.08), transparent 50%),
    linear-gradient(160deg, var(--burgundy-deep) 0%, var(--burgundy) 55%, var(--burgundy-light) 100%);
}

/* subtle woven-fabric texture, evokes toub cloth */
body::before{
  content:"";
  position:fixed;
  inset:0;
  pointer-events:none;
  opacity:0.05;
  background-image:
    repeating-linear-gradient(45deg, var(--gold) 0px, var(--gold) 1px, transparent 1px, transparent 14px),
    repeating-linear-gradient(-45deg, var(--gold) 0px, var(--gold) 1px, transparent 1px, transparent 14px);
  z-index:0;
}

/* soft floating fabric-fold shapes */
.drift{
  position:fixed;
  border-radius:50%;
  filter:blur(60px);
  opacity:0.35;
  pointer-events:none;
  z-index:0;
  animation:drift 16s ease-in-out infinite;
}
.drift.one{
  width:320px; height:320px;
  top:-80px; right:-100px;
  background:radial-gradient(circle, var(--gold-dim), transparent 70%);
  animation-delay:0s;
}
.drift.two{
  width:260px; height:260px;
  bottom:-90px; left:-80px;
  background:radial-gradient(circle, var(--burgundy-light), transparent 70%);
  animation-delay:3s;
}

@keyframes drift{
  0%, 100%{ transform:translateY(0) translateX(0); }
  50%{ transform:translateY(-25px) translateX(15px); }
}

.login-box{
  position:relative;
  z-index:1;
  width:100%;
  max-width:470px;
  background:linear-gradient(180deg, rgba(122,32,56,0.55), rgba(42,8,16,0.75));
  backdrop-filter:blur(18px);
  -webkit-backdrop-filter:blur(18px);
  padding:44px 38px 36px;
  border-radius:var(--radius-lg);
  border:1px solid rgba(212,175,55,0.45);
  box-shadow:
    0 25px 60px rgba(0,0,0,0.45),
    0 0 0 1px rgba(212,175,55,0.06) inset;
  animation:cardIn 0.7s cubic-bezier(0.22,1,0.36,1) both;
}

@keyframes cardIn{
  from{ opacity:0; transform:translateY(24px) scale(0.98); }
  to{ opacity:1; transform:translateY(0) scale(1); }
}

/* ===== signature element: woven drape divider ===== */
.drape{
  width:100%;
  height:22px;
  margin-bottom:26px;
  opacity:0.9;
}
.drape path{
  fill:none;
  stroke:var(--gold);
  stroke-width:1.4;
  stroke-linecap:round;
  opacity:0.65;
}

/* ===== logo / brand block ===== */
.brand{
  text-align:center;
  margin-bottom:30px;
  animation:fadeUp 0.8s ease 0.1s both;
}

.logo-frame{
  width:112px;
  height:112px;
  margin:0 auto 18px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(145deg, rgba(212,175,55,0.18), rgba(212,175,55,0.02));
  border:1px solid rgba(212,175,55,0.5);
  box-shadow:0 8px 24px rgba(0,0,0,0.35);
  overflow:hidden;
  position:relative;
}

.logo-frame img{
  width:78%;
  height:78%;
  object-fit:contain;
}

/* graceful fallback monogram when logo.png is missing */
.logo-frame .fallback{
  font-family:'Amiri', serif;
  font-size:44px;
  font-weight:700;
  color:var(--gold-light);
  line-height:1;
}

.brand h1{
  font-family:'Amiri', serif;
  font-size:29px;
  font-weight:700;
  color:var(--gold-light);
  letter-spacing:0.5px;
  margin-bottom:6px;
}

.brand .subtitle{
  font-size:13px;
  color:var(--rose-muted);
  letter-spacing:1.5px;
  text-transform:uppercase;
}

.welcome{
  text-align:center;
  margin-bottom:28px;
  animation:fadeUp 0.8s ease 0.18s both;
}

.welcome h2{
  font-size:21px;
  font-weight:700;
  color:var(--cream);
  margin-bottom:6px;
}

.welcome p{
  font-size:13.5px;
  color:var(--rose-muted);
  line-height:1.6;
}

/* ===== error message ===== */
.error{
  background:linear-gradient(135deg, var(--error-bg), #5e1524);
  border:1px solid rgba(212,175,55,0.25);
  padding:14px 16px;
  border-radius:var(--radius-sm);
  text-align:center;
  margin-bottom:22px;
  font-size:14px;
  animation:shake 0.5s ease;
}

@keyframes shake{
  0%, 100%{ transform:translateX(0); }
  20%{ transform:translateX(-6px); }
  40%{ transform:translateX(6px); }
  60%{ transform:translateX(-4px); }
  80%{ transform:translateX(4px); }
}

/* ===== form ===== */
form{
  animation:fadeUp 0.8s ease 0.26s both;
}

@keyframes fadeUp{
  from{ opacity:0; transform:translateY(14px); }
  to{ opacity:1; transform:translateY(0); }
}

.field{
  position:relative;
  margin-bottom:20px;
}

.field .icon{
  position:absolute;
  top:50%;
  right:18px;
  transform:translateY(-50%);
  width:19px;
  height:19px;
  color:var(--gold-dim);
  transition:color 0.25s ease;
  pointer-events:none;
}

.field input{
  width:100%;
  padding:16px 50px 16px 18px;
  background:rgba(255,255,255,0.04);
  border:1.5px solid rgba(212,175,55,0.3);
  border-radius:var(--radius-md);
  color:var(--cream);
  font-family:'Cairo', sans-serif;
  font-size:15px;
  transition:border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
}

.field input::placeholder{
  color:rgba(245,237,224,0.4);
}

.field input:focus{
  outline:none;
  border-color:var(--gold);
  background:rgba(255,255,255,0.07);
  box-shadow:0 0 0 4px rgba(212,175,55,0.12);
}

.field input:focus ~ .icon{
  color:var(--gold);
}

.field.password input{
  padding-left:50px;
}

.toggle-pass{
  position:absolute;
  top:50%;
  left:16px;
  transform:translateY(-50%);
  background:none;
  border:none;
  cursor:pointer;
  color:var(--gold-dim);
  width:auto;
  padding:4px;
  display:flex;
  align-items:center;
  transition:color 0.2s ease;
}

.toggle-pass:hover{
  color:var(--gold);
}

.toggle-pass svg{
  width:19px;
  height:19px;
}

button[name="login"]{
  width:100%;
  padding:16px;
  margin-top:6px;
  background:linear-gradient(135deg, var(--gold-light), var(--gold) 55%, var(--gold-dim));
  background-size:200% auto;
  border:none;
  border-radius:44px;
  font-family:'Cairo', sans-serif;
  font-size:17px;
  font-weight:700;
  color:var(--burgundy-deep);
  cursor:pointer;
  box-shadow:0 10px 28px rgba(212,175,55,0.28);
  transition:transform 0.25s ease, box-shadow 0.25s ease, background-position 0.5s ease;
  position:relative;
}

button[name="login"]:hover{
  transform:translateY(-2px) scale(1.015);
  box-shadow:0 14px 34px rgba(212,175,55,0.4);
  background-position:right center;
}

button[name="login"]:active{
  transform:translateY(0) scale(0.99);
}

a{
  display:block;
  text-align:center;
  margin-top:24px;
  color:var(--rose-muted);
  text-decoration:none;
  font-size:14px;
  transition:color 0.2s ease;
}

a:hover{
  color:var(--gold-light);
}

/* ===== responsive ===== */
@media (max-width:600px){
  .login-box{
    max-width:95%;
    padding:34px 22px 28px;
    border-radius:22px;
  }
  .logo-frame{
    width:92px;
    height:92px;
  }
  .brand h1{
    font-size:25px;
  }
  .welcome h2{
    font-size:19px;
  }
}

@media (min-width:601px) and (max-width:900px){
  .login-box{
    max-width:90%;
  }
}

@media (min-width:1600px){
  .login-box{
    max-width:500px;
  }
}

@media (prefers-reduced-motion: reduce){
  *, *::before, *::after{
    animation-duration:0.001ms !important;
    animation-iteration-count:1 !important;
    transition-duration:0.001ms !important;
  }
  .drift{ animation:none; }
}

</style>


</head>



<body>

<div class="drift one"></div>
<div class="drift two"></div>


<div class="login-box">

  <svg class="drape" viewBox="0 0 470 22" preserveAspectRatio="none">
    <path d="M0,11 C60,2 100,20 160,11 C220,2 260,20 320,11 C380,2 420,20 470,11" />
  </svg>

  <div class="brand">
    <div class="logo-frame">
      <img src="assets/images/logo.png" alt="توب سودان" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
      <span class="fallback" style="display:none; align-items:center; justify-content:center;">ت</span>
    </div>
    <h1>Toob Sudan</h1>
    <div class="subtitle">Sudanese Traditional Fashion</div>
  </div>

  <div class="welcome">
    <h2>أهلاً بعودتك</h2>
    <p>سجّل دخولك لمتابعة تسوّق أفخم الأزياء السودانية التقليدية</p>
  </div>

<?php if(isset($error)){ ?>

<div class="error">

<?php echo $error; ?>

</div>

<?php } ?>




<form method="POST">



<div class="field">
<input

type="email"

name="email"

placeholder="البريد الإلكتروني"

required>
<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
  <path d="M3 6.5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-11Z"/>
  <path d="M3.5 7l8.5 6 8.5-6"/>
</svg>
</div>




<div class="field password">
<input

id="passwordInput"
type="password"

name="password"

placeholder="كلمة المرور"

required>
<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
  <rect x="4.5" y="10.5" width="15" height="10" rx="2.2"/>
  <path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>
</svg>
<button type="button" class="toggle-pass" onclick="togglePassword()" aria-label="إظهار كلمة المرور">
  <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
    <path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/>
    <circle cx="12" cy="12" r="3.2"/>
  </svg>
</button>
</div>




<button name="login">

دخول

</button>



</form>



<a href="register.php">

إنشاء حساب جديد

</a>



</div>

<script>
function togglePassword(){
  var input = document.getElementById('passwordInput');
  var eye = document.getElementById('eyeIcon');
  if(input.type === 'password'){
    input.type = 'text';
    eye.innerHTML = '<path d="M3 3l18 18"/><path d="M10.6 10.6a3.2 3.2 0 0 0 4.5 4.5"/><path d="M6.6 6.7C3.8 8.4 1.5 12 1.5 12s3.5 7 10.5 7c1.9 0 3.5-.5 4.9-1.2"/><path d="M17.5 6.2C16 5.4 14.2 5 12 5c-.6 0-1.1 0-1.7.1"/><path d="M21.5 12s-.9 1.8-2.6 3.4"/>';
  }else{
    input.type = 'password';
    eye.innerHTML = '<path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3.2"/>';
  }
}
</script>


</body>


</html>