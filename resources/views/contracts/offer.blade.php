<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 40px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
        }
        h2 {
            font-size: 14px;
            margin-top: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .section {
            margin-bottom: 15px;
        }
        .signature-block {
            margin-top: 60px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            display: inline-block;
            margin-top: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        td {
            padding: 5px 10px;
            vertical-align: top;
        }
        .meta td:first-child {
            font-weight: bold;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ДОГОВОР-ОФЕРТА НА ДИСТРИБУЦИЮ МУЗЫКАЛЬНОГО ПРОИЗВЕДЕНИЯ</h1>
        <p>Номер: {{ $contract->key }}</p>
        <p>Дата: {{ $date }}</p>
    </div>

    <div class="section">
        <h2>1. Стороны договора</h2>
        <table class="meta">
            <tr>
                <td>Лейбл:</td>
                <td>Future Label (ООО «Фьючер Лейбл»)</td>
            </tr>
            <tr>
                <td>Артист:</td>
                <td>{{ $user->stage_name ?? $user->name }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $user->email }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>2. Предмет договора</h2>
        <table class="meta">
            <tr>
                <td>Релиз:</td>
                <td>{{ $release->title }}</td>
            </tr>
            <tr>
                <td>Исполнитель:</td>
                <td>{{ $release->artist_name ?? ($user->stage_name ?? $user->name) }}</td>
            </tr>
            <tr>
                <td>Тип:</td>
                <td>{{ $release->type->value }}</td>
            </tr>
        </table>
        <p>
            Лейбл обязуется осуществить дистрибуцию указанного музыкального произведения
            на цифровых площадках в соответствии с условиями настоящего договора.
        </p>
    </div>

    <div class="section">
        <h2>3. Условия</h2>
        <p>
            3.1. Артист предоставляет Лейблу неисключительное право на дистрибуцию произведения
            на территории всех стран мира.
        </p>
        <p>
            3.2. Срок действия договора — 1 (один) год с момента принятия оферты.
        </p>
        <p>
            3.3. Артист гарантирует, что является правообладателем произведения и имеет
            все необходимые права для заключения настоящего договора.
        </p>
        <p>
            3.4. Лейбл обязуется перечислять Артисту роялти в размере, определённом
            тарифным планом, выбранным Артистом.
        </p>
    </div>

    <div class="section">
        <h2>4. Ответственность сторон</h2>
        <p>
            4.1. В случае нарушения авторских прав третьих лиц ответственность несёт Артист.
        </p>
        <p>
            4.2. Лейбл не несёт ответственности за действия цифровых площадок.
        </p>
    </div>

    <div class="section">
        <h2>5. Заключительные положения</h2>
        <p>
            5.1. Принятие оферты осуществляется путём нажатия кнопки «Принять» в личном кабинете.
        </p>
        <p>
            5.2. Версия шаблона: {{ $contract->template_version }}
        </p>
    </div>

    <div class="signature-block">
        <table>
            <tr>
                <td style="width: 50%;">
                    <p><strong>Лейбл:</strong></p>
                    <p>Future Label</p>
                    <div class="signature-line"></div>
                </td>
                <td style="width: 50%;">
                    <p><strong>Артист:</strong></p>
                    <p>{{ $user->stage_name ?? $user->name }}</p>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
