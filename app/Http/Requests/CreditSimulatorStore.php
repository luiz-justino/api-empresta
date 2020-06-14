<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditSimulatorStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'valor' => 'required|numeric',
            // 'parcelas' => 'required',
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'valor.required' => 'Um valor é obrigatório.',
            'valor.is_numeric' => 'O valor deve ser numérico.',
            // 'parcelas.required' => 'É necessário informar a quantidade de parcelas desejada.',
            // 'parcelas.numeric' => 'O campo parcelas deve ser numérico.',
        ];
    }
}
