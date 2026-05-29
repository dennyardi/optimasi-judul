# AI YouTube SEO Workspace

Single-user Laravel dashboard untuk generate optimasi SEO YouTube dengan OpenAI API.

## Stack

- Laravel 13
- Blade Template
- TailwindCSS 4
- AlpineJS
- MySQL
- OpenAI Responses API dengan structured JSON output

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

Atur koneksi MySQL di `.env`, lalu buka halaman Settings untuk menyimpan OpenAI API key, model AI aktif, dan channel context.

## Admin Login

Set admin credentials di `.env`, lalu jalankan seeder:

```bash
ADMIN_NAME="Administrator"
ADMIN_USERNAME=admin
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=change-this-password
php artisan db:seed --force
```

## OpenAI Models

Model yang tersedia di Settings:

- `gpt-4.1`
- `gpt-4.1-mini`
- `gpt-4o`
- `gpt-4o-mini`
- `gpt-5`
- `gpt-5-mini`
- `gpt-5-nano`

## Fitur

- Dashboard statistik dan status API
- Generate SEO YouTube dengan loading state dan output cards
- Copy-to-clipboard untuk title, description, 10 meta tags terbaik, hashtags, dan pin comment
- History list, detail, dan delete
- Settings OpenAI dan channel prompt context
- Responsive sidebar, dark mode toggle, toast notification
