# wz-accounts
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

Set up SQLITE database with the following tables & columns:
* `user_accounts` table with `username`, `password`, `balance_date` columns
* `money_accounts` table with `id`, `username`, `account` columns
* `transactions` table with `id`, `username`, `item`, `type`, `amount`, `account`, `transaction_date` columns

Manually insert new username & password hash to `user_accounts` table with PHP's inbuilt `password_hash()` function.

Update `config.php` with SQLITE file info.

### Screenshots
<img width="948" alt="a" src="https://user-images.githubusercontent.com/30838747/236214757-04292810-1404-4bd9-bbf4-dae259e3cd8c.png">
<img width="911" alt="b" src="https://user-images.githubusercontent.com/30838747/236214776-277203e2-03ba-4e0d-91ea-f9a7efb029d4.png">
<img width="915" alt="c" src="https://user-images.githubusercontent.com/30838747/236214783-6626c322-2c03-49e4-82bb-97505d26d568.png">
<img width="911" alt="d" src="https://user-images.githubusercontent.com/30838747/236214796-07e6208b-1013-46fb-afb2-39ea83bdceb7.png">
