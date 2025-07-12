# Routine of Raskot Banda
A modern news and media portal for Raskot, built with PHP, MySQL, and TailwindCSS.

## Features
- Professional homepage with news cards, categories, and trending/top news
- Admin dashboard for posting news/articles and uploading tracks
- News/article management: post, list, and delete
- Track management: upload, list, and delete audio tracks
- Responsive, modern UI with dark mode support
- Category-based navigation and filtering
- Image upload for news/articles
- Analytics summary for admin (total news, total tracks)

## Folder Structure
```
├── admin/
│   ├── dashboard.php         # Admin dashboard (news & track management)
│   ├── create_admin.php      # Create new admin user
│   ├── login.php             # Admin login
│   ├── logout.php            # Admin logout
│   └── index.php             # Admin landing
├── config/
│   └── config.php            # Database connection
├── database/
│   └── news.sql              # Database schema
├── uploads/                  # Uploaded images and tracks
├── article.php               # News/article detail page
├── category.php              # Category listing page
├── header.php                # Shared navbar/header
├── index.php                 # Homepage
└── README.md                 # This file
```

## Setup Instructions
1. **Clone or copy the project files to your web server (e.g., XAMPP htdocs).**
2. **Import the database:**
   - Open `database/news.sql` in phpMyAdmin or MySQL and import it to create the required tables.
3. **Configure database connection:**
   - Edit `config/config.php` and set your MySQL credentials.
4. **Set permissions:**
   - Ensure the `uploads/` folder is writable by the web server for image and track uploads.
5. **Access the site:**
   - Visit `index.php` for the homepage.
   - Visit `admin/login.php` to log in as admin (default credentials are in your database).

## Usage
- **Post News/Articles:** Use the admin dashboard to post news with title, content, category, and image.
- **Upload Tracks:** Upload audio tracks (MP3/WAV) from the dashboard.
- **Delete News/Tracks:** Use the dashboard to manage and delete content.
- **Browse by Category:** Use the homepage or category pages to filter news.

## Technologies Used
- PHP 7+
- MySQL
- TailwindCSS (via CDN)
- HTML5/CSS3

## Credits
Developed by abhayabikramshahi and contributors.

---

For any issues or contributions, please open an issue or pull request.
# Routine of raskot banda