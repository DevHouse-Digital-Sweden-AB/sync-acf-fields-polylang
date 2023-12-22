/**
 * Sync ACF data
 * By Ross and Alex @DevHouse.
 */
function syncAcfData()
{
    // Variables
    $foreignLang = 'de';
    $field = 'related_products';

    // Arguments
    $args = [
        'post_type' => 'products',
        'posts_per_page' => -1,
        'lang' => $foreignLang,
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
        $englishFieldData = get_field($field, $englishPostID);

        // Find translations of these items and push to post translation
        if (!$englishFieldData) {
            continue;
        }

        // Loop through foreign post ACF data and update array
        foreach ($englishFieldData as $englishItem) {
            $foreignPost = pll_get_post($englishItem, $foreignLang);

            if ($foreignPost) {
                $newAcfData[] = $foreignPost;
            }
        }

        // Update field data for selected foreign posts
        if (!empty($newAcfData)) {
            update_field($field, $newAcfData, $post);
            var_dump('Fields have been updated!');
        } else {
            var_dump('Nothing to update!');
        }
    }
}
// add_action('template_redirect', 'syncAcfData');
