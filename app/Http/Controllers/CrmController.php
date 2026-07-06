<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CustomerComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CrmController extends Controller
{
    /**
     * Tampilkan database pelanggan internal
     */
    public function index(): View
    {
        $customers = Order::select(
            'orders.customer_phone',
            DB::raw('MAX(orders.customer_name) as customer_name'),
            DB::raw('MAX(orders.customer_address) as customer_address'),
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('MAX(orders.created_at) as last_order_date'),
            DB::raw('SUM(orders.shipping_cost + COALESCE((SELECT SUM(quantity * price) FROM order_items WHERE order_items.order_id = orders.id), 0)) as total_spent'),
            DB::raw('(SELECT status FROM orders o2 WHERE o2.customer_phone = orders.customer_phone ORDER BY o2.created_at DESC LIMIT 1) as last_shipping_status'),
            DB::raw('(SELECT payment_status FROM orders o3 WHERE o3.customer_phone = orders.customer_phone ORDER BY o3.created_at DESC LIMIT 1) as last_payment_status')
        )
        ->groupBy('orders.customer_phone')
        ->orderBy('last_order_date', 'desc')
        ->get()
        ->toArray();

        $totalCustomers = count($customers);
        $vipCount = 0;
        $atRiskCount = 0;

        foreach ($customers as &$customer) {
            $lastOrder = \Carbon\Carbon::parse($customer['last_order_date']);
            $isAtRisk = $lastOrder->diffInDays(now()) > 60;
            
            if ($isAtRisk) {
                $customer['segment'] = 'At Risk';
                $atRiskCount++;
            } elseif ($customer['total_spent'] >= 1000000 && $customer['total_orders'] >= 5) {
                $customer['segment'] = 'VIP';
                $vipCount++;
            } elseif ($customer['total_orders'] >= 3) {
                $customer['segment'] = 'Loyal';
            } else {
                $customer['segment'] = 'New';
            }
        }

        $activeComplaintsCount = CustomerComplaint::where('status', '!=', 'resolved')->count();

        return view('crm.index', compact('customers', 'totalCustomers', 'vipCount', 'atRiskCount', 'activeComplaintsCount'));
    }

    /**
     * Tampilkan detail pelanggan, riwayat pesanan, dan keluhan
     */
    public function showDetail(string $phone): View
    {
        $orders = Order::with('items')
            ->where('customer_phone', $phone)
            ->latest()
            ->get();

        if ($orders->isEmpty()) {
            abort(404, 'Pelanggan tidak ditemukan.');
        }

        $customerName = $orders->first()->customer_name;
        $customerAddress = $orders->first()->customer_address;
        
        $totalOrders = $orders->count();
        $totalSpent = 0;
        foreach ($orders as $order) {
            $itemsTotal = $order->items->sum(fn($i) => $i->pivot->quantity * $i->pivot->price);
            $totalSpent += $order->shipping_cost + $itemsTotal;
        }

        $lastOrderDate = $orders->first()->created_at;
        $isAtRisk = $lastOrderDate->diffInDays(now()) > 60;
        
        if ($isAtRisk) {
            $segment = 'At Risk';
        } elseif ($totalSpent >= 1000000 && $totalOrders >= 5) {
            $segment = 'VIP';
        } elseif ($totalOrders >= 3) {
            $segment = 'Loyal';
        } else {
            $segment = 'New';
        }

        $complaints = CustomerComplaint::where('customer_phone', $phone)
            ->latest()
            ->get();

        return view('crm.detail', compact(
            'phone',
            'customerName',
            'customerAddress',
            'orders',
            'totalOrders',
            'totalSpent',
            'segment',
            'complaints'
        ));
    }

    /**
     * Tampilkan daftar keluhan customer
     */
    public function complaints(Request $request): View
    {
        $status = $request->input('status', 'all');
        $query = CustomerComplaint::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $complaints = $query->latest()->get();

        $counts = [
            'all' => CustomerComplaint::count(),
            'open' => CustomerComplaint::where('status', 'open')->count(),
            'in_progress' => CustomerComplaint::where('status', 'in_progress')->count(),
            'resolved' => CustomerComplaint::where('status', 'resolved')->count(),
        ];

        // Dapatkan data order terakhir untuk memudahkan pengisian manual form keluhan
        $recentOrders = Order::latest()->take(50)->get();

        return view('crm.complaints', compact('complaints', 'counts', 'status', 'recentOrders'));
    }

    /**
     * Simpan keluhan customer baru
     */
    public function storeComplaint(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'customer_phone' => 'required|string',
            'customer_name' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        CustomerComplaint::create($validated);

        return back()->with('status', 'Keluhan customer berhasil ditambahkan.');
    }

    /**
     * Update status keluhan
     */
    public function updateComplaintStatus(string $id, Request $request): RedirectResponse
    {
        $complaint = CustomerComplaint::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
        ]);

        $updateData = ['status' => $validated['status']];
        if ($validated['status'] === 'resolved') {
            $updateData['resolved_at'] = now();
        } else {
            $updateData['resolved_at'] = null;
        }

        $complaint->update($updateData);

        return back()->with('status', 'Status keluhan berhasil diperbarui.');
    }

    /**
     * Halaman template pesan WhatsApp CRM
     */
    public function showTemplates(Request $request): View
    {
        $orderId = $request->input('order_id');
        $order = null;
        if ($orderId) {
            $order = Order::with('items')->find($orderId);
        }

        // Template default
        $templates = [
            'process' => [
                'title' => 'Pesanan Diproses',
                'body' => "Halo *{customer_name}*, terima kasih telah berbelanja di MitraSpace. Pesanan Anda *{order_number}* saat ini sedang kami siapkan untuk dikirim. Terima kasih!",
            ],
            'shipping' => [
                'title' => 'Resi Pengiriman',
                'body' => "Halo *{customer_name}*, pesanan Anda *{order_number}* telah diserahkan ke kurir *{courier}* (*{service}*). Nomor resi pengiriman Anda adalah *{waybill}*. Silakan lacak paket Anda secara berkala. Terima kasih!",
            ],
            'thankyou' => [
                'title' => 'Terima Kasih / Selesai',
                'body' => "Halo *{customer_name}*, pesanan Anda *{order_number}* telah sukses terkirim dan diterima. Semoga menyukai produk kami! Ulasan Anda sangat berarti bagi kami. Selamat berbelanja kembali di MitraSpace!",
            ],
        ];

        // Format pesan jika order dipilih
        $formattedTemplates = [];
        if ($order) {
            foreach ($templates as $key => $tpl) {
                $text = $tpl['body'];
                $text = str_replace('{customer_name}', $order->customer_name, $text);
                $text = str_replace('{order_number}', $order->order_number, $text);
                $text = str_replace('{courier}', strtoupper($order->courier), $text);
                $text = str_replace('{service}', $order->shipping_service, $text);
                $text = str_replace('{waybill}', $order->waybill ?? '[BELUM TERBIT]', $text);
                
                $formattedTemplates[$key] = [
                    'title' => $tpl['title'],
                    'original' => $tpl['body'],
                    'formatted' => $text,
                    'wa_link' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $order->customer_phone) . '?text=' . urlencode($text),
                ];
            }
        }

        return view('crm.templates', compact('order', 'templates', 'formattedTemplates'));
    }
}
