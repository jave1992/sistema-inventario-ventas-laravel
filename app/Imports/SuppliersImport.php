<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'identity_id' => 'required|integer|exists:identities,id',
                'document_number' => 'required|integer',
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 1,
                    'errors' => $validator->errors()->all(),
                ];

                continue;
            }

            Supplier::create($data);

            $this->importedCount++;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
