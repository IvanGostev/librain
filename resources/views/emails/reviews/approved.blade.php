<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ваш отзыв опубликован</title>
</head>

<body style="font-family: sans-serif; background-color: #1a1d21; color: #ffffff; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background-color: #212529; padding: 20px; border-radius: 8px; border: 1px solid #2c3035;">
        <h1 style="color: #0d6efd; margin-bottom: 20px;">Ваш отзыв опубликован!</h1>
        <p>Здравствуйте, {{ $review->user->name }}!</p>
        <p>Ваш отзыв к книге <strong>{{ $review->book->title }}</strong> прошел модерацию и теперь доступен для
            просмотра всем пользователям.</p>

        <div style="background-color: #2c3035; padding: 15px; border-radius: 4px; margin: 20px 0; font-style: italic;">
            "{{ $review->comment }}"
        </div>

        <p>Спасибо, что делитесь своим мнением!</p>

        <div style="margin-top: 30px;">
            <a href="{{ route('books.show', $review->book->slug) }}"
                style="background-color: #0d6efd; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">Перейти
                к книге</a>
        </div>

        <p style="margin-top: 30px; font-size: 12px; color: #6c757d;">С уважением,<br>Команда Librain</p>
    </div>
</body>

</html>