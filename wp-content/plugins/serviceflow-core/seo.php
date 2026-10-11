<?php
/** Basic SEO for published ServiceFlow pages; WordPress owns XML sitemap/robots. */
if (!defined('ABSPATH')) exit;
function sf_seo_route($post = null) {
    $post = $post ?: get_post();
    if (!$post || !has_shortcode($post->post_content, 'serviceflow_app')) return null;
    if (preg_match('/\[serviceflow_app\b[^\]]*\broute=["\']([^"\']*)["\']/', $post->post_content, $m)) return $m[1];
    return '';
}
function sf_seo_private($post = null) {
    $route = sf_seo_route($post);
    return $route !== null && in_array(explode('/', $route)[0], ['account','cart','checkout','thanks','analytics'], true);
}
function sf_seo_plugin_active() {
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION');
}
function sf_seo_description($post) {
    $custom = trim((string)get_post_meta($post->ID, '_sf_meta_description', true));
    if ($custom) return wp_strip_all_tags($custom);
    $descriptions = [
        '' => 'ServiceFlow — демонстрація сайту автосервісу: квіз із попередньою оцінкою, запис та пакети обслуговування.',
        'services' => 'Послуги ServiceFlow: діагностика, планове обслуговування та ремонт. Демонстрація каталогу й вибору послуги.',
        'quiz' => 'Опишіть симптоми автомобіля та отримайте попередню оцінку. Інтерактивний квіз ServiceFlow; ціни демонстраційні.',
        'packages' => 'Порівняйте пакети обслуговування ServiceFlow та додайте потрібні опції. Демонстраційні ціни й сценарій кошика.',
        'booking' => 'Оберіть послугу, дату й час у формі запису ServiceFlow. Демонстрація інтерфейсу без реального бронювання.',
        'about' => 'ServiceFlow: концепція прозорого процесу обслуговування авто — оцінка, погодження робіт та зрозумілий наступний крок.',
        'blog' => 'Матеріали ServiceFlow для підготовки до звернення в автосервіс та опису симптомів автомобіля.',
        'contacts' => 'Контакти та форма звернення демонстраційного проєкту ServiceFlow.',
        'cases' => 'Демонстраційні сценарії ServiceFlow: проблема автомобіля, план перевірки та наступний крок.'
    ];
    return $descriptions[sf_seo_route($post)] ?? wp_strip_all_tags(get_the_excerpt($post));
}
add_filter('wp_robots', function($robots) {
    if (sf_seo_private()) {unset($robots['index']); $robots['noindex'] = true; $robots['follow'] = true;}
    return $robots;
});
add_filter('wp_sitemaps_add_provider', function($provider, $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);
add_filter('wp_sitemaps_posts_query_args', function($args, $type) {
    if ($type !== 'page') return $args;
    $excluded = [];
    foreach (get_posts(['post_type'=>'page','post_status'=>'publish','numberposts'=>-1]) as $page) {
        if (sf_seo_private($page)) $excluded[] = $page->ID;
    }
    if (function_exists('wc_get_page_id')) foreach (['cart','checkout','myaccount'] as $key) {
        $id = wc_get_page_id($key); if ($id > 0) $excluded[] = $id;
    }
    $args['post__not_in'] = array_unique(array_merge($args['post__not_in'] ?? [], $excluded));
    return $args;
}, 10, 2);
add_action('wp_head', function() {
    if (!is_singular() || sf_seo_route() === null || sf_seo_plugin_active()) return;
    $post = get_post(); $url = get_permalink($post); $title = get_the_title($post).' — '.get_bloginfo('name');
    $desc = sf_seo_description($post);
    // Core rel_canonical owns canonical; do not emit a duplicate.
    foreach (['description'=>$desc] as $name=>$value) echo '<meta name="'.esc_attr($name).'" content="'.esc_attr($value).'">'."\n";
    foreach (['og:type'=>'website','og:locale'=>'uk_UA','og:title'=>$title,'og:description'=>$desc,'og:url'=>$url,'og:site_name'=>get_bloginfo('name')] as $name=>$value) echo '<meta property="'.esc_attr($name).'" content="'.esc_attr($value).'">'."\n";
    $image = get_the_post_thumbnail_url($post, 'large') ?: SF_CORE_URL.'assets/workshop.jpg';
    echo '<meta property="og:image" content="'.esc_url($image).'">'."\n";
    echo '<meta name="twitter:card" content="summary_large_image">'."\n";
    $schema = ['@context'=>'https://schema.org','@type'=>'WebPage','name'=>$title,'description'=>$desc,'url'=>$url,'inLanguage'=>'uk'];
    echo '<script type="application/ld+json">'.wp_json_encode($schema, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>'."\n";
}, 5);
