# 🇸🇩 Toob Sudan | طوب السودان

---

# 🇸🇩 العربية

## 📌 نبذة عن المشروع

**Toob Sudan** هو مشروع متجر إلكتروني متكامل تم تطويره باستخدام **PHP و MySQL**، ويهدف إلى توفير تجربة تسوق إلكترونية سهلة ومنظمة للمستخدم، بالإضافة إلى لوحة تحكم متكاملة لإدارة المتجر.

يسمح النظام للمستخدمين بتصفح المنتجات والتصنيفات، إضافة المنتجات إلى السلة أو المفضلة، إنشاء الطلبات، تسجيل الحسابات، وإضافة التقييمات.

كما يوفر النظام لوحة تحكم للإدارة يمكن من خلالها إدارة المنتجات والتصنيفات والطلبات والمستخدمين والتقييمات والرسائل والإشعارات وإعدادات المتجر.

---

## ✨ المميزات

### 🛍️ مميزات المستخدم

- 🏠 الصفحة الرئيسية
- 🛒 سلة المشتريات
- ❤️ قائمة المفضلة
- 📦 عرض المنتجات
- 🗂️ تصنيفات المنتجات
- 🔎 صفحة تفاصيل المنتج
- 🧾 إتمام الطلب
- 📋 عرض ومتابعة الطلبات
- 👤 إنشاء حساب وتسجيل الدخول
- ⭐ إضافة تقييمات للمنتجات
- 🎁 عرض المنتجات والعروض
- 📩 صفحة التواصل
- 🔧 وضع الصيانة
- 🔒 إمكانية إغلاق المتجر

### 👨‍💼 لوحة التحكم

- 📊 لوحة المعلومات الرئيسية
- 📦 إدارة المنتجات
- ➕ إضافة المنتجات
- ✏️ تعديل المنتجات
- 🗑️ حذف المنتجات
- 🗂️ إدارة التصنيفات
- 🧾 إدارة الطلبات
- 👥 إدارة المستخدمين
- ⭐ إدارة التقييمات
- 💬 إدارة الرسائل
- 🔔 إدارة الإشعارات
- 📈 التقارير
- ⚙️ إعدادات المتجر
- 🔐 التحكم في حالة المستخدمين

---

## 🛠️ التقنيات المستخدمة

- **PHP**
- **MySQL**
- **HTML5**
- **CSS3**
- **JavaScript**
- **Bootstrap**
- **Font Awesome**
- **WAMP Server**

---

## 📁 هيكل المشروع

```text
toob_sudan/
│
├── admin/
│   ├── dashboard.php
│   ├── products.php
│   ├── add_product.php
│   ├── edit_product.php
│   ├── delete_product.php
│   ├── categories.php
│   ├── add_category.php
│   ├── edit_category.php
│   ├── delete_category.php
│   ├── orders.php
│   ├── users.php
│   ├── reviews.php
│   ├── messages.php
│   ├── notifications.php
│   ├── Reports.php
│   └── settings.php
│
├── assets/
│   └── images/
│
├── includes/
│   └── db.php
│
├── uploads/
│   ├── categories/
│   └── products/
│
├── index.php
├── products.php
├── product.php
├── categories.php
├── offers.php
├── cart.php
├── checkout.php
├── orders.php
├── wishlist.php
├── add_to_wishlist.php
├── remove_wishlist.php
├── login.php
├── register.php
├── logout.php
├── contact.php
├── about.php
└── maintenance.php
```

---

## ⚙️ طريقة التشغيل

### 1. تثبيت WAMP

قم بتثبيت وتشغيل WAMP Server على جهازك.

### 2. وضع المشروع

ضع مجلد المشروع داخل مجلد السيرفر:

```text
D:\wamp\www\toob_sudan
```

### 3. إنشاء قاعدة البيانات

أنشئ قاعدة بيانات MySQL باسم:

```text
toob_sudan
```

ثم قم باستيراد ملف قاعدة البيانات الخاص بالمشروع.

### 4. إعداد الاتصال

قم بإعداد بيانات اتصال MySQL في ملف الإعدادات المحلي.

> ⚠️ لا تقم برفع بيانات قاعدة البيانات الحقيقية إلى مستودع GitHub عام.

### 5. تشغيل المشروع

شغّل:

- Apache
- MySQL

ثم افتح:

```text
http://localhost/toob_sudan/
```

---

## 🔐 الأمان

تم الاهتمام بعدة جوانب أساسية في أمان المشروع، منها:

- تشفير كلمات المرور باستخدام Password Hashing.
- استخدام Sessions لتسجيل دخول المستخدمين.
- فصل لوحة الإدارة عن صفحات المستخدم.
- التحكم في صلاحيات الإدارة.
- استبعاد ملفات الإعدادات الحساسة من Git.
- عدم نشر بيانات الدخول الخاصة بقاعدة البيانات.

