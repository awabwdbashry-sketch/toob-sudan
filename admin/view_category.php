<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: categories.php");
    exit;
}

$id = (int)$_GET['id'];

$category = mysqli_query($conn,"
SELECT *
FROM categories
WHERE id='$id'
");

if(mysqli_num_rows($category)==0){
    header("Location: categories.php");
    exit;
}

$category = mysqli_fetch_assoc($category);

$products = mysqli_query($conn,"
SELECT *
FROM products
WHERE category_id='$id'
ORDER BY id DESC
");

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>عرض التصنيف</title>

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
        radial-gradient(circle at 8% 10%, rgba(212,175,55,.10), transparent 40%),
        radial-gradient(circle at 92% 15%, rgba(91,16,40,.08), transparent 45%),
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
    max-width:1240px;
    margin:0 auto;
    position:relative;
    z-index:1;
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(14px);}
    to{opacity:1;transform:translateY(0);}
}

@keyframes slideUp{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

/* ===== Breadcrumb + Back ===== */

.top-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:22px;
    flex-wrap:wrap;
    animation:slideUp .55s ease;
}

.breadcrumb{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    color:var(--text-gray);
    font-weight:600;
}

.breadcrumb span.current{
    color:var(--burgundy);
    font-weight:700;
}

.breadcrumb i{
    font-size:10px;
    color:var(--gold);
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
    padding:12px 20px;
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

/* ===== Header / Category Hero ===== */

.hero-card{
    display:flex;
    align-items:center;
    gap:28px;
    background:linear-gradient(160deg,var(--burgundy) 0%,var(--burgundy-dark) 100%);
    border-radius:var(--radius-lg);
    padding:34px 36px;
    color:#fff;
    box-shadow:var(--shadow-soft);
    margin-bottom:28px;
    position:relative;
    overflow:hidden;
    animation:slideUp .65s ease;
    flex-wrap:wrap;
}

.hero-card::before{
    content:"";
    position:absolute;
    width:260px;
    height:260px;
    background:radial-gradient(circle,rgba(212,175,55,.30),transparent 70%);
    top:-90px;
    left:-90px;
    border-radius:50%;
}

.hero-card img{
    width:120px;
    height:120px;
    border-radius:20px;
    object-fit:cover;
    box-shadow:0 14px 30px rgba(0,0,0,.30);
    border:2px solid rgba(212,175,55,.5);
    position:relative;
    z-index:1;
    flex-shrink:0;
}

.hero-info{
    position:relative;
    z-index:1;
    flex:1;
    min-width:200px;
}

.hero-info .label{
    font-size:11.5px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    color:var(--gold-light);
    font-weight:700;
    margin-bottom:8px;
}

.hero-info h1{
    font-size:28px;
    font-weight:800;
    margin-bottom:12px;
    word-break:break-word;
}

.hero-stats{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    position:relative;
    z-index:1;
}

.stat-pill{
    background:rgba(255,255,255,.10);
    border:1px solid rgba(212,175,55,.4);
    color:#fff;
    padding:10px 18px;
    border-radius:50px;
    font-size:13.5px;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:8px;
}

.stat-pill i{
    color:var(--gold-light);
}

.stat-pill b{
    color:var(--gold-light);
    font-weight:800;
}

/* ===== Section Title ===== */

.section-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:19px;
    font-weight:800;
    color:var(--burgundy);
    margin-bottom:20px;
}

.section-title i{
    color:var(--gold);
}

/* ===== Table Card ===== */

.card{
    background:rgba(255,255,255,.75);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.6);
    border-radius:var(--radius-lg);
    box-shadow:var(--shadow-soft);
    padding:8px;
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

.table-scroll{
    overflow-x:auto;
    padding:26px;
}

table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 10px;
}

table thead th{
    background:transparent;
    color:var(--text-gray);
    font-size:12.5px;
    font-weight:800;
    letter-spacing:.5px;
    padding:10px 15px;
    text-align:center;
    text-transform:uppercase;
}

table tbody tr{
    background:#fff;
    transition:var(--transition);
    box-shadow:0 6px 16px rgba(0,0,0,.04);
}

table tbody tr:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 30px rgba(91,16,40,.10);
}

table td{
    padding:16px 15px;
    text-align:center;
    font-size:14.5px;
    font-weight:600;
    color:#333;
    border-top:1px solid #f0eef1;
    border-bottom:1px solid #f0eef1;
}

