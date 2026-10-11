# WordPress → n8n → Google Sheets / Telegram / email

Webhook body: `{lead_id,source,service,name,phone,email,comment,consent,consent_at,utm,answers?,result?,date?,time?}`.
Headers: `X-ServiceFlow-Secret`, `Idempotency-Key: <lead_id>`. Secret перевіряється через n8n Header Auth credential. Не приймати webhook без auth. Не комітити export credentials або tokens.

## Workflow, який потрібно налаштувати
1. Webhook POST `/serviceflow`, authentication Header Auth.
2. Google Sheets **Append or Update Row**, matching column `lead_id`; spreadsheet/table ID із власного акаунта. Поля відповідають Lead model, headers додаються один раз. Це upsert, не звичайний append.
3. За delivery ledger, якщо `telegram_sent_at` порожній, надіслати Telegram повідомлення через credential; позначити timestamp тільки після підтвердження. Аналогічно для SMTP/email.
4. Respond to Webhook 200 лише після підтверджених required actions; error branch повертає 5xx для WP retry. Provider errors приховувати від користувача.
5. n8n concurrency 1 для цього workflow або окремий transactional ledger із UNIQUE lead_id. Sheets upsert не є гарантією distributed exactly-once. Telegram не має idempotency key: вікно між send і ledger update може спричинити дубль; описати й моніторити.

Це специфікація для підключення. Реального n8n execution/Telegram/Sheets/email не було, бо credentials не надано. Схема не використовується як доказ фактичної доставки.

## dataLayer
Events: view_service, click_phone, click_messenger, quiz_start, quiz_step, quiz_complete, generate_lead, view_item, add_to_cart, begin_checkout, purchase.
Параметри whitelist задані в `demo/assets/app.mjs`. PII у GA4 не передавати. У GTM створити Custom Event triggers і GA4 tags, Measurement ID/Consent Mode налаштувати у своєму акаунті. generate_lead та purchase — key events. Демо purchase є симуляцією; не змішувати із реальною аналітикою продажів.

GSC: підтвердження власності хостингу/домену, sitemap native WordPress, демо noindex. JSON-LD не має fake ratings, address або sales.

## Імпортований стартовий workflow
`n8n-workflow.json` — неактивний стартовий шаблон: authenticated webhook → Sheets upsert → Telegram → response. Замініть CONFIGURE_* і перевірте версію nodes у своєму n8n. Email і delivery ledger описані вище й потребують налаштування. Без ledger повторний успішний Telegram send може дублювати повідомлення, тому перед production додайте його. Шаблон не активувався і не виконувався.
