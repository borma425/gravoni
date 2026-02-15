<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سياسة الخصوصية - {{ config('app.name', 'Gravoni') }}</title>
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
            border-bottom: 3px solid #3498db;
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
            border-right: 4px solid #3498db;
        }
        .contact-info {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>سياسة الخصوصية</h1>
        
        <div class="last-updated">
            آخر تحديث: {{ date('Y-m-d') }}
        </div>

        <section>
            <h2>1. مقدمة</h2>
            <p>
                نحن في {{ config('app.name', 'Gravoni') }} نحترم خصوصيتك ونلتزم بحماية معلوماتك الشخصية. 
                تشرح هذه السياسة كيفية جمع واستخدام وحماية معلوماتك عند استخدام خدماتنا.
            </p>
        </section>

        <section>
            <h2>2. المعلومات التي نجمعها</h2>
            <h3>2.1 المعلومات التي تقدمها لنا</h3>
            <ul>
                <li>الاسم والعنوان البريدي</li>
                <li>عنوان البريد الإلكتروني</li>
                <li>رقم الهاتف</li>
                <li>أي معلومات أخرى تختار مشاركتها معنا</li>
            </ul>

            <h3>2.2 المعلومات التي نجمعها تلقائياً</h3>
            <ul>
                <li>عنوان IP</li>
                <li>نوع المتصفح ونظام التشغيل</li>
                <li>معلومات عن جهازك</li>
                <li>سجل الأنشطة على الموقع</li>
            </ul>
        </section>

        <section>
            <h2>3. كيفية استخدامنا للمعلومات</h2>
            <p>نستخدم المعلومات التي نجمعها للأغراض التالية:</p>
            <ul>
                <li>توفير وتحسين خدماتنا</li>
                <li>التواصل معك بشأن خدماتنا</li>
                <li>إرسال التحديثات والإشعارات</li>
                <li>تحليل استخدام الموقع لتحسين تجربة المستخدم</li>
                <li>الامتثال للالتزامات القانونية</li>
            </ul>
        </section>

        <section>
            <h2>4. مشاركة المعلومات</h2>
            <p>نحن لا نبيع معلوماتك الشخصية. قد نشارك معلوماتك في الحالات التالية:</p>
            <ul>
                <li>مع مزودي الخدمات الذين يساعدوننا في تشغيل موقعنا</li>
                <li>عندما يتطلب القانون ذلك</li>
                <li>لحماية حقوقنا وممتلكاتنا</li>
                <li>مع موافقتك الصريحة</li>
            </ul>
        </section>

        <section>
            <h2>5. حماية المعلومات</h2>
            <p>
                نتخذ إجراءات أمنية مناسبة لحماية معلوماتك من الوصول غير المصرح به أو التغيير أو الكشف أو التدمير. 
                ومع ذلك، لا يمكن ضمان الأمان الكامل لأي معلومات عبر الإنترنت.
            </p>
        </section>

        <section>
            <h2>6. ملفات تعريف الارتباط (Cookies)</h2>
            <p>
                نستخدم ملفات تعريف الارتباط لتحسين تجربتك على موقعنا. يمكنك تعطيل ملفات تعريف الارتباط 
                من خلال إعدادات المتصفح، ولكن قد يؤثر ذلك على وظائف الموقع.
            </p>
        </section>

        <section>
            <h2>7. حقوقك</h2>
            <p>لديك الحق في:</p>
            <ul>
                <li>الوصول إلى معلوماتك الشخصية</li>
                <li>تصحيح معلوماتك الشخصية</li>
                <li>حذف معلوماتك الشخصية</li>
                <li>الاعتراض على معالجة معلوماتك</li>
                <li>طلب نقل معلوماتك</li>
            </ul>
        </section>

        <section>
            <h2>8. التغييرات على سياسة الخصوصية</h2>
            <p>
                قد نحدث هذه السياسة من وقت لآخر. سنقوم بإشعارك بأي تغييرات جوهرية 
                من خلال نشر السياسة المحدثة على هذه الصفحة.
            </p>
        </section>

        <section>
            <h2>9. الاتصال بنا</h2>
            <div class="contact-info">
                <p>إذا كان لديك أي أسئلة حول سياسة الخصوصية هذه، يرجى الاتصال بنا:</p>
                <p>
                    <strong>البريد الإلكتروني:</strong> privacy@gravoni.com<br>
                    <strong>الموقع:</strong> {{ config('app.url', 'https://gravoni.com') }}
                </p>
            </div>
        </section>

        <a href="{{ url('/') }}" class="back-link">← العودة إلى الصفحة الرئيسية</a>
    </div>
</body>
</html>

