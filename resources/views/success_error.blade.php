{{-- 📍 Place this anywhere in your base layout (like base.blade.php), ideally before </body> --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000">

    {{-- ✅ Success Toast --}}
    @if (session('success'))
        <div class="toast align-items-center border border-success text-success bg-white show mb-2 shadow-sm"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body small fw-semibold">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close text-success me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- ❌ Error Toast --}}
    @if (session('error'))
        <div class="toast align-items-center border border-danger text-danger bg-white show mb-2 shadow-sm"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body small fw-semibold">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close text-danger me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- ⚠️ Validation Errors --}}
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="toast align-items-center border border-warning text-warning bg-white show mb-2 shadow-sm"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body small fw-semibold">
                        {{ $error }}
                    </div>
                    <button type="button" class="btn-close text-warning me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endforeach
    @endif

</div>
