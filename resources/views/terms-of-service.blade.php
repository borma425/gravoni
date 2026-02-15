<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شروط الخدمة - {{ config('app.name', 'Gravoni') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2.5em;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 15px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.8em;
        }
        h3 {
            color: #555;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.3em;
        }
        p {
            margin-bottom: 15px;
            text-align: justify;
        }
        ul, ol {
            margin-right: 30px;
            margin-bottom: 15px;
        }
        li {
            margin-bottom: 10px;
        }
        .last-updated {
            color: #7f8c8d;
            font-style: italic;
            margin-bottom: 30px;
            padding: 10px;
            background: #ecf0f1;
            border-right: 4px solid #e74c3c;
        }
        .contact-info {
            background: #ffe8e8;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .warning {
            background: #fff3cd;
            border-right: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>شروط الخدمة</h1>
        
        <div class="last-updated">
            آخر تحديث: {{ date('Y-m-d') }}
        </div>

        <section>
            <h2>1. القبول</h2>
            <p>
                من خلال الوصول إلى واستخدام موقع {{ config('app.name', 'Gravoni') }} وخدماته، 
                فإنك تقبل وتوافق على الالتزام بشروط الخدمة هذه. إذا كنت لا توافق على هذه الشروط، 
                يرجى عدم استخدام خدماتنا.
            </p>
        </section>

        <section>
            <h2>2. استخدام الخدمة</h2>
            <h3>2.1 الأهلية</h3>
            <p>يجب أن تكون:</p>
            <ul>
                <li>عمرك 18 عاماً على الأقل، أو لديك موافقة الوالدين</li>
                <li>قادراً قانونياً على الدخول في اتفاقية ملزمة</li>
                <li>غير محظور من استخدام الخدمة بموجب القانون</li>
            </ul>

            <h3>2.2 حساب المستخدم</h3>
            <p>أنت مسؤول عن:</p>
            <ul>
                <li>الحفاظ على سرية معلومات حسابك</li>
                <li>جميع الأنشطة التي تحدث تحت حسابك</li>
                <li>إبلاغنا فوراً بأي استخدام غير مصرح به</li>
            </ul>
        </section>

        <section>
            <h2>3. قواعد الاستخدام</h2>
            <div class="warning">
                <strong>تحذير:</strong> يحظر عليك استخدام خدماتنا لأي غرض غير قانوني أو غير مصرح به.
            </div>
            <p>يُحظر عليك:</p>
            <ul>
                <li>انتهاك أي قوانين أو لوائح محلية أو دولية</li>
                <li>انتهاك حقوق الملكية الفكرية للآخرين</li>
                <li>إرسال محتوى ضار أو خبيث أو فيروسي</li>
                <li>محاولة الوصول غير المصرح به إلى أنظمتنا</li>
                <li>استخدام الخدمة لإرسال رسائل غير مرغوب فيها</li>
                <li>انتحال شخصية أي شخص أو كيان</li>
                <li>التدخل في عمل الخدمة أو تعطيلها</li>
            </ul>
        </section>

        <section>
            <h2>4. المحتوى</h2>
            <h3>4.1 محتوى المستخدم</h3>
            <p>
                أنت تحتفظ بحقوق الملكية للمحتوى الذي تنشره. من خلال نشر المحتوى، 
                تمنحنا ترخيصاً غير حصري لاستخدامه وتوزيعه وعرضه.
            </p>

            <h3>4.2 محتوى الخدمة</h3>
            <p>
                جميع حقوق الملكية الفكرية في الخدمة ومحتواها مملوكة لنا أو لمرخصينا. 
                لا يجوز لك نسخ أو تعديل أو توزيع محتوى الخدمة دون إذن كتابي.
            </p>
        </section>

        <section>
            <h2>5. إخلاء المسؤولية</h2>
            <p>
                نقدم الخدمة "كما هي" و"كما هو متاح". لا نضمن أن الخدمة ستكون:
            </p>
            <ul>
                <li>غير متقطعة أو خالية من الأخطاء</li>
                <li>آمنة من الفيروسات أو المكونات الضارة</li>
                <li>تلبي احتياجاتك الخاصة</li>
            </ul>
            <p>
                لن نكون مسؤولين عن أي أضرار مباشرة أو غير مباشرة ناتجة عن استخدام أو عدم القدرة 
                على استخدام الخدمة.
            </p>
        </section>

        <section>
            <h2>6. الحد من المسؤولية</h2>
            <p>
                في أقصى حد يسمح به القانون، لن تكون مسؤوليتنا الإجمالية تجاهك تتجاوز المبلغ 
                الذي دفعته لنا في الـ 12 شهراً السابقة.
            </p>
        </section>

        <section>
            <h2>7. الإنهاء</h2>
            <p>نحتفظ بالحق في:</p>
            <ul>
                <li>إيقاف أو تعليق حسابك في أي وقت</li>
                <li>إنهاء أو تعليق وصولك إلى الخدمة</li>
                <li>حذف أي محتوى ينتهك هذه الشروط</li>
            </ul>
            <p>
                يمكنك أيضاً إنهاء استخدامك للخدمة في أي وقت عن طريق إلغاء حسابك.
            </p>
        </section>

        <section>
            <h2>8. التعديلات</h2>
            <p>
                نحتفظ بالحق في تعديل هذه الشروط في أي وقت. سنقوم بإشعارك بأي تغييرات جوهرية 
                من خلال نشر الشروط المحدثة على هذه الصفحة.
            </p>
        </section>

        <section>
            <h2>9. القانون الحاكم</h2>
            <p>
                تخضع هذه الشروط وتفسر وفقاً لقوانين [البلد/المنطقة]. أي نزاعات تنشأ من هذه الشروط 
                ستخضع للولاية القضائية الحصرية للمحاكم في [المدينة/المنطقة].
            </p>
        </section>

        <section>
            <h2>10. الاتصال بنا</h2>
            <div class="contact-info">
                <p>إذا كان لديك أي أسئلة حول شروط الخدمة هذه، يرجى الاتصال بنا:</p>
                <p>
                    <strong>البريد الإلكتروني:</strong> legal@gravoni.com<br>
                    <strong>الموقع:</strong> {{ config('app.url', 'https://gravoni.com') }}
                </p>
            </div>
        </section>

        <a href="{{ url('/') }}" class="back-link">← العودة إلى الصفحة الرئيسية</a>
    </div>
</body>
</html>

