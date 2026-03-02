<?php

namespace App\Console\Commands;

use App\Services\WhatsAppSogService;
use Illuminate\Console\Command;

class TestWhatsAppFileCommand extends Command
{
    protected $signature = 'whatsapp:test-file {--url=}';
    protected $description = 'Test WhatsApp SOG - send message + PDF file to admin number (201019434517)';

    protected const SAMPLE_PDF_URL = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';

    /** Minimal valid PDF for testing (when URL fails) */
    protected const MINIMAL_PDF = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF\n";

    public function handle(): int
    {
        $service = app(WhatsAppSogService::class);
        if (!$service->isConfigured()) {
            $this->error('WhatsApp SOG غير مُعد.');
            return 1;
        }

        $adminNumber = config('plugins.whatsapp_sog.admin_number', '201019434517');
        if (empty($adminNumber)) {
            $this->warn('WHATSAPP_SOG_ADMIN_NUMBER غير معرّف - استخدام 201019434517');
            $adminNumber = '201019434517';
        }

        $message = 'اختبار إرسال ملف PDF - ' . now()->format('H:i:s');

        $fileUrl = $this->option('url');
        $fileContent = null;

        if ($fileUrl) {
            $this->info("رابط الملف: {$fileUrl}");
            $result = $service->sendMessage($adminNumber, $message, $fileUrl);
        } else {
            $this->info("جاري تحميل PDF تجريبي...");
            $response = \Illuminate\Support\Facades\Http::timeout(15)->get(self::SAMPLE_PDF_URL);
            $pdfContent = $response->successful() && strlen($response->body()) > 100 ? $response->body() : null;
            if (!$pdfContent) {
                $this->warn("استخدام PDF تجريبي مدمج");
                $pdfContent = self::MINIMAL_PDF;
            }
            $this->info("إرسال كـ binary upload (مثل لوحة SOG)");
            $result = $service->sendMessage($adminNumber, $message, null, $pdfContent, 'sample.pdf');
        }
        $this->info("---");

        if ($result['success']) {
            $this->info('تم الإرسال بنجاح!');
            return 0;
        }

        $this->error('فشل الإرسال: ' . ($result['error'] ?? 'Unknown'));
        return 1;
    }
}
