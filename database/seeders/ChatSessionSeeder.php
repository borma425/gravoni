<?php

namespace Database\Seeders;

use App\Models\ChatSession;
use Illuminate\Database\Seeder;

class ChatSessionSeeder extends Seeder
{
    /**
     * إضافة محادثات تجريبية لجدول chat_sessions (قاعدة store_agent)
     */
    public function run(): void
    {
        $sessions = [
            ['user_id' => 'test_user_001', 'agent_type' => 'human', 'user_name' => 'أحمد محمد', 'agent_name' => 'سارة'],
            ['user_id' => 'test_user_002', 'agent_type' => 'human', 'user_name' => 'فاطمة علي', 'agent_name' => 'محمد'],
            ['user_id' => 'test_user_003', 'agent_type' => 'greeting', 'user_name' => 'خالد سالم', 'agent_name' => 'نور'],
            ['user_id' => 'test_user_004', 'agent_type' => 'human', 'user_name' => 'مريم حسن', 'agent_name' => 'أحمد'],
            ['user_id' => 'test_user_005', 'agent_type' => 'human', 'user_name' => 'عمر يوسف', 'agent_name' => 'ليلى'],
            ['user_id' => 'test_user_006', 'agent_type' => 'greeting', 'user_name' => 'نورة عبدالله', 'agent_name' => 'سعيد'],
            ['user_id' => 'test_user_007', 'agent_type' => 'human', 'user_name' => 'راشد إبراهيم', 'agent_name' => 'هدى'],
            ['user_id' => 'test_user_008', 'agent_type' => 'human', 'user_name' => 'سلمى كامل', 'agent_name' => 'فهد'],
            ['user_id' => 'test_user_009', 'agent_type' => 'human', 'user_name' => 'تركي فيصل', 'agent_name' => 'ريم'],
            ['user_id' => 'test_user_010', 'agent_type' => 'human', 'user_name' => 'هدى سعد', 'agent_name' => 'عبدالله'],
        ];

        $now = now();
        foreach ($sessions as $i => $session) {
            ChatSession::updateOrCreate(
                ['user_id' => $session['user_id']],
                [
                    'agent_type' => $session['agent_type'],
                    'user_name' => $session['user_name'],
                    'agent_name' => $session['agent_name'],
                    'updated_at' => $now->copy()->subMinutes($i * 5),
                ]
            );
        }

        $this->command->info('تمت إضافة 10 محادثات تجريبية بنجاح.');
    }
}
