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
<img width="948" alt="a" src="https://user-images.githubusercontent.com/30838747/236216061-f562013b-050e-42e3-94cc-abdc91dbbd7d.png">
<img width="915" alt="b" src="https://user-images.githubusercontent.com/30838747/236216076-6f26dc07-d06b-4a0a-9793-9a266be53449.png">
<img width="915" alt="c" src="https://user-images.githubusercontent.com/30838747/236216177-89072824-73df-4b8f-91a2-a7fe0a89d493.png">
<img width="915" alt="d" src="https://user-images.githubusercontent.com/30838747/236216537-464497c9-5f97-46b2-9dcc-8658e193dd0e.png">
