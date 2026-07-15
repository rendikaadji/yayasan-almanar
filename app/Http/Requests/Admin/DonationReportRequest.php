<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DonationReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donation_program_id' => ['required', 'exists:donation_programs,id'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ];
    }
}
