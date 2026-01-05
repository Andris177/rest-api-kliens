<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 70px;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .logo {
            float: left;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 100px;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/logo.png') }}" class="logo" width="60">
    <div class="title">Színészek listája</div>
</header>

<footer>
    Generálva: {{ now()->format('Y-m-d H:i') }} |
    Oldal: <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_text(520, 820, "{PAGE_NUM}", null, 10, [0,0,0]);
        }
    </script>
</footer>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Név</th>
            <th>Születési dátum</th>
            <th>Nem</th>
        </tr>
    </thead>
    <tbody>
        @foreach($actors as $actor)
            <tr>
                <td>{{ $actor['id'] }}</td>
                <td>{{ $actor['name'] }}</td>
                <td>{{ $actor['birth_date'] ?? '-' }}</td>
                <td>{{ $actor['gender'] ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
