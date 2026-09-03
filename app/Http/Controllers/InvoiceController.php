<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Site;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['company', 'site'])->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->get();

        // Calculate Dashboard Stats matching Screenshot 1
        $totalInvoicesCount = Invoice::count();
        $totalInvoicesSum = Invoice::sum('total');

        $totalOverdueCount = Invoice::where('status', 'overdue')->count();
        $totalOverdueSum = Invoice::where('status', 'overdue')->sum('amount_due');

        $paidCount = Invoice::where('status', 'paid')->count();
        $paidSum = Invoice::where('status', 'paid')->sum('total');

        $draftCount = Invoice::where('status', 'draft')->count();
        $draftSum = Invoice::where('status', 'draft')->sum('total');

        return view('admin.invoices.index', compact(
            'invoices',
            'totalInvoicesCount',
            'totalInvoicesSum',
            'totalOverdueCount',
            'totalOverdueSum',
            'paidCount',
            'paidSum',
            'draftCount',
            'draftSum'
        ));
    }

    public function create()
    {
        $companies = Company::orderBy('name', 'asc')->get();
        $nextInvoiceNumber = Invoice::generateNextInvoiceNumber();
        $products = Product::with('tax')->orderBy('name', 'asc')->get();
        $taxes = Tax::orderBy('name', 'asc')->get();

        return view('admin.invoices.create', compact('companies', 'nextInvoiceNumber', 'products', 'taxes'));
    }

    public function getSitesByCompany($company_id)
    {
        $sites = Site::where('company_id', $company_id)->orderBy('name', 'asc')->get(['id', 'name']);
        return response()->json(['sites' => $sites]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'title'          => 'required|string|max:255',
            'summary'        => 'nullable|string',
            'company_id'     => 'required|exists:companies,id',
            'site_id'        => 'nullable|exists:sites,id',
            'invoice_date'   => 'required|date',
            'due_date'       => 'required|date',
            'po_so_number'   => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_service' => 'required|string',
            'items.*.quantity'        => 'required|numeric|min:0',
            'items.*.rate'            => 'required|numeric|min:0',
            'items.*.tax'             => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $taxTotal = 0;

            $itemsData = [];
            foreach ($request->items as $item) {
                $qty = (float) ($item['quantity'] ?? 0);
                $rate = (float) ($item['rate'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $itemSub = $qty * $rate;
                $itemTax = $tax; // tax value
                $itemAmount = $itemSub + $itemTax;

                $subtotal += $itemSub;
                $taxTotal += $itemTax;

                $itemsData[] = [
                    'product_service' => $item['product_service'],
                    'quantity'        => $qty,
                    'rate'            => $rate,
                    'tax'             => $tax,
                    'amount'          => $itemAmount,
                ];
            }

            $grandTotal = $subtotal + $taxTotal;
            $status = $request->input('action_type') === 'draft' ? 'draft' : 'overdue';
            $invoiceNumber = $request->invoice_number ?: Invoice::generateNextInvoiceNumber();

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'title'          => $request->title ?: 'Invoice',
                'summary'        => $request->summary,
                'company_id'     => $request->company_id,
                'site_id'        => $request->site_id,
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'po_so_number'   => $request->po_so_number,
                'subtotal'       => $subtotal,
                'tax_total'      => $taxTotal,
                'total'          => $grandTotal,
                'amount_due'     => $status === 'paid' ? 0.00 : $grandTotal,
                'notes'          => $request->notes,
                'status'         => $status,
            ]);

            foreach ($itemsData as $iData) {
                $invoice->items()->create($iData);
            }

            DB::commit();

            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['company', 'site', 'items'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['company', 'site', 'items'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->download('Invoice_' . $invoice->invoice_number . '.pdf');
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['company', 'site', 'items'])->findOrFail($id);
        $companies = Company::orderBy('name', 'asc')->get();
        $sites = Site::where('company_id', $invoice->company_id)->orderBy('name', 'asc')->get();
        $products = Product::with('tax')->orderBy('name', 'asc')->get();
        $taxes = Tax::orderBy('name', 'asc')->get();

        return view('admin.invoices.edit', compact('invoice', 'companies', 'sites', 'products', 'taxes'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'title'          => 'required|string|max:255',
            'summary'        => 'nullable|string',
            'company_id'     => 'required|exists:companies,id',
            'site_id'        => 'nullable|exists:sites,id',
            'invoice_date'   => 'required|date',
            'due_date'       => 'required|date',
            'po_so_number'   => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_service' => 'required|string',
            'items.*.quantity'        => 'required|numeric|min:0',
            'items.*.rate'            => 'required|numeric|min:0',
            'items.*.tax'             => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $taxTotal = 0;

            $itemsData = [];
            foreach ($request->items as $item) {
                $qty = (float) ($item['quantity'] ?? 0);
                $rate = (float) ($item['rate'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $itemSub = $qty * $rate;
                $itemTax = $tax;
                $itemAmount = $itemSub + $itemTax;

                $subtotal += $itemSub;
                $taxTotal += $itemTax;

                $itemsData[] = [
                    'product_service' => $item['product_service'],
                    'quantity'        => $qty,
                    'rate'            => $rate,
                    'tax'             => $tax,
                    'amount'          => $itemAmount,
                ];
            }

            $grandTotal = $subtotal + $taxTotal;
            $status = $request->input('action_type') === 'draft' 
                ? 'draft' 
                : ($request->input('status') ?: ($invoice->status === 'draft' ? 'overdue' : $invoice->status));

            $invoice->update([
                'invoice_number' => $request->invoice_number,
                'title'          => $request->title ?: 'Invoice',
                'summary'        => $request->summary,
                'company_id'     => $request->company_id,
                'site_id'        => $request->site_id,
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'po_so_number'   => $request->po_so_number,
                'subtotal'       => $subtotal,
                'tax_total'      => $taxTotal,
                'total'          => $grandTotal,
                'amount_due'     => $status === 'paid' ? 0.00 : $grandTotal,
                'notes'          => $request->notes,
                'status'         => $status,
            ]);

            $invoice->items()->delete();
            foreach ($itemsData as $iData) {
                $invoice->items()->create($iData);
            }

            DB::commit();

            return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
