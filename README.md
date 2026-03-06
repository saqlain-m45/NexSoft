# 🚀 NexSoft Hub — Software Consulting Agency Website

<div align="center">

![NexSoft Hub](https://img.shields.io/badge/NexSoft-Hub-0e8f8e?style=for-the-badge&logo=code&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-Ready-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)

A **production-ready, full-stack website** for a software consulting agency — built with PHP, MySQL, Bootstrap 5, and vanilla CSS/JS. Features a premium UI/UX with glassmorphism, smooth animations, full admin panel, and team/blog/project management.

[Live Demo](#) • [Report Bug](https://github.com/saqlain-m45/NexSoft/issues) • [Request Feature](https://github.com/saqlain-m45/NexSoft/issues)

</div>

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Pages](#-pages)
- [Admin Panel](#-admin-panel)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Configuration](#-configuration)
- [Screenshots](#-screenshots)
- [License](#-license)

---

## ✨ Features

### Frontend
- 🎨 **Premium UI/UX** — glassmorphism navbar, smooth scroll animations, reveal effects
- 📱 **Fully Responsive** — optimized for desktop, tablet, and mobile
- 🌐 **Route-based Routing** — clean URLs via `.htaccess` (no `.php` extensions)
- 👥 **Team Org-Tree** — circular avatar org-chart with bio tooltips and pulsing root ring
- 📝 **Blog System** — dynamic blog with categories, reading time, and single-post view
- 💬 **Contact Form** — validation, anti-spam, and database-stored messages
- 📋 **Join Our Team Form** — career applications with skill/role selection

### Admin Panel
- 🔐 **Secure Login** — session-based authentication with password hashing
- 📊 **Dashboard** — live stats for projects, blogs, messages, and registrations
- 🧑‍💼 **Team Management** — CRUD with photo upload (circular avatars on frontend)
- 📁 **Projects Management** — add/edit/delete portfolio projects with images
- ✍️ **Blog Management** — rich content editor, featured images, tags
- 📬 **Messages Management** — view and manage contact form submissions
- 👤 **Registrations** — verify/reject applicants with automatic SMTP email notifications
- 🏷️ **Status Filtering** — filter registrations by Pending / Verified / Rejected

### Technical
- 🔒 **Security** — prepared statements, XSS sanitization, CSRF awareness
- 📧 **SMTP Mailer** — native PHP socket-based SMTP with TLS/SSL + `mail()` fallback
- 🗄️ **Auto-Migration** — admin pages auto-create missing DB tables on first load
- 🖼️ **Smart Image Handling** — JS `onerror` fallback to initials avatars

---

## 🛠 Tech Stack

| Layer      | Technology                          |
|------------|--------------------------------------|
| Frontend   | HTML5, CSS3, JavaScript (ES6+)       |
| Framework  | Bootstrap 5.3                        |
| Icons      | Bootstrap Icons                      |
| Fonts      | Google Fonts (Inter)                 |
| Backend    | PHP 8.x                              |
| Database   | MySQL 8.0 (via MySQLi)               |
| Server     | Apache (XAMPP)                       |
| Routing    | `.htaccess` mod_rewrite              |

---

## 📁 Project Structure

```
NexSoft/
├── admin/                    # Admin panel pages
│   ├── auth.php              # Session auth guard
│   ├── dashboard.php         # Stats overview
│   ├── team.php              # Team member CRUD
│   ├── projects.php          # Projects CRUD
│   ├── blogs.php             # Blog CRUD
│   ├── messages.php          # Contact messages
│   ├── registrations.php     # Job applications management
│   ├── layout-header.php     # Admin layout header
│   └── layout-footer.php     # Admin layout footer
│
├── assets/
│   ├── css/
│   │   ├── style.css         # Main frontend styles
│   │   └── admin.css         # Admin panel styles
│   ├── js/
│   │   └── main.js           # Frontend JS (animations, forms)
│   └── uploads/
│       ├── team/             # Team member photos
│       ├── projects/         # Project screenshots
│       └── blogs/            # Blog featured images
│
├── components/
│   ├── header.php            # Site navbar + head
│   └── footer.php            # Site footer + scripts
│
├── config/
│   ├── database.php          # DB connection + ROOT_PATH
│   └── mailer.php            # SMTP email config + sendMail()
│
├── controllers/              # Route controllers
│   ├── HomeController.php
│   ├── AboutController.php
│   ├── ServicesController.php
│   ├── BlogController.php
│   ├── ContactController.php
│   ├── PricingController.php
│   └── RegisterController.php
│
├── database/
│   ├── nexsoft_hub.sql       # Full database schema + seed data
│   └── add_team_members.sql  # Team table migration
│
├── views/                    # Page views
│   ├── home.php
│   ├── about.php
│   ├── services.php
│   ├── blog.php
│   ├── blog-single.php
│   ├── contact.php
│   ├── pricing.php
│   ├── register.php
│   └── 404.php
│
├── index.php                 # Front controller / router
└── .htaccess                 # URL rewriting rules
```

---

## 📄 Pages

| Route | Page | Description |
|-------|------|-------------|
| `/` | Home | Hero, services overview, projects, testimonials, blog preview, CTA |
| `/about` | About Us | Mission/Vision, Team org-chart, Company story, Core values |
| `/services` | Services | 6 service cards (Web, App, WordPress, UI/UX, Content, Video) |
| `/blog` | Blog | Blog listing with categories and search |
| `/blog/{slug}` | Blog Post | Full single blog post view |
| `/pricing` | Pricing | Tiered pricing plans with feature comparison |
| `/contact` | Contact | Contact form + office info + map |
| `/register` | Join Our Team | Career application form |

---

## 🖥️ Admin Panel

Access at: `http://localhost/NexSoft/admin/`

| Page | URL | Function |
|------|-----|----------|
| Login | `/admin/` | Secure admin login |
| Dashboard | `/admin/dashboard.php` | Live stats overview |
| Team | `/admin/team.php` | Add/edit/delete team members + photo upload |
| Projects | `/admin/projects.php` | Portfolio project management |
| Blogs | `/admin/blogs.php` | Blog post management |
| Messages | `/admin/messages.php` | Contact form submissions |
| Registrations | `/admin/registrations.php` | Verify/reject job applications + email |

### Default Admin Credentials
```
Username: admin
Password: admin123
```
> ⚠️ **Change these immediately** after first login in `admin/login.php`

---

## ⚙️ Installation

### Requirements
- XAMPP (or any Apache + PHP 8+ + MySQL stack)
- PHP 8.0+
- MySQL 8.0+

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/saqlain-m45/NexSoft.git
# Move to XAMPP htdocs
mv NexSoft /Applications/XAMPP/xamppfiles/htdocs/
# Or on Windows:
# Move to C:\xampp\htdocs\
```

**2. Start XAMPP**
- Start **Apache** and **MySQL** from the XAMPP control panel

**3. Set upload folder permissions (macOS/Linux)**
```bash
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/NexSoft/assets/uploads/
```

**4. Import the database** *(see [Database Setup](#-database-setup))*

**5. Configure database** *(see [Configuration](#-configuration))*

**6. Open in browser**
```
http://localhost/NexSoft/
http://localhost/NexSoft/admin/
```

---

## 🗄️ Database Setup

1. Open **phpMyAdmin** → `http://localhost/phpmyadmin/`
2. Create a new database named `nexsoft_hub`
3. Click **Import** → select `database/nexsoft_hub.sql`
4. Click **Go** to import all tables and seed data

### Tables Created
| Table | Purpose |
|-------|---------|
| `admin_users` | Admin login credentials |
| `team_members` | Team member profiles |
| `projects` | Portfolio projects |
| `blog_posts` | Blog articles |
| `contacts` | Contact form messages |
| `registrations` | Job applications |

---

## 🔧 Configuration

### Database (`config/database.php`)
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
define('DB_NAME', 'nexsoft_hub');
```

### SMTP Email (`config/mailer.php`)
Configure to enable automatic email notifications for job application approvals/rejections:
```php
define('SMTP_HOST',   'smtp.gmail.com');
define('SMTP_PORT',   587);               // 587 for TLS, 465 for SSL
define('SMTP_USER',   'your@email.com');
define('SMTP_PASS',   'your_app_password');
define('SMTP_FROM',   'your@email.com');
define('SMTP_NAME',   'NexSoft Hub');
define('SMTP_SECURE', 'tls');             // 'tls' or 'ssl'
```
> If SMTP is not configured, the system falls back to PHP's `mail()` function.

---

## 🔐 Security Notes

- All database queries use **prepared statements** (MySQLi)
- User inputs are sanitized with `htmlspecialchars()`
- Admin area is protected by **session-based authentication**
- File uploads are validated by type and stored in isolated directories
- **Never commit real credentials** — consider using a `.env` file for production

---

## 📦 Deployment Checklist

- [ ] Change default admin password
- [ ] Set correct DB credentials in `config/database.php`
- [ ] Configure SMTP in `config/mailer.php`
- [ ] Set `chmod 755` on upload directories (production)
- [ ] Enable HTTPS on your server
- [ ] Review `.htaccess` for your server environment

---

## 👤 Author

**Saqlain Muzafar**
- GitHub: [@saqlain-m45](https://github.com/saqlain-m45)

---

## 📄 License

This project is proprietary software developed for **NexSoft Hub**.
All rights reserved © 2024 NexSoft Hub.

---

<div align="center">
  Built with ❤️ by the NexSoft Hub Team
</div>
