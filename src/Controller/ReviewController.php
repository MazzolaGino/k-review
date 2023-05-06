<?php

namespace KReview\Controller;

use KLib2\Interfaces\IPath;
use KLib2\Controller\Controller;
use KReview\Lib\ReviewManager;

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

        add_meta_box('custom_fields_box', 'Review', function() use ($post) {
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
 
}
