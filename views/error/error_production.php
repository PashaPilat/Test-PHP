<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Ошибка приложения</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        .error-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .error-footer {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <div class="error-title">Что-то пошло не так</div>
        <div class="error-message">Мы уже работаем над исправлением.</div>
        <div class="error-footer">Попробуйте обновить страницу позже.</div>
    </div>
</body>
</html>
