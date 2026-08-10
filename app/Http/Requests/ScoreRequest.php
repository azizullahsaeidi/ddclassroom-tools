<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxScore = $this->type == 1 ? 40 : 100;

        return [
            'total' => ['required', 'numeric', 'min:0', "max:$maxScore"],
            'written' => ['nullable', 'numeric', 'min:0'],
            'activity' => ['nullable', 'numeric', 'min:0'],
            'verbal' => ['nullable', 'numeric', 'min:0'],
            'attendance' => ['nullable', 'numeric', 'min:0'],
            'homework' => ['nullable', 'numeric', 'min:0'],
            'evaluation' => ['nullable', 'numeric', 'min:0'],
            'chance' => ['sometimes', 'nullable', 'string', 'in:first,second,third'],
        ];
    }
}
