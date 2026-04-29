<?php
header('Content-Type: application/json');

// ThingSpeak API details
$api_key = '49SEP6J5VVH2HOZM';
$channel_id = '2674121';

// Fetch data from ThingSpeak
$url = "https://api.thingspeak.com/channels/$channel_id/feeds.json?api_key=$api_key&results=1";
$json_data = file_get_contents($url);

// Verify if data is received correctly
if ($json_data) {
    $data = json_decode($json_data, true);

    // Check if 'feeds' data exists
    if (isset($data['feeds'][0])) {
        $feeds = $data['feeds'][0];

        // Extract fields and timestamp
        $temperature = number_format($feeds['field1'], 1); // Format temperature to one decimal place
        $humidity = (int)$feeds['field2'];
        $soilMoisture1 = $feeds['field3'];
        $soilMoisture2 = $feeds['field4']; // Change if you have different fields
        $lastUpdate = $feeds['created_at']; // Timestamp from ThingSpeak

        // Return JSON response
        echo json_encode([
            'status' => 'success',
            'temperature' => $temperature,
            'humidity' => $humidity,
            'soilMoisture1' => $soilMoisture1,
            'soilMoisture2' => $soilMoisture2,
            'lastUpdate' => $lastUpdate
        ]);
    } else {
        // Handle the case where data isn't available
        echo json_encode([
            'status' => 'error',
            'message' => 'No data found in the channel.'
        ]);
    }
} else {
    // Handle the case where the ThingSpeak request fails
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve data from ThingSpeak.'
    ]);
}
?>
