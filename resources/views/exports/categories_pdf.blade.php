<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans; }
        header { text-align: center; margin-bottom: 20px; }
        footer { position: fixed; bottom: 0; text-align: center; font-size: 12px; }
    </style>
</head>
<body>

<header>
    <h2>Kategóriák listája</h2>
</header>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Név</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $c)
            <tr>
                <td>{{ $c['id'] }}</td>
                <td>{{ $c['name'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<footer>
    Laravel REST API Client
</footer>

</body>
</html>
