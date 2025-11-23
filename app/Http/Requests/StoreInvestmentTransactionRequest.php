<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreInvestmentTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan user terotentikasi
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'portfolio_id' => [
                'required',
                'integer',
                // Pastikan portfolio_id yang dikirim adalah milik user yang login
                Rule::exists('portfolios', 'id')->where('user_id', Auth::id())
            ],
            'asset_id' => [
                'required',
                'integer',
                Rule::exists('assets', 'id') // Pastikan asetnya ada
            ],
            'transaction_type' => [
                'required',
                Rule::in(['buy', 'sell'])
            ],
            'transaction_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|numeric|gt:0', // gt:0 = greater than 0
            'price_per_unit' => 'required|numeric|gt:0',
            'fees' => 'nullable|numeric|min:0',
            // Validasi Baru
            'source_account_id' => [
                'nullable',
                'required_if:transaction_type,buy', // Wajib jika Beli
                Rule::exists('accounts', 'id')->where('user_id', Auth::id())
            ],
            'destination_account_id' => [
                'nullable',
                'required_if:transaction_type,sell', // Wajib jika Jual
                Rule::exists('accounts', 'id')->where('user_id', Auth::id())
            ],
        ];
    }
}
