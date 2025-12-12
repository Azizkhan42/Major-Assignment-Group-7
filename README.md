# Major-Assignment-Group-7
Personal Budget Dashboard - Major Assignment for Software Construction &amp; Development (City University Peshawar)

# Personal Budget Dashboard

A comprehensive web-based financial management system built with PHP, MySQL, Bootstrap, and Chart.js. Manage your income, expenses, and create detailed financial reports.

## Features

### ✅ Authentication & Authorization

- User registration and login
- Password encryption with bcrypt
- Two user roles: Admin and User
- Session-based authentication
- Secure logout functionality

### 💰 Financial Management

- **Income Tracking**: Add, edit, delete income entries with categories and sources
- **Expense Tracking**: Comprehensive expense management with detailed categorization
- **Transaction History**: View all transactions with filtering and search capabilities

### 📊 Dashboard & Analytics

- Real-time financial summary (income, expenses, savings)
- Interactive charts and graphs:
  - Pie charts for expense/income by category
  - Visual budget overview
- Recent transactions display
- Quick action buttons

### 📈 Reports & Export

- Monthly and yearly financial reports
- Category-wise breakdown
- Export to CSV and PDF formats
- Transaction filtering by date range and category

### 🏷️ Category Management

- Create custom expense categories
- Create custom income categories
- Edit and delete categories
- Pre-loaded default categories

### 👤 User Profile Management

- Update name and email
- Change password securely
- View personal statistics
- All-time financial summary

### 🎨 User Interface

- Responsive design (mobile, tablet, desktop)
- Dark-mode ready CSS
- Clean and intuitive interface
- Collapsible sidebar navigation
- Bootstrap 5 framework

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP, WAMP, or similar local server
- Modern web browser

## Installation

### Step 1: Download & Setup

1. Extract the project to your XAMPP htdocs folder:
   ```
   C:\xampp\htdocs\Personal-budget-dashboard\
   ```

### Step 2: Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin/)
2. Create a new database or import the SQL file:

   - Go to `Import` tab
   - Select `sql/database.sql`
   - Click `Go`

3. Or manually import:
   ```sql
   mysql -u root -p < sql/database.sql
   ```

### Step 3: Configure Database Connection

Edit `config/database.php` and update credentials if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Leave empty if no password
define('DB_NAME', 'personal_budget_dashboard');
```

### Step 4: Access the Application

1. Start XAMPP (Apache and MySQL)
2. Open browser and navigate to:
   ```
   http://localhost/Personal-budget-dashboard/
   ```

### Step 5: First Login

Use demo credentials:

- **Email**: user@budgetdashboard.com
- **Password**: Create your own during registration

Or register a new account.

## Project Structure

```
personal-budget-dashboard/
├── assets/
│   ├── css/
│   │   └── style.css              # Main stylesheet
│   ├── js/
│   │   └── script.js              # Client-side validation & interactions
│   └── images/                    # Logo and icons
├── config/
│   └── database.php               # Database configuration
├── controllers/
│   ├── authController.php         # Authentication logic
│   ├── userController.php         # User profile management
│   ├── incomeController.php       # Income CRUD operations
│   ├── expenseController.php      # Expense CRUD operations
│   ├── categoryController.php     # Category management
│   └── reportController.php       # Report generation
├── helpers/
│   ├── session.php                # Session management
│   ├── validation.php             # Server-side validation
│   └── utils.php                  # Helper functions
├── includes/
│   ├── header.php                 # Page header with CSS/JS includes
│   ├── footer.php                 # Page footer
│   └── sidebar.php                # Navigation sidebar
├── modules/
│   ├── dashboard.php              # Main dashboard
│   ├── transactions.php           # Income & expense management
│   ├── categories.php             # Category management
│   ├── reports.php                # Financial reports
│   └── profile.php                # User profile
├── sql/
│   └── database.sql               # Database schema
├── index.php                      # Login page
├── register.php                   # Registration page
├── logout.php                     # Logout handler
└── README.md                      # This file
```

## Usage Guide

### Dashboard

- View financial summary at a glance
- See recent transactions
- Quick access to all modules
- Visual charts for expense and income analysis

### Transactions

- **Add Income**: Record new income with source and category
- **Add Expense**: Record new expense with category and description
- **Edit/Delete**: Modify or remove transactions
- **Filter**: View transactions by date range or category

### Categories

- Create new expense and income categories
- Customize category descriptions
- Manage category hierarchy
- Delete unused categories

### Reports

- Generate monthly or yearly reports
- View category-wise breakdown
- Export data to CSV format
- Track financial trends

### Profile

- Update personal information
- Change password securely
- View comprehensive statistics
- Account settings

## Security Features

✅ **Password Hashing**: Bcrypt encryption for all passwords
✅ **SQL Injection Prevention**: Prepared statements with parameterized queries
✅ **Session Security**: Secure session-based authentication
✅ **Input Validation**: Server-side and client-side validation
✅ **Authorization**: Role-based access control
✅ **CSRF Protection**: Token-based protection framework ready

## Database Schema

### Users Table

- User authentication and profile information
- Two roles: admin, user

### Income/Expense Tables

- Transaction records with timestamps
- Category associations
- User ownership

### Category Tables

- Separate tables for income and expense categories
- User-specific categories
- Description and metadata

### Budget Limits Table

- Budget tracking per category
- Weekly and monthly periods
- Spending alerts

## API-like Endpoints

All modules support GET and POST requests for CRUD operations:

- `/modules/transactions.php` - View, add, edit, delete transactions
- `/modules/categories.php` - Manage categories
- `/modules/reports.php` - Generate and export reports
- `/modules/profile.php` - User settings

## Features Coming Soon

🔄 Budget alerts and notifications
📱 Mobile app version
🌙 Dark mode toggle
🔔 Email notifications for budget overruns
💳 Multi-currency support
📊 Advanced analytics and forecasting
🤖 AI-powered spending insights

## Troubleshooting

### Database Connection Error

- Ensure MySQL is running
- Check database credentials in `config/database.php`
- Verify database exists

### Login Issues

- Clear browser cache and cookies
- Verify user exists in database
- Check password is correct

### File Upload Issues

- Ensure proper permissions on project folder
- Check XAMPP settings for file upload limits

### Chart Not Displaying

- Ensure Chart.js CDN is accessible
- Check browser console for errors
- Verify data is available

## Performance Tips

1. **Regular Backups**: Backup your database regularly
2. **Data Cleanup**: Archive old transactions periodically
3. **Index Optimization**: Ensure database indexes are optimized
4. **Cache**: Browser caching is enabled for static assets

## Support & Credits

Built with:

- **PHP 7.4+** - Server-side logic
- **MySQL** - Database management
- **Bootstrap 5** - UI framework
- **Chart.js** - Data visualization
- **Font Awesome** - Icons

## License

This project is open source and available for personal and educational use.

## Contributing

To contribute improvements:

1. Test thoroughly
2. Maintain code quality
3. Follow existing patterns
4. Submit feedback

## Contact & Support

For issues or questions, please refer to the documentation or create an issue.

---

**Happy budgeting! 💰**

Last Updated: December 2024

