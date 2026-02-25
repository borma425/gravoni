# Plugins

مجلد التكاملات مع الخدمات الخارجية.

## الهيكل

```
plugins/
├── Payment/               # طرق الدفع
│   └── CashUpCash/
│       └── CashUpCashService.php
└── Shipping/              # شركات الشحن
    ├── Contracts/
    │   └── ShippingProviderInterface.php
    └── Mylerz/
        ├── MylerzClient.php
        └── MylerzService.php
```

## طرق الدفع (plugins/Payment/)

- **CashUpCash/** - التحقق من الدفع قبل تأكيد الطلب (محفظة إلكترونية / InstaPay)

## شركات الشحن (plugins/Shipping/)

- **Mylerz/** - تكامل شركة الشحن Mylerz

## إضافة تكامل شحن جديد

1. أنشئ مجلداً داخل `Shipping/` باسم الشركة (مثلاً `Shipping/NewProvider/`)
2. نفّذ الواجهة `Plugins\Shipping\Contracts\ShippingProviderInterface`
3. أضف الإعدادات في `config/plugins.php`
4. أضف متغيرات البيئة في `.env`
