# BookMyClass

A web-based classroom booking and management system.

## Technologies Used
- HTML
- CSS
- JavaScript
- PHP
- MySQL

## Setup Instructions

1. **Prerequisites**: Ensure you have a local web server environment installed (like XAMPP, WAMP, or LAMP) which includes Apache, PHP, and MySQL.
2. **Clone the repository**: Place the project files into your web server's root directory (e.g., `htdocs` for XAMPP, `www` for WAMP).
3. **Database Setup**: 
   - Create a new MySQL database named `mydb`.
   - Import the provided SQL schema (`schema.sql` or run the setup PHP scripts) to create the necessary tables.
4. **Configuration**: 
   - Update `config.php` with your database credentials if they differ from the defaults (typically `root` with a blank password for local environments).
5. **Run the Application**: 
   - Start Apache and MySQL from your server control panel.
   - Open your web browser and navigate to `http://localhost/bookmyclass`.

## Docker Run
- Start services: `docker-compose up -d --build`
- App URL: `http://localhost:8081`

## Smoke Test
- Run: `bash scripts/smoke_test.sh`
- The script checks:
  - Docker services are up
  - MySQL health is `healthy`
  - Required tables exist in `mydb`
  - Web app responds on `http://localhost:8081`

## Required Packages
The system requires standard PHP and MySQL extensions to function correctly. A list of expected server dependencies can be found in `requirements.txt`.
