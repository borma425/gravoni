<?php

return [
    /*
    | عدد الأيام بعدها يُخفى زر رفض/حذف الأوردر المقبول.
    | مثال: 14 = أسبوعين
    */
    'order_reject_delete_days_limit' => (int) env('ORDER_REJECT_DELETE_DAYS_LIMIT', 10),
];
