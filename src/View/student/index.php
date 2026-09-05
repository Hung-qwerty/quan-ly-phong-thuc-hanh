<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý phòng thực hành</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }


        /* =========================
           LAYOUT
        ========================= */

        .layout {
            display: flex;
            min-height: 100vh;
        }


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 250px;
            background: #1e293b;
            color: #fff;
            padding: 20px 15px;
            flex-shrink: 0;
        }

        .logo {
            font-size: 21px;
            font-weight: 700;
            padding: 10px;
            margin-bottom: 25px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu a {
            display: block;
            padding: 12px 14px;
            border-radius: 8px;
            color: #cbd5e1;
            transition: 0.2s;
        }

        .menu a:hover {
            background: #334155;
            color: #fff;
        }

        .menu a.active {
            background: #2563eb;
            color: #fff;
        }


        /* =========================
           MAIN
        ========================= */

        .main {
            flex: 1;
            padding: 25px;
            min-width: 0;
        }

        .topbar {
            background: #fff;
            padding: 18px 22px;
            border-radius: 12px;
            margin-bottom: 25px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .topbar h1 {
            font-size: 22px;
        }

        .user-info {
            color: #64748b;
            font-size: 14px;
        }


        /* =========================
           CARD
        ========================= */

        .cards {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(220px, 1fr)
                );

            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 22px;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 17px;
        }

        .card-value {
            font-size: 28px;
            font-weight: 700;
            color: #2563eb;
        }


        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h2 {
            font-size: 20px;
        }


        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            overflow-x: auto;
            background: #fff;
            border-radius: 12px;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            white-space: nowrap;
        }

        th {
            background: #f8fafc;
            font-size: 14px;
        }

        td {
            font-size: 14px;
        }


        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;

            font-size: 12px;
            font-weight: 600;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-maintenance {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-occupied {
            background: #fef3c7;
            color: #92400e;
        }

        .status-default {
            background: #e5e7eb;
            color: #374151;
        }


        /* =========================
           FORM
        ========================= */

        .form-card {
            max-width: 750px;

            background: #fff;
            padding: 25px;
            border-radius: 12px;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;

            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            width: 100%;

            padding: 11px 13px;

            border: 1px solid #d1d5db;
            border-radius: 7px;

            outline: none;
        }

        .form-control:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }


        /* =========================
           BUTTON
        ========================= */

        .btn {
            display: inline-block;

            padding: 11px 18px;

            border: none;
            border-radius: 7px;

            cursor: pointer;

            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }


        /* =========================
           ALERT
        ========================= */

        .alert {
            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .error-text {
            color: #dc2626;

            font-size: 13px;

            margin-top: 5px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {

            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .menu {
                flex-direction: row;
                overflow-x: auto;
            }

            .menu a {
                white-space: nowrap;
            }

            .main {
                padding: 15px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

        }

    </style>

</head>