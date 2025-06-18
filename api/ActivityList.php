<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

$comparisons = $DB->store('comparisons')
        ->where('user_id', '=', $currentUser['id'])
        ->orderBy('DESC', 'id')
        ->fetch();

$response = [];
foreach ($comparisons as $comparison) {
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
//
    $comparison['destination'] = $details['destinationName'];
    $comparison['departure_date'] = $details['departureDate'];
    $comparison['return_date'] = $details['returnDate'];
    $comparison['travelers_count'] = $details['travelersCount'];
    $comparison['client_name'] = $client['name'];
    $comparison['insurance_type'] = 'travel';
    $comparison['recommended_offer'] =
            (isset($comparison['recommendation'])) ? $comparison['recommendation'] : $recommended->recommendation;

    $comparison['companies_compared'] = implode(', ', $companies_compared);

    $comparison['client'] = $DB->store('clients')
            ->where('id', '=', $comparison['client_id'])
            ->one()
            ->fetch();

    $response[] = $comparison;
}

echo json_encode($response);