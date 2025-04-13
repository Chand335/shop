posts
-title
-slug
-Content
-created_by
-category
-publish_date
-tags
-image
-status

post_category
-name
-slug
-description
-Visibility // Visible to customers.

posts_comments
-title
customer
visibility
content

authors
-name
-email
-bio
-github
-Twitter

links
-title
-color
-Description
-URL
-Image

customers
-Name
-email
-phone
-birthday

customers_Addresses
-street
-zip
-city
-state
-country

php artisan make:model PostCategory -m
php artisan make:model PostTag -m
php artisan make:model PostComment -m

php artisan make:migration create_post_tags_table
php artisan make:migration create_post_comments_table

php artisan make:filament-user

php artisan make:filament-resource Post
php artisan make:filament-resource PostCategory
php artisan make:filament-resource PostComment
php artisan make:filament-resource PostTag

php artisan make:filament-page ViewPost --resource=PostResource --type=ViewRecord
php artisan make:filament-page ViewPostTag --resource=PostTagResource --type=ViewRecord