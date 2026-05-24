<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Config;

class ElasticsearchService
{
    protected $client;
    protected $index = 'smart_assets';

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(Config::get('integrations.elasticsearch_hosts'))
            ->build();
    }

    /**
     * Index an asset
     */
    public function indexAsset(array $data): void
    {
        $this->client->index([
            'index' => $this->index,
            'type' => 'asset',
            'id' => $data['id'],
            'body' => [
                'name' => $data['name'] ?? '',
                'serial_number' => $data['serial_number'] ?? '',
                'asset_type' => $data['asset_type'] ?? '',
                'status' => $data['status'] ?? '',
                'department_id' => $data['department_id'] ?? null,
                'location' => $data['location'] ?? '',
                'created_at' => $data['created_at'] ?? now(),
            ],
        ]);
    }

    /**
     * Search assets
     */
    public function searchAssets(string $query, array $filters = []): array
    {
        $body = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['name', 'serial_number', 'asset_type', 'location'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Add filters
        if (isset($filters['status'])) {
            $body['query']['bool']['filter'][] = [
                'term' => ['status' => $filters['status']],
            ];
        }

        if (isset($filters['department_id'])) {
            $body['query']['bool']['filter'][] = [
                'term' => ['department_id' => $filters['department_id']],
            ];
        }

        $result = $this->client->search([
            'index' => $this->index,
            'type' => 'asset',
            'body' => $body,
        ]);

        return $result['hits']['hits'] ?? [];
    }

    /**
     * Delete asset from index
     */
    public function deleteAsset(int $assetId): void
    {
        $this->client->delete([
            'index' => $this->index,
            'type' => 'asset',
            'id' => $assetId,
        ]);
    }
}
