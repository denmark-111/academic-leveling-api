<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
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
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'is_public' => 'sometimes|boolean',

            // QUESTIONS
            'questions' => 'sometimes|array|min:1',

            'questions.*.question_text' => 'required_with:questions|string',
            'questions.*.type' => 'required_with:questions|string|in:multiple_choice,true_false,identification',
            'questions.*.points' => 'nullable|integer|min:1',
            'questions.*.order' => 'nullable|integer',

            // identification
            'questions.*.correct_answer' => 'nullable|string',

            // choices
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.choice_text' => 'required_with:questions.*.choices|string',
            'questions.*.choices.*.is_correct' => 'required_with:questions.*.choices|boolean',
        ];
    }
}
