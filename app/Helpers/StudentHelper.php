<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // 👈 add this

class StudentHelper
{
    /**
     * Validate student data and return structured response.
     *
     * @param  array  $data
     * @return array
     */
    public static function validate(array $data): array
    {
        $isUpdate = isset($data['id']) && !empty($data['id']);

        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                Rule::unique('students', 'email')
                    ->ignore((string)($data['id'] ?? ''), '_id'),
            ],

            'phone' => 'nullable|string|max:20',

            'roll_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'roll_number')
                    ->ignore((string)($data['id'] ?? ''), '_id'),
            ],

            'age'             => 'nullable|integer|min:1|max:100',
            'gender'          => 'nullable|in:male,female,other',
            'date_of_birth'   => 'nullable|date',
            'admission_date'  => 'nullable|date',
            'class_time'      => 'nullable|date_format:H:i',
            'address'         => 'nullable|string|max:255',
            'bio'             => 'nullable|string|max:500',
            'course'          => 'nullable|string|max:100',
            'department'      => 'nullable|string|max:50',
            'batch'           => 'nullable|integer|min:2000|max:2100',
            'is_active'       => 'boolean',
            'has_scholarship' => 'boolean',
            'grade'           => 'nullable|numeric|min:0|max:100',
            'website'         => 'nullable|url',
            'favorite_color'  => 'nullable|string|max:20',
            'password'        => $isUpdate ? 'nullable|min:6' : 'required|min:6',
            'hobbies'         => 'nullable|array',
            'profile_photo'   => 'nullable|string|max:255',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Log::warning('Student validation failed', [
                'errors' => $validator->errors(),
                'input'  => $data,
            ]);

            return [
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ];
        }

        $validatedData = $validator->validated();

        // ✅ Ensure profile_photo is preserved if present in $data
        if (array_key_exists('profile_photo', $data)) {
            $validatedData['profile_photo'] = $data['profile_photo'];
        }

        // 👇 Add logging here to confirm
        Log::info('Validated Student Data (StudentHelper)', [
            'id'             => $data['id'] ?? null,
            'profile_photo'  => $validatedData['profile_photo'] ?? null,
            'has_photo_key'  => array_key_exists('profile_photo', $validatedData),
        ]);

        return [
            'status'  => true,
            'message' => 'Validation successful',
            'data'    => $validatedData,
        ];
    }
}
