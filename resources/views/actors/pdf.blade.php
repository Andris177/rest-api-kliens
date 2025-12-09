<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Actors PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        header { text-align: center; margin-bottom: 20px; }
        header img { height: 50px; }
        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #999;
            padding: 4px 6px;
        }
        th {
            background: #eee;
        }
    </style>
</head>
<body>
<header>
    <img src="{{ public_path('logo.png') }}" alt="Logo">
    <h2>Actors list</h2>
</header>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Birth date</th>
        <th>Gender</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    @foreach($actors as $actor)
        <tr>
            <td>{{ $actor['id'] ?? '' }}</td>
            <td>{{ $actor['name'] ?? '' }}</td>
            <td>{{ $actor['birth_date'] ?? '' }}</td>
            <td>{{ $actor['gender'] ?? '' }}</td>
            <td>{{ $actor['description'] ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<footer>
    Laravel REST API Client – {{ date('Y-m-d H:i') }}
</footer>
</body>
</html>
