<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Support Ticket</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #cb3737 0%, #8a2424 100%);
            color: #fff;
            padding: 24px 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .ticket-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .ticket-info h2 {
            margin: 0 0 15px;
            font-size: 16px;
            color: #cb3737;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            width: 120px;
            flex-shrink: 0;
        }
        .info-value {
            color: #333;
        }
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #ffc107;
            color: #333;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .description-box {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .description-box h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .description-box p {
            margin: 0;
            color: #333;
            white-space: pre-wrap;
        }
        .action-btn {
            display: inline-block;
            background: #cb3737;
            color: #fff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 New Support Ticket Submitted</h1>
            <p>A customer has submitted a new support request</p>
        </div>
        
        <div class="content">
            <div class="ticket-info">
                <h2>Ticket #{{ $ticket->ticket_id }}</h2>
                
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $ticket->name }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $ticket->email }}</span>
                </div>
                
                @if($ticket->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $ticket->phone }}</span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Booking ID:</span>
                    <span class="info-value">#{{ $ticket->booking_id }}</span>
                </div>
                
                @if($ticket->car)
                <div class="info-row">
                    <span class="info-label">Vehicle:</span>
                    <span class="info-value">{{ $ticket->car->brand }} {{ $ticket->car->model }} ({{ $ticket->car->plate_number }})</span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Category:</span>
                    <span class="info-value">
                        <span class="category-badge">{{ ucwords(str_replace('_', ' ', $ticket->category)) }}</span>
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Subject:</span>
                    <span class="info-value"><strong>{{ $ticket->subject }}</strong></span>
                </div>
            </div>
            
            <div class="description-box">
                <h3>Description</h3>
                <p>{{ $ticket->description }}</p>
            </div>
            
            <a href="{{ route('staff.support-tickets.show', $ticket->ticket_id) }}" class="action-btn">
                View Ticket in Dashboard →
            </a>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from Hasta GoRent.</p>
            <p>© {{ date('Y') }} Hasta Travels & Tours. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
