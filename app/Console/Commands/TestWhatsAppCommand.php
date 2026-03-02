<?php

namespace App\Console\Commands;

use App\Services\WhatsAppSogService;
use Illuminate\Console\Command;

class TestWhatsAppCommand extends Command
{
    protected $signature = 'whatsapp:test {phone=201147544303} {--message=}';
    protected $description = 'Test WhatsApp SOG - send a message to the given phone';

    public function handle(): int
    {
        $service = app(WhatsAppSogService::class);
        if (!$service->isConfigured()) {
            $this->error('WhatsApp SOG غير مُعد. تحقق من WHATSAPP_SOG_ENABLED و APPKEY و AUTHKEY في .env');
            return 1;
        }
        $phone = $this->argument('phone');
        $message = $this->option('message') ?: 'مرحبا، هذه رسالة تجريبية من جرافوني.';
        $this->info("Sending to: {$phone}");
        $result = $service->sendMessage($phone, $message);
        if ($result['success']) {
            $this->info('تم الإرسال بنجاح!');
            return 0;
        }
        $this->error('فشل الإرسال: ' . ($result['error'] ?? 'Unknown'));
        return 1;
    }
}
