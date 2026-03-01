<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\WhatsAppSogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SendOrderWhatsAppCommand extends Command
{
    protected $signature = 'whatsapp:send-order {order : Order ID or tracking_id (e.g. 32 or GRV379EC8994)}';
    protected $description = 'إرسال رسالة واتساب مع ملف PDF لطلب مسجّل على Mylerz إلى رقم الإدارة';

    public function handle(): int
    {
        $orderInput = $this->argument('order');
        $order = is_numeric($orderInput)
            ? Order::find($orderInput)
            : Order::where('tracking_id', $orderInput)->first();

        if (!$order) {
            $this->error("الطلب غير موجود: {$orderInput}");
            return 1;
        }

        $barcode = $order->shipping_data['barcode'] ?? null;
        if (!$barcode) {
            $this->error("الطلب #{$order->id} ليس مسجّلاً على Mylerz (لا يوجد باركود)");
            return 1;
        }

        $this->info("الطلب: #{$order->id} | {$order->tracking_id} | {$order->customer_name}");
        $this->info("باركود Mylerz: {$barcode}");
        $this->info("---");

        $service = app(WhatsAppSogService::class);
        if (!$service->isConfigured()) {
            $this->error('WhatsApp SOG غير مُعد.');
            return 1;
        }

        $adminNumber = config('plugins.whatsapp_sog.admin_number', '');
        if (empty($adminNumber)) {
            $this->error('WHATSAPP_SOG_ADMIN_NUMBER غير معرّف في .env');
            return 1;
        }

        $this->info("جاري تحميل PDF من Mylerz...");
        $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
        $pdf = $mylerz->getShippingLabelPdf($order);
        if (!$pdf) {
            $this->error('فشل تحميل ملصق PDF من Mylerz.');
            return 1;
        }

        $this->info("حفظ PDF في التخزين...");
        $filename = 'mylerz-labels/mylerz-' . $order->tracking_id . '.pdf';
        Storage::disk('public')->put($filename, $pdf);
        $pdfUrl = Storage::disk('public')->url($filename);
        $this->line("رابط الملف: {$pdfUrl}");

        $this->info("إرسال واتساب إلى {$adminNumber}...");
        $result = $service->sendAdminOrderNotification($order, null, $pdfUrl);

        if ($result['success']) {
            $this->info('تم الإرسال بنجاح.');
            return 0;
        }

        $this->error('فشل الإرسال: ' . ($result['error'] ?? 'Unknown'));
        $this->warn('ملاحظة: إذا كان الموقع على localhost، رابط PDF لن يكون متاحاً لخوادم WhatsApp.');
        return 1;
    }
}
