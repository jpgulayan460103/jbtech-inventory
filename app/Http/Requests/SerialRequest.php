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
            'serial_number' => 'required|unique:item_details,serial_number',
            'quantity' => 'required|min:0|numeric',
            'warehouse_id' => 'required',
        ];
    }
}
