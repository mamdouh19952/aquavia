{{-- Bootstrap JS is bundled via Vite (see resources/js/app.js) --}}

{{-- Toastr Notifications (CDN — optional dependency, not bundled) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    window.showLoader = function () {
        document.getElementById('loader')?.classList.remove('d-none');
    };

    window.hideLoader = function () {
        document.getElementById('loader')?.classList.add('d-none');
    };

    document.addEventListener('submit', function (e) {
        if (e.target.dataset.noLoader === undefined) {
            showLoader();
        }
    });

    window.addEventListener('pageshow', hideLoader);
</script>
