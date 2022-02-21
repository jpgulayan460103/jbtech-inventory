<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SerialRequest extends FormRequest
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
            'serial_number' => [
                'required',
                (request()->has('receive_type') && request('receive_type') == "in") ? 'unique:item_details,serial_number,NULL,id,deleted_at,NULL': 'exists:item_details,serial_number,deleted_at,NULL',
            ],
            'quantity' => 'required|min:0|numeric',
            'warehouse_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'serial_number.unique' => 'The serial number is already in the database.',
            'serial_number.exists' => 'The serial number does not exist in the database.'
        ];
    }
}
