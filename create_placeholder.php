<?php
// Create a very simple placeholder image using PHP's file functions
$placeholder_path = 'images/placeholder.jpg';

// Copy an existing image as the placeholder
$source_image = 'images/tomatoes.jpg'; // Using an existing image
if (file_exists($source_image) && !file_exists($placeholder_path)) {
    if (copy($source_image, $placeholder_path)) {
        echo "Placeholder image created by copying an existing image.<br>";
    } else {
        echo "Failed to create placeholder image.<br>";
    }
} else if (file_exists($placeholder_path)) {
    echo "Placeholder image already exists.<br>";
} else {
    echo "Source image not found.<br>";
}

echo "<p>Try accessing the <a href='pages/marketplace.php'>marketplace</a> now.</p>";
?>
