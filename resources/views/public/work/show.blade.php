@extends('layouts.public')

@section('title', $project->title())

@section('content')
<section class="py-4">
    <a href="{{ route('work.index') }}" class="d-inline-block mb-4 text-decoration-none">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
        {{ __('site.work.back_to_work') }}
    </a>

    <h1 class="mb-3">{{ $project->title() }}</h1>
    <div class="mb-4"><span class="meta-tag"><i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $project->location }}</span></div>

    @php $images = $project->galleryImages(); @endphp
    @if ($images->isNotEmpty())
        <div class="project-gallery mb-4">
            <button type="button" class="gallery-featured border-0 p-0 w-100" data-bs-toggle="modal" data-bs-target="#galleryLightbox" data-index="0">
                <img src="{{ $images->first()->getUrl() }}" alt="{{ $project->title() }}">
            </button>

            @if ($images->count() > 1)
                <div class="row g-3 mt-1">
                    @foreach ($images->skip(1) as $i => $image)
                        <div class="col-4 col-md-3">
                            <button type="button" class="gallery-thumb border-0 p-0 w-100" data-bs-toggle="modal" data-bs-target="#galleryLightbox" data-index="{{ $i + 1 }}">
                                <img src="{{ $image->getUrl() }}" alt="{{ $project->title() }}">
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="modal fade" id="galleryLightbox" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content gallery-lightbox-content">
                    <button type="button" class="btn-close btn-close-white gallery-lightbox-close" data-bs-dismiss="modal" aria-label="{{ __('site.work.gallery_close') }}"></button>

                    @if ($images->count() > 1)
                        <button type="button" class="gallery-lightbox-nav gallery-lightbox-prev" aria-label="{{ __('site.work.gallery_prev') }}">
                            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="gallery-lightbox-nav gallery-lightbox-next" aria-label="{{ __('site.work.gallery_next') }}">
                            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </button>
                    @endif

                    <img src="" alt="" class="gallery-lightbox-img" id="galleryLightboxImg">
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const modalEl = document.getElementById('galleryLightbox');
                    if (!modalEl) return;

                    const imgEl = document.getElementById('galleryLightboxImg');
                    const thumbs = [...document.querySelectorAll('.gallery-featured img, .gallery-thumb img')];
                    const prevBtn = modalEl.querySelector('.gallery-lightbox-prev');
                    const nextBtn = modalEl.querySelector('.gallery-lightbox-next');
                    let currentIndex = 0;

                    const show = (index) => {
                        currentIndex = (index + thumbs.length) % thumbs.length;
                        imgEl.src = thumbs[currentIndex].src;
                        imgEl.alt = thumbs[currentIndex].alt;
                    };

                    modalEl.addEventListener('show.bs.modal', (event) => {
                        const trigger = event.relatedTarget;
                        show(trigger ? Number(trigger.dataset.index) : 0);
                    });

                    prevBtn?.addEventListener('click', () => show(currentIndex - 1));
                    nextBtn?.addEventListener('click', () => show(currentIndex + 1));

                    document.addEventListener('keydown', (event) => {
                        if (!modalEl.classList.contains('show')) return;
                        const isRtl = document.documentElement.dir === 'rtl';
                        if (event.key === 'ArrowRight') show(currentIndex + (isRtl ? -1 : 1));
                        if (event.key === 'ArrowLeft') show(currentIndex + (isRtl ? 1 : -1));
                    });
                });
            </script>
        @endpush
    @endif

    <p class="fs-5" style="max-width: 720px;">{{ $project->description() }}</p>
</section>
@endsection
