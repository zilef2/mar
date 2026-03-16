<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            margin: 0;
            padding: 30px;
        }

        .header {
            border-bottom: 2px solid #ebf0f5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-top {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-top td {
            vertical-align: top;
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            color: #1a56db;
            margin: 0;
        }

        .document-title {
            font-size: 24px;
            font-weight: 600;
            text-align: right;
            color: #4b5563;
            margin: 0;
        }

        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-section td {
            vertical-align: top;
            width: 50%;
        }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .info-value {
            font-size: 15px;
            color: #111827;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th {
            text-align: left;
            padding: 12px;
            background-color: #f9fafb;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #1f2937;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 10px 12px;
            font-size: 14px;
        }

        .totals .label {
            color: #4b5563;
            text-align: right;
            width: 70%;
        }

        .totals .value {
            color: #111827;
            text-align: right;
            font-weight: 600;
        }

        .totals .grand-total-label {
            font-weight: 700;
            color: #1a56db;
            font-size: 16px;
            border-top: 2px solid #ebf0f5;
            padding-top: 15px;
        }

        .totals .grand-total-value {
            font-weight: 700;
            color: #1a56db;
            font-size: 18px;
            border-top: 2px solid #ebf0f5;
            padding-top: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background-color: #dbeafe;
            color: #1e40af;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-top">
            <tr>
                <td>
                    <h1 class="company-name">Cotizaciones IML</h1>
                </td>
                <td>
                    <h2 class="document-title">COTIZACIÓN #{{ str_pad($cotizacion->id, 5, '0', STR_PAD_LEFT) }}</h2>
                </td>
            </tr>
        </table>

        <table class="info-section">
            <tr>
                <td>
                    <div class="info-label">Compañía / Cliente</div>
                    <div class="info-value">{{ $cotizacion->company }}</div>
                    
                    <div class="info-label">Proyecto</div>
                    <div class="info-value">{{ $cotizacion->project }}</div>
                </td>
                <td style="text-align: right;">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($cotizacion->date)->format('d/m/Y') }}</div>
                    
                    <div class="info-label">Ingeniero Referente</div>
                    <div class="info-value">{{ $cotizacion->engineer_name }}</div>

                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        <span class="status-badge">{{ $cotizacion->status ?? 'Pendiente' }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Descripción / Detalles</th>
                <!-- Empty headers for visual structure if needed -->
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3">
                    <strong>Servicios Profesionales de Ingeniería / Proyecto:</strong><br><br>
                    Detalles y costos asociados a {{ $cotizacion->project }} para {{ $cotizacion->company }}.
                </td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">${{ number_format($cotizacion->subtotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Impuestos ({{ number_format($cotizacion->tax_rate, 2) }}%):</td>
            <td class="value">${{ number_format($cotizacion->tax_amount, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label grand-total-label">Subtotal de Cotización:</td>
            <td class="value grand-total-value">${{ number_format($cotizacion->total, 2, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Este documento es una estimación de costos y no representa un contrato final hasta ser aprobado y firmado.<br>
        Generado automáticamente por el sistema.
    </div>

</body>
</html>
