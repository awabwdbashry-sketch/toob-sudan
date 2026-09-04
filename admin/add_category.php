<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

$error = "";

if(isset($_POST['add'])){

    $name = mysqli_real_escape_string($conn,$_POST['name']);

    $image_name = "";

    if(isset($_FILES['image']) && $_FILES['image']['error']==0){

        $ext = strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));

        $allow = ['jpg','jpeg','png','webp'];

        if(in_array($ext,$allow)){

            $image_name = time().rand(1000,9999).".".$ext;

            move_uploaded_file(

                $_FILES['image']['tmp_name'],

                "../uploads/categories/".$image_name

            );

        }else{

            $error = "صيغة الصورة غير مدعومة.";

        }

    }

    if($error==""){

        mysqli_query($conn,"

        INSERT INTO categories(

        name,
        image

        )

        VALUES(

        '$name',
        '$image_name'

        )

        ");

        header("Location: categories.php");
        exit;

    }

}

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>إضافة تصنيف</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

:root{
    --burgundy:#5B1028;
    --burgundy-light:#7A1637;
    --burgundy-dark:#3E0B1C;
    --gold:#D4AF37;
    --gold-light:#E8C866;
    --white:#ffffff;
    --light-gray:#F5F5F7;
    --text-gray:#8a8a8f;
    --radius-lg:24px;
    --radius-md:16px;
    --radius-sm:10px;
    --shadow-soft:0 20px 60px rgba(91,16,40,.10);
    --shadow-hover:0 25px 70px rgba(91,16,40,.18);
    --transition:all .35s cubic-bezier(.4,0,.2,1);
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Cairo',sans-serif;
}

body{
    min-height:100vh;
    background:
        radial-gradient(circle at 10% 10%, rgba(212,175,55,.10), transparent 40%),
        radial-gradient(circle at 90% 20%, rgba(91,16,40,.08), transparent 45%),
        radial-gradient(circle at 50% 100%, rgba(212,175,55,.08), transparent 50%),
        linear-gradient(160deg,#faf9fb 0%,#f3f1f4 100%);
    padding:40px 20px;
    position:relative;
    overflow-x:hidden;
}

body::before,
body::after{
    content:"";
    position:fixed;
    border-radius:50%;
    filter:blur(60px);
    z-index:0;
    pointer-events:none;
}

body::before{
    width:320px;
    height:320px;
    top:-100px;
    left:-100px;
    background:radial-gradient(circle,rgba(212,175,55,.22),transparent 70%);
}

body::after{
    width:380px;
    height:380px;
    bottom:-120px;
    right:-120px;
    background:radial-gradient(circle,rgba(91,16,40,.14),transparent 70%);
}

.page-wrap{
    max-width:920px;
    margin:0 auto;
    position:relative;
    z-index:1;
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(14px);}
    to{opacity:1;transform:translateY(0);}
}

/* ===== Header ===== */

.page-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    margin-bottom:28px;
    flex-wrap:wrap;
    animation:slideUp .6s ease;
}

@keyframes slideUp{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

.header-text .breadcrumb{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    color:var(--text-gray);
    margin-bottom:10px;
    font-weight:600;
}

.header-text .breadcrumb span.current{
    color:var(--burgundy);
    font-weight:700;
}

.header-text .breadcrumb i{
    font-size:10px;
    color:var(--gold);
}

.header-text h1{
    font-size:30px;
    font-weight:800;
    color:var(--burgundy);
    display:flex;
    align-items:center;
    gap:12px;
    letter-spacing:-.5px;
}

.header-text h1 .icon-badge{
    width:46px;
    height:46px;
    border-radius:14px;
    background:linear-gradient(135deg,var(--burgundy),var(--burgundy-light));
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--gold-light);
    font-size:20px;
    box-shadow:0 10px 24px rgba(91,16,40,.30);
}

.header-text p.desc{
    margin-top:8px;
    color:var(--text-gray);
    font-size:14.5px;
    font-weight:600;
}

.back-btn{
    display:flex;
    align-items:center;
    gap:10px;
    background:var(--white);
    border:1px solid rgba(91,16,40,.12);
    color:var(--burgundy);
    font-weight:700;
    font-size:14px;
    padding:13px 22px;
    border-radius:50px;
    cursor:pointer;
    text-decoration:none;
    box-shadow:0 8px 20px rgba(0,0,0,.05);
    transition:var(--transition);
    white-space:nowrap;
}

.back-btn:hover{
    background:var(--burgundy);
    color:#fff;
    transform:translateX(-4px);
    box-shadow:0 12px 26px rgba(91,16,40,.25);
}

