<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DonationProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $donationProgramId = $this->route('donation_program')?->id ?? $this->route('donation_program');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('donation_programs', 'slug')->ignore($donationProgramId),
            ],
            'description' => ['required', 'string'],
            'target_amount' => ['required', 'integer', 'min:0'],
            'collected_amount' => ['nullable', 'integer', 'min:0'],
            'deadline' => ['nullable', 'date'],
            'image' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }
}
