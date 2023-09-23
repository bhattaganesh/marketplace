<?php

namespace Marketplace\Services;

class ItemSearchService
{
    private $endpoints;

    public function __construct()
    {
        $this->endpoints = [
            'https://marketplace.test/seller1/items',
            'https://marketplace.test/seller2/items',
        ];
    }

    public function searchItems($searchTerm)
    {
        $allItems = [];

        // Fetch items from each endpoint.
        foreach ($this->endpoints as $endpoint) {
            $items = $this->fetchItemsFromEndpoint($endpoint);
            if (!empty($items)) {
                $allItems = array_merge($allItems, $items);
            }
        }

        // Filter items based on the search term.
        $filteredItems = array_filter($allItems, function ($item) use ($searchTerm) {
            return stripos($item['item_name'], $searchTerm) !== false;
        });

        // Sort the items by price.
        usort($filteredItems, function ($a, $b) {
            return $a['price_of_unit'] - $b['price_of_unit'];
        });

        return $filteredItems;
    }

    private function fetchItemsFromEndpoint($endpoint)
    {
        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
        $context = stream_context_create($contextOptions);
        $output = file_get_contents($endpoint, false, $context);

        if ($output === false) {
            return [];
        }

        // Assuming the output is in JSON format. Decode it.
        $items = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log or handle the JSON decode error accordingly.
            error_log('JSON Decode Error: ' . json_last_error_msg());
            return [];
        }

        if (!isset($items['data']) || !is_array($items['data'])) {
            error_log('Unexpected response format from ' . $endpoint);
            return [];
        }

        // Modify items to include the endpoint.
        foreach ($items['data'] as &$item) {
            $item['seller_endpoint'] = $endpoint;
        }

        return $items['data'];
    }
}