.back-btn i{
    transition:var(--transition);
}

/* ===== Layout ===== */

.layout{
    display:grid;
    grid-template-columns:1.6fr 1fr;
    gap:26px;
    align-items:start;
}

/* ===== Card ===== */

.card{
    background:rgba(255,255,255,.75);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.6);
    border-radius:var(--radius-lg);
    box-shadow:var(--shadow-soft);
    padding:38px;
    position:relative;
    overflow:hidden;
    animation:slideUp .7s ease;
}

.card::before{
    content:"";
    position:absolute;
    top:0;left:0;right:0;
    height:4px;
    background:linear-gradient(90deg,var(--gold),var(--burgundy),var(--gold));
    background-size:200% 100%;
    animation:shimmer 5s linear infinite;
}

@keyframes shimmer{
    0%{background-position:0% 0%;}
    100%{background-position:200% 0%;}
}

.section-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:18px;
    font-weight:800;
    color:var(--burgundy);
    margin-bottom:26px;
    padding-bottom:16px;
    border-bottom:1px dashed rgba(91,16,40,.15);
}

.section-title i{
    color:var(--gold);
    font-size:17px;
}

/* ===== Error Banner ===== */

.error-banner{
    display:flex;
    align-items:center;
    gap:12px;
    background:linear-gradient(135deg,#fdecee,#fbe1e5);
    border:1px solid #f3b7c0;
    color:#8a1f2d;
    padding:16px 20px;
    border-radius:var(--radius-md);
    margin-bottom:24px;
    font-weight:700;
    font-size:14.5px;
    animation:shake .5s ease;
}

@keyframes shake{
    0%,100%{transform:translateX(0);}
    20%{transform:translateX(-6px);}
    40%{transform:translateX(6px);}
    60%{transform:translateX(-4px);}
    80%{transform:translateX(4px);}
}

.error-banner i{
    font-size:20px;
    color:#c0293b;
}

/* ===== Form Groups ===== */

.group{
    margin-bottom:26px;
    position:relative;
}

.group label.field-label{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:10px;
    font-weight:700;
    color:var(--burgundy);
    font-size:14.5px;
}

.group label.field-label i{
    color:var(--gold);
    font-size:13px;
}

.input-wrap{
    position:relative;
}

.input-wrap i.field-icon{
    position:absolute;
    top:50%;
    right:18px;
    transform:translateY(-50%);
    color:var(--gold);
    font-size:16px;
    transition:var(--transition);
    pointer-events:none;
}

.group input[type="text"]{
    width:100%;
    padding:16px 50px 16px 18px;
    border:1.5px solid #e6e2e6;
    border-radius:var(--radius-sm);
    font-size:15px;
    background:#fff;
    color:#2b2b2f;
    transition:var(--transition);
    font-weight:600;
}

.group input[type="text"]::placeholder{
    color:#b9b7bc;
    font-weight:500;
}

.group input[type="text"]:hover{
    border-color:#d8c8cd;
}

.group input[type="text"]:focus{
    outline:none;
    border-color:var(--burgundy);
    box-shadow:0 0 0 4px rgba(91,16,40,.10);
}

.group input[type="text"]:focus ~ i.field-icon,
.input-wrap:focus-within i.field-icon{
    color:var(--burgundy);
    transform:translateY(-50%) scale(1.1);
}

.group input.valid{
    border-color:#3aa66b;
    box-shadow:0 0 0 4px rgba(58,166,107,.10);
}

.group input.invalid{
    border-color:#d64550;
    box-shadow:0 0 0 4px rgba(214,69,80,.10);
    animation:shake .45s ease;
}

.field-hint{
    display:flex;
    align-items:center;
    gap:6px;
    margin-top:8px;
    font-size:12.5px;
    font-weight:600;
    min-height:16px;
}

.field-hint.success{color:#2f8f5b;}
.field-hint.error{color:#c0293b;}

/* ===== Image Upload ===== */

.image-box{
    display:block;
    border:2px dashed var(--gold);
    border-radius:var(--radius-md);
    padding:38px 20px;
    text-align:center;
    cursor:pointer;
    transition:var(--transition);
    background:linear-gradient(135deg,rgba(212,175,55,.05),rgba(91,16,40,.03));
    position:relative;
    overflow:hidden;
}

.image-box:hover{
    background:linear-gradient(135deg,rgba(212,175,55,.12),rgba(91,16,40,.05));
    border-color:var(--burgundy);
    transform:translateY(-2px);
    box-shadow:0 14px 30px rgba(212,175,55,.18);
}

.image-box.dragover{
    background:linear-gradient(135deg,rgba(212,175,55,.18),rgba(91,16,40,.08));
    border-color:var(--burgundy);
    transform:scale(1.01);
}

.image-box img{
    width:180px;
    height:180px;
    display:none;
    margin:0 auto 16px;
    object-fit:cover;
    border-radius:var(--radius-md);
    box-shadow:0 12px 28px rgba(0,0,0,.18);
    animation:fadeIn .5s ease;
}

#text i{
    font-size:42px;
    color:var(--gold);
    margin-bottom:14px;
    display:inline-block;
    animation:floatIcon 3s ease-in-out infinite;
}

@keyframes floatIcon{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-8px);}
}

