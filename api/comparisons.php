<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

if ($data !== null) {

    $comparison_id = $DB->store('comparisons')
            ->insert([
                    'user_id' => 1,
                    'client_id' => $data['client']['id'],
                    'prompt' => json_encode($data['prompt']),
                    'status' => 'pending',
                    'created_at' => time()
            ]);

    $data['travel']['comparison_id'] = $comparison_id;
    $DB->store('travel_insurance_details')
            ->insert($data['travel']);

    $data['coverage']['comparison_id'] = $comparison_id;
    foreach ($data['coverage']['optional'] as $optional) {
        $optional['comparison_id'] = $comparison_id;
        $DB->store('comparison_coverage')
                ->insert($optional);
    }

    foreach ($data['companies']['regular'] as $companies) {
        $companies['comparison_id'] = $comparison_id;
        $companies['insurance_company_id'] = $companies['id'];
        unset($companies['id']);

        $DB->store('comparison_companies')
                ->insert($companies);
    }

    foreach ($data['companies']['direct'] as $companies) {
        $companies['comparison_id'] = $comparison_id;
        $companies['insurance_company_id'] = $companies['id'];
        unset($companies['id']);

        $DB->store('comparison_companies')
                ->insert($companies);
    }
}

// Replace with your OpenAI API Key and Assistant ID
$responseMessage = '';

// 1. Create a thread
$thread = apiRequest('https://api.openai.com/v1/threads', $apiKey, []);
if (!$thread || !isset($thread['id'])) {
    $responseMessage = "Error: Couldn't create thread.";
} else {
    $threadId = $thread['id'];

    // 2. Add user message to the thread
    apiRequest("https://api.openai.com/v1/threads/$threadId/messages", $apiKey, [
            'role' => 'user',
            'content' => $data['prompt']
    ]);

    // 3. Run the assistant on the thread
    $run = apiRequest("https://api.openai.com/v1/threads/$threadId/runs", $apiKey, [
            'assistant_id' => $assistantId
    ]);

    $runId = $run['id'] ?? null;

    // 4. Poll until the run completes
    if ($runId) {
        $status = 'queued';
        while (in_array($status, ['queued', 'in_progress'])) {
            sleep(1);
            $runStatus = apiRequest("https://api.openai.com/v1/threads/$threadId/runs/$runId", $apiKey, null, 'GET');
            $status = $runStatus['status'] ?? 'failed';
        }

        // 5. Fetch the messages
        $messages = apiRequest("https://api.openai.com/v1/threads/$threadId/messages", $apiKey, null, 'GET');
        if (!empty($messages['data'])) {
            // Find the latest assistant message
            foreach ($messages['data'] as $msg) {
                if ($msg['role'] === 'assistant') {
                    $responseMessage = $msg['content'][0]['text']['value'] ?? 'No response content.';
                    break;
                }
            }
        } else {
            $responseMessage = 'No messages returned.';
        }
    } else {
        $responseMessage = 'Error creating assistant run.';
    }
}

// Helper function for OpenAI API requests
function apiRequest($url, $apiKey, $data = null, $method = 'POST') {
    $ch = curl_init($url);
    $headers = [
            "Authorization: Bearer $apiKey",
            "OpenAI-Beta: assistants=v2",
            "Content-Type: application/json"
    ];

    if ($method === 'POST' && $data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } else if ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$DB->store('comparison_results')
        ->insert([
                'comparison_id' => $comparison_id,
                'response' => $responseMessage,
                'created_at' => time()
        ]);

echo $responseMessage;
