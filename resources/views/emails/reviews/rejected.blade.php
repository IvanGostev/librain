<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ваш отзыв отклонен</title>
</head>

<body style="font-family: sans-serif; background-color: #1a1d21; color: #ffffff; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background-color: #212529; padding: 20px; border-radius: 8px; border: 1px solid #2c3035;">
        <h1 style="color: #dc3545; margin-bottom: 20px;">Ваш отзыв отклонен</h1>
        <p>Здравствуйте!</p>
        <p>К сожалению, ваш отзыв к книге <strong>{{ $bookTitle }}</strong> не прошел модерацию и был удален.</p>

        <div style="background-color: #2c3035; padding: 15px; border-radius: 4px; margin: 20px 0; font-style: italic;">
            "{{ $comment }}"
        </div>

        <p>Пожалуйста, убедитесь, что ваши комментарии соответствуют правилам нашего сообщества.</p>

        <p style="margin-top: 30px; font-size: 12px; color: #6c757d;">С уважением,<br>Команда Librain</p>
    </div>
</body>

</html>