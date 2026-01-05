<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans; }
        header { text-align: center; margin-bottom: 20px; }
        footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        th { background: #eee; }
    </style>
</head>
<body>

<header>
    <h2>Filmek listája</h2>
</header>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Cím</th>
        <th>Director</th>
        <th>Category</th>
    </tr>
    </thead>
    <tbody>
    @foreach($movies as $m)
        <tr>
            <td>{{ $m['id'] ?? '' }}</td>
            <td>{{ $m['title'] ?? '' }}</td>
            <td>{{ $m['director_id'] ?? '' }}</td>
            <td>{{ $m['category_id'] ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<footer>
    Laravel REST API Client
</footer>

</body>
</html>
