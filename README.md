# Рейтинг учебных групп

Веб-приложение для учёта достижений групп и работы кураторов. В нём можно просматривать рейтинг, общаться, отправлять обращения и следить за событиями календаря.

## Возможности

- Рейтинг групп с настраиваемыми критериями оценки.
- Обращения в поддержку с ответами администрации.
- Календарь событий, встреч и дедлайнов.
- Чат в реальном времени.
- Светлая и тёмная темы оформления.

## Технологии

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Vue 3, Inertia.js
- **Стили:** Tailwind CSS, PrimeVue
- **База данных:** PostgreSQL
- **Realtime:** Laravel Reverb
- **Среда разработки:** Docker, Laravel Sail

## Запуск

Нужны Docker и Composer.

```bash
git clone https://github.com/donk1hong/curator-rating.git
cd curator-rating

composer install
cp .env.example .env

sail up -d
sail artisan key:generate
sail artisan migrate

sail npm install
sail npm run dev

Убедитесь, что в .env также заданы параметры REVERB_APP_ID, REVERB_APP_KEY, REVERB_APP_SECRET, REVERB_HOST, REVERB_PORT и REVERB_SCHEME.
Запустите Reverb и обработчик очереди в отдельных терминалах:
sail artisan reverb:start
sail artisan queue:work
