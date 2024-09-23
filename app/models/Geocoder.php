<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
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
