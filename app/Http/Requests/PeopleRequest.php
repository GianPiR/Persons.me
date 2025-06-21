<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PeopleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $personId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'unique:people,cpf,' . $personId,
                function ($attribute, $value, $fail) {
                    // Remove caracteres não numéricos
                    $cleanValue = preg_replace('/\D/', '', $value);

                    // Verifica se é CPF (11 dígitos) ou CNPJ (14 dígitos)
                    if (!in_array(strlen($cleanValue), [11, 14])) {
                        $fail('O CPF deve ter 11 dígitos ou CNPJ deve ter 14 dígitos.');
                        return;
                    }

                    // Se foi informado o tipo, valida consistência
                    $type = $this->input('type');
                    if ($type) {
                        if ($type === 'individual' && strlen($cleanValue) !== 11) {
                            $fail('Para pessoa física, o CPF deve ter 11 dígitos.');
                        } elseif ($type === 'legal_entity' && strlen($cleanValue) !== 14) {
                            $fail('Para pessoa jurídica, o CNPJ deve ter 14 dígitos.');
                        }
                    }
                }
            ],
            'type' => 'required|in:individual,legal_entity',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'cpf.required' => 'O CPF/CNPJ é obrigatório.',
            'cpf.string' => 'O CPF/CNPJ deve ser um texto.',
            'cpf.unique' => 'Este CPF/CNPJ já está cadastrado.',

            'type.required' => 'O tipo é obrigatório.',
            'type.in' => 'O tipo deve ser "individual" ou "legal_entity".',

            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',

            'email.email' => 'O email deve ter um formato válido.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Dados de validação inválidos.',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
