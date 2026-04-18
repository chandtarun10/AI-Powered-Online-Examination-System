# \# 🎓 AI-Powered Online Examination System

# 

# <div align="center">

# 

# !\[PHP](https://img.shields.io/badge/PHP-7.2%2B-777BB4?style=for-the-badge\&logo=php\&logoColor=white)

# !\[MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge\&logo=mysql\&logoColor=white)

# !\[Bootstrap](https://img.shields.io/badge/Bootstrap-3.3.7-7952B3?style=for-the-badge\&logo=bootstrap\&logoColor=white)

# !\[jQuery](https://img.shields.io/badge/jQuery-3.3.1-0769AD?style=for-the-badge\&logo=jquery\&logoColor=white)

# !\[OpenAI](https://img.shields.io/badge/OpenAI-GPT--3.5--Turbo-412991?style=for-the-badge\&logo=openai\&logoColor=white)

# !\[License](https://img.shields.io/badge/License-CC%20BY--NC--ND%204.0-lightgrey?style=for-the-badge)

# 

# \*\*A full-stack, multi-role web-based examination platform with AI-powered question generation, real-time leaderboards, and automated result analytics.\*\*

# 

# \[Features](#-features) · \[Architecture](#-system-architecture) · \[Database Schema](#-database-schema) · \[Installation](#-installation) · \[Usage](#-usage-guide) · \[AI Integration](#-ai-integration) · \[Security](#-security-notes) · \[License](#-license)

# 

# </div>

# 

# \---

# 

# \## 📌 Overview

# 

# The \*\*AI-Powered Online Examination System\*\* is a PHP/MySQL web application that digitizes and streamlines the entire examination lifecycle — from quiz creation to result analysis. It eliminates paperwork, enables remote testing, and brings AI-powered features to both teachers and students.

# 

# The system supports \*\*three distinct roles\*\* (Head Admin, Teacher, Student), each with a dedicated dashboard and scoped access control. AI capabilities powered by \*\*OpenAI GPT-3.5-Turbo\*\* allow teachers to auto-generate MCQ question banks from any topic in seconds, while students get personalized performance feedback after every exam.

# 

# > Built as a final-year academic project at \*\*Graphic Era Hill University, Bhimtal Campus, Uttarakhand, India.\*\*

# 

# \---

# 

# \## ✨ Features

# 

# \### 👤 Student Portal

# \- Self-registration with name, gender, college, email, mobile, and password

# \- Secure login via email/password with PHP session management

# \- Browse all available quizzes with full metadata (marks, negative marking, time limit)

# \- Attempt quizzes question-by-question with a countdown timer

# \- Automatic prevention of re-attempting completed quizzes

# \- View detailed exam history (score, correct, wrong, attempted questions, date)

# \- Per-quiz leaderboard with 🥇🥈🥉 medal rankings (AJAX-powered, no page reload)

# \- AI Result Insights — personalized academic feedback powered by GPT-3.5-Turbo

# 

# \### 👩‍🏫 Teacher Dashboard

# \- Separate login portal with role-based session isolation

# \- Create custom quizzes with configurable total questions, positive marks, negative marks, and time limits

# \- Add MCQ questions manually with four options (A/B/C/D) and a correct answer selector

# \- View all student scores across quizzes they created

# \- Per-quiz ranking table with AJAX live loading

# \- Delete quizzes (with cascading cleanup of related questions and history)

# \- \*\*🤖 AI Question Generator\*\* — generate and auto-save structured MCQ question banks by entering any topic name and question count

# 

# \### 🛡️ Head Admin Dashboard

# \- Highest-privilege role (key-protected)

# \- View and manage all registered student users (delete with cascading data cleanup)

# \- Add and remove teachers; optional cascading quiz deletion when a teacher is removed

# \- View all student feedback/contact submissions with delete functionality

# \- System-wide ranking view across all quizzes

# 

# \### 🌐 Landing Page

# \- Responsive single-page design with smooth scroll navigation

# \- Modal-based login/signup flows for all three roles (no page redirects)

# \- Embedded interactive OpenStreetMap with Leaflet.js showing the institution's location

# \- Contact form with server-side storage to the `feedback` table

# 

# \---

# 

# \## 🏗️ System Architecture

# 

# ```

# ┌─────────────────────────────────────────────────────────┐

# │                     CLIENT (Browser)                     │

# │  Bootstrap 3 · jQuery · Leaflet.js · Google Fonts        │

# └────────────────────────┬────────────────────────────────┘

# &#x20;                        │ HTTP / AJAX

