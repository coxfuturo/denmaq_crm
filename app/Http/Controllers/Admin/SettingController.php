<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show Company Settings.
     */
    public function company()
    {
        $company = Company::first();

        return view('admin.settings.company', compact('company'));
    }

    /**
     * Update Company Settings.
     */
    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $company = Company::first();

        if (!$company) {
            $company = new Company();
        }

        $company->name = $validated['name'];
        $company->email = $validated['email'] ?? null;
        $company->phone = $validated['phone'] ?? null;
        $company->website = $validated['website'] ?? null;
        $company->address = $validated['address'] ?? null;
        $company->city = $validated['city'] ?? null;
        $company->state = $validated['state'] ?? null;
        $company->country = $validated['country'] ?? null;
        $company->postal_code = $validated['postal_code'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Company Logo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {

            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            $company->logo = $request
                ->file('logo')
                ->store('company', 'public');
        }

        $company->save();

        return redirect()
            ->route('admin.settings.company')
            ->with(
                'success',
                'Company settings updated successfully.'
            );
    }
}