---

## 🚀 التطويرات المستقبلية

يمكن تطوير المشروع مستقبلاً بإضافة:

- 💳 بوابات الدفع الإلكتروني.
- 🚚 نظام إدارة وتحديث التوصيل.
- 📱 تطبيق Android و iOS.
- 🔔 نظام إشعارات متقدم.
- 📊 إحصائيات وتقارير أكثر تفصيلاً.
- 🔍 بحث متقدم عن المنتجات.
- 🌐 دعم لغات إضافية.
- 💬 نظام دعم العملاء.

---

## 👨‍💻 المطور

**Awab Wdbashry**

**Information Technology Developer**

---

# 🇬🇧 English

## 📌 About the Project

**Toob Sudan** is a full-featured e-commerce web application developed using **PHP and MySQL**.

The project is designed to provide customers with a simple and organized online shopping experience while providing administrators with a complete dashboard for managing the store.

Users can browse products and categories, add products to their shopping cart or wishlist, create orders, register accounts, and submit product reviews.

The administration system provides tools for managing products, categories, orders, users, reviews, messages, notifications, reports, and store settings.

---

## ✨ Features

### 🛍️ Customer Features

- 🏠 Home Page
- 🛒 Shopping Cart
- ❤️ Wishlist
- 📦 Product Browsing
- 🗂️ Product Categories
- 🔎 Product Details
- 🧾 Checkout
- 📋 Order Management
- 👤 User Registration & Login
- ⭐ Product Reviews
- 🎁 Offers
- 📩 Contact Page
- 🔧 Maintenance Mode
- 🔒 Store Open / Closed Status

### 👨‍💼 Admin Dashboard

- 📊 Dashboard
- 📦 Product Management
- ➕ Add Products
- ✏️ Edit Products
- 🗑️ Delete Products
- 🗂️ Category Management
- 🧾 Order Management
- 👥 User Management
- ⭐ Review Management
- 💬 Message Management
- 🔔 Notifications
- 📈 Reports
- ⚙️ Store Settings
- 🔐 User Account Control

---

## 🛠️ Technologies

- **PHP**
- **MySQL**
- **HTML5**
- **CSS3**
- **JavaScript**
- **Bootstrap**
- **Font Awesome**
- **WAMP Server**

---

## 📁 Project Structure

```text
toob_sudan/
│
├── admin/
│   ├── dashboard.php
│   ├── products.php
│   ├── add_product.php
│   ├── edit_product.php
│   ├── delete_product.php
│   ├── categories.php
│   ├── add_category.php
│   ├── edit_category.php
│   ├── delete_category.php
│   ├── orders.php
│   ├── users.php
│   ├── reviews.php
│   ├── messages.php
│   ├── notifications.php
│   ├── Reports.php
│   └── settings.php
│
├── assets/
│   └── images/
│
├── includes/
│   └── db.php
│
├── uploads/
│   ├── categories/
│   └── products/
│
├── index.php
├── products.php
├── product.php
├── categories.php
├── offers.php
├── cart.php
├── checkout.php
├── orders.php
├── wishlist.php
├── add_to_wishlist.php
├── remove_wishlist.php
├── login.php
├── register.php
├── logout.php
├── contact.php
├── about.php
└── maintenance.php
```

---

## ⚙️ Installation & Setup

### 1. Install WAMP

Install and start **WAMP Server** on your computer.

### 2. Place the Project

Place the project inside your web server directory:

```text
D:\wamp\www\toob_sudan
```

### 3. Create the Database

Create a MySQL database named:

```text
toob_sudan
```

Then import the project's database SQL file.

### 4. Configure the Database

Configure the local MySQL connection settings in the local configuration file.

> ⚠️ Never publish real database credentials in a public GitHub repository.

### 5. Run the Project

Start:

- Apache
- MySQL

Then open:

```text
http://localhost/toob_sudan/
```

---

## 🔐 Security

The project follows several basic security practices, including:

- Password hashing.
- Session-based authentication.
- Separate administration pages.
- Admin access control.
- Database connection management.
- Sensitive configuration files excluded from Git.
- Local administrator setup files excluded from the repository.

---

## 🚀 Future Improvements

Potential future improvements include:

- 💳 Online Payment Integration.
- 🚚 Delivery Management System.
- 📱 Android & iOS Mobile Application.
- 🔔 Advanced Notification System.
- 📊 Advanced Analytics & Reports.
- 🔍 Advanced Product Search.
- 🌐 Additional Language Support.
- 💬 Customer Support System.

---

## 👨‍💻 Developer

**Awab Wdbashry**

**Information Technology Developer**

---

## 📄 License

This project is currently intended for development and educational purposes.