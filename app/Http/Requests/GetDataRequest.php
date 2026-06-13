<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetDataRequest extends FormRequest
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
            //
                'number_of_pump'=> 'required',
                'motor_power'   => 'required',
                'application' => 'required',
                'supply_voltage'   => 'required', 
                'ambient_temp'   => 'required', 
                'stater_type'   => 'required',
                'communication_protocol'   => 'required', 
                'ip_rating'   => 'required', 
                'components'   => 'required', 
                'enclosure'   => 'required', 
        ];
    }
    public function messages()
    {
        return [
            
    'number_of_pump.required' => 'Please Select Number of Pumps',
    'motor_power.required' => 'Please Select  Motor Power',
    'application.required' => 'Please Select Application',
    'supply_voltage.required' => 'Please Select Supply Voltage',
    'ambient_temp.required' => 'Please Select Ambient',
    'stater_type.required' => 'Please Select Stater Type',
    'communication_protocol.required' => 'Please Select Communication Protocol',
    'ip_rating.required' => 'Please Select IP Rating',
    'components.required' => 'Please Select Components',
    'enclosure.required' => 'Please Select Enclosure',
        ];
    }
}