# ┌────────────────────────▼────────────────────────────────┐

# │                  PHP Application Layer                    │

# │                                                          │

# │  index.php         →  Landing page + multi-role login    │

# │  login.php         →  Student authentication             │

# │  admin.php         →  Teacher authentication             │

# │  head.php          →  Head admin authentication          │

# │  sign.php          →  Student registration               │

# │  signadmin.php     →  Teacher registration (admin only)  │

# │  account.php       →  Student dashboard + exam engine    │

# │  dash.php          →  Teacher dashboard                  │

# │  headdash.php      →  Head admin dashboard               │

# │  update.php        →  Unified write/mutation handler     │

# │  fetch\_ranking.php →  AJAX JSON endpoint (leaderboard)   │

# │  generate\_ai\_questions.php → AI question generator UI    │

# │  process\_ai\_save.php       → OpenAI API bridge + DB save │

# │  process\_ai.php            → SerpAPI bridge              │

# │  ai\_result\_feedback.php    → GPT result insights UI      │

# │  feed.php / feed\_submit.php → Contact form handling      │

# │  logout.php        →  Session destroy + redirect         │

# │  dbConnection.php  →  MySQLi connection singleton        │

# └────────────────────────┬────────────────────────────────┘

# &#x20;                        │ MySQLi

# ┌────────────────────────▼────────────────────────────────┐

# │                     MySQL Database                        │

# │                       (project1)                         │

# │  admin · user · quiz · questions · options               │

# │  answer · history · rank · feedback                      │

# └─────────────────────────────────────────────────────────┘

# &#x20;                        │ External APIs

# ┌────────────────────────▼────────────────────────────────┐

# │  OpenAI GPT-3.5-Turbo  →  MCQ generation + AI feedback  │

# │  SerpAPI               →  Web search-based question hints│

# │  OpenStreetMap/Leaflet →  Campus location map            │

# └─────────────────────────────────────────────────────────┘

# ```

# 

# \### Request / Session Flow

# 

# ```

# User visits index.php

# &#x20;   │

# &#x20;   ├── Student → login.php → $\_SESSION\['email', 'name'] → account.php

# &#x20;   ├── Teacher → admin.php → $\_SESSION\['email', 'name'] → dash.php

# &#x20;   └── Head    → head.php  → $\_SESSION\['email', 'key']  → headdash.php

# 

# All protected pages check session at the top:

# &#x20;   if (!isset($\_SESSION\['email'])) → redirect to index.php

# ```

# 

# \---

# 

# \## 🗃️ Database Schema

# 

# Database name: \*\*`project1`\*\*

# 

# \### `user` — Registered students

# | Column     | Type           | Notes               |

# |------------|----------------|---------------------|

# | `email`    | VARCHAR(50) PK | Primary key         |

# | `name`     | VARCHAR(50)    |                     |

# | `gender`   | VARCHAR(5)     | M / F               |

# | `college`  | VARCHAR(100)   |                     |

# | `mob`      | BIGINT(20)     | Mobile number       |

# | `password` | VARCHAR(50)    | Plain text (see security notes) |

# 

# \### `admin` — Teachers \& Head admin

# | Column     | Type           | Notes                         |

# |------------|----------------|-------------------------------|

# | `email`    | VARCHAR(50) PK | Primary key                   |

# | `password` | VARCHAR(500)   |                               |

# | `role`     | VARCHAR(10)    | `'admin'` (teacher) or `'head'` |

# 

# Default seed data includes: `head@gmail.com` (head), `teacher1–3@gmail.com` (teachers).

# 

# \### `quiz` — Quiz metadata

# | Column       | Type           | Notes                        |

# |--------------|----------------|------------------------------|

# | `eid`        | TEXT           | Unique exam ID (uniqid)      |

# | `title`      | VARCHAR(100)   |                              |

# | `sahi`       | INT            | Marks per correct answer     |

# | `wrong`      | INT            | Deduction per wrong answer   |

# | `total`      | INT            | Total question count         |

# | `time`       | BIGINT         | Time limit in minutes        |

# | `intro`      | TEXT           | Quiz description             |

# | `tag`        | VARCHAR(100)   | Topic tag                    |

# | `date`       | TIMESTAMP      |                              |

# | `email`/`created\_by` | VARCHAR(50) | Creator's email      |

# 

# \### `questions` — MCQ questions (new schema)

# | Column    | Type   | Notes                                        |

# |-----------|--------|----------------------------------------------|

# | `eid`     | TEXT   | Foreign key → `quiz.eid`                    |

# | `qid`     | TEXT   | Unique question ID                           |

