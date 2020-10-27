<?php

namespace Uteq\Move\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings as WithHeadingsContract;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Uteq\Move\Concerns\WithChunkCount;
use Uteq\Move\Concerns\WithDisk;
use Uteq\Move\Concerns\WithFilename;
use Uteq\Move\Concerns\WithHeadings;
use Uteq\Move\Exceptions\InvalidExcelDownloadException;

abstract class ExportToExcel extends Action implements FromCollection, WithCustomChunkSize, WithHeadingsContract, WithMapping
{
    use WithChunkCount,
        WithDisk,
        WithFilename,
        WithHeadings;

    public \Uteq\Move\Resource $resource;
    public Collection $collection;

    public function handleLivewireRequest(
        \Uteq\Move\Resource $resource,
        Collection $collection,
        array $actionFields
    )
    {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->withHeadings(array_keys($this->map($collection->first())));

        return $this->handle();
    }

    public function handle()
    {
        $response = Excel::download($this, 'test.xlsx', null, $this->headings());

        if (!$response instanceof BinaryFileResponse || $response->isInvalid()) {
            throw new InvalidExcelDownloadException('Resource could not be exported.', 500);
        }

        return [
            'handle' => function($livewire) use ($response) {
                $livewire->emit('_blank', URL::temporarySignedroute('move.download', now()->addMinutes(1), [
                    'path'     => encrypt($response->getFile()->getPathname()),
                    'filename' => strtolower($livewire->resource()->label() . '-' . now()->isoFormat('DD-MMMM-YYYY')) . '.xlsx',
                ]));
            }
        ];
    }

    public function collection()
    {
        return $this->collection;
    }

    abstract public function map($row): array;
}
