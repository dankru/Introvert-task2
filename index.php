<?php

require_once(__DIR__ . '/vendor/autoload.php');

$apiKey = "23bc075b710da43f0ffb50ff9e889aed";

$api = new Introvert\ApiClient();
$api->getConfig()->setHost('https://api.s1.yadrocrm.ru/tmp');
$api->getConfig()->setApiKey('key', '23bc075b710da43f0ffb50ff9e889aed ');
echo '<pre>';

function getLeadsByStatusAndDate($statusArray, $dateToday, $dateOffset, $api): array
{

    $leadsByDate = [];
    $leads = getAllLeads($statusArray, $api);

    foreach ($leads["result"] as $lead) {
        $fields = $lead["custom_fields"];
        // filter leads by date
        $fields = array_filter($fields, fn($field) => $field["id"] === 1523113 && $field["values"][0]["value"] > $dateToday->format('Y-m-d') . " 00:00:00"
        && $field["values"][0]["value"] < $dateOffset);
        // if no fields by this date, continue
        if (count($fields) === 0) {
            continue;
        } else {
            // else set custom_fields to contain required field only
            $lead["custom_fields"] = $fields;
            // only date value
            $dateValue = $lead["custom_fields"][0]["values"][0]["value"];
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

function getAllLeads($statusArray, $api): array
{
    $limit = 100;
    $offset = 0;
    $leads = [];
    do {
        $leadsByPage = $api->lead->getAll(8967010, $statusArray, null, null, $limit, $offset);
        $leads = array_merge_recursive($leads, $leadsByPage);
        $count = count($leadsByPage["result"]);
        $offset += $limit;
    } while ($count >= $limit);
    return $leads;
}

// custom field date_test_2 id = 1523113
$dateToday = new DateTime(); // Y-m-d
$dateOffset = new DateTime(); // Y-m-d
$dateOffset->add(new DateInterval('P30D')); // Today + 30 days

getLeadsByStatusAndDate([142, 143], $dateToday, $dateOffset, $api);