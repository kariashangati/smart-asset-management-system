<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    protected $client;
    protected string $index = 'smart_assets';

    public function __construct()
    {
        try {
            $this->client = ClientBuilder::create()
                ->setHosts(Config::get('integrations.elasticsearch_hosts', ['localhost:9200']))
                ->build();
        } catch (\Exception $e) {
            Log::warning('Elasticsearch connection failed: ' . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Check if client is available
     */
    private function isAvailable(): bool
    {
        return $this->client !== null;
    }

    /**
     * Index an asset document
     * Note: 'type' parameter removed in Elasticsearch v8
     */
    public function indexAsset(array $data): void
    {
        if (!$this->isAvailable()) {
            Log::warning('Elasticsearch not available — skipping asset index.');
            return;
        }

        try {
            $this->client->index([
                'index' => $this->index,
                'id'    => $data['id'],
                'body'  => [
                    'name'          => $data['name'] ?? '',
                    'serial_number' => $data['serial_number'] ?? '',
                    'asset_type'    => $data['asset_type'] ?? '',
                    'status'        => $data['status'] ?? '',
                    'department_id' => $data['department_id'] ?? null,
                    'location'      => $data['location'] ?? '',
                    'created_at'    => $data['created_at'] ?? now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Elasticsearch index error: ' . $e->getMessage(), [
                'asset_id' => $data['id'] ?? null,
            ]);
        }
    }

    /**
     * Search assets
     * Note: 'type' parameter removed in Elasticsearch v8
     */
    public function searchAssets(string $query, array $filters = []): array
    {
        if (!$this->isAvailable()) {
            Log::warning('Elasticsearch not available — returning empty results.');
            return [];
        }

        try {
            $must = [
                [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['name', 'serial_number', 'asset_type', 'location'],
                    ],
                ],
            ];

            $filter = [];

            if (isset($filters['status'])) {
                $filter[] = ['term' => ['status' => $filters['status']]];
            }

            if (isset($filters['department_id'])) {
                $filter[] = ['term' => ['department_id' => $filters['department_id']]];
            }

            $body = [
                'query' => [
                    'bool' => [
                        'must'   => $must,
                        'filter' => $filter,
                    ],
                ],
            ];

            $result = $this->client->search([
                'index' => $this->index,
                'body'  => $body,
            ]);

            return $result['hits']['hits'] ?? [];

        } catch (\Exception $e) {
            Log::error('Elasticsearch search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete an asset from the index
     */
    public function deleteAsset(int $assetId): void
    {
        if (!$this->isAvailable()) {
            return;
        }

        try {
            $this->client->delete([
                'index' => $this->index,
                'id'    => $assetId,
            ]);
        } catch (\Exception $e) {
            Log::error('Elasticsearch delete error: ' . $e->getMessage(), [
                'asset_id' => $assetId,
            ]);
        }
    }
}