# | `qn`      | INT    | Question number within quiz                  |

# | `question`| TEXT   | Question text                                |

# | `optiona` – `optiond` | TEXT | Four answer options          |

# | `ans`     | TEXT   | Correct answer: A / B / C / D               |

# 

# > \*\*Note:\*\* The legacy schema uses `qns` (question text), `choice` (option count), and `sn` (sequence number) stored alongside a separate `options` and `answer` table. `account.php` supports both schemas transparently for backward compatibility.

# 

# \### `history` — Exam attempt records

# | Column  | Type         | Notes                           |

# |---------|--------------|---------------------------------|

# | `email` | VARCHAR(50)  | FK → `user.email`               |

# | `eid`   | TEXT         | FK → `quiz.eid`                 |

# | `score` | INT          | Final calculated score          |

# | `level` | INT          | Number of questions attempted   |

# | `sahi`  | INT          | Correct answers count           |

# | `wrong` | INT          | Wrong answers count             |

# | `date`  | TIMESTAMP    | Auto-set on insert/update       |

# 

# \### `feedback` — Contact form submissions

# | Column     | Type         |

# |------------|--------------|

# | `id`       | TEXT         |

# | `name`     | VARCHAR(50)  |

# | `email`    | VARCHAR(50)  |

# | `subject`  | VARCHAR(500) |

# | `feedback` | VARCHAR(500) |

# | `date`     | DATE         |

# | `time`     | VARCHAR(50)  |

# 

# \---

# 

# \## ⚙️ Installation

# 

# \### Prerequisites

# 

# \- \*\*PHP\*\* 7.2 or higher (with `curl` and `mysqli` extensions enabled)

# \- \*\*MySQL\*\* 5.7+ or \*\*MariaDB\*\* 10.1+

# \- \*\*Apache\*\* or \*\*Nginx\*\* web server (XAMPP / WAMP / LAMP stack recommended)

# \- A modern web browser

# 

# \### Step 1 — Clone or Extract the Project

# 

# ```bash

# \# If cloning from Git

# git clone https://github.com/your-username/online-exam-system.git

# 

# \# Or extract the zip

# unzip Online-Exam-System\_Final.zip -d /your/htdocs/

# ```

# 

# Place the `Online-Exam-System/` folder inside your web server root:

# \- XAMPP → `C:/xampp/htdocs/Online-Exam-System/`

# \- LAMP  → `/var/www/html/Online-Exam-System/`

# 

# \### Step 2 — Create the Database

# 

# 1\. Open \*\*phpMyAdmin\*\* (or any MySQL client)

# 2\. Create a new database named \*\*`project1`\*\*

# 3\. Import the provided SQL dump:

# 

# ```bash

# mysql -u root -p project1 < project1.sql

# ```

# 

# Or via phpMyAdmin: select `project1` → \*\*Import\*\* → choose `project1.sql` → Go.

# 

# This creates all 8 tables and seeds default admin/teacher accounts and sample quiz data.

# 

# \### Step 3 — Configure the Database Connection

# 

# Open `dbConnection.php` and update if needed:

# 

# ```php

# $con = new mysqli('localhost', 'root', '', 'project1');

# ```

# 

# | Parameter | Default     | Change if...                         |

# |-----------|-------------|--------------------------------------|

# | Host      | `localhost` | DB is on a remote server             |

# | User      | `root`      | You use a different MySQL user       |

# | Password  | `''`        | Your MySQL root has a password       |

# | Database  | `project1`  | You named the DB differently         |

# 

# \### Step 4 — Configure API Keys (for AI features)

# 

# Open `process\_ai\_save.php` and `ai\_result\_feedback.php` and replace the placeholder API key with your own:

# 

# ```php

# // process\_ai\_save.php

# $apiKey = "YOUR\_OPENAI\_API\_KEY\_HERE";

# ```

# 

# > ⚠️ \*\*Never commit real API keys to a public repository.\*\* Use environment variables or a `.env` file in production.

# 

# To get an OpenAI API key, sign up at \[platform.openai.com](https://platform.openai.com).

# 

# \### Step 5 — Start the Server and Visit the App

# 

# Start Apache and MySQL (via XAMPP Control Panel or `systemctl`), then open:

# 

# ```

# http://localhost/Online-Exam-System/

# ```

# 

# \---

# 

# \## 📖 Usage Guide

# 

# \### Default Login Credentials

# 

# | Role        | Email                   | Password    |

# |-------------|-------------------------|-------------|

# | Head Admin  | `head@gmail.com`        | `head`      |

