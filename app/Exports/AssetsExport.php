<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection()
    {
        $query = Asset::query();

        if (isset($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->with(['department', 'assetValue'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Serial Number',
            'Status',
            'Department',
            'Purchase Price',
            'Current Value',
            'Created At',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->name,
            $asset->asset_type,
            $asset->serial_number,
            $asset->status,
            $asset->department?->name ?? 'N/A',
            $asset->assetValue?->purchase_price ?? 0,
            $asset->assetValue?->current_value ?? 0,
            $asset->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
