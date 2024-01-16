<?php

// This scripts gets all leads with transfered statuses from today to 30 days in the future and returns json object,
// containing the amount of leads for each timestamp date, date as key, leads count as value

require_once(__DIR__ . '/vendor/autoload.php');

$apiKey = "23bc075b710da43f0ffb50ff9e889aed";

$api = new Introvert\ApiClient();
$api->getConfig()->setHost('https://api.s1.yadrocrm.ru/tmp');
$api->getConfig()->setApiKey('key', '23bc075b710da43f0ffb50ff9e889aed ');

// Main function,
function getLeadsByStatusAndDate($customFieldId, $statusArray, $api): array
{

    $dateToday = new DateTime(); // Y-m-d
    $dateOffset = new DateTime(); // Y-m-d
    $dateOffset->add(new DateInterval('P30D')); // Today + 30 days


    $leadsByDate = [];
    $leads = getAllLeads($statusArray, $api);

    foreach ($leads["result"] as $lead) {
        $fields = $lead["custom_fields"];
        // filter leads by date from chosen field
        $fields = array_filter($fields, fn($field) => $field["id"] === $customFieldId && $field["values"][0]["value"] > $dateToday->format('Y-m-d') . " 00:00:00"
        && $field["values"][0]["value"] < $dateOffset);
        // if no fields with this date, continue
        if (count($fields) === 0) {
            continue;
        } else {
            // else set custom_fields to contain required field only
            $lead["custom_fields"] = $fields;
            // only date value
            $dateValue = strtotime($lead["custom_fields"][0]["values"][0]["value"]);
            // count leads by date with associative array
            if (isset($leadsByDate[$dateValue])) {
                $leadsByDate[$dateValue] += 1;
            } else {
                $leadsByDate[$dateValue] = 1;
            }
        }
    }
    return $leadsByDate;
}

// Get all leads in a loop
function getAllLeads($statusArray, $api): array
{
    $limit = 100;
    $offset = 0;
    $leads = [];
    do {
        // user id hardcoded 8967010
        $leadsByPage = $api->lead->getAll(8967010, $statusArray, null, null, $limit, $offset);
        $leads = array_merge_recursive($leads, $leadsByPage);
        $count = count($leadsByPage["result"]);
        $offset += $limit;
    } while ($count >= $limit);
    return $leads;
}


echo json_encode(getLeadsByStatusAndDate(1523113, [142, 143], $api));