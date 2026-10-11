# ServiceFlow

Сайт автосервісу: попередня оцінка через квіз, онлайн-запис, каталог пакетів і checkout.

**Публічне демо:** https://dm-studio-production.up.railway.app/demos/serviceflow/

**Кейс:** https://dm-studio-production.up.railway.app/cases/serviceflow/

## Що є в репозиторії

- `demo/`: інтерактивна портфоліо-версія без зовнішньої передачі даних або реальних оплат. Квіз, фільтри, картки послуг, пакети з опціями, кошик, симуляція checkout (success/failure), запис, демокабінет, блог, сценарії, політики.
- `wp-content/themes/serviceflow-child`: дочірня тема Hello Elementor.
- `wp-content/plugins/serviceflow-core`: WordPress-плагін. REST-заявки, серверна калькуляція квізу, атомарна резервація слотів, ідемпотентний lead_id, адмінський список/фільтри/CSV/повтор доставки, outbox і WP-Cron, інтеграція з WooCommerce та тестовий gateway.
- `elementor/`: 12 імпортованих сторінкових шаблонів (shortcode application), окрема native Elementor hero-секція (heading/text/button), design tokens. Інтерактивна частина працює як shortcode, а не як сотні незалежних Elementor widgets.
- `integrations/`: схема контракту для n8n, webhook та аналітики.
- `docs/`: актуальна матриця всіх 163 задач backlog, встановлення, QA й обмеження.

## Локальна перевірка

```bash
npm run prepare
npm run check
npm test
python -m http.server 8080 --directory demo
```

Демо не потребує npm-пакетів. `prepare` синхронізує assets у WordPress-плагін і створює Elementor JSON. JavaScript pricing-модель має автоматичні тести всіх гілок.

## Встановлення WordPress

1. На staging встановити WordPress, Hello Elementor, Elementor та WooCommerce. Elementor Pro не включений і для наших shortcode/templates не потрібний.
2. Скопіювати папки з `wp-content/` у відповідні папки WordPress. Увімкнути ServiceFlow Child та ServiceFlow Core.
3. В адмінці **ServiceFlow → Створити відсутні демосторінки та пакети**. Дія не перезаписує існуючі сторінки. Встановити головною сторінку `sf-home` через Settings → Reading. Встановити часовий пояс `Europe/Kyiv`.
4. За потреби імпортувати `elementor/*.json` через Elementor → Templates. Усі application templates монтують той самий UI з потрібним маршрутом. Native hero можна редагувати окремими Elementor widgets.
5. У WooCommerce перевірити валюту UAH, сторінки cart/checkout/myaccount. Для демо-шлюзу потрібен **classic shortcode checkout**, не Checkout Block. Демо gateway спочатку **вимкнений**. Вмикати лише на staging. Він створює `on-hold`, не `paid`, не передає payment data провайдеру.
6. Увімкнути реєстрацію у WooCommerce Accounts & Privacy, якщо потрібна реєстрація через стандартний кабінет. Серверні заявки прив’язуються тільки до поточного WordPress user_id; гостьові заявки не можна публічно читати.
7. Перевірити сценарії з `docs/QA.md` до публікації.

## Важливі межі перевірки

WordPress-хостинг не надано: установка в живий WordPress/Elementor/WooCommerce, доставлення зовнішніх повідомлень, імпорт Elementor у реальний редактор, SMTP, provider sandbox, GA4 DebugView, GSC і PageSpeed **не підтверджені**. PHP перевірено синтаксично; це не заміна E2E в WordPress.

Демо checkout — браузерна симуляція. WP demo gateway — локальний тестовий gateway. Жоден не є LiqPay/Stripe sandbox. Секрети не включені, відгуки/рейтинг/реальна адреса/результати продажів не вигадані. Деталі у `docs/BACKLOG.md`.
