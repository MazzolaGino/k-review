<?php

namespace KReview\Lib;

class Review
{
    public $review_rating;
    public $review_description;
    public $review_positive_points;
    public $review_negative_points;
    public $review_game_title;
    public $review_developer;
    public $review_publisher;
    public $review_review_summary;
    public $review_release_date_japan;
    public $review_release_date_europe;
    public $review_console;
    public $review_genre;

    private \WP_POST $post;

    /**
     * @param string $rating
     * @param string $description
     * @param string $positivePoints
     * @param string $negativePoints
     * @param string $gameTitle
     * @param string $developer
     * @param string $publisher
     * @param string $reviewSummary
     * @param string $releaseDateJapan
     * @param string $releaseDateEurope
     * @param string $console
     * @param string $genre
     * 
     */
    public function __construct(\WP_POST $post = null)
    {
        if ($post instanceof \WP_Post) {
            $this->post = $post;
            $this->bindCustomFields();
        }
    }

    public function getTitles()
    {
        return [
            'review_rating' => 'Rating',
            'review_description' => 'Description',
            'review_positive_points' => 'Positive Points',
            'review_negative_points' => 'Negative Points',
            'review_game_title' => 'Game Title',
            'review_developer' => 'Developer',
            'review_publisher' => 'Publisher',
            'review_review_summary' => 'Review Summary',
            'review_release_date_japan' => 'Release Date (Japan)',
            'review_release_date_europe' => 'Release Date (Europe)',
            'review_console' => 'Consoles',
            'review_genre' => 'Genres'
        ];
    }

    public function getConsoles()
    {
        return [
            'Ps4',
            'Ps5',
            'Android',
            'iOS',
            'Switch',
            'PC',
            'Series',
            'One'
        ];
    }

    public function getGenre()
    {
        return [
            'Tour par tour',
            'Tactique',
            'Action',
            'Survival Horror',
            'Visual Novel',
            'Simulation'
        ];
    }

    public function saveToDatabase($postId, $post_request)
    {
        // echo '<pre>' . print_r($post_request, true) . '</pre>';

        $reflection = new \ReflectionClass($this);

        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {

            $key = $property->getName();
            
            if(isset($post_request[$key])) 
            {
                $metaValue = $post_request[$key];

                if (in_array($key, ['review_genre', 'review_console'])) {
    
                    $metaValue = implode(';', $metaValue);
                }
    
                update_post_meta($postId, $key, $metaValue);
            }
            
        }
    }

    public function bindCustomFields()
    {
        $reflection = new \ReflectionClass($this);

        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {

            $propertyName = $property->getName();

            $metaValue = get_post_meta($this->post->ID, $propertyName, true);

            if (in_array($propertyName, ['review_genre', 'review_console'])) {
                $metaValue = explode(';', $metaValue);
            }

            $this->$propertyName = $metaValue;
        }
    }

    public function toArray()
    {

        $reviewToArray = [];

        foreach (get_class_vars(get_class($this)) as $prop) {
            $reviewToArray[$prop] = $this->$prop;
        }

        return $reviewToArray;
    }
}
