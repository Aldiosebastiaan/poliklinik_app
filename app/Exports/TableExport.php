<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TableExport implements FromArray, ShouldAutoSize, WithHeadings
{
    /** @param list<array<int, int|string|null>> $rows
     * @param  list<string>  $headings
     */
    public function __construct(private array $rows, private array $headings) {}

    /** @return list<array<int, int|string|null>> */
    public function array(): array
    {
        return $this->rows;
    }

    /** @return list<string> */
    public function headings(): array
    {
        return $this->headings;
    }
}
