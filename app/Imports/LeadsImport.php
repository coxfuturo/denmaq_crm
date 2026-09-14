<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $userId = auth()->id();

        foreach ($rows as $row) {
            if (empty($row['name']) && empty($row['phone'])) {
                continue;
            }

            Lead::create([
                'name' => $row['name'] ?? null,
                'company_name' => $row['company_name'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'alternate_phone' => $row['alternate_phone'] ?? null,
                'source' => $row['source'] ?? null,
                'service' => $row['service'] ?? null,
                'status' => $row['status'] ?? 'New',
                'assigned_to' => $row['assigned_to'] ?? null,
                'follow_up_date' => $row['follow_up_date'] ?? null,
                'budget' => $row['budget'] ?? null,
                'notes' => $row['notes'] ?? null,
                'created_by' => $userId,
            ]);
        }
    }
}
