# Базове SEO ServiceFlow

Реалізовано в `serviceflow-core/seo.php`: description, Open Graph, Twitter card, WebPage JSON-LD, noindex для службових екранів, виключення їх та WooCommerce cart/checkout/myaccount зі стандартного XML sitemap. User sitemap вимкнено. Canonical формує WordPress core; дубль не додається. За Yoast / Rank Math / AIOSEO власні метатеги не дублюються.

WordPress 5.5+ формує `/wp-sitemap.xml` та додає його в віртуальний `/robots.txt`, коли сайт публічний. Код не примусово знімає заборону індексації staging. Не створюйте фізичний robots.txt, який перекриє віртуальний. Не блокуйте CSS/JS або noindex-сторінки від crawling: пошуковик має прочитати noindex.

Після встановлення: створіть реальні опубліковані сторінки з короткими URL, встановіть головну, увімкніть постійні посилання та перевірте «Налаштування → Читання → Видимість для пошукових систем». Вкажіть справжній домен у WordPress Address / Site Address. Власний опис можна задавати через custom field `_sf_meta_description`; для SEO-плагіну використовуйте його інтерфейс.

Публічне портфоліо-демо залишається `noindex,follow`: hash-екрани не є окремими SEO-URL та не додаються в sitemap. SEO-кейс портфоліо індексується окремо. Не заявляється фактичний запуск WordPress або індексація Google.

Обмеження: контент shortcode поки рендериться JavaScript. Для production SEO потрібен серверний контент послуг/статей у WordPress/Elementor та звичайні міжсторінкові посилання. Elementor-шаблон із одним shortcode сам по собі цього не забезпечує. Не додавайте AutoRepair schema з вигаданими адресою, рейтингами чи годинами роботи.

Перевірка після запуску: HTTP 200 для `/wp-sitemap.xml` і `/robots.txt`; абсолютні URL правильного домену; відсутність account/cart/checkout/thanks/analytics у sitemap; один canonical без hash/query; унікальні title/description; noindex службових сторінок; sitemap подати у Search Console після підтвердження власності. У цьому середовищі WordPress/PHP runtime не доступний — потрібен staging E2E.
