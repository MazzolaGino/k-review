<?php

namespace KReview\Lib;

use KReview\Path;
use Timber\Post;
use Timber\Timber;

class ReviewManager
{
    public static function registerCustomFields()
    {
        add_action('add_meta_boxes', array(__CLASS__, 'addCustomFields'));
        add_action('save_post', array(__CLASS__, 'saveCustomFields'));
    }

    public static function addCustomFields()
    {
        add_meta_box(
            'game_review_fields',
            'Game Review Fields',
            array(__CLASS__, 'renderCustomFields'),
            'post',
            'normal',
            'default'
        );
    }

    public static function renderCustomFields($post)
    {
        $review = new Review($post);

        $context = [
            'review' => $review,
            'review_genres' => $review->review_genre,
            'review_consoles' => $review->review_console,
            'genre_options' => $review->getGenre(),
            'console_options' => $review->getConsoles(),
        ];

        Timber::render((new Path())->dir('templates/review/review-fields.html.twig'), $context);
    }

    public static function saveCustomFields($post_id)
    {
        $review = new Review();
        $review->saveToDatabase($post_id, $_POST);
    }

    public static function displayPostReviewMetadata($postId)
    {
        $metadata = get_post_meta($postId);

        foreach ($metadata as $key => $value) {

            $v = $value[0];

            if (strpos($key, 'wp_review') === 0) {
                switch ($key) {

                    case 'wp_review_total': {
                            update_post_meta($postId, 'review_rating', $v);
                        }
                        break;

                    case 'wp_review_desc': {
                            update_post_meta($postId, 'review_positive_points', self::extractULTags($v)[0]);
                            update_post_meta($postId, 'review_negative_points', self::extractULTags($v)[1]);
                        }
                        break;

                    case 'wp_review_heading': {
                            update_post_meta($postId, 'review_game_title', $v);
                        }
                        break;

                    case 'wp_review_schema_options': {
                            $wpreview = unserialize($v);
                            update_post_meta($postId, 'review_description', $wpreview['Game']['description']);
                        }
                        break;

                    default:
                        break;
                }
            }
        }
    }

    public static function extractULTags($htmlString)
    {

        $ulPattern = '/<ul>(.*?)<\/ul>/s';

        preg_match_all($ulPattern, $htmlString, $matches);

        $firstUL = $matches[0][0];
        $secondUL = $matches[0][1];

        return array($firstUL, $secondUL);
    }

    public static function getTestArticlesMetadata()
    {
        $args = array(
            'category_name' => 'test',
            'posts_per_page' => -1,
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $postId = get_the_ID();
                self::displayPostReviewMetadata($postId);
            }
        }

        wp_reset_postdata();
    }
}
