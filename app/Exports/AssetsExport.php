<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Asset::with('department')
            ->get()
            ->map(function ($asset) {
                return [
                    $asset->id,
                    $asset->name,
                    $asset->asset_type,
                    $asset->serial_number,
                    $asset->status,
                    $asset->department?->name,
                    $asset->asset_value,
                    $asset->purchase_date?->format('Y-m-d'),
                    $asset->created_at->format('Y-m-d H:i:s'),
                ];
            });
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
            'Value',
            'Purchase Date',
            'Created At',
        ];
    }
}
