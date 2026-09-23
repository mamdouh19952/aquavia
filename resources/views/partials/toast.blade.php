{{-- Toastr Notifications --}}
@if ($message = Session::get('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof toastr !== 'undefined') {
                toastr.success("{{ $message }}");
            } else {
                console.log('Success: {{ $message }}');
            }
        });
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof toastr !== 'undefined') {
                toastr.error("{{ $message }}");
            } else {
                console.log('Error: {{ $message }}');
            }
        });
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof toastr !== 'undefined') {
                toastr.warning("{{ $message }}");
            } else {
                console.log('Warning: {{ $message }}');
            }
        });
    </script>
@endif

@if ($message = Session::get('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof toastr !== 'undefined') {
                toastr.info("{{ $message }}");
            } else {
                console.log('Info: {{ $message }}');
            }
        });
    </script>
@endif
