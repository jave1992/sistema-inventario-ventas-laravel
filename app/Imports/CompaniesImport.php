<?php

namespace App\Imports;

use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompaniesImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'razon_social' => 'required|string|max:255',
                'ruc' => 'required|digits:11|unique:companies,ruc',
                'direccion' => 'required|string|max:255',
                'sol_user' => 'required|string|max:255',
                'sol_pass' => 'required|string|max:255',
                'production' => 'required|integer|max:1',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 1,
                    'errors' => $validator->errors()->all(),
                ];

                continue;
            }

            Company::create($data);

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
