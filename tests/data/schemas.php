<?php
/**
 * Dummy Data: Schema Types
 * 
 * Returns an array of field values for testing schema generation.
 */

return array(
    // FREE Types
    'Article' => array(
        'headline' => 'Test Article Headline',
        'url' => 'https://example.com/test-article',
        'description' => 'A summary of the test article.',
        'imageUrl' => 'https://example.com/images/article.jpg',
        'authorType' => 'Person',
        'authorName' => 'John Doe',
        'publisherName' => 'Test Publisher',
        'datePublished' => '2023-01-01T12:00:00+00:00',
        'dateModified' => '2023-01-02T12:00:00+00:00',
    ),
    'Person' => array(
        'name' => 'Jane Doe',
        'jobTitle' => 'Software Engineer',
        'url' => 'https://example.com/jane-doe',
        'image' => 'https://example.com/images/jane.jpg',
        'sameAs' => array(
            'https://twitter.com/janedoe',
            'https://linkedin.com/in/janedoe'
        ),
        'description' => 'A bio regarding Jane Doe.',
        'email' => 'jane@example.com',
        'telephone' => '+1-555-0100',
    ),
    'Organization' => array(
        'name' => 'Acme Corp',
        'url' => 'https://example.com',
        'logo' => 'https://example.com/logo.png',
        'sameAs' => array(
            'https://facebook.com/acmecorp',
            'https://twitter.com/acmecorp'
        ),
        'contactPoint' => array(
            'telephone' => '+1-555-0199',
            'contactType' => 'customer service',
        ),
    ),
    'LocalBusiness' => array(
        'name' => 'Joe\'s Pizza',
        'description' => 'Best pizza in town.',
        'image' => 'https://example.com/pizza.jpg',
        'telephone' => '+1-555-0123',
        'priceRange' => '$$',
        'address' => array(
            'streetAddress' => '123 Main St',
            'addressLocality' => 'New York',
            'addressRegion' => 'NY',
            'postalCode' => '10001',
            'addressCountry' => 'US',
        ),
        'geo' => array(
            'latitude' => '40.7128',
            'longitude' => '-74.0060',
        ),
        'openingHours' => array(
            'Mo-Fr 09:00-17:00',
            'Sa 10:00-14:00'
        ),
    ),
    'Product' => array(
        'name' => 'Super Widget',
        'description' => 'It does everything.',
        'image' => 'https://example.com/widget.jpg',
        'sku' => 'WIDGET-001',
        'brand' => 'Acme',
        'offers' => array(
            'price' => '19.99',
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock',
            'url' => 'https://example.com/product/widget',
        ),
        'aggregateRating' => array(
            'ratingValue' => '4.5',
            'reviewCount' => '100',
        ),
    ),
    'Review' => array(
        'itemReviewed' => array(
            '@type' => 'Product',
            'name' => 'Super Widget',
        ),
        'reviewRating' => array(
            'ratingValue' => '5',
        ),
        'author' => array(
            '@type' => 'Person',
            'name' => 'Happy Customer',
        ),
        'reviewBody' => 'This product is amazing!',
    ),
    'FAQ' => array(
        'mainEntity' => array(
            array(
                'question' => 'What is the refund policy?',
                'answer' => '30 days money back guarantee.',
            ),
            array(
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes, we ship worldwide.',
            ),
        ),
    ),
    'JobPosting' => array(
        'title' => 'Senior Developer',
        'description' => 'We are looking for a PHP expert.',
        'datePosted' => '2023-05-01',
        'validThrough' => '2023-06-01',
        'employmentType' => 'FULL_TIME',
        'hiringOrganization' => array(
            'name' => 'Tech Corp',
            'sameAs' => 'https://example.com',
        ),
        'jobLocation' => array(
            'address' => array(
                'addressLocality' => 'San Francisco',
                'addressRegion' => 'CA',
                'addressCountry' => 'US',
            ),
        ),
        'baseSalary' => array(
            'currency' => 'USD',
            'value' => array(
                'minValue' => '100000',
                'maxValue' => '150000',
                'unitText' => 'YEAR',
            ),
        ),
    ),
    'VideoObject' => array(
        'name' => 'How to use our product',
        'description' => 'A tutorial video.',
        'thumbnailUrl' => 'https://example.com/thumb.jpg',
        'uploadDate' => '2023-03-01T08:00:00+00:00',
        'duration' => 'PT5M30S',
        'contentUrl' => 'https://example.com/video.mp4',
        'embedUrl' => 'https://example.com/embed/123',
    ),
    'BreadcrumbList' => array(
        'itemListElement' => array(
            array(
                'position' => 1,
                'name' => 'Home',
                'item' => 'https://example.com',
            ),
            array(
                'position' => 2,
                'name' => 'Category',
                'item' => 'https://example.com/category',
            ),
            array(
                'position' => 3,
                'name' => 'Current Page',
                'item' => 'https://example.com/category/page',
            ),
        ),
    ),

    // PRO Types
    'Recipe' => array(
        'name' => 'Chocolate Cake',
        'description' => 'Delicious dark chocolate cake.',
        'image' => 'https://example.com/cake.jpg',
        'author' => 'Chef Baker',
        'prepTime' => 'PT20M',
        'cookTime' => 'PT45M',
        'totalTime' => 'PT1H5M',
        'recipeYield' => '8 servings',
        'recipeIngredient' => array(
            '2 cups flour',
            '1 cup sugar',
            '1/2 cup cocoa powder',
        ),
        'recipeInstructions' => array(
            array(
                '@type' => 'HowToStep',
                'text' => 'Mix dry ingredients.',
            ),
            array(
                '@type' => 'HowToStep',
                'text' => 'Bake at 350F for 45 minutes.',
            ),
        ),
        'nutrition' => array(
            'calories' => '350 calories',
        ),
    ),
    'Event' => array(
        'name' => 'Tech Conference 2023',
        'startDate' => '2023-09-15T09:00:00+00:00',
        'endDate' => '2023-09-17T17:00:00+00:00',
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'location' => array(
            '@type' => 'Place',
            'name' => 'Convention Center',
            'address' => array(
                'streetAddress' => '123 Expo Blvd',
                'addressLocality' => 'Las Vegas',
                'addressRegion' => 'NV',
                'postalCode' => '89109',
                'addressCountry' => 'US',
            ),
        ),
        'organizer' => array(
            '@type' => 'Organization',
            'name' => 'Tech Events LLC',
            'url' => 'https://techevents.com',
        ),
        'offers' => array(
            'price' => '299',
            'priceCurrency' => 'USD',
            'url' => 'https://example.com/tickets',
            'availability' => 'https://schema.org/InStock',
        ),
    ),
    'HowTo' => array(
        'name' => 'How to tie a tie',
        'description' => 'Simple steps to tie a Windsor knot.',
        'step' => array(
            array(
                'text' => 'Cross the wide end over the narrow end.',
                'image' => 'https://example.com/step1.jpg',
            ),
            array(
                'text' => 'Bring the wide end up through the loop.',
                'image' => 'https://example.com/step2.jpg',
            ),
        ),
        'totalTime' => 'PT5M',
        'supply' => array('Tie', 'Mirror'),
    ),
    'PodcastEpisode' => array(
        'headline' => 'The Future of AI',
        'url' => 'https://example.com/podcast/ai-future',
        'description' => 'Discussing LLMs and agents.',
        'imageUrl' => 'https://example.com/podcast-cover.jpg',
        'authorName' => 'Tech Host',
        'partOfSeries' => 'https://example.com/podcast-series',
        'episodeNumber' => '42',
        'seasonNumber' => '3',
        'duration' => 'PT45M',
        'audioUrl' => 'https://example.com/audio/ep42.mp3',
        'datePublished' => '2023-12-01',
    ),
    'WebSite' => array(
        'name' => 'My Awesome Site',
        'url' => 'https://example.com',
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => 'https://example.com/?s={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ),
    ),
);
