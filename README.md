<div align="center">

# 🎓 AI-Powered Online Examination System

### *Digitizing education, one exam at a time.*

![PHP](https://img.shields.io/badge/PHP-7.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-3.3.7-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-3.3.1-0769AD?style=flat-square&logo=jquery&logoColor=white)
![OpenAI](https://img.shields.io/badge/OpenAI-GPT--3.5--Turbo-412991?style=flat-square&logo=openai&logoColor=white)
![License](https://img.shields.io/badge/License-CC%20BY--NC--ND%204.0-lightgrey?style=flat-square)

</div>

---

## 📖 About

A **full-stack web-based examination platform** built with PHP & MySQL that completely digitizes the examination process — from quiz creation to AI-generated result feedback. No paperwork. No manual grading. Just smart, fast, and efficient online testing.

> 🏫 Built as a final-year project**

---

## ✨ Key Highlights

| 🤖 AI Question Generator | 🏆 Live Leaderboard | 👥 Multi-Role System |
|:---:|:---:|:---:|
| Auto-generate MCQ question banks on any topic using OpenAI GPT-3.5-Turbo | Real-time AJAX-powered rankings with 🥇🥈🥉 medals | Separate dashboards for Students, Teachers & Head Admin |

| 📊 Result Analytics | ⏱️ Timed Exams | 📱 Responsive UI |
|:---:|:---:|:---:|
| Personalized AI feedback on exam performance | Configurable time limits with negative marking | Works on all screen sizes via Bootstrap 3 |

---

## 🚀 Features

### 🎓 Student
- Register, login, and browse available quizzes
- Attempt timed MCQ exams with positive & negative marking
- View full exam history (score, correct, wrong, date)
- Check per-quiz leaderboard rankings
- Get **AI-powered personalized feedback** after every exam

### 👩‍🏫 Teacher
- Create quizzes with custom marks, time limits & negative marking
- Add questions manually (4 options, correct answer selector)
- **🤖 AI Question Generator** — type a topic → AI generates & saves MCQs instantly
- View student scores and live leaderboard for own quizzes
- Delete quizzes with full cascading cleanup

### 🛡️ Head Admin
- Manage all students (view, delete with data cleanup)
- Add & remove teachers (with optional quiz deletion)
- View all contact/feedback submissions
- System-wide ranking across all quizzes

---

## 🏗️ System Architecture

```
┌──────────────────────────────────────────┐
│            Browser (Client)              │
│   Bootstrap · jQuery · Leaflet.js        │
└──────────────────┬───────────────────────┘
                   │ HTTP / AJAX
┌──────────────────▼───────────────────────┐
│          PHP Application Layer           │
│                                          │
│  index.php      → Landing + login modals │
│  account.php    → Student dashboard      │
│  dash.php       → Teacher dashboard      │
│  headdash.php   → Admin dashboard        │
│  update.php     → All write operations   │
│  process_ai_save.php → OpenAI API bridge │
└──────────────────┬───────────────────────┘
                   │ MySQLi
┌──────────────────▼───────────────────────┐
│         MySQL Database (project1)        │
│  user · admin · quiz · questions         │
│  options · answer · history · feedback   │
└──────────────────┬───────────────────────┘
                   │ External APIs
┌──────────────────▼───────────────────────┐
│  OpenAI GPT-3.5  → MCQ gen + feedback   │
│  SerpAPI         → Search-based hints    │
│  OpenStreetMap   → Campus map            │
└──────────────────────────────────────────┘
```

---

## 🗄️ Database Schema

**Database:** `project1`

| Table | Purpose |
|-------|---------|
| `user` | Registered students (email, name, gender, college, mobile, password) |
| `admin` | Teachers & Head Admin (email, password, role) |
| `quiz` | Quiz metadata (title, marks, negative marks, time, total questions) |
| `questions` | MCQ questions with 4 options and correct answer |
| `options` | Legacy option storage |
| `answer` | Legacy correct answer mapping |
| `history` | Student exam attempt records (score, correct, wrong, date) |
| `feedback` | Contact form submissions |

---

## ⚙️ Installation

### Requirements
- PHP 7.2+ (with `mysqli` and `curl` enabled)
- MySQL 5.7+ or MariaDB 10.1+
- Apache/Nginx (XAMPP recommended)

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/chandtarun10/AI-Powered-Online-Examination-System.git
```

**2. Move to your server root**
```
XAMPP → C:/xampp/htdocs/
LAMP  → /var/www/html/
```

**3. Create database & import schema**
```bash
mysql -u root -p project1 < project1.sql
```

**4. Configure DB connection** in `dbConnection.php`
```php
$con = new mysqli('localhost', 'root', '', 'project1');
```

**5. Add your OpenAI API key** in `process_ai_save.php` and `ai_result_feedback.php`
```php
$apiKey = "YOUR_OPENAI_API_KEY_HERE";
```

**6. Visit in browser**
```
http://localhost/AI-Powered-Online-Examination-System/
```

---

## 🔑 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| 🛡️ Head Admin | head@gmail.com | head |
| 👩‍🏫 Teacher 1 | teacher1@gmail.com | teacher1 |
| 👩‍🏫 Teacher 2 | teacher2@gmail.com | teacher2 |
| 🎓 Student | Register a new account | — |

> ⚠️ Change these credentials before any live deployment.

---

## 🤖 AI Integration

### Question Generator
Teachers enter a **topic name** + **question count** → the system calls **OpenAI GPT-3.5-Turbo** → structured MCQs are generated in JSON format and saved directly to the database.

```json
{
  "question": "What does SQL stand for?",
  "options": { "A": "Structured Query Language", "B": "Simple Query Language", "C": "Sequel Query Language", "D": "None" },
  "answer": "A"
}
```

### Result Insights
Students enter their subject and score → GPT analyzes performance → returns personalized feedback with weak areas and revision suggestions.

---

## 📁 Project Structure

```
AI-Powered-Online-Examination-System/
│
├── index.php                  # Landing page
├── account.php                # Student dashboard + exam engine
├── dash.php                   # Teacher dashboard
├── headdash.php               # Head admin dashboard
├── update.php                 # Unified write/mutation handler
├── dbConnection.php           # Database connection
├── project1.sql               # Full DB schema + seed data
│
├── generate_ai_questions.php  # AI question generator UI
├── process_ai_save.php        # OpenAI API handler
├── ai_result_feedback.php     # AI result feedback UI
├── fetch_ranking.php          # AJAX leaderboard endpoint
│
├── login.php / logout.php     # Auth handlers
├── sign.php / signadmin.php   # Registration handlers
├── head.php / admin.php       # Role-based login handlers
│
├── css/                       # Bootstrap + custom styles
├── js/                        # jQuery + Bootstrap JS
├── fonts/                     # Glyphicons
└── Er diagram.pdf             # Entity-Relationship diagram
```

---

## 🔐 Security Disclaimer

This is an **academic prototype**. For production use, the following improvements are recommended:

- Use **PDO prepared statements** to prevent SQL injection
- Hash passwords with `password_hash()` / `password_verify()`
- Add **CSRF tokens** to all forms
- Store API keys in **environment variables**, not source code
- Sanitize all output with `htmlspecialchars()`

---

## 🌱 Future Enhancements

- [ ] Full-screen proctoring with tab-switch detection
- [ ] Question randomization per attempt
- [ ] Bulk question import via CSV
- [ ] Email notifications for results
- [ ] OAuth login (Google / GitHub)
- [ ] Mobile app via REST API
- [ ] Dark mode UI

---

## 👨‍💻 Author

**Tarun Chand**
📧 chandtarun1234@gmail.com
📍 Bhimtal, Uttarakhand, India
🏫 Graphic Era Hill University

---

## 📜 License

Licensed under **CC BY-NC-ND 4.0** — free to share with attribution, no commercial use, no modifications for redistribution.

---

<div align="center">

⭐ **If you found this project useful, give it a star!** ⭐

*Made with ❤️ for modern, paperless education*

</div>
