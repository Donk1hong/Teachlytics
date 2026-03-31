# 🚀 Цифровая метрика успеха групп

**Цифровая метрика успеха** — это высокотехнологичная платформа для автоматизированного мониторинга достижений и дисциплины студенческих групп. Система предоставляет прозрачную аналитику в реальном времени, помогая кураторам и администрации эффективно управлять образовательным процессом.

![UI Style](https://img.shields.io/badge/UI-Apple--Style-blueviolet)
![Tech Stack](https://img.shields.io/badge/Stack-Laravel%2011%20|%20Vue%203%20|%20Inertia-orange)
![Realtime](https://img.shields.io/badge/Realtime-Reverb-blue)

---

## ✨ Основные возможности

* **Интерактивный лидерборд:** Объективный расчет баллов групп на основе настраиваемых критериев.
* **Система тикетов (Поддержка):** Полноценный цикл обращений пользователей с уведомлениями и ответами администрации.
* **Умный календарь:** Визуализация событий, дедлайнов и собраний с кастомными модальными окнами.
* **Чат в реальном времени:** Мгновенный обмен сообщениями между пользователями и администрацией.
* **Премиальный UI/UX:** Glassmorphism-дизайн, поддержка темной темы и кастомные скроллбары.

---

## 🛠 Технологический стек

* **Backend:** Laravel 11 (PHP 8.2+)
* **Frontend:** Vue 3 (Composition API), Inertia.js
* **Real-time:** Laravel Reverb (WebSockets)
* **Styling:** Tailwind CSS, PrimeVue
* **Environment:** Docker (Laravel Sail)
* **Database:** PostgreSQL

---

## 📦 Установка и запуск

1. **Клонирование репозитория:**
```bash
git clone [https://github.com/donk1hong/curator-rating.git](https://github.com/donk1hong/curator-rating.git)
cd curator-rating
```

2. **Установка зависимостей:**
```bash 
composer install
npm install
```

3. **Настройка окружения:**
```bash 
cp .env.example .env
php artisan key:generate
```

4. **Запуск через Docker (Laravel Sail):**
```bash 
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

---

##  📡 Realtime & Фоновые задачи (Reverb + Queues)

1. **Запуск обработчика очередей (Queue Worker):**

```bash 
php artisan reverb:start
```

2. **Запуск обработчика очередей (Queue Worker):**

```bash 
php artisan queue:work
```

3. **Настройка .env:**

```bash 
BROADCAST\_CONNECTION=reverb
QUEUE\_CONNECTION=database
REVERB\_APP\_ID=ваша\_id
REVERB\_APP\_KEY=ваш\_ключ
REVERB\_APP\_SECRET=ваш\_секрет
REVERB\_HOST="localhost"
REVERB\_PORT=8080
REVERB\_SCHEME=http
```

---

**Разработано с акцентом на детали и производительность. Author: Ruslan (donk1hong)**




