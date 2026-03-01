<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsAppSogService;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    /**
     * Send WhatsApp order confirmation to customer + admin notification (after Mylerz success).
     * Fails silently - does not block order flow.
     */
    protected function sendWhatsAppOrderConfirmation(Order $order): void
    {
        try {
            $service = app(WhatsAppSogService::class);
            if (!$service->isConfigured()) {
                \Illuminate\Support\Facades\Log::info('WhatsApp: skipped (not configured or disabled)', ['order_id' => $order->id]);
                return;
            }
            $result = $service->sendOrderConfirmation($order);
            if ($result['success']) {
                \Illuminate\Support\Facades\Log::info('WhatsApp: order confirmation sent', ['order_id' => $order->id, 'phone' => $order->customer_numbers[0] ?? '']);
            } else {
                \Illuminate\Support\Facades\Log::warning('WhatsApp order confirmation failed', [
                    'order_id' => $order->id,
                    'error' => $result['error'] ?? 'Unknown',
                ]);
            }

            $this->sendAdminOrderNotification($service, $order);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp order confirmation exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send admin notification with order details + Mylerz PDF (to fixed admin number).
     */
    protected function sendAdminOrderNotification(WhatsAppSogService $service, Order $order): void
    {
        try {
            if (empty(config('plugins.whatsapp_sog.admin_number'))) {
                return;
            }
            $pdfUrl = null;
            if (config('plugins.whatsapp_sog.admin_send_file', true) && !empty($order->shipping_data['barcode'])) {
                $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
                $pdf = $mylerz->getShippingLabelPdf($order);
                if ($pdf) {
                    $filename = 'mylerz-labels/mylerz-' . $order->tracking_id . '.pdf';
                    Storage::disk('public')->put($filename, $pdf);
                    $pdfUrl = Storage::disk('public')->url($filename);
                }
            }
            $result = $service->sendAdminOrderNotification($order, null, $pdfUrl);
            if ($result['success']) {
                \Illuminate\Support\Facades\Log::info('WhatsApp: admin notification sent', ['order_id' => $order->id]);
            } else {
                \Illuminate\Support\Facades\Log::warning('WhatsApp admin notification failed', [
                    'order_id' => $order->id,
                    'error' => $result['error'] ?? 'Unknown',
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp admin notification exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
