<?php
/*
 * AWS S3 Model is a Model for working with objects stored in an AWS S3 bucket.
 *
 * First, make sure to install the AWS SDK via Composer if you haven’t done so:
 * composer require aws/aws-sdk-php
 *
 * EXAMPLES OF USAGE:
 *
 * These values should NEVER be stored in your public scripts.
 * Use a .env file or a service like Parameter Store on AWS to make the available to this app.
 *
 * $bucketName = 'your-bucket-name';
 * $region = 'your-region';
 * $version = 'latest';
 * $accessKey = 'your-access-key';
 * $secretKey = 'your-secret-key';
 *
 * $s3Manager = new S3Manager($bucketName, $region, $version, $accessKey, $secretKey);
 *
 * // Upload a file
 * $s3Manager->uploadFile('test-file.txt', '/path/to/local/file.txt');
 *
 * // Download a file
 * $s3Manager->downloadFile('test-file.txt', '/path/to/save/file.txt');
 *
 * // Update a file
 * $s3Manager->updateFile('test-file.txt', '/path/to/new/file.txt');
 *
 * // Delete a file
 * $s3Manager->deleteFile('test-file.txt');
 *
 * // List files
 * $files = $s3Manager->listFiles();
 * print_r($files);
 *
 * // Get file timestamp
 * $timestamp = $s3Manager->getFileTimestamp('test-file.txt');
 * echo $timestamp;
 *
 */

require_once '../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AWSS3Model
{
    private $s3Client;
    private $bucketName;

    public function __construct($bucketName = AWS_BUCKET_NAME, $region = AWS_REGION, $version = AWS_VERSION, $accessKey = AWS_ACCESS_KEY, $secretKey = AWS_SECRET_KEY)
    {
        $this->bucketName = $bucketName;
        $this->s3Client = new S3Client([
            'region'  => $region,
            'version' => $version,
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
        ]);
    }

    // TEST
    public function test()
    {
        echo "FOUND ME";
    }

    // CREATE: Upload a file to S3
    public function uploadFile($key, $filePath): mixed
    {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
                'SourceFile' => $filePath,
            ]);
            return $result['ObjectURL'];
        } catch (AwsException $e) {
            echo "Error uploading file: " . $e->getMessage();
        }
    }

    // READ: Download a file from S3
    public function downloadFile($key, $saveAs): mixed
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
                'SaveAs' => $saveAs,
            ]);
            return $saveAs;
        } catch (AwsException $e) {
            echo "Error downloading file: " . $e->getMessage();
        }
    }

    // UPDATE: Update a file on S3 (by uploading a new version)
    public function updateFile($key, $newFilePath): mixed
    {
        return $this->uploadFile($key, $newFilePath);
    }

    // DELETE: Delete a file from S3
    public function deleteFile($key): mixed
    {
        try {
            $result = $this->s3Client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
            return "File deleted successfully.";
        } catch (AwsException $e) {
            echo "Error deleting file: " . $e->getMessage();
        }
    }

    // LIST: List all files in the bucket
    public function listFiles(): mixed
    {
        try {
            $result = $this->s3Client->listObjects([
                'Bucket' => $this->bucketName,
            ]);
            return $result['Contents'];
        } catch (AwsException $e) {
            echo "Error listing files: " . $e->getMessage();
        }
    }

    // Get the date stamp of an object on S3
    public function getFileTimestamp($key): mixed
    {
        try {
            $result = $this->s3Client->headObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
            return $result['LastModified'];
        } catch (AwsException $e) {
            echo "Error getting file timestamp: " . $e->getMessage();
        }
    }

    // Get the date stamp of an object on S3
    public function getFileSize($key): mixed
    {
        try {
            $result = $this->s3Client->headObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
            return $result['ContentLength'];
        } catch (AwsException $e) {
            echo "Error getting file timestamp: " . $e->getMessage();
        }
    }
}