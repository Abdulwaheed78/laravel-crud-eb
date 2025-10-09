<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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

        #notification-list::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari */
        }

        #notification-list {
            -ms-overflow-style: none;
            /* IE & Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-light" href="/">Student Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('user/*') ? 'active' : '' }}" href="/user/index">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('images') || request()->is('images/*') ? 'active' : '' }}" href="{{ route('images.index') }}">
                            Images
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('notifications') || request()->is('notifications/*') ? 'active' : '' }}" href="{{ route('notifications.list') }}">
                            Notifications List
                        </a>
                    </li>

                </ul>

                <!-- Navbar Notification Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        🔔
                        <span id="notification-count"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display:none;">
                            0
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end overflow-auto"
                        aria-labelledby="navbarDropdown"
                        id="notification-list"
                        style="max-height: 350px; width: 380px;">

                        <!-- Header -->
                        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                            <span class="fw-semibold">Notifications</span>
                            <button id="markAllReadBtn" class="btn btn-sm btn-link text-decoration-none p-0">
                                Mark all as read
                            </button>
                        </li>

                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>

                        <!-- Example Notification Items -->
                        <li class="px-2">
                            <div class="border rounded-3 p-2 mb-2 bg-light">
                                <strong class="text-primary small d-block">Student Created</strong>
                                <span class="small">A new student record was successfully added.</span>
                            </div>
                        </li>

                        <li class="px-2">
                            <div class="border rounded-3 p-2 mb-2 bg-light">
                                <strong class="text-danger small d-block">Error Processing CSV</strong>
                                <span class="small">Email field missing or invalid in row 3.</span>
                            </div>
                        </li>

                    </ul>

                </li>


            </div>
        </div>
    </nav>

    @include('success_error')

    <div class="container d-flex justify-content-center align-items-center mt-3">
        @yield('content')
    </div>
    <script>
        function loadNotifications() {
            fetch('/notifications')
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('notification-list');
                    const count = document.getElementById('notification-count');

                    // Keep header + "Mark All Read" button
                    list.innerHTML = `
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
            <span class="fw-semibold">Notifications</span>
            <button id="markAllReadBtn" class="btn btn-sm btn-link text-decoration-none p-0">
                Mark all as read
            </button>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
    `;

                    if (!data || data.length === 0) {
                        list.innerHTML += `
            <li class="px-3 py-2 text-center">
                <span class="small text-secondary">No new notifications</span>
            </li>`;
                        count.style.display = 'none';
                        return;
                    }

                    // Update badge count
                    count.textContent = data.length;
                    count.style.display = 'inline-block';
                    // Loop through each notification
                    data.forEach(n => {
                        const item = document.createElement('li');
                        item.classList.add('px-2'); // spacing

                        // Default color: danger
                        let colorClass = 'text-danger';
                        const action = (n.title || '').toLowerCase();

                        // 🎨 Color logic based on action keywords
                        if (action.includes('create') || action.includes('created')) {
                            colorClass = 'text-success'; // blue
                        } else if (action.includes('update') || action.includes('updated')) {
                            colorClass = 'text-primary'; // yellow
                        }

                        // 🏷️ Type label from DB (or fallback)
                        const typeLabel = (n.type || '').charAt(0).toUpperCase() + (n.type || '').slice(1) || 'Info';

                        // 💬 Bootstrap mini-card style
                        item.innerHTML = `
        <div class="border rounded-3 p-2 mb-2 bg-light shadow-sm">
            <strong class="${colorClass} small d-block">
                ${typeLabel}: ${n.title}
            </strong>
            <span class="small text-dark">${n.message}</span>
        </div>
    `;

                        list.appendChild(item);
                    });


                    // Attach event after re-rendering
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = tokenMeta ? tokenMeta.content : '';

                    const markBtn = document.getElementById('markAllReadBtn');
                    if (markBtn) {
                        markBtn.addEventListener('click', () => {
                            fetch('/notifications/mark-as-read', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                })
                                .then(() => loadNotifications())
                                .catch(err => console.error('Failed to mark as read:', err));
                        });
                    }
                })
                .catch(err => console.error('Failed to load notifications:', err));
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            setInterval(loadNotifications, 3000);
        });
    </script>

    <style>
        /* Highlight animation for new notifications */
        .new-notification {
            background-color: #e8f9e9 !important;
            animation: flash 1s ease-in-out;
            border-left: 3px solid #28a745;
        }

        @keyframes flash {
            0% {
                background-color: #c3f7c8;
            }

            50% {
                background-color: #ffffff;
            }

            100% {
                background-color: #e8f9e9;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <script>
        $(document).ready(function() {
            $('#students-table').DataTable({
                pageLength: 10,
                responsive: true,
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>