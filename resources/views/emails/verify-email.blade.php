<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Активация аккаунта</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #212529; /* bg-body-tertiary mostly dark maybe? Wait, but email clients need safe styles */
            background-color: #1a1d20; /* Darker bg */
            color: #f8f9fa;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #2b3035; /* bg-dark-card */
            border-radius: 8px;
            padding: 40px;
            margin-top: 40px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1); /* border-white-10 */
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
            color: #f8f9fa;
            text-decoration: none;
            display: inline-block;
        }
        .text-primary {
            color: #0d6efd;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #f8f9fa;
            font-weight: 600;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.7); /* text-white-50ish */
        }
        .btn {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 50rem; /* rounded-pill */
            font-weight: 500;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }
        .small {
            font-size: 13px;
            color: #6c757d;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 20px;
            word-break: break-all;
            text-align: left;
        }
        .small a {
            color: #0d6efd;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="{{ url('/') }}" class="logo">
                <span class="text-primary">Lib</span><span style="color: #f8f9fa;">rain</span>
            </a>
            <h1>Подтверждение регистрации</h1>
            <p>Здравствуйте{{ isset($user) && $user->name ? ', ' . $user->name : '' }}!</p>
            <p>Спасибо за регистрацию в Librain — вашей цифровой библиотеке. Чтобы завершить создание аккаунта и начать пользоваться всеми функциями сайта, пожалуйста, подтвердите ваш адрес электронной почты.</p>
            
            <a href="{{ $url }}" class="btn">Активировать аккаунт</a>
            
            <p>Если вы не создавали аккаунт на нашем сайте, просто проигнорируйте это письмо.</p>
            
            <div class="small">
                Если у вас возникли проблемы с нажатием кнопки «Активировать аккаунт», скопируйте и вставьте приведенный ниже URL-адрес в свой веб-браузер:
                <br>
                <a href="{{ $url }}">{{ $url }}</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Librain. Все права защищены.</p>
        </div>
    </div>
</body>
</html>
