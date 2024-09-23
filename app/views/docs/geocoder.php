<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP :: Geocoder Model</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<p><a href="/docs">Go Back</a></p>

<h1>Geocoder Model</h1>

<p>The Census Geocoder provides a programmatic (REST) API to users interested in matching addresses
    to geographic locations and entities containing those addresses. The Census Geocoder only geocodes addresses that
    are within the United States, Puerto Rico, and the U.S. Island Areas.
    Learn more about the Census Geocoder here: <a href="https://geocoding.geo.census.gov/geocoder/">https://geocoding.geo.census.gov/geocoder/</a></p>

<p>Lightsail has a wrapper Model for the Geocoder API here: <strong>/app/models/Geocoder.php</strong></p>

<h2>How to use</h2>

<p>Preview the Controller creating this page to see how to implement the Geocoder, or read on...</p>

<h2>Geocoding Model</h2>

<p>Located here: <strong>/app/models/Geocoder.php</strong></p>

<p>This is the Model used by the Controller that rendered this View (webpage).
    The output shown below is a live call to the CENSUS Geocoder API for the following US Addresses.</p>

<pre>
<code class="language-php">/**
 * Geocoder
 *
 * This Model is wrapper around the US CENSUS Geocoder API.
 * This allows you to get a complete/correct address with Geo-location.
 *
 * There are two methods:
 * geocodeMultipleAddresses() - Input is a CSV file, Output is a CSV file (data stream) - Limited to 10,000 records per file.
 * geocodeSingleAddress() - Input is a STRING, Output is JSON.
 */

class Geocoder {

    private string $geocoder_api_url;

    public function __construct()
    {
        // US CENSUS Geocoder API Endpoint. Ref: https://geocoding.geo.census.gov/geocoder/
        $this->geocoder_api_url = 'https://geocoding.geo.census.gov/geocoder/locations/addressbatch';
    }

    /**
     * @param string $temp_filename
     * @param array $address_records
     * @return bool
     */
    private function makeCSV(string $temp_filename, array $address_records): bool
    {
        if(!isset($temp_filename) || !isset($address_records)) {
            return false;
        }

        $fp = fopen($temp_filename, 'w');

        foreach ($address_records as $record) {
            fputcsv($fp, $record);
        }

        fclose($fp);

        return true;
    }

    /**
     * @param string $temp_filename
     * @return void
     */
    private function deleteCSV(string $temp_filename): void
    {
        $filePath = getcwd() . "/" . $temp_filename;
        unlink($filePath);
    }

    /**
     * @param string $source_filename
     * @param array $address_records
     * @param string $destination_filename
     * @return string|bool
     */
    public function getMultipleAddresses(string $source_filename, array $address_records, string $destination_filename): string|bool
    {
        if(!isset($source_filename) || !isset($destination_filename)) {
            return false;
        }

        $this->makeCSV($source_filename, $address_records);

        $data = array(
            'benchmark' => 'Public_AR_Current',
            'vintage' => 'ACS2019_Current',
            'format' => 'csv',
            'addressFile' => new CURLFile($source_filename, 'text/csv', $destination_filename),
        );

        $ch = curl_init($this->geocoder_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if ($response === false) {
            return 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        $this->deleteCSV($source_filename);

        return $response;
    }

    /**
     * @param string $source_filename
     * @param array $address_records
     * @param string $destination_filename
     * @return string|bool
     */
    public function geocodeMultipleAddresses(string $source_filename, array $address_records, string $destination_filename): string|bool
    {
        return $this->getMultipleAddresses($source_filename, $address_records, $destination_filename);
    }

    /**
     * @param string $address
     * @return string|bool
     */
    public function geocodeSingleAddress(string $address): string|bool
    {
        // Address format: “4600 Silver Hill Rd, Washington, DC 20233”
        $url = "https://geocoding.geo.census.gov/geocoder/locations/onelineaddress" .
                "?address=" . urlencode($address).
                "&benchmark=4" .
                "&format=json";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);           // Optional: Set a timeout
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);  // Optional: Follow redirects

        $response = curl_exec($curl);

        curl_close($curl);

        if ($response === false) {
            return 'Curl error: ' . curl_error($curl);
        } else {
            return $response;
        }
    }
}
</code>
</pre><br>

<h2>Single Address</h2>

<p><strong>Single Address Input:</strong></p>

<pre>
<code class="language-json">4600 Silver Hill Rd, Washington, DC 20233
</code></pre><br>

<p><strong>Single Address Output:</strong></p>
<pre>
<code class="language-json">{
  "result": {
    "input": {
      "address": {
        "address": "4600 Silver Hill Rd, Washington, DC 20233"
      },
      "benchmark": {
        "isDefault": true,
        "benchmarkDescription": "Public Address Ranges - Current Benchmark",
        "id": "4",
        "benchmarkName": "Public_AR_Current"
      }
    },
    "addressMatches": [
      {
        "tigerLine": {
          "side": "L",
          "tigerLineId": "76355984"
        },
        "coordinates": {
          "x": -76.92748724230096,
          "y": 38.84601622386617
        },
        "addressComponents": {
          "zip": "20233",
          "streetName": "SILVER HILL",
          "preType": "",
          "city": "WASHINGTON",
          "preDirection": "",
          "suffixDirection": "",
          "fromAddress": "4600",
          "state": "DC",
          "suffixType": "RD",
          "toAddress": "4700",
          "suffixQualifier": "",
          "preQualifier": ""
        },
        "matchedAddress": "4600 SILVER HILL RD, WASHINGTON, DC, 20233"
      }
    ]
  }
}
</code>
</pre><br>

<h2>Multiple Addresses</h2>

<p><strong>Multiple Address Input:</strong></p>

<ul>
    <li>If a component is missing from the dataset, it must still retain the delimited format with a null value.
        Unique ID and Street address are required fields.
    </li>
    <li>If there are commas that are part of one of the fields, the whole field needs to be enclosed in quote marks for proper parsing.
    </li>
    <li>There is currently an upper limit of 10,000 records per batch file.</li>
</ul>

<pre>
<code class="language-text-text"><?php print_r($multiple_address_input); ?>
</code>
</pre><br>

<p><strong>Multiple Address Output:</strong></p>
<p>The CENSUS Geocoder returns each row with the original address followed by the corrected address
    with lon/lat geolocation information as a CSV data string.</p>
<pre>
<code class="language-text"><?php print_r($multiple_address_output); ?>
</code>
</pre><br>






<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>
