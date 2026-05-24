<?php

namespace App\Exports;

use Spatie\ActivityLog\Models\Activity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuditLogsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection()
    {
        $query = Activity::query();

        if (isset($this->filters['user_id'])) {
            $query->where('causer_id', $this->filters['user_id']);
        }
        if (isset($this->filters['model'])) {
            $query->where('subject_type', $this->filters['model']);
        }
        if (isset($this->filters['event'])) {
            $query->where('description', $this->filters['event']);
        }
        if (isset($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (isset($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->with('causer')->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Action',
            'Subject Type',
            'Subject ID',
            'Created At',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->causer?->name ?? 'System',
            $log->description,
            $log->subject_type,
            $log->subject_id,
            $log->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
