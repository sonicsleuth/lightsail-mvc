<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP :: Using AWS S3 Storage Service</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<p><a href="/docs">Go Back</a></p>

<h2>Using AWS S3 Object Storage Service</h2>

<p><strong>Requirements:</strong> </p>
<p>To use the AWS S3 Model, you must have an Amazon Web Services (AWS) Account and have set up an S3 Bucket with permissions.</p>


<p><strong>Configuration:</strong> </p>
<p>The following configuration values should be set up in your local (and server) <strong>.env</strong> Environment file.<br>
    These values should NEVER be visible in your repository or PHP files. <strong>Keep them a secret.</strong> </p>

<p>NOTE: You can also configure the environment values in the Docker Container <strong>docker-compose.yml</strong>
    file then rebuild this docker container to have those values available to PHP. Run: <strong>docker compose build</strong> in a terminal.</p>

<pre><code class="language-text">AWS_BUCKET_NAME: your_bucket_name
AWS_REGION: your_bucket_region
AWS_VERSION: latest
AWS_ACCESS_KEY: your_access_key
AWS_SECRET_KEY: your_secret_key
</code></pre><br>

<p><strong>First, include the core AWS S3 Model in your Controller</strong> </p>
<pre><code class="language-php">class myController
    {
        private mixed $s3Manager;

        public function __construct()
        {
            // Directly accessible when installed as a core service here: app/init.php
            $s3Manager = new AWS_S3_Model();
        }
    }
</code></pre><br>

<p><strong>Then, use the following functions to manage file objects on AWS S3.</strong></p>

<p><strong>Upload a file</strong> </p>
<pre><code class="language-php">$s3Manager->uploadFile('test-file.txt', '/path/to/local/file.txt');
</code></pre><br>

<p><strong>Download a file</strong> </p>
<pre><code class="language-php">$s3Manager->downloadFile('test-file.txt', '/path/to/save/file.txt');
</code></pre><br>

<p><strong>Update a file</strong> </p>
<pre><code class="language-php">$s3Manager->updateFile('test-file.txt', '/path/to/new/file.txt');
</code></pre><br>

<p><strong>Delete a file</strong> </p>
<pre><code class="language-php">$s3Manager->deleteFile('test-file.txt');
</code></pre><br>

<p><strong>List files</strong> </p>
<pre><code class="language-php">$files = $s3Manager->listFiles();
print_r($files);
</code></pre><br>

<p><strong>Get file timestamp</strong> </p>
<pre><code class="language-php">$timestamp = $s3Manager->getFileTimestamp('test-file.txt');
echo $timestamp;
</code></pre><br>

<h2>Try it...</h2>
<p>If you have configured your AWS environment settings as noted above and created a bucket called <strong>lightsail-mvc</strong>
within your AWS S3 account, then you can click the following link to test the AWS S3 Model in action.</p>

<p><a href="/docs/aws_s3_example" target="_blank">Try the AWS S3 Model</a></p>

<p>If you receive any PHP errors after clicking the above link, update the settings in your <strong>docker-compose.yml</strong> file<br>
    Then restart your containers, run: <strong>docker-compose up -d</strong></p>

<pre><code class="language-yml"># AWS S3 settings
AWS_BUCKET_NAME: your_aws_s3_bucket_name
AWS_REGION: your_aws_s3_bucket_region
AWS_VERSION: latest
AWS_ACCESS_KEY: your_aws_access_key
AWS_SECRET_KEY: your_aws_secret_key
</code></pre><br>


<p><strong>IF SUCCESS:</strong></p>
<p>You should see output like the following which confirms everything is working correctly.</p>
<pre><code class="language-text">File uploaded from: app/public/img/light-sail-logo.png

List of Files:
Array
(
    [0] => Array
        (
            [Key] => light-sail-logo.png
            [LastModified] => Aws\Api\DateTimeResult Object
                (
                    [date] => 2024-09-20 03:17:09.000000
                    [timezone_type] => 2
                    [timezone] => Z
                )

            [ETag] => "abeea9ab4db2ffb1a57b65e53c50acd7"
            [Size] => 10390
            [StorageClass] => STANDARD
            [Owner] => Array
                (
                    [DisplayName] => your_owner_name
                    [ID] => your_secret_id
                )

        )

)

Timestamp on the file light-sail-logo.png:
2024-09-20T03:17:09+00:00

File Size of light-sail-logo.png:
10390

File downloaded to: app/public/img/light-sail-logo-2.png
Check that directory to see the downloaded file!
</code></pre><br>

<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>

