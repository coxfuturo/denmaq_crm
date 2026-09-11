<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if (
                empty($row['company_name']) ||
                empty($row['contact_person']) ||
                empty($row['mobile'])
            ) {
                continue;
            }

            Client::create([
                'company_name' => trim($row['company_name']),

                'contact_person' => trim($row['contact_person']),

                'email' => !empty($row['email'])
                    ? trim($row['email'])
                    : null,

                'mobile' => trim($row['mobile']),

                'alternate_mobile' => !empty($row['alternate_mobile'])
                    ? trim($row['alternate_mobile'])
                    : null,

                'address1' => !empty($row['address1'])
                    ? trim($row['address1'])
                    : null,

                'address2' => !empty($row['address2'])
                    ? trim($row['address2'])
                    : null,

                'city' => !empty($row['city'])
                    ? trim($row['city'])
                    : null,

                'state' => !empty($row['state'])
                    ? trim($row['state'])
                    : null,

                'country' => !empty($row['country'])
                    ? trim($row['country'])
                    : null,

                'pincode' => !empty($row['pincode'])
                    ? trim($row['pincode'])
                    : null,

                'website' => !empty($row['website'])
                    ? trim($row['website'])
                    : null,

                'gst_number' => !empty($row['gst_number'])
                    ? trim($row['gst_number'])
                    : null,

                'pan_number' => !empty($row['pan_number'])
                    ? trim($row['pan_number'])
                    : null,

                'notes' => !empty($row['notes'])
                    ? trim($row['notes'])
                    : null,

                'status' => !empty($row['status'])
                    ? strtolower(trim($row['status']))
                    : 'active',

                'created_by' => Auth::id(),

                'updated_by' => Auth::id(),
            ]);
        }
    }
}