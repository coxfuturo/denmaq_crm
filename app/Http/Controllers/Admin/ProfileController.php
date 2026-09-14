<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show admin profile.
     */
    public function index()
    {
        $user = auth()->user();

        return view('admin.profile', compact('user'));
    }

    /**
     * Update admin profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Server-Side Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[A-Za-z\s]+$/',
            ],

            'last_name' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                'regex:/^[A-Za-z\s]+$/',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'mobile' => [
                'nullable',
                'regex:/^[0-9]{10,13}$/',
            ],

            'profile_image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
                'confirmed',
            ],
        ], [
            /*
            |--------------------------------------------------------------------------
            | Custom Validation Messages
            |--------------------------------------------------------------------------
            */

            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a valid text.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot be more than 50 characters.',
            'first_name.regex' => 'First name may contain only letters and spaces.',

            'last_name.string' => 'Last name must be a valid text.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot be more than 50 characters.',
            'last_name.regex' => 'Last name may contain only letters and spaces.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address cannot be more than 255 characters.',
            'email.unique' => 'This email address is already in use.',

            'mobile.regex' => 'Mobile number must contain 10 to 13 digits.',

            'profile_image.file' => 'Please upload a valid file.',
            'profile_image.image' => 'Profile image must be a valid image.',
            'profile_image.mimes' => 'Profile image must be JPG, JPEG, PNG, or WEBP.',
            'profile_image.max' => 'Profile image cannot be larger than 2 MB.',

            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot be more than 255 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Basic Information
        |--------------------------------------------------------------------------
        */

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'] ?? null;
        $user->email = $validated['email'];
        $user->mobile = $validated['mobile'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Profile Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_image')) {

            // Delete old profile image
            if ($user->profile_image) {
                Storage::disk('public')->delete(
                    $user->profile_image
                );
            }

            // Store new profile image
            $user->profile_image = $request
                ->file('profile_image')
                ->store('profiles', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {
            $user->password = Hash::make(
                $validated['password']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Save User
        |--------------------------------------------------------------------------
        */

        $user->save();

        return redirect()
            ->route('admin.profile')
            ->with(
                'success',
                'Profile updated successfully.'
            );
    }
}