# | Teacher 1   | `teacher1@gmail.com`    | `teacher1`  |

# | Teacher 2   | `teacher2@gmail.com`    | `teacher2`  |

# | Teacher 3   | `teacher3@gmail.com`    | `teacher3`  |

# | Student     | Register a new account  | —           |

# 

# > Change these credentials immediately after first login in a production environment.

# 

# \### As a Student

# 

# 1\. Click \*\*SIGN UP\*\* on the landing page and fill in the registration form.

# 2\. Click \*\*SIGN IN\*\* and log in with your email/password.

# 3\. From your dashboard, select any available quiz and click \*\*Start Quiz\*\*.

# 4\. Answer each MCQ and click \*\*Submit Answer\*\* to proceed to the next question.

# 5\. After completion, check your \*\*History\*\* tab for score details.

# 6\. View the \*\*Ranking\*\* tab to compare your score with other students.

# 

# \### As a Teacher

# 

# 1\. Click \*\*TEACHER\*\* on the landing page and log in.

# 2\. Go to \*\*Quiz → Add Quiz\*\*, fill in the quiz metadata, and submit.

# 3\. Add questions one by one using the question form.

# 4\. Alternatively, use \*\*Quiz → 🤖 AI Question Generator\*\* to auto-generate and save questions.

# 5\. Monitor student performance under the \*\*Scores\*\* tab.

# 6\. View per-quiz leaderboards under the \*\*Ranking\*\* tab.

# 

# \### As Head Admin

# 

# 1\. Click \*\*ADMIN\*\* on the landing page and log in.

# 2\. Manage students under the \*\*Users\*\* tab (view, delete).

# 3\. Add or remove teachers under the \*\*Teacher\*\* dropdown.

# 4\. View all feedback submissions under \*\*Feedback\*\*.

# 5\. View system-wide rankings under \*\*Ranking\*\*.

# 

# \---

# 

# \## 🤖 AI Integration

# 

# \### AI Question Generator (`generate\_ai\_questions.php` + `process\_ai\_save.php`)

# 

# Teachers can generate structured MCQ question sets automatically:

# 

# 1\. Navigate to \*\*Quiz → 🤖 AI Question Generator\*\*

# 2\. Enter a \*\*topic\*\* (e.g., `"Database Management System"`) and \*\*question count\*\* (1–10)

# 3\. Click \*\*Generate \& Save Questions\*\*

# 

# \*\*Under the hood:\*\*

# \- A POST request is sent to `process\_ai\_save.php`

# \- It calls the \*\*OpenAI Chat Completions API\*\* (`gpt-3.5-turbo`) with a structured JSON prompt

# \- The response is parsed and each question is inserted into the `questions` table linked to a newly created quiz

# \- The generated questions are displayed on screen with a success confirmation

# 

# \*\*Expected JSON response format from AI:\*\*

# ```json

# \[

# &#x20; {

# &#x20;   "question": "What does SQL stand for?",

# &#x20;   "options": { "A": "Structured Query Language", "B": "Simple Query Language", "C": "Standard Question Language", "D": "None" },

# &#x20;   "answer": "A"

# &#x20; }

# ]

# ```

# 

# \### AI Result Insights (`ai\_result\_feedback.php`)

# 

# After an exam, students can submit their subject and score to receive personalized AI-generated feedback:

# \- Identifies weak areas based on the score

# \- Suggests specific topics to revise

# \- Recommends learning resources

# 

# \---

# 

# \## 📁 Project Structure

# 

# ```

# Online-Exam-System/

# │

# ├── index.php                 # Landing page (home, login/signup modals, map)

# ├── login.php                 # Student login handler

# ├── admin.php                 # Teacher login handler

# ├── head.php                  # Head admin login handler

# ├── sign.php                  # Student registration handler

# ├── signadmin.php             # Teacher registration handler (admin-only)

# ├── logout.php                # Session destroy + redirect

# │

# ├── account.php               # Student dashboard + quiz engine

# ├── dash.php                  # Teacher dashboard

# ├── headdash.php              # Head admin dashboard

# │

# ├── update.php                # Unified write handler (quiz CRUD, scoring, admin ops)

# ├── fetch\_ranking.php         # AJAX JSON endpoint for leaderboard data

# ├── check.php                 # Session/auth check utility

# │

# ├── generate\_ai\_questions.php # AI MCQ generator UI (teacher)

# ├── process\_ai\_save.php       # OpenAI API handler — generates \& saves questions

# ├── process\_ai.php            # SerpAPI handler — search-based question hints

