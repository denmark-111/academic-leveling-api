<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'nullable|string',
            'grade_level' => 'nullable|string|in:all,g7,g8,g9,g10,g11,g12,college',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
            'timer_mode' => 'nullable|string|in:none,question,quiz',
            'is_question_shuffled' => 'boolean',
            'is_choices_shuffled' => 'boolean',
            'is_public' => 'boolean',

            // QUESTIONS ARRAY
            'questions' => 'required|array|min:1',

            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|string|in:multiple_choice,true_false,identification',
            'questions.*.points' => 'nullable|integer|min:1',
            'questions.*.order' => 'nullable|integer',

            // For identification
            'questions.*.correct_answer' => 'nullable|string',

            // For multiple choice / true_false
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.choice_text' => 'required_with:questions.*.choices|string',
            'questions.*.choices.*.is_correct' => 'required_with:questions.*.choices|boolean',
        ];
    }
}
