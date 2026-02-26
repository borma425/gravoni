<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    /**
     * اتصال بقاعدة بيانات store_agent (من .env)
     */
    protected $connection = 'store_agent';

    protected $table = 'chat_sessions';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_id',
        'agent_type',
        'user_name',
        'agent_name',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * أنواع الوكيل: greeting (ذكاء اصطناعي) | human (إنسان بشري)
     */
    public const TYPE_GREETING = 'greeting';
    public const TYPE_HUMAN = 'human';

    /**
     * اسم العميل للعرض (user_name أو user_id إذا لم يتوفر)
     */
    public function getClientNameAttribute(): string
    {
        return $this->user_name ?? $this->user_id ?? 'عميل غير معروف';
    }

    /**
     * هل المحادثة من نوع إنسان بشري؟
     */
    public function getIsHumanAttribute(): bool
    {
        return strtolower($this->agent_type ?? '') === self::TYPE_HUMAN;
    }

    /**
     * نوع المحادثة للعرض في التبديل: "human" | "greeting"
     */
    public function getConversationTypeAttribute(): string
    {
        return $this->is_human ? self::TYPE_HUMAN : self::TYPE_GREETING;
    }
}