#text h3{
    color:var(--burgundy);
    margin-bottom:8px;
    font-size:16.5px;
    font-weight:800;
}

#text p{
    color:var(--text-gray);
    font-size:13px;
    font-weight:600;
    letter-spacing:.5px;
}

/* ===== Buttons ===== */

.btn-row{
    display:flex;
    gap:14px;
    margin-top:10px;
}

.btn{
    flex:1;
    position:relative;
    overflow:hidden;
    padding:17px;
    border:none;
    border-radius:50px;
    font-size:16px;
    font-weight:800;
    cursor:pointer;
    transition:var(--transition);
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    min-height:56px;
}

.btn-primary{
    background:linear-gradient(135deg,var(--burgundy) 0%,var(--burgundy-light) 60%,var(--burgundy) 100%);
    color:#fff;
    box-shadow:0 14px 30px rgba(91,16,40,.30);
}

.btn-primary:hover{
    background:linear-gradient(135deg,var(--gold) 0%,var(--gold-light) 50%,var(--gold) 100%);
    color:var(--burgundy-dark);
    transform:translateY(-3px);
    box-shadow:0 18px 36px rgba(212,175,55,.35);
}

.btn-primary:active{
    transform:translateY(-1px);
}

.btn-primary.loading{
    pointer-events:none;
    color:transparent;
}

.btn-primary.loading::after{
    content:"";
    position:absolute;
    width:22px;
    height:22px;
    border-radius:50%;
    border:3px solid rgba(255,255,255,.4);
    border-top-color:#fff;
    animation:spin .7s linear infinite;
}

@keyframes spin{
    to{transform:rotate(360deg);}
}

.ripple{
    position:absolute;
    border-radius:50%;
    background:rgba(255,255,255,.55);
    transform:scale(0);
    animation:rippleAnim .6s ease-out;
    pointer-events:none;
}

@keyframes rippleAnim{
    to{transform:scale(3);opacity:0;}
}

.btn-secondary{
    flex:.6;
    background:var(--light-gray);
    color:#5a5a5f;
    box-shadow:none;
    border:1.5px solid #e4e2e6;
}

.btn-secondary:hover{
    background:#ececef;
    color:#2b2b2f;
    transform:translateY(-2px);
}

/* ===== Sidebar / Preview ===== */

.sidebar{
    position:sticky;
    top:30px;
    display:flex;
    flex-direction:column;
    gap:22px;
    animation:slideUp .8s ease;
}

.preview-card{
    background:linear-gradient(160deg,var(--burgundy) 0%,var(--burgundy-dark) 100%);
    border-radius:var(--radius-lg);
    padding:30px 26px;
    color:#fff;
    box-shadow:var(--shadow-soft);
    position:relative;
    overflow:hidden;
}

.preview-card::before{
    content:"";
    position:absolute;
    width:180px;
    height:180px;
    background:radial-gradient(circle,rgba(212,175,55,.35),transparent 70%);
    top:-60px;
    left:-60px;
    border-radius:50%;
}

.preview-label{
    font-size:12px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    color:var(--gold-light);
    font-weight:700;
    margin-bottom:18px;
    display:flex;
    align-items:center;
    gap:8px;
}

.preview-image-wrap{
    width:100%;
    height:160px;
    border-radius:var(--radius-md);
    background:rgba(255,255,255,.08);
    border:1.5px dashed rgba(212,175,55,.5);
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
    margin-bottom:18px;
    position:relative;
}

.preview-image-wrap img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:none;
    animation:fadeIn .5s ease;
}

.preview-image-wrap .placeholder-icon{
    font-size:34px;
    color:rgba(255,255,255,.35);
}

.preview-name{
    font-size:19px;
    font-weight:800;
    margin-bottom:6px;
    min-height:26px;
    word-break:break-word;
}

