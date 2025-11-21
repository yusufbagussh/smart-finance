<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Liability;

class TransactionService
{
    /**
     * Menyesuaikan saldo AKUN (Dompet/Bank) berdasarkan transaksi.
     */
    public function handleAccountBalance(Transaction $transaction, bool $revert = false)
    {
        $amount = $transaction->amount;
        $multiplier = $revert ? -1 : 1;

        if ($transaction->type === 'expense') {
            if ($transaction->source_account_id) {
                Account::where('id', $transaction->source_account_id)
                    ->decrement('current_balance', $amount * $multiplier);
            }
        } elseif ($transaction->type === 'income') {
            if ($transaction->destination_account_id) {
                Account::where('id', $transaction->destination_account_id)
                    ->increment('current_balance', $amount * $multiplier);
            }
        } elseif ($transaction->type === 'transfer') {
            if ($transaction->source_account_id) {
                Account::where('id', $transaction->source_account_id)
                    ->decrement('current_balance', $amount * $multiplier);
            }
            if ($transaction->destination_account_id) {
                Account::where('id', $transaction->destination_account_id)
                    ->increment('current_balance', $amount * $multiplier);
            }
        }
    }

    /**
     * Menyesuaikan saldo HUTANG (Liabilities) saat ada pembayaran/pelunasan.
     */
    public function handleLiabilityBalance(Transaction $transaction, bool $revert = false)
    {
        if (!$transaction->liability_id) return;

        $liability = Liability::find($transaction->liability_id);
        if (!$liability) return;

        $amount = $transaction->amount;
        $multiplier = $revert ? -1 : 1;

        // Expense = Bayar Hutang / Kasih Pinjaman (Saldo hutang berkurang / Saldo piutang bertambah)
        if ($transaction->type === 'expense') {
            if ($liability->type === 'payable') {
                $liability->decrement('current_balance', $amount * $multiplier);
            } else { // receivable
                $liability->increment('current_balance', $amount * $multiplier);
            }
        }
        // Income = Terima Hutang / Terima Bayaran Piutang
        elseif ($transaction->type === 'income') {
            if ($liability->type === 'payable') {
                $liability->increment('current_balance', $amount * $multiplier);
            } else { // receivable
                $liability->decrement('current_balance', $amount * $multiplier);
            }
        }
    }
}
