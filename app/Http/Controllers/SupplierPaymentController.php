<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierPaymentController extends Controller
{
    public function store(Request $request, Supplier $supplier)
    {
        if (!Gate::allows('hasRole', ['Admin', 'Manager'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:Cash,Bank Transfer,Cheque,Online',
            'note'           => 'nullable|string|max:500',
        ]);

        $supplier->supplierPayments()->create($validated);

        return redirect()
            ->route('suppliers.show', $supplier->id)
            ->banner('Payment recorded successfully.');
    }

    public function destroy(SupplierPayment $supplierPayment)
    {
        if (!Gate::allows('hasRole', ['Admin', 'Manager'])) {
            abort(403, 'Unauthorized');
        }

        $supplierId = $supplierPayment->supplier_id;
        $supplierPayment->delete();

        return redirect()
            ->route('suppliers.show', $supplierId)
            ->banner('Payment deleted successfully.');
    }
}
