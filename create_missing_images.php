<?php
// Define source and target image mappings
$image_mappings = [
    // Main images folder
    'images/beans.jpg' => 'images/cabbage.jpg',
    'images/cassava.jpg' => 'images/sweet_potatoes.jpeg',
    'images/maize.jpg' => 'images/onions.jpg',
    'images/mangoes.jpg' => 'images/oranges.jpg',
    'images/rice.jpg' => 'images/potatoes.jpg',
    'images/sweet-potatoes.jpg' => 'images/sweet_potatoes.jpeg',
    
    // Product images folder
    'images/products/fruit-bananas.jpg' => 'images/bananas.jpg',
    'images/products/fruit-oranges.jpg' => 'images/oranges.jpg',
    'images/products/fruit-pineapples.jpg' => 'images/pineapples.jpg',
    'images/products/grain-maize.jpg' => 'images/onions.jpg',
    'images/products/grain-millet.jpg' => 'images/onions.jpg',
    'images/products/grain-sorghum.jpg' => 'images/onions.jpg',
    'images/products/herb-chili.jpg' => 'images/green_pepper.jpg',
    'images/products/herb-garlic.jpg' => 'images/onions.jpg',
    'images/products/herb-turmeric.jpg' => 'images/carrots.jpg',
    'images/products/legume-beans.jpg' => 'images/cabbage.jpg',
    'images/products/legume-groundnuts.jpg' => 'images/onions.jpg',
    'images/products/legume-peas.jpg' => 'images/cabbage.jpg',
    'images/products/legume-soybeans.jpg' => 'images/cabbage.jpg',
    'images/products/tuber-cassava.jpg' => 'images/sweet_potatoes.jpeg',
    'images/products/tuber-irish-potatoes.jpg' => 'images/potatoes.jpg',
    'images/products/tuber-sweet-potatoes.jpg' => 'images/sweet_potatoes.jpeg',
    'images/products/tuber-yams.jpg' => 'images/sweet_potatoes.jpeg',
    'images/products/veg-carrots.jpg' => 'images/carrots.jpg',
    'images/products/veg-peppers.jpg' => 'images/green_pepper.jpg',
    'images/products/veg-tomatoes.jpg' => 'images/tomatoes.jpg'
];

// Create images/products directory if it doesn't exist
if (!file_exists('images/products')) {
    mkdir('images/products', 0777, true);
    echo "Created images/products directory.<br>";
}

// Copy images
$created_count = 0;
$skipped_count = 0;
$error_count = 0;

foreach ($image_mappings as $target => $source) {
    if (!file_exists($target) && file_exists($source)) {
        if (copy($source, $target)) {
            echo "Created $target by copying $source<br>";
            $created_count++;
        } else {
            echo "Failed to create $target<br>";
            $error_count++;
        }
    } else if (file_exists($target)) {
        echo "Skipped $target (already exists)<br>";
        $skipped_count++;
    } else {
        echo "Skipped $target (source $source not found)<br>";
        $error_count++;
    }
}

echo "<h2>Image Creation Summary</h2>";
echo "Created: $created_count<br>";
echo "Skipped: $skipped_count<br>";
echo "Errors: $error_count<br>";

echo "<p>Now try accessing the <a href='pages/marketplace.php'>marketplace</a>!</p>";
?>