# ├── ai\_result\_feedback.php    # AI-powered post-exam feedback UI

# │

# ├── feed.php                  # Contact/feedback form page

# ├── feed\_submit.php           # Contact form submission handler

# │

# ├── dbConnection.php          # MySQLi database connection

# ├── project1.sql              # Full database schema + seed data

# │

# ├── css/

# │   ├── bootstrap.min.css

# │   ├── bootstrap-theme.min.css

# │   ├── main.css              # Custom styles

# │   └── font.css

# │

# ├── js/

# │   ├── jquery.js

# │   ├── bootstrap.min.js

# │   ├── bootstrap.js

# │   ├── modernizr.js

# │   └── main.js

# │

# ├── fonts/

# │   └── glyphicons-halflings-regular.\*  (eot, svg, ttf, woff, woff2)

# │

# ├── Er diagram.pdf            # Entity-Relationship diagram

# └── LICENSE                   # CC BY-NC-ND 4.0

# ```

# 

# \---

# 

# \## 🔐 Security Notes

# 

# This project was built as an academic prototype. Before deploying to any production or public-facing environment, the following issues \*\*must be addressed\*\*:

# 

# | Issue | Location | Recommended Fix |

# |-------|----------|-----------------|

# | SQL Injection | Multiple files (raw `$\_GET`/`$\_POST` in queries) | Use \*\*PDO prepared statements\*\* throughout |

# | Plain-text passwords | `user` table, `admin` table | Use `password\_hash()` / `password\_verify()` |

# | Reflected XSS | `index.php` `$\_GET\['w']` echoed into `alert()` | Sanitize all output with `htmlspecialchars()` |

# | Hardcoded API keys | `process\_ai\_save.php`, `ai\_result\_feedback.php` | Move to `.env` or server environment variables |

# | Hardcoded admin key | `update.php` `'prasanth123'` | Use a database role-check instead |

# | No CSRF protection | All POST forms | Add CSRF tokens to all state-changing forms |

# | `@` error suppression | Throughout | Remove suppression; use proper null checks |

# 

# \---

# 

# \## 🗺️ Entity-Relationship Overview

# 

# An ER diagram is included in the project as \*\*`Er diagram.pdf`\*\*. At a high level:

# 

# \- A \*\*User\*\* attempts many \*\*Quizzes\*\* → recorded in \*\*History\*\*

# \- A \*\*Quiz\*\* contains many \*\*Questions\*\*

# \- Each \*\*Question\*\* has many \*\*Options\*\*; one option is the correct \*\*Answer\*\*

# \- An \*\*Admin\*\* (teacher) creates many \*\*Quizzes\*\*

# \- \*\*Feedback\*\* is submitted by visitors and managed by the Head admin

# 

# \---

# 

# \## 🚀 Potential Enhancements

# 

# \- \*\*Full-screen proctoring mode\*\* with tab-switch detection

# \- \*\*Timer per question\*\* (not just per quiz)

# \- \*\*Question randomization\*\* to prevent answer sharing

# \- \*\*Bulk CSV/JSON question import\*\* for teachers

# \- \*\*Email notifications\*\* (registration confirmation, result delivery)

# \- \*\*OAuth login\*\* (Google / GitHub)

# \- \*\*REST API layer\*\* for mobile app integration

# \- \*\*Dark mode\*\* UI

# 

# \---

# 

# \## 🤝 Contributing

# 

# Pull requests and issues are welcome. If you're improving this project:

# 

# 1\. Fork the repository

# 2\. Create a feature branch: `git checkout -b feature/your-feature`

# 3\. Commit your changes: `git commit -m 'Add your feature'`

# 4\. Push to the branch: `git push origin feature/your-feature`

# 5\. Open a pull request

# 

# \---

# 

# \## 👨‍💻 Author

# 

# \*\*Tarun Chand\*\*

# 📧 chandtarun1234@gmail.com

# 📍 Bhimtal, Uttarakhand, India

# 🏫 Graphic Era Hill University

# 

# \---

# 

# \## 📜 License

# 

# This project is licensed under the \*\*Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International (CC BY-NC-ND 4.0)\*\* license.

# 

# \- ✅ You may share and redistribute this project with proper attribution

# \- ❌ You may not use it for commercial purposes

# \- ❌ You may not distribute modified versions

# 

# Full license: \[https://creativecommons.org/licenses/by-nc-nd/4.0/](https://creativecommons.org/licenses/by-nc-nd/4.0/)

# 

# \---

# 

# <div align="center">

# 

# Made with ❤️ for modern, paperless education.

# 

# </div>

