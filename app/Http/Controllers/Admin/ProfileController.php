<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        try {

            $validated = $request->validate(
                [
                    'first_name' => [
                        'required',
                        'string',
                        'max:100',
                    ],
                    'last_name' => [
                        'required',
                        'string',
                        'max:100',
                    ],
                    'email' => [
                        'required',
                        'email',
                        'max:255',
                        Rule::unique('users', 'email')->ignore($user->id),
                    ],
                    'mobile' => [
                        'required',
                        'string',
                        'max:20',
                    ],
                    'company_name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'profile_image' => [
                        'nullable',
                        'image',
                        'mimes:jpg,jpeg,png,webp',
                        'max:2048',
                    ],
                    'password' => [
                        'nullable',
                        'string',
                        'min:8',
                        'confirmed',
                    ],
                ],
                [
                    'first_name.required' => 'First name is required.',
                    'first_name.max' => 'First name cannot exceed 100 characters.',
                    'last_name.required' => 'Last name is required.',
                    'last_name.max' => 'Last name cannot exceed 100 characters.',
                    'email.required' => 'Email address is required.',
                    'email.email' => 'Please enter a valid email address.',
                    'email.unique' => 'This email address is already registered.',
                    'mobile.required' => 'Mobile number is required.',
                    'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                    'company_name.required' => 'Company name is required.',
                    'profile_image.image' => 'The selected file must be an image.',
                    'profile_image.mimes' => 'Profile image must be JPG, JPEG, PNG or WEBP.',
                    'profile_image.max' => 'Profile image cannot be larger than 2 MB.',
                    'password.min' => 'Password must be at least 8 characters.',
                    'password.confirmed' => 'Password confirmation does not match.',
                ]
            );

            $firstName = trim($validated['first_name']);
            $lastName = trim($validated['last_name']);
            $email = trim($validated['email']);
            $mobile = trim($validated['mobile']);
            $companyName = trim($validated['company_name']);

            $hasTextChanges =
            $user->first_name !== $firstName ||
            $user->last_name !== $lastName ||
            $user->email !== $email ||
            ($user->mobile ?? '') !== $mobile ||
            ($user->company_name ?? '') !== $companyName;

            $hasPasswordChange = $request->filled('password');

            $hasImageChange = $request->hasFile('profile_image');

            if (
                !$hasTextChanges &&
                !$hasPasswordChange &&
                !$hasImageChange
            ) {
                return redirect()
                ->route('admin.profile.edit')
                ->with('warning', 'No changes found. Please update at least one field.');
            }

            $oldImage = $user->profile_image;
            $newImage = null;

            if ($hasImageChange) {
                $newImage = $request
                ->file('profile_image')
                ->store('profiles', 'public');

                if (!$newImage) {
                    return redirect()
                    ->route('admin.profile.edit')
                    ->with('error', 'Profile image could not be uploaded. Please try again.');
                }
            }

            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $email;
            $user->mobile = $mobile;
            $user->company_name = $companyName;

            if ($hasImageChange) {
                $user->profile_image = $newImage;
            }

            if ($hasPasswordChange) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            if (
                $hasImageChange &&
                $oldImage &&
                $oldImage !== $newImage &&
                Storage::disk('public')->exists($oldImage)
            ) {
                Storage::disk('public')->delete($oldImage);
            }

            return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');

        } catch (Throwable $e) {
            return redirect()
            ->route('admin.profile.edit')
            ->withInput()
            ->with('error', 'Unable to update profile. Please try again.');
        }
    }
}
