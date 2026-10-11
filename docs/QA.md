# Перевірка ServiceFlow

## Автоматично
`npm test`: всі 54 комбінації problem/car/urgency, порядок цін, модифікатори, дубль опцій, телефон/рік/дата, структура Elementor JSON. `npm run check`: синтаксис браузерного JS. PHP parsed with PHP 8.1 grammar. Published demo tested separately in browser (see verification section below).

## E2E — демо
- E2E-01: Квіз → SUV → ходова → стук+вібрація → 1–3 дні → модель → рік → результат. Назад не губить відповідь; reload зберігає крок. Lead форму заповнити тільки тестовими даними. В кабінеті — одна заявка.
- E2E-02: Standard → SUV + кондиціонер → 6000 грн → cart → кількість 2 = 12000 грн. Failure не чистить cart; success створює один order і чистить cart. Без реальної оплати.
- E2E-03: booking на найближчий робочий день → збереження → повтор тієї самої date/time відхиляється. Sunday/минулу дату відхилити.
- Services: пошук і фільтр, detail CTA, FAQ, keyboard focus. Account: профіль, авто, заявки/замовлення, очистити session.
- Analytics: /#/analytics — лише дозволені поля, без name/phone/email/comment/vehicle model.

## E2E — WordPress (очікує staging)
- Активація creates tables; повторна активація не втрачає дані.
- Nonce, origin, phone/year/consent/source/service validation.
- 2 одночасні booking requests на один slot: тільки одна успішна.
- Повторний lead_id: не створює повторного запису і не повторює delivery.
- my-leads/profile: гість 401/403; user A не бачить записів user B.
- n8n HTTP 500: pending → failed → retry; без дубля Sheets. Вимкнений endpoint: not_configured. Manual retry: nonce+manage_options.
- Native WooCommerce add-to-cart recalculates server-side; malicious client price ignored. Classic checkout failure leaves cart; success on-hold. Email only after SMTP delivery test.
- Elementor imports 12 templates + native hero. Responsive settings inspect in installed editor.
- Chrome/Safari/Firefox/Edge; 360/390/768/1024/1280/1440; screen reader/keyboard; contrast; Lighthouse/PSI. Не заявляти невиконані перевірки.

## Стан
Модельні тести та PHP/JS syntax passed. WordPress E2E, cross-browser, GA4 DebugView, GSC, provider sandbox і delivery потребують доступу. Опубліковане демо перевірено 2026-10-11 у Chrome: SUV → ходова → стук+вібрація → 1–3 дні дає 1050–4750 грн; reload зберігає крок; демозаявка відображається в кабінеті. Standard + SUV + кондиціонер = 6000 грн, кількість 2 = 12000 грн. Failure checkout залишає кошик, success створює тестове замовлення. Запис на 2026-10-12 09:00 збережено; повторний слот відхилено. Кейс і демо відкриваються на Railway. Виправлено MIME для .mjs та версіоновано URL модулів після кешованої помилкової відповіді. Mobile/cross-browser та WordPress runtime не перевірені.


## Додатковий аудит опублікованого демо — 2026-10-11

Chrome, desktop (viewport близько 1363 px); використовувалися лише вигадані дані QA. Реальних оплат, зовнішніх заявок або бронювань не виконувалося.

- Пройдено головну, каталог, 8 послуг, 3 пакети, сторінки запису, про сервіс, контакти, кабінет, кошик, checkout, thanks, 3 сценарії, 2 статті, блог, політики й локальну аналітику. У перевірених сторінках немає порожнього main, битих завантажених зображень або горизонтального переповнення на desktop.
- Пошук «гальм», пошук без результатів та категорія «Ремонт» працюють (3 результати).
- Планове ТО пропускає симптоми; повернення зберігає відповідь; без вибору проблеми далі не переходить; рік 1800 відхилено; 2020 дає результат 1800–4200 грн.
- Короткий телефон, неділя 2026-10-18, минула дата 2026-10-10, відсутність згоди відхиляються. Демозаявка зберігається та з’являється в кабінеті.
- Basic: 2500 грн; кількість 2 = 5000 грн; кількість 0 не приймається. Failure не підтверджує замовлення; success підтверджує тестове замовлення й очищає cart. Видалення товару дає порожній кошик і суму 0.
- У локальному журналі подій немає name/phone/email/comment; текст «<b>QA авто</b>» у кабінеті екранується.
- Знайдено й виправлено: skip-link #app помилково активував маршрут notFound; у детальних сторінках document.title був «ServiceFlow — ServiceFlow». Skip-link тепер фокусує main без зміни маршруту, заголовок вкладки відповідає сторінці.
- Модельні тести 5/5 пройшли; JS syntax пройшов.

Реальні мобільні пристрої, Safari/Firefox/Edge, Lighthouse/PSI, WordPress runtime і зовнішні інтеграції в цьому аудиті не перевірені. Цей звіт не підтверджує production-платежі або встановлення WordPress.

Повторна перевірка після успішного Railway deploy: на #/article/noise Tab → Enter залишає URL незмінним, activeElement.id = app, document.title = «Стук у ходовій: як підготуватися до діагностики — ServiceFlow». FAQ відкриває відповідь, зміст статті не змінює маршрут, невідома послуга показує сторінку «Не знайдено». Виправлення підтверджені у live-демо.
