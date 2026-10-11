# Встановлення, staging та rollback

## Портфоліо
`demo/` копіюється в `dm-studio/public/demos/serviceflow/`. Портфоліо збирається Vite + prerender і Railway публікує main. ServiceFlow не потребує окремого Railway service або бази для демо.

## WordPress
Встановлення описане в README. Перед активацією зробіть backup файлів і SQL та протестуйте на staging. Встановіть timezone Europe/Kyiv. Увімкніть WooCommerce classic checkout, перевірте consent і тестовий gateway. Реальні способи оплати залишіть вимкненими. Коректність імпорту Elementor потребує перевірки в установленому редакторі.

## Секрети
Тільки серверні конфігураційні значення, не комітити:
```php
// wp-config.php, до require_once ABSPATH . 'wp-settings.php'
define('SF_WEBHOOK_URL', 'https://YOUR-N8N-HOST/webhook/serviceflow');
define('SF_WEBHOOK_SECRET', 'GENERATE-A-LONG-RANDOM-SECRET');
```
Це placeholders, не готові облікові дані. Токен Telegram і Google OAuth зберігаються в n8n credentials. WP outbound webhook використовує HTTPS, safe HTTP API, secret header та Idempotency-Key. Після додавання налаштувань повторіть `not_configured` заявки вручну в адмінці.

## Черга
Новий lead спершу записується в WordPress. WP-Cron виконує delivery. До 4 спроб із наростаючою затримкою; HTTP 2xx означає підтвердження endpoint. n8n має повертати 2xx лише після успішних кроків. `delivery`, `attempts` — технічні статуси без повідомлень із PII. Ручний retry можливий адміністратору. У production запускайте WP-Cron реальним scheduler.

## Персональні дані
Затвердіть політику, строк зберігання та процедуру видалення перед запуском. Текст демо не є юридичною політикою реального сервісу. CSV доступний лише manage_options + nonce; значення, що починаються з формульних символів, екрануються. Гостьові записи не віддаються /my-leads. Зарезервовані слоти потребують операційного процесу підтвердження/скасування. Поточна конфігурація моделює одну сервісну лінію (одне авто в слот).

## Rollback
1. Зафіксувати останній перевірений commit і backup SQL.
2. Деактивувати ServiceFlow Core — cron очищується, записи не видаляються.
3. Повернути попередні файли теми/плагіна, відновити SQL лише якщо необхідно, за погодженим планом.
4. Перевірити /health портфоліо та сторінки WP; перевірити одну тестову заявку й тестовий checkout.
5. Не відкочувати базу без оцінки нових лідів після backup.

## Що потребує доступу
WordPress staging/live, SMTP, n8n, Telegram bot/chat, Google Sheets OAuth, GTM/GA4/GSC, provider sandbox. Реальні інтеграції й метрики не заявляються до read-back та E2E.
