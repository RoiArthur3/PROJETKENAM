<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 20px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-decoration-underline {
            text-decoration: underline;
        }
        
        .border-bottom {
            border-bottom: 1px solid #000;
        }
        
        .border {
            border: 1px solid #000;
        }
        
        .border-3 {
            border-width: 3px;
        }
        
        .border-dark {
            border-color: #000 !important;
        }
        
        .p-3 {
            padding: 1rem;
        }
        
        .p-4 {
            padding: 1.5rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .bg-light {
            background-color: #f8f9fa;
        }
        
        .bg-dark {
            background-color: #343a40;
            color: #fff;
        }
        
        .badge {
            padding: 0.5em 1em;
            font-weight: bold;
            border-radius: 0.25rem;
        }
        
        .bg-success {
            background-color: #28a745;
            color: #fff;
        }
        
        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .bg-danger {
            background-color: #dc3545;
            color: #fff;
        }
        
        .bg-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        
        .fs-6 {
            font-size: 1rem;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        .col-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        .col-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        @media print {
            body {
                font-size: 11px;
                margin: 10px;
            }
            
            .table {
                font-size: 10px;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
