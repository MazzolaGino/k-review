<?php

namespace KReview\Controller;

use KLib2\Interfaces\IPath;
use KLib2\Controller\Controller;
use KReview\Lib\Review;
use KReview\Lib\ReviewManager;
use Timber\Timber;

class ReviewController extends Controller
{

    public function __construct(IPath $p)
    {
        parent::__construct($p);
    }

    /**
     * @action add_action
     * @hook add_meta_boxes
     * @priority 10
     * @args 1
     */
    public function reviewFields($post)
    {
        global $post;

        add_meta_box('custom_fields_box', 'Review', function () use ($post) {
            ReviewManager::renderCustomFields($post);
        }, 'post', 'normal', 'default');
    }

    /**
     * @action add_action
     * @hook save_post
     * @priority 10
     * @args 1
     */

    public function save($post_id)
    {
        ReviewManager::saveCustomFields($post_id);
    }

    /**
     * @action add_shortcode
     * @hook wp-review
     * @priority 10
     * @args 1
     */
    public function noreview($atts)
    {
        global $post;

        ob_start();

        $data = Timber::render($this->path->dir('templates/review/review.html.twig'), [
            'post_id' => $post->ID,
            'note' => get_post_meta($post->ID, 'review_rating', true)
        ]);

        ob_end_clean();

        return $data;
    }

    /**
     * @action add_action
     * @hook wp_head
     * @priority 10
     * @args 0
     */
    public function reviewHeader()
    {
        global $post;
        ob_start();
        $context =[

            'rating'        => \get_post_meta($post->ID, 'review_rating', true),
            'game_title'    => \addslashes(\get_post_meta($post->ID, 'review_game_title', true)),
            'description'   => \addslashes(\get_post_meta($post->ID, 'review_description', true)),
            'title'         => \addslashes($post->post_title),
            'date'          => \get_the_time('d/m/Y', $post),
            'author'        => \get_the_author_meta('display_name', $post->post_author),
            'url'           => \get_the_permalink($post)

        ];

        $data = Timber::render($this->path->dir('templates/review/review-header.html'), $context);

        ob_end_clean();

        echo $data;
    }
}
