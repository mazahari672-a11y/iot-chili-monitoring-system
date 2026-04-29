<?php
// Include the database configuration file
require 'config.php';

// ThingSpeak API details
$api_key = '49SEP6J5VVH2HOZM'; // Replace with your ThingSpeak API key
$channel_id = '2674121'; // Replace with your ThingSpeak channel ID

// Set the timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

while (true) {
    // Fetch data from ThingSpeak API
    $url = "https://api.thingspeak.com/channels/$channel_id/feeds.json?api_key=$api_key&results=1";
    $json_data = file_get_contents($url);

    // Ensure data is fetched successfully
    if ($json_data) {
        $data = json_decode($json_data, true);

        if (isset($data['feeds'][0])) {
            $feeds = $data['feeds'][0];

            // Extract data
            $temperature = number_format($feeds['field1'], 1); // Temperature
            $humidity = (int)$feeds['field2']; // Humidity
            $soilMoisture1 = $feeds['field3']; // Soil moisture 1
            $soilMoisture2 = $feeds['field4']; // Soil moisture 2
            $latency1 = $feeds['field5'];
            $rssi = $feeds['field6'];
            $iso_timestamp = $feeds['created_at']; // Timestamp from ThingSpeak

            // Create a DateTime object with the UTC timestamp and set timezone to Kuala Lumpur
            $datetime = new DateTime($iso_timestamp, new DateTimeZone('UTC'));
            $datetime->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));

            // Format datetime to MySQL DATETIME format
            $formatted_datetime = $datetime->format('Y-m-d H:i:s');

            // Calculate latency (current time - data time)
            $current_time = time(); // Current server time in Unix timestamp format
            $thingspeak_time = strtotime($iso_timestamp); // ThingSpeak data time in Unix timestamp format
            $latency2 = $current_time - $thingspeak_time; // Calculate latency in seconds
            $latency = $latency1 + $latency2;

            // Check if the timestamp already exists in the database
            $check_query = "SELECT COUNT(*) FROM datahistory WHERE time = ?";
            $stmt_check = $conn->prepare($check_query);
            $stmt_check->bind_param('s', $formatted_datetime);
            $stmt_check->execute();
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($count == 0) {
                // Insert data into the database if timestamp does not exist
                $query = "INSERT INTO datahistory (time, temperature, humidity, soilMoisture1, soilMoisture2, latency, rssi) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                if ($stmt) {
                    $stmt->bind_param('sddiiii', $formatted_datetime, $temperature, $humidity, $soilMoisture1, $soilMoisture2, $latency, $rssi);
                    if ($stmt->execute()) {
                        echo "Data inserted successfully! <br>";
                    } else {
                        echo "Error inserting data: " . $stmt->error . "<br>";
                    }
                } else {
                    echo "Error preparing query: " . $conn->error . "<br>";
                }
            } else {
                echo "Duplicate entry found for timestamp $formatted_datetime. Skipping insertion.<br>";
            }
        } else {
            echo "No data found in the channel.<br>";
        }
    } else {
        echo "Failed to fetch data from ThingSpeak.<br>";
    }

    // Wait for 60 seconds before fetching again
    sleep(60); // Wait for 60 seconds before fetching data again
}
?>
