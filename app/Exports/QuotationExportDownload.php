<?php

namespace App\Exports;

use App\Quotation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Throwable;
// use Maatwebsite\Excel\Concerns\FromQuery;

class QuotationExportDownload implements ShouldQueue, FromCollection, WithHeadings
{
    private $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
     public function headings():array {
        return[
            'Id',
            'Sr.no.',
            'date',
            'User Name',
            'Country',
            'Quotation Number',
            'Project Name',
            'Customer Name',
            'Project Country',
            'Project Location',
            'Article Number',
            'full Article Number',
            'Description',
            'Quantity',
            'Unit Price',
            'Total Price',
            'Pump Type',
            'System Pressure',
            'Impeller Material',
            'No Of Pumps',
            'Manifold Material',
            'Seal/Gland Pack',
            'Pump Description',
            'Motor Power',
            'Voltage',
            'Frequency',
            'No Of Poles',
            'Efficicency',
            'Motor Brand',
            'Application',
            'Adder Ids',
            'Mechanical Adder Ids Details',
            'Shipping Charge',
            'Packing charge',
            'Painting charge',
            'Assembly charge',
            'Insulated bearing',
            'Accessories price',
            'Inter company margin',
            'Over head',
            'Mechanical Total Adder Id Price',
            'Total adder id price',
            'CP price',
            'Cable Price',
            'Mechnical System price',
            'Pump price',
            'Pump article number',
            'Ambient Temp',
            'Starter Type',
            'Communication Protocol',
            'Ip Rating',
            'Component',
            'Enclosure',
            'Module',
            'Status',
            'Reason',
        ];
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function failed(Throwable $exception): void
    {
        // handle failed export
    }
}
