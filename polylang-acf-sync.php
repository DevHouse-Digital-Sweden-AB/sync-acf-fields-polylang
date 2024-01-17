/**
 * Sync ACF data
 * By Ross and Alex @DevHouse.
 */
function syncAcfData()
{
    // Check for polylang installation
    if (!function_exists('pll_get_post')) {
        return;
    }

    // Variables
    $foreignLangs = pll_languages_list();
    $postType = 'products';
    $field_key = 'related_articles';

    // Remove EN from languages
    if (in_array('en', $foreignLangs)) {
        unset($foreignLangs[0]);
    }

    // Arguments
    $args = [
        'post_type' => $postType,
        'posts_per_page' => -1,
        'lang' => $foreignLangs,
    ];

    $posts = get_posts($args);

    if (!$posts) {
        return;
    }

    $posts = array_column($posts, 'ID');

    // For each foreign post
    foreach ($posts as $post) {
        $newAcfData = [];
        $englishPostID = pll_get_post($post, 'en');

        if (!$englishPostID) {
            continue;
        }

        $englishFieldData = get_field($field_key, $englishPostID);

        // Find translations of these items and push to post translation
        if (!$englishFieldData) {
            continue;
        }

        // Loop through foreign post ACF data and update array
        foreach ($englishFieldData as $englishItem) {
            $foreignPost = pll_get_post($englishItem, pll_get_post_language($post));

            if ($foreignPost) {
                $newAcfData[] = $foreignPost;
            }
        }

        // Update field data for selected foreign posts
        if (!empty($newAcfData)) {
            update_field($field_key, $newAcfData, $post);
        }
    }
}
// add_action('template_redirect', 'syncAcfData');

/**
 * Sync ACF data - Page
 * By Ross and Alex @DevHouse.
 */
function syncAcfPageData()
{
    // Check for polylang installation
    if (!function_exists('pll_get_post')) {
        return;
    }

    // Variables
    $englishPageID = 198;
    $foreignLangs = pll_languages_list();
    $field_key = 'featured_problem_areas';

    // Remove EN from languages
    if (in_array('en', $foreignLangs)) {
        unset($foreignLangs[0]);
    }

    // Foreach page translation
    foreach ($foreignLangs as $lang) {
        $translationID = pll_get_post($englishPageID, $lang);
        $newAcfData = [];

        if (!$translationID) {
            continue;
        }

        $englishFieldData = get_field($field_key, $englishPageID);

        // Find translations of these items and push to post translation
        if (!$englishFieldData) {
            continue;
        }

        // Loop through foreign post ACF data and update array
        foreach ($englishFieldData as $englishItem) {
            $foreignPost = pll_get_post($englishItem, $lang);

            if ($foreignPost) {
                $newAcfData[] = $foreignPost;
            }
        }

        // Update field data for selected foreign posts
        if (!empty($newAcfData)) {
            update_field($field_key, $newAcfData, $translationID);
        }
    }
}
// add_action('template_redirect', 'syncAcfPageData');
