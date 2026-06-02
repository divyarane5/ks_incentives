<?php

namespace App\Exports;

use App\Models\BookingBrokeragePayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BrokeragePaymentsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = BookingBrokeragePayment::with([
            'booking.project',
            'booking.developer'
        ]);

        if (!empty($this->filters['project_id'])) {
            $query->whereHas('booking', function ($q) {
                $q->where(
                    'project_id',
                    $this->filters['project_id']
                );
            });
        }

        if (!empty($this->filters['developer_id'])) {
            $query->whereHas('booking', function ($q) {
                $q->where(
                    'developer_id',
                    $this->filters['developer_id']
                );
            });
        }

        return $query->get()->map(function ($row) {

            return [

                $row->id,

                $row->booking_id,

                optional($row->booking)->client_name,

                optional($row->booking->project)->name,

                optional($row->booking->developer)->name,

                $row->invoice_percent,

                $row->invoice_amount,

                $row->invoice_date,

                $row->bank_received_amount,

                $row->bank_received_date,

                $row->tds_amount,

                $row->status,

                $row->remarks,

                $row->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [

            'ID',
            'Booking ID',
            'Client Name',
            'Project',
            'Developer',
            'Invoice %',
            'Invoice Amount',
            'Invoice Date',
            'Received Amount',
            'Received Date',
            'TDS Amount',
            'Status',
            'Remarks',
            'Created At'
        ];
    }
}