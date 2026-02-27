<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #1a1d20;
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
            background-color: #2b3035;
            border-radius: 8px;
            padding: 40px;
            margin-top: 40px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
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
            color: rgba(255,255,255,0.7);
        }
        .btn {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 50rem;
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
            <h1>Сброс пароля</h1>
            <p>Здравствуйте{{ isset($user) && $user->name ? ', ' . $user->name : '' }}!</p>
            <p>Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учетной записи.</p>
            
            <a href="{{ $url }}" class="btn">Сбросить пароль</a>
            
            <p>Срок действия ссылки для сброса пароля истекает через {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} минут.</p>
            <p>Если вы не запрашивали сброс пароля, никаких дальнейших действий не требуется.</p>
            
            <div class="small">
                Если у вас возникли проблемы с нажатием кнопки «Сбросить пароль», скопируйте и вставьте приведенный ниже URL-адрес в свой веб-браузер:
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