.preview-name.placeholder{
    color:rgba(255,255,255,.4);
    font-weight:600;
    font-size:15px;
}

.preview-desc{
    font-size:13px;
    color:rgba(255,255,255,.65);
    line-height:1.6;
    font-weight:600;
}

.tip-card{
    background:var(--white);
    border:1px solid rgba(91,16,40,.08);
    border-radius:var(--radius-md);
    padding:22px 24px;
    box-shadow:0 10px 24px rgba(0,0,0,.04);
}

.tip-card h4{
    color:var(--burgundy);
    font-size:14.5px;
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:12px;
    font-weight:800;
}

.tip-card h4 i{color:var(--gold);}

.tip-card ul{
    list-style:none;
    display:flex;
    flex-direction:column;
    gap:9px;
}

.tip-card li{
    font-size:13px;
    color:#6b6b70;
    font-weight:600;
    display:flex;
    align-items:flex-start;
    gap:8px;
}

.tip-card li i{
    color:#3aa66b;
    margin-top:2px;
    font-size:12px;
}

/* ===== Responsive ===== */

@media (max-width:900px){
    .layout{
        grid-template-columns:1fr;
    }
    .sidebar{
        position:static;
    }
    .card{
        padding:28px;
    }
}

@media (max-width:560px){
    body{
        padding:18px 14px;
    }
    .page-header{
        flex-direction:column;
        align-items:flex-start;
    }
    .back-btn{
        width:100%;
        justify-content:center;
    }
    .header-text h1{
        font-size:24px;
    }
    .card{
        padding:22px;
        border-radius:18px;
    }
    .btn-row{
        flex-direction:column;
    }
    .btn-secondary{
        flex:1;
    }
    .btn,
    .back-btn,
    .group input[type="text"]{
        min-height:50px;
    }
    .image-box{
        padding:28px 14px;
    }
    .image-box img{
        width:140px;
        height:140px;
    }
}

/* Accessibility */

a:focus-visible,
button:focus-visible,
input:focus-visible,
label.image-box:focus-within{
    outline:3px solid var(--gold);
    outline-offset:2px;
}

</style>

</head>

<body>

<div class="page-wrap">

    <div class="page-header">

        <div class="header-text">

            <div class="breadcrumb">

                <span>الرئيسية</span>

                <i class="fa-solid fa-chevron-left"></i>

                <span>التصنيفات</span>

                <i class="fa-solid fa-chevron-left"></i>

                <span class="current">إضافة تصنيف</span>

            </div>

            <h1>

                <span class="icon-badge"><i class="fa-solid fa-folder-plus"></i></span>

                إضافة تصنيف جديد

            </h1>

            <p class="desc">أنشئ تصنيفاً جديداً لتنظيم منتجات المتجر.</p>

        </div>

        <a href="categories.php" class="back-btn">

            <i class="fa-solid fa-arrow-right"></i>

            <span>رجوع</span>

        </a>

    </div>

    <div class="layout">

        <div class="card">

            <?php if($error!=""){ ?>

            <div class="error-banner">

                <i class="fa-solid fa-circle-exclamation"></i>

                <span><?php echo $error; ?></span>

            </div>

            <?php } ?>

            <div class="section-title">

                <i class="fa-solid fa-folder"></i>

                معلومات التصنيف

            </div>

            <form method="POST" enctype="multipart/form-data" id="categoryForm">

                <div class="group">

                    <label class="field-label">

                        <i class="fa-solid fa-tag"></i>

                        اسم التصنيف

                    </label>

                    <div class="input-wrap">

                        <input
                        type="text"
                        name="name"
                        id="nameInput"
                        placeholder="مثال: إلكترونيات، أزياء، مستلزمات منزلية..."
                        autocomplete="off"
                        required>

                        <i class="fa-solid fa-tag field-icon"></i>

                    </div>

                    <div class="field-hint" id="nameHint"></div>

                </div>

                <div class="group">

                    <label class="field-label">

                        <i class="fa-solid fa-image"></i>

                        صورة التصنيف

                    </label>

                    <label class="image-box" id="imageBox" tabindex="0">

                        <img id="preview" alt="معاينة صورة التصنيف">

                        <div id="text">

                            <i class="fa-solid fa-cloud-arrow-up"></i>

                            <h3>اضغط أو اسحب الصورة هنا</h3>

                            <p>JPG · PNG · WEBP</p>

                        </div>

                        <input

                        type="file"

                        name="image"

                        id="imageInput"

                        accept="image/*"

                        hidden

                        required

                        onchange="previewImage(event)">

                    </label>

                </div>

                <div class="btn-row">

                    <button type="button" class="btn btn-secondary" onclick="window.location.href='categories.php'">

                        إلغاء

                    </button>

                    <button
                    class="btn btn-primary"
                    name="add"
                    id="submitBtn"
                    type="submit">

                        <i class="fa-solid fa-circle-plus"></i>

                        <span>إضافة التصنيف</span>

                    </button>

                </div>

            </form>

        </div>

        <div class="sidebar">

            <div class="preview-card">

                <div class="preview-label">

                    <i class="fa-solid fa-eye"></i>

                    معاينة مباشرة

                </div>

                <div class="preview-image-wrap">

                    <i class="fa-solid fa-image placeholder-icon" id="previewPlaceholderIcon"></i>

                    <img id="previewSideImg" alt="معاينة التصنيف">

                </div>

                <div class="preview-name placeholder" id="previewSideName">اسم التصنيف سيظهر هنا</div>

                <div class="preview-desc">هكذا سيظهر التصنيف الجديد في متجرك بعد الإضافة.</div>

            </div>

            <div class="tip-card">

                <h4><i class="fa-solid fa-lightbulb"></i> نصائح سريعة</h4>

                <ul>

                    <li><i class="fa-solid fa-check"></i> اختر اسماً واضحاً وقصيراً للتصنيف.</li>

                    <li><i class="fa-solid fa-check"></i> استخدم صورة عالية الجودة بخلفية بسيطة.</li>

                    <li><i class="fa-solid fa-check"></i> الصيغ المدعومة: JPG, PNG, WEBP فقط.</li>

                </ul>

            </div>

        </div>

    </div>