table td:first-child{
    border-right:1px solid #f0eef1;
    border-top-right-radius:14px;
    border-bottom-right-radius:14px;
    color:var(--burgundy);
    font-weight:800;
}

table td:last-child{
    border-left:1px solid #f0eef1;
    border-top-left-radius:14px;
    border-bottom-left-radius:14px;
}

.product-img{
    width:64px;
    height:64px;
    object-fit:cover;
    border-radius:14px;
    box-shadow:0 6px 14px rgba(0,0,0,.10);
    transition:var(--transition);
}

table tbody tr:hover .product-img{
    transform:scale(1.08);
}

.price-tag{
    color:var(--burgundy);
    font-weight:800;
}

.qty-badge{
    display:inline-block;
    background:rgba(212,175,55,.14);
    color:#8a6d1a;
    font-weight:800;
    padding:6px 14px;
    border-radius:50px;
    font-size:13px;
}

/* ===== Action Buttons ===== */

.actions{
    display:flex;
    justify-content:center;
    gap:8px;
}

.actions a{
    width:40px;
    height:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#fff;
    border-radius:12px;
    transition:var(--transition);
    position:relative;
    overflow:hidden;
    box-shadow:0 6px 14px rgba(0,0,0,.10);
}

.view{
    background:linear-gradient(135deg,#0d6efd,#3b8bff);
}

.edit{
    background:linear-gradient(135deg,#f0ad0e,#ffc107);
    color:#3a2b00 !important;
}

.delete{
    background:linear-gradient(135deg,#dc3545,#f2606d);
}

.actions a:hover{
    transform:translateY(-4px) scale(1.06);
    box-shadow:0 12px 24px rgba(0,0,0,.20);
}

/* ===== Empty State ===== */

.empty{
    padding:70px 30px;
    text-align:center;
    animation:fadeIn .6s ease;
}

.empty .empty-icon{
    width:96px;
    height:96px;
    border-radius:50%;
    background:linear-gradient(135deg,rgba(212,175,55,.15),rgba(91,16,40,.08));
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 22px;
}

.empty .empty-icon i{
    font-size:42px;
    color:var(--gold);
}

.empty h2{
    color:var(--burgundy);
    font-size:20px;
    font-weight:800;
    margin-bottom:10px;
}

.empty p{
    color:var(--text-gray);
    font-size:14.5px;
    font-weight:600;
}

/* ===== Mobile stacked cards ===== */

.mobile-cards{
    display:none;
}

@media (max-width:820px){

    body{
        padding:18px 14px;
    }

    .top-row{
        flex-direction:column;
        align-items:flex-start;
    }

    .back-btn{
        width:100%;
        justify-content:center;
    }

    .hero-card{
        padding:24px;
        gap:18px;
    }

    .hero-card img{
        width:88px;
        height:88px;
    }

    .hero-info h1{
        font-size:22px;
    }

    /* Hide raw table on mobile, show stacked cards instead */

    .table-scroll{
        display:none;
    }

    .mobile-cards{
        display:flex;
        flex-direction:column;
        gap:14px;
        padding:20px;
    }

    .product-card{
        background:#fff;
        border-radius:var(--radius-md);
        padding:18px;
        box-shadow:0 10px 24px rgba(0,0,0,.06);
        display:flex;
        gap:14px;
        align-items:center;
        animation:slideUp .5s ease;
    }

    .product-card img{
        width:72px;
        height:72px;
        border-radius:14px;
        object-fit:cover;
        flex-shrink:0;
        box-shadow:0 6px 14px rgba(0,0,0,.10);
    }

    .product-card .pc-info{
        flex:1;
        min-width:0;
    }

    .product-card .pc-info .pc-name{
        font-weight:800;
        color:var(--burgundy);
        font-size:15px;
        margin-bottom:6px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .product-card .pc-info .pc-meta{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        font-size:13px;
        color:#555;
        font-weight:700;
        margin-bottom:10px;
    }

    .product-card .pc-info .pc-meta .price-tag{
        color:var(--burgundy);
    }

    .product-card .actions{
        justify-content:flex-start;
    }

    .product-card .actions a{
        width:44px;
        height:44px;
    }

}

/* Accessibility */

a:focus-visible{
    outline:3px solid var(--gold);
    outline-offset:2px;
}

</style>

</head>

<body>

<div class="page-wrap">

    <div class="top-row">

        <div class="breadcrumb">

            <span>الرئيسية</span>

            <i class="fa-solid fa-chevron-left"></i>

            <span>التصنيفات</span>

            <i class="fa-solid fa-chevron-left"></i>

            <span class="current">عرض التصنيف</span>

        </div>

        <a href="categories.php" class="back-btn">

            <i class="fa-solid fa-arrow-right"></i>

            <span>رجوع للتصنيفات</span>

        </a>

    </div>

    <div class="hero-card">

        <img
        src="../uploads/categories/<?php echo $category['image']; ?>"
        alt="<?php echo $category['name']; ?>">

        <div class="hero-info">

            <div class="label">تصنيف</div>

            <h1><?php echo $category['name']; ?></h1>

            <div class="hero-stats">

                <div class="stat-pill">

                    <i class="fa-solid fa-boxes-stacked"></i>

                    عدد المنتجات: <b><?php echo mysqli_num_rows($products); ?></b>

                </div>

            </div>

        </div>

    </div>

    <?php if(mysqli_num_rows($products)>0){ ?>

    <div class="section-title">

        <i class="fa-solid fa-shirt"></i>

        منتجات هذا التصنيف

    </div>

    <div class="card">

        <div class="table-scroll">

            <table>

                <thead>

                    <tr>

                        <th>#</th>

                        <th>الصورة</th>

                        <th>اسم المنتج</th>

                        <th>السعر</th>

                        <th>الكمية</th>

                        <th>الإجراءات</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row=mysqli_fetch_assoc($products)){ ?>

                    <tr>

                        <td><?php echo $row['id']; ?></td>

                        <td>

                            <img
                            class="product-img"
                            src="../uploads/products/<?php echo $row['image']; ?>">

                        </td>

                        <td><?php echo $row['name']; ?></td>

                        <td>

                            <span class="price-tag"><?php echo number_format($row['price'],2); ?> ج.س</span>

                        </td>

                        <td>

                            <span class="qty-badge"><?php echo $row['quantity']; ?></span>

                        </td>

                        <td>

                            <div class="actions">

                                <a
                                href="view_product.php?id=<?php echo $row['id']; ?>"
                                class="view">

                                    <i class="fa-solid fa-eye"></i>

                                </a>

                                <a
                                href="edit_product.php?id=<?php echo $row['id']; ?>"
                                class="edit">

                                    <i class="fa-solid fa-pen"></i>

                                </a>

                                <a
                                href="delete_product.php?id=<?php echo $row['id']; ?>"
                                class="delete"
                                onclick="return confirm('هل تريد حذف هذا المنتج؟')">

                                    <i class="fa-solid fa-trash"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

        <div class="mobile-cards" id="mobileCards"></div>

    </div>

    <?php } else { ?>

    <div class="card">

        <div class="empty">

            <div class="empty-icon">

                <i class="fa-solid fa-box-open"></i>

            </div>

            <h2>لا توجد منتجات داخل هذا التصنيف</h2>

            <p>قم بإضافة منتجات لهذا التصنيف لتظهر هنا.</p>

        </div>

    </div>

    <?php } ?>

</div>

<script>

/* Build lightweight mobile stacked cards by cloning the table rows,
   purely a frontend presentation layer — no PHP or data logic touched. */

(function(){

    const table = document.querySelector("table");

    if(!table) return;

    const mobileWrap = document.getElementById("mobileCards");

    const rows = table.querySelectorAll("tbody tr");

    rows.forEach(function(row){

        const cells = row.querySelectorAll("td");

        if(cells.length < 6) return;

        const id = cells[0].textContent.trim();
        const imgSrc = cells[1].querySelector("img") ? cells[1].querySelector("img").src : "";
        const name = cells[2].textContent.trim();
        const price = cells[3].textContent.trim();
        const qty = cells[4].textContent.trim();
        const actionsHTML = cells[5].querySelector(".actions").innerHTML;

        const card = document.createElement("div");

        card.className = "product-card";

        card.innerHTML = `
            <img src="${imgSrc}" alt="${name}">
            <div class="pc-info">
                <div class="pc-name">${name}</div>
                <div class="pc-meta">
                    <span class="price-tag">${price}</span>
                    <span>الكمية: ${qty}</span>
                    <span>#${id}</span>
                </div>
                <div class="actions">${actionsHTML}</div>
            </div>
        `;

        mobileWrap.appendChild(card);

    });

})();

</script>

</body>

</html>