<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Labels</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            padding: 10mm;
        }
        .label {
            width: 85mm; /* حجم الملصق */
            height: 50mm;
            padding: 5mm;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        h2, p {
            margin: 5px 0;
            font-size: 16px;
            color: #333;
        }
        .expiry-date {
            background-color: #ff6b6b;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            margin: 5px 0;
        }
        .batch-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .barcode img {
            width: 75%;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="label-container">
        @for ($i = 0; $i < $quantity; $i++)
            <div class="label">
                <h2>{{ $medicine->medicine_name }}</h2>
                <p>Price: {{ $medicine->people_price }} $</p>
                @if(isset($batch) && $batch)
                    <div class="expiry-date">Exp: {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }}</div>
                    <p class="batch-info">Batch: {{ $batch->batch_number }}</p>
                @else
                    <div class="expiry-date">Exp: {{ $medicine->expiry_date ? \Carbon\Carbon::parse($medicine->expiry_date)->format('d/m/Y') : 'N/A' }}</div>
                @endif
                <div class="barcode">
                    <img src="data:image/png;base64,{{ $barcode }}" />
                </div>
            </div>
        @endfor
    </div>

</body>
</html>
