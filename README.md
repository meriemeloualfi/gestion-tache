# Gestion des Tâches

Task management web application built with PHP and MySQL, with separate areas
for administrators and users.

## Features

- Authentication (login / logout)
- Admin area (`admin/`)
- User area (`user/`)
- [Create, edit, delete and assign tasks]
- [Task status tracking: to do, in progress, done]
- [User management by the admin]

## Tech Stack

| Category | Tools |
|----------|-------|
| Back-end | PHP |
| Database | MySQL |
| Front-end | HTML, CSS, JavaScript |
| Local server | [XAMPP / WAMP] |

## Project Structure

```
gestion-tache/
├── admin/                # Administrator pages
├── auth/                 # Authentication (login, logout)
├── user/                 # User pages
├── classes/              # PHP classes
├── includes/             # Shared PHP files
├── css/                  # Stylesheets
├── js/                   # JavaScript files
├── config.php            # Project configuration (BASE_URL)
├── gestion_taches.sql    # Database script
├── index.php             # Entry point
└── README.md
```

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL / MariaDB
- A local server such as XAMPP or WAMP

### Installation

1. Clone the repository into your server's web folder (`htdocs` for XAMPP), and name the folder `gestion_tache`:
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/meriemeloualfi/gestion-tache.git gestion_tache
   ```
   The folder name must match `BASE_URL` in `config.php` (`/gestion_tache/`).

2. Start Apache and MySQL from the XAMPP control panel.

3. Create the database:
   - Open http://localhost/phpmyadmin
   - Create a database (for example `gestion_taches`)
   - Select it, click **Import**, and choose `gestion_taches.sql`

4. Check the database connection settings in the PHP file that connects to MySQL
   (host, user, password, database name).

5. Open http://localhost/gestion_tache/ in your browser.

## Default Accounts

[If `gestion_taches.sql` creates default accounts, list them here, for example:
admin / password]

## Author

**Meriem El Oualfi**
GitHub: [@meriemeloualfi](https://github.com/meriemeloualfi)
