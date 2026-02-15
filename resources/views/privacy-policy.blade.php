<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - {{ config('app.name', 'Gravoni') }}</title>
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
            font-size: 1.5em;
        }
        h3 {
            color: #555;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        p {
            margin-bottom: 15px;
            text-align: justify;
        }
        ul, ol {
            margin-left: 30px;
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
            border-left: 4px solid #3498db;
        }
        .highlight-box {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        .warning-box {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Privacy Policy</h1>
        
        <div class="last-updated">
            Last Updated: {{ date('F d, Y') }}
        </div>

        <section>
            <h2>1. Introduction</h2>
            <p>
                Welcome to {{ config('app.name', 'Gravoni') }}. This Privacy Policy explains how we collect, use, 
                and protect your information when you interact with our automated messaging service through 
                Facebook Messenger.
            </p>
            <p>
                By using our Messenger service, you agree to the collection and use of information in accordance 
                with this policy.
            </p>
        </section>

        <section>
            <h2>2. Information We Collect</h2>
            <p>When you interact with our Messenger bot, we may collect:</p>
            
            <h3>2.1 Message Content</h3>
            <ul>
                <li>Text messages you send to our page</li>
                <li>Your Facebook Messenger User ID (PSID)</li>
                <li>Timestamp of messages</li>
            </ul>

            <h3>2.2 Profile Information</h3>
            <ul>
                <li>Your public Facebook profile name (if available)</li>
                <li>Profile picture URL (if available)</li>
            </ul>

            <div class="highlight-box">
                <strong>Note:</strong> We do NOT collect your email address, phone number, or any other 
                personal information beyond what is necessary for the automated response service.
            </div>
        </section>

        <section>
            <h2>3. Purpose of Data Collection</h2>
            <p>We collect and process your data solely for the following purposes:</p>
            
            <table>
                <tr>
                    <th>Purpose</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td><strong>Automated Responses</strong></td>
                    <td>To provide instant automated replies to your inquiries</td>
                </tr>
                <tr>
                    <td><strong>AI Processing</strong></td>
                    <td>To generate intelligent and relevant responses using AI services</td>
                </tr>
                <tr>
                    <td><strong>Service Improvement</strong></td>
                    <td>To improve the quality of our automated responses</td>
                </tr>
            </table>
        </section>

        <section>
            <h2>4. Third-Party AI Service Providers</h2>
            <p>
                To provide intelligent automated responses, your messages may be processed by the following 
                third-party AI service providers:
            </p>
            
            <div class="warning-box">
                <h3>OpenAI</h3>
                <p>
                    Your messages may be sent to OpenAI's API for processing and generating responses.
                    <br><a href="https://openai.com/privacy" target="_blank">OpenAI Privacy Policy</a>
                </p>
            </div>

            <div class="warning-box">
                <h3>Google Gemini (Google AI)</h3>
                <p>
                    Your messages may be sent to Google's Gemini AI for processing and generating responses.
                    <br><a href="https://policies.google.com/privacy" target="_blank">Google Privacy Policy</a>
                </p>
            </div>

            <p>
                <strong>Important:</strong> These AI providers process your messages solely to generate responses. 
                We do not share any additional personal information with these services.
            </p>
        </section>

        <section>
            <h2>5. Data Storage and Retention</h2>
            
            <h3>5.1 Storage</h3>
            <p>
                Your messages are temporarily stored on our secure servers for the purpose of providing 
                the automated response service.
            </p>

            <h3>5.2 Retention Period</h3>
            <div class="highlight-box">
                <p><strong>We retain your messages for a maximum of 30 days.</strong></p>
                <p>After this period, all message data is automatically deleted from our systems.</p>
            </div>

            <h3>5.3 What We Store</h3>
            <ul>
                <li>Message content (text only)</li>
                <li>Sender ID (Facebook PSID)</li>
                <li>Timestamp</li>
                <li>Response sent</li>
            </ul>
        </section>

        <section>
            <h2>6. Data Security</h2>
            <p>We implement appropriate security measures to protect your data:</p>
            <ul>
                <li>Encrypted data transmission (HTTPS/SSL)</li>
                <li>Secure server infrastructure</li>
                <li>Limited access to data (authorized personnel only)</li>
                <li>Regular security audits</li>
            </ul>
        </section>

        <section>
            <h2>7. Your Rights</h2>
            <p>You have the following rights regarding your data:</p>
            
            <h3>7.1 Right to Access</h3>
            <p>You can request a copy of all data we have collected about you.</p>

            <h3>7.2 Right to Deletion</h3>
            <p>
                You can request the deletion of all your data at any time. To request deletion:
            </p>
            <div class="highlight-box">
                <p><strong>How to Request Data Deletion:</strong></p>
                <ol>
                    <li>Send a message to our page saying "DELETE MY DATA"</li>
                    <li>Or email us at: <strong>privacy@gravoni.com</strong></li>
                    <li>Include your Facebook name or Messenger ID if emailing</li>
                </ol>
                <p>We will process your request within <strong>48 hours</strong>.</p>
            </div>

            <h3>7.3 Right to Opt-Out</h3>
            <p>
                You can stop using our Messenger service at any time. Simply stop sending messages 
                to our page, and no new data will be collected.
            </p>
        </section>

        <section>
            <h2>8. Children's Privacy</h2>
            <p>
                Our service is not intended for children under 13 years of age. We do not knowingly 
                collect personal information from children under 13. If you are a parent or guardian 
                and believe your child has provided us with personal information, please contact us.
            </p>
        </section>

        <section>
            <h2>9. Changes to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. We will notify you of any changes 
                by posting the new Privacy Policy on this page and updating the "Last Updated" date.
            </p>
        </section>

        <section>
            <h2>10. Contact Us</h2>
            <div class="contact-info">
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <p>
                    <strong>Email:</strong> privacy@gravoni.com<br>
                    <strong>Website:</strong> {{ config('app.url', 'https://gravoni.com') }}<br>
                    <strong>Facebook Page:</strong> Send us a message on Messenger
                </p>
            </div>
        </section>

        <section>
            <h2>Summary</h2>
            <div class="highlight-box">
                <ul>
                    <li>✅ We collect only your messages for automated response purposes</li>
                    <li>✅ Messages are processed by OpenAI and Google Gemini for AI-powered responses</li>
                    <li>✅ Data is retained for a maximum of <strong>30 days</strong></li>
                    <li>✅ You can request deletion of your data at any time</li>
                    <li>✅ Your data is never sold or shared for marketing purposes</li>
                </ul>
            </div>
        </section>

        <a href="{{ url('/') }}" class="back-link">← Back to Home</a>
    </div>
</body>
</html>
