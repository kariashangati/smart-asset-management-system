<?php

namespace App\Exports;

use App\Models\Alert;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlertsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection()
    {
        $query = Alert::query();

        if (isset($this->filters['severity'])) {
            $query->where('severity', $this->filters['severity']);
        }
        if (isset($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (isset($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->with('asset')->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asset Name',
            'Alert Type',
            'Severity',
            'Status',
            'Description',
            'Created At',
            'Resolved At',
        ];
    }

    public function map($alert): array
    {
        return [
            $alert->id,
            $alert->asset?->name ?? 'N/A',
            $alert->alert_type,
            $alert->severity,
            $alert->status,
            $alert->description,
            $alert->created_at->format('Y-m-d H:i:s'),
            $alert->resolved_at?->format('Y-m-d H:i:s') ?? 'N/A',
        ];
    }
}
