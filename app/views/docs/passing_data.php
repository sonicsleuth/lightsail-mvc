<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<p><a href="/docs">Go Back</a></p>

<p><strong>NOTE:</strong> You may see the PHP Error "DB Connection Error:" at the top of this page if you have not yet
        configured your Database settings in the <strong>/app/config/database.php</strong> settings file.
        Configuring the database is not required to read this page.</p>

<h2>Passing values from a Controller to a View</h2>

<p>Examine the following files for details:</p>
<ul>
    <li> /app/models/User.php -> method: getUsers() 
    <li> /app/controllers/Examples.php
    <li> /app/views/docs/passing_data.php (this file) 
</ul>

<pre><code class="language-php">PHP CODE:
// Dump the whole $data parameter passed into this View.
print_r($data);
// Access the values like so: $data['users'], $data['is_admin']
// However, see below for a more efficient way to get these values.
</code></pre>

<pre><code class="language-text">OUTPUT:
<?php
print_r($data);
?>
</code></pre>

<h2>Accessing $data values as PHP $variables:</h2>

<p>Passing in the associative array $data to a View results in the 
auto-magically created PHP variables for each Key in the $data array.
 This allows you to refer to the keys more easilly as shown below where
 $users is much cleaner than $data['users'].</p>

<pre><code class="language-php">PHP CODE:
// Looping over the list of $users passed into this View, if they exist.
if($users != '') {
    foreach($users as $user) {
        echo "Name: " . $user['name'] . "\r\n";
        print_r($user);
    }
}
</code></pre>

<pre><code class="language-text">OUTPUT:
<?php
if($users != '') {
    foreach($users as $user) {
        echo "Name: " . $user['name'] . "\r\n";
        print_r($user);
    }
}
?>
</code></pre>



<pre><code class="language-php">PHP CODE:
// Another example, where we are accessing a the string $is_admin below.
echo "Are you an Admin? " . $is_admin;
</code></pre>

<pre><code class="language-text">OUTPUT:
<?php
echo "Are you an Admin? " . $is_admin;
?>
</code></pre>




<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>