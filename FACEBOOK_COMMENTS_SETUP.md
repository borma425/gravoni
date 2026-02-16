# إعداد الرد التلقائي على تعليقات Facebook

## ✅ الأذونات المتوفرة
لديك جميع الأذونات المطلوبة:
- ✅ `pages_read_engagement` - قراءة التعليقات
- ✅ `pages_manage_posts` - الرد على التعليقات

## 📋 الخطوات المطلوبة في Facebook Developer Console

### 1. تفعيل Webhook للتعليقات

1. اذهب إلى [Facebook Developers](https://developers.facebook.com/)
2. اختر التطبيق الخاص بك
3. اذهب إلى **Webhooks** في القائمة الجانبية
4. اضغط على **Edit Subscription** بجانب Webhook الخاص بك
5. في **Subscription Fields**، تأكد من إضافة:
   - ✅ `messages` (موجود بالفعل لـ Messenger)
   - ✅ `feed` (مطلوب للتعليقات) ← **أضف هذا**
6. احفظ التغييرات

### 2. إعداد متغيرات البيئة

أضف في ملف `.env`:

```env
# تفعيل الرد التلقائي على التعليقات
MESSENGER_AUTO_REPLY_COMMENTS_ENABLED=true
```

### 3. التحقق من Page Access Token

تأكد من أن `MESSENGER_PAGE_ACCESS_TOKEN` في ملف `.env` يحتوي على Page Access Token صحيح وله الصلاحيات المطلوبة.

## 🧪 اختبار الميزة

1. اذهب إلى صفحة Facebook الخاصة بك
2. أنشئ منشور جديد أو اختر منشور موجود
3. اكتب تعليق على المنشور (يمكنك استخدام حساب آخر للاختبار)
4. تحقق من السجلات في `storage/logs/laravel.log` لرؤية ما إذا تم استلام الحدث
5. يجب أن يظهر رد تلقائي على التعليق

## 📝 ملاحظات مهمة

### بنية البيانات من Facebook

Facebook يرسل أحداث التعليقات بهذه البنية:

```json
{
  "object": "page",
  "entry": [
    {
      "id": "PAGE_ID",
      "time": 1234567890,
      "changes": [
        {
          "value": {
            "item": "comment",
            "comment_id": "COMMENT_ID",
            "post_id": "POST_ID",
            "verb": "add",
            "message": "نص التعليق",
            "from": {
              "id": "USER_ID",
              "name": "اسم المستخدم"
            }
          },
          "field": "feed"
        }
      ]
    }
  ]
}
```

### الرد على التعليقات

الكود يستخدم Graph API endpoint:
```
POST https://graph.facebook.com/v18.0/{comment_id}/comments
```

### تفعيل/تعطيل الميزة

- **تفعيل**: `MESSENGER_AUTO_REPLY_COMMENTS_ENABLED=true`
- **تعطيل**: `MESSENGER_AUTO_REPLY_COMMENTS_ENABLED=false`

## 🔍 استكشاف الأخطاء

### المشكلة: لا يتم استلام أحداث التعليقات

**الحل:**
1. تحقق من أن Webhook نشط في Facebook Developer Console
2. تأكد من إضافة `feed` في Subscription Fields
3. تحقق من السجلات في `storage/logs/laravel.log`

### المشكلة: يتم استلام الأحداث لكن لا يتم الرد

**الحل:**
1. تحقق من `MESSENGER_AUTO_REPLY_COMMENTS_ENABLED=true` في `.env`
2. تحقق من `MESSENGER_PAGE_ACCESS_TOKEN` موجود وصحيح
3. تحقق من السجلات للأخطاء

### المشكلة: خطأ في الصلاحيات

**الحل:**
1. تأكد من أن Page Access Token لديه الصلاحيات:
   - `pages_read_engagement`
   - `pages_manage_posts`
2. يمكنك إنشاء Page Access Token جديد من:
   - Facebook Developer Console → Tools → Graph API Explorer
   - أو من إعدادات الصفحة

## 📚 المراجع

- [Facebook Graph API - Comments](https://developers.facebook.com/docs/graph-api/reference/comment)
- [Facebook Webhooks Documentation](https://developers.facebook.com/docs/graph-api/webhooks)

