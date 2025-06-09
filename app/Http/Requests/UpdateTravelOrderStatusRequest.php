<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelOrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cancel_reason' => 'required_if:status,cancelado|nullable|string|max:500',
            'status' => 'required_without:cancel_reason|in:solicitado,aprovado,cancelado',
        ];
    }
}