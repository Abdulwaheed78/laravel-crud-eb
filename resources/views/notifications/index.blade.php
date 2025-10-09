@extends('base')

@section('title', 'All Notifications')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📬 Notifications</h2>
        <div>
            <a href="{{ route('notifications.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </a>
            <a href="{{ route('notifications.export') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <div class="card shadow-sm p-3">
        <form id="bulkDeleteForm" action="{{ route('notifications.bulkDelete') }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete selected notifications?');">
            @csrf
            @method('DELETE')

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($notifications as $notification)
                                <tr>
                                    {{-- Title --}}
                                    <td>
                                        @php
                                            $title = strtolower($notification->title);
                                            $color = match(true) {
                                                str_contains($title, 'created') => 'text-success',
                                                str_contains($title, 'updated') => 'text-primary',
                                                str_contains($title, 'deleted'), str_contains($title, 'removed') => 'text-danger',
                                                str_contains($title, 'deactivated') => 'text-warning',
                                                default => 'text-secondary'
                                            };
                                        @endphp
                                        <strong class="{{ $color }}">{{ $notification->title }}</strong>
                                    </td>

                                    {{-- Message --}}
                                    <td>{{ $notification->message }}</td>

                                    {{-- Status --}}
                                    <td>
                                        @if($notification->is_read)
                                            <span class="badge bg-secondary">Read</span>
                                        @else
                                            <span class="badge bg-success">Unread</span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td>{{ $notification->created_at->diffForHumans() }}</td>

                                    {{-- Actions --}}
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger single-delete"
                                                data-id="{{ (string) $notification->_id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <input type="checkbox" name="ids[]" value="{{ (string) $notification->_id }}"
                                               class="notification-checkbox ms-2">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No notifications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing all notifications</small>
                <div>
                    <button type="submit" class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn" disabled>
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    <input type="checkbox" id="selectAll" class="ms-1">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const singleDeleteButtons = document.querySelectorAll('.single-delete');
    const bulkForm = document.getElementById('bulkDeleteForm');

    function updateDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        deleteBtn.disabled = !anyChecked;
    }

    // Select all checkboxes
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
    });

    // Update delete button when individual checkbox changes
    checkboxes.forEach(cb => cb.addEventListener('change', updateDeleteButton));

    // Single delete now uses bulk form
    singleDeleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            // Uncheck all, then check only this
            checkboxes.forEach(cb => cb.checked = false);
            const target = document.querySelector(`input[value="${id}"]`);
            if (target) target.checked = true;

            if (confirm('Delete this notification?')) {
                bulkForm.submit();
            }
        });
    });
});
</script>
@endsection