</div>

<script>

function previewImage(event){

    const file = event.target.files[0];

    if(!file) return;

    const reader = new FileReader();

    reader.onload = function(){

        document.getElementById("preview").src = reader.result;

        document.getElementById("preview").style.display = "block";

        document.getElementById("text").style.display = "none";

        const sideImg = document.getElementById("previewSideImg");
        const placeholderIcon = document.getElementById("previewPlaceholderIcon");

        sideImg.src = reader.result;
        sideImg.style.display = "block";
        placeholderIcon.style.display = "none";

    }

    reader.readAsDataURL(file);

}

/* Drag & drop enhancement for upload box */

const imageBox = document.getElementById("imageBox");
const imageInput = document.getElementById("imageInput");

["dragenter","dragover"].forEach(evt=>{

    imageBox.addEventListener(evt,function(e){

        e.preventDefault();
        e.stopPropagation();
        imageBox.classList.add("dragover");

    });

});

["dragleave","drop"].forEach(evt=>{

    imageBox.addEventListener(evt,function(e){

        e.preventDefault();
        e.stopPropagation();
        imageBox.classList.remove("dragover");

    });

});

imageBox.addEventListener("drop",function(e){

    const files = e.dataTransfer.files;

    if(files && files.length){

        imageInput.files = files;

        previewImage({target:imageInput});

    }

});

/* Live name preview */

const nameInput = document.getElementById("nameInput");
const previewSideName = document.getElementById("previewSideName");
const nameHint = document.getElementById("nameHint");

nameInput.addEventListener("input",function(){

    const val = nameInput.value.trim();

    if(val.length > 0){

        previewSideName.textContent = val;
        previewSideName.classList.remove("placeholder");

        nameInput.classList.remove("invalid");
        nameInput.classList.add("valid");
        nameHint.textContent = "اسم مناسب ✓";
        nameHint.className = "field-hint success";

    }else{

        previewSideName.textContent = "اسم التصنيف سيظهر هنا";
        previewSideName.classList.add("placeholder");

        nameInput.classList.remove("valid","invalid");
        nameHint.textContent = "";
        nameHint.className = "field-hint";

    }

});

/* Ripple + loading state on submit */

const submitBtn = document.getElementById("submitBtn");
const categoryForm = document.getElementById("categoryForm");

submitBtn.addEventListener("click",function(e){

    const rect = submitBtn.getBoundingClientRect();

    const circle = document.createElement("span");

    const size = Math.max(rect.width, rect.height);

    circle.style.width = circle.style.height = size + "px";

    circle.style.left = (e.clientX - rect.left - size/2) + "px";

    circle.style.top = (e.clientY - rect.top - size/2) + "px";

    circle.classList.add("ripple");

    submitBtn.appendChild(circle);

    setTimeout(()=>circle.remove(),650);

});

categoryForm.addEventListener("submit",function(){

    if(categoryForm.checkValidity()){

        submitBtn.classList.add("loading");

    }

});

</script>

</body>

</html>