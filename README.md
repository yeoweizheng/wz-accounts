### Introduction
This is a simple personal expenses & income tracking website with the following features:
* User authentication
* Add, update, delete transactions
* Multiple transaction accounts per user
* View daily transactions
* Search & filter transactions by start & end dates
* Export transactions to CSV
### Installation
Clone repository, point web server to `public/` folder.

Set up SQL database with the following tables & columns:
* `user_accounts` table with `username`, `password`, `balance_date` columns
* `money_accounts` table with `id`, `username`, `account` columns
* `transactions` table with `id`, `username`, `item`, `type`, `amount`, `account`, `date` columns

Manually insert new username & password hash to `user_accounts` table with PHP's inbuilt `password_hash()` function.

Update `config.php` with SQL authentication info.

Log in and enjoy!
