<?php

namespace App\Exports;

use App\Models\VisitorAnalytic;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AnalyticsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = VisitorAnalytic::query();
        
        if (isset($this->filters['date_range'])) {
            // Apply date filters
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'IP Address',
            'Country',
            'City',
            'Page URL',
            'Device Type',
            'Browser',
            'OS',
            'Visited At'
        ];
    }

    public function map($visitor): array
    {
        return [
            $visitor->id,
            $visitor->ip_address,
            $visitor->country,
            $visitor->city,
            $visitor->page_url,
            $visitor->device_type,
            $visitor->browser,
            $visitor->os,
            $visitor->visited_at
        ];
    }
} 