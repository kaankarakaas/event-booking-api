<!DOCTYPE html>
<html>
<head>
    <title>Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .ticket {
            border: 1px solid #000;
            padding: 20px;
            margin: 20px;
        }
        .ticket h1 {
            text-align: center;
        }
        .ticket p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="ticket">
    <h1>Event Ticket</h1>
    <h5>Powered by (:Kaan:)</h5>
    <p><strong>Event:</strong> {{ $ticket->event->name }}</p>
    <p><strong>Seat:</strong> {{ $ticket->seat->number }}</p>
    <p><strong>Price:</strong> ${{ $ticket->price }}</p>
    <p><strong>Status:</strong> {{ $ticket->status }}</p>
    <p><strong>Code:</strong> {{ $ticket->code }}</p>
</div>
</body>
</html>
