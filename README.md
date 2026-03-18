# btcmi

Laravel app for Bitcoin Network Michigan: price history from Binance, Coinbase, and Gemini, plus meetups, news, and contact.

## Requirements

- PHP 8.2+
- Composer
- Node/npm (for frontend assets)

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure .env (DB, etc.)
php artisan migrate
npm install && npm run dev
php artisan serve
```

Optional: schedule price fetches via cron (`* * * * * php /path/to/artisan schedule:run`).

## Architecture

Exchange price clients (Binance, Coinbase, Gemini) use constructor-injected HTTP and repositories, with unit tests that fake HTTP and mock persistence. Endpoints live in `config/services.php`; bindings and clients are registered in `ExchangeServiceProvider`.

## License

MIT
