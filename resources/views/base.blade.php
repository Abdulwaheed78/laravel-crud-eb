<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        /* Fix extra spacing between pagination buttons */
        .pagination .page-item {
            margin: 0 2px;
        }

        /* Prevent pagination from overflowing */
        .pagination-wrapper {
            overflow-x: auto;
            white-space: nowrap;
            max-width: 100%;
        }

        /* Optional: make pagination look cleaner */
        .pagination .page-link {
            padding: 4px 10px;
            border-radius: 6px;
        }
    </style>

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/user/index">Users</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">

        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>