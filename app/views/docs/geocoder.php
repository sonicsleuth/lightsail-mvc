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

<h1>Geocoder</h1>

<p>The Census Geocoder provides a programmatic (REST) API to users interested in matching addresses
    to geographic locations and entities containing those addresses. The Census Geocoder only geocodes addresses that
    are within the United States, Puerto Rico, and the U.S. Island Areas.
    Learn more about the Census Geocoder here: <a href="https://geocoding.geo.census.gov/geocoder/">https://geocoding.geo.census.gov/geocoder/</a></p>

<p>Lightsail has a wrapper Model for the Geocoder API here: <strong>/app/core/plugins/Geocoder.php</strong></p>

<p>The Controller that rendered this View (webpage) is a live call to the CENSUS Geocoder API for the following US Addresses.</p>

<h2>Single Address</h2>

<p><strong>How to implement:</strong></p>
<pre>
<code class="language-php">
$Geocoder = new Geocoder();

$single_address_input = "4600 Silver Hill Rd, Washington, DC 20233";

$single_address_output = $Geocoder->geocodeSingleAddress($single_address_input);
</code></pre><br>

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

<p><strong>How to implement:</strong></p>
<pre>
<code class="language-php">
$Geocoder = new Geocoder();

$multiple_address_input =
    array(
        array(
            'id' => '111',
            'street' => '1600 Amphitheatre Parkway',
            'city' => 'Mountain View',
            'state' => 'CA',
            'zip' => '94043',
        ),
        array(
            'id' => '222',
            'street' => '1600 Pennsylvania Avenue',
            'city' => 'Washington',
            'state' => 'DC',
            'zip' => '20500',
        ),
        // Add more addresses as needed
    );

$multiple_address_output = $Geocoder->geocodeMultipleAddresses('temp_csv_filename', $multiple_address_input, 'destination.csv');
</code></pre><br>

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
