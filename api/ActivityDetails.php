<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);
$comparison_id = $_GET['id'];

$comparison = $DB->store('comparisons')
        ->where('id', '=', $comparison_id)
        ->one()
        ->fetch();

$client = $DB->store('clients')
        ->where('id', '=', $comparison['client_id'])
        ->one()
        ->fetch();

$details = $DB->store('travel_insurance_details')
        ->where('comparison_id', '=', $comparison['id'])
        ->one()
        ->fetch();

$results = $DB->store('comparison_results')
        ->where('comparison_id', '=', $comparison['id'])
        ->one()
        ->fetch();

$recommended = json_decode($results['response']);

$companies = $DB->store('comparison_companies')
        ->where('comparison_id', '=', $comparison['id'])
        ->fetch();

$companies_compared = [];
foreach ($companies as $company) {
    $companies_compared[] = $company['name'];
}

$comparison['destination'] = $details['destinationName'];
$comparison['departure_date'] = $details['departureDate'];
$comparison['return_date'] = $details['returnDate'];
$comparison['travelers_count'] = $details['travelersCount'];
$comparison['client_name'] = $client['name'];
$comparison['insurance_type'] = 'travel';
$comparison['notes'] = $comparison['notes'];
$comparison['recommendation'] =
        (isset($comparison['recommendation'])) ? $comparison['recommendation'] : $recommended->recommendation;
$comparison['comparisons'] = $recommended->comparisons;

$comparison['companies_compared'] = implode(', ', $companies_compared);

echo json_encode($comparison);