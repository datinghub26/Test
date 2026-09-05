@props(['offer', 'isLocked' => false])
@php
    $isLocked = auth()->check() && $isLocked;
    $rawImage = $offer->image ?? '';
    if (empty($rawImage)) {
        $imgSrc = '/assets/img/placeholder-provider.svg';
    } elseif (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://')) {
        $imgSrc = $rawImage;
    } else {
        $cleaned = ltrim($rawImage, '/');
        if (str_starts_with($cleaned, 'storage/')) {
            $cleaned = substr($cleaned, 8);
        }
        $imgSrc = '/storage/' . $cleaned;
    }
@endphp
<div {{ $attributes->merge([
    'class' => 'partner-card card text-white',
    'style' => ($isLocked ? "cursor: not-allowed;" : "") . "background: $offer->bg_color;"
]) }}

     data-bs-url="{{ $isLocked ? '' : $offer->finalUrl }}"
     data-bs-title="{{ $offer->name }}"
     data-bs-toggle="{{ $isLocked ? '' : 'modal' }}"
     data-bs-target="{{ auth()->check() ? '#offerPartnerModal' : '#authModal' }}">

    <div class="card-header mt-0">
        @if(!empty($offer->badge))
            <span class="badge px-1 position-absolute text-end fw-bold rounded-full"
                  style="background-color: {{ $offer->badge_bg_color }} !important; top: 10px; right: 10px; border-radius: 7px !important;">
                {{ $offer->badge }}
            </span>
        @endif
    </div>

    <div class="card-body text-center align-items-center d-flex justify-content-center position-relative pb-0">
        @if($isLocked)
            <img src="{{ $imgSrc }}" class="w-100 my-3 object-fit-contain" style="max-height: 80px; filter: blur(2px) brightness(70%);" alt="{{ $offer->name }}"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='/assets/img/placeholder-provider.svg'; this.onerror=function(){this.style.opacity='0';};">

            <div class="position-absolute text-white">
                <x-heroicon-s-lock-closed style="width: 38px; height:  38px"/>
                <p class="fw-bold" style="font-size: 11px">Unlock at level {{ $offer->unlock_level }}</p>
            </div>
        @else
            <img src="{{ $imgSrc }}" class="w-100 my-3 object-fit-contain" style="max-height: 80px;" alt="{{ $offer->name }}"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='/assets/img/placeholder-provider.svg'; this.onerror=function(){this.style.opacity='0';};">
            <i class="fa-solid fa-play position-absolute text-white bg-primary rounded-circle"></i>
        @endif

    </div>

    <div class="card-footer text-center px-0">
        <span class="mb-1 fw-normal text-white h6" style="font-size: 14px">{{ $offer->name }}</span>
        @if($offer->show_rate)
            <div class="text-warning" style="font-size: x-small">
                @foreach(range(1, 5) as $i)
                    @if($i <= $offer->rate)
                        <i class="fa-solid fa-star"></i>
                    @else
                        <i class="fa-regular fa-star"></i>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

@assets
<style>
    #offerPartnerModal .modal-dialog {
        max-width: 960px;
        width: 95%;
        margin: 1.75rem auto;
    }
    #offerPartnerModal .modal-content {
        height: 84vh;
        max-height: 800px;
        min-height: 520px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
    }
    #offerPartnerModal .modal-body {
        height: calc(84vh - 58px);
        max-height: 742px;
    }
    @media (max-width: 767.98px) {
        #offerPartnerModal .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
        }
        #offerPartnerModal .modal-content {
            height: 100% !important;
            max-height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
        }
        #offerPartnerModal .modal-body {
            height: calc(100vh - 55px) !important;
            max-height: none !important;
        }
    }
</style>
@endassets

@once
    @push('modals')
        <div class="modal fade" id="offerPartnerModal" tabindex="-1" aria-labelledby="offerPartnerModal"
             aria-hidden="true" style="z-index: 9999999;">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
                <div class="modal-content bg-dark overflow-hidden d-flex flex-column">
                    <div class="modal-header py-2 px-3 border-secondary d-flex align-items-center justify-content-between" style="flex: 0 0 auto; background: #14181f; border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <div class="d-flex align-items-center gap-2">
                            <x-heroicon-s-arrow-top-right-on-square width="20px" class="text-primary"/>
                            <span class="fw-semibold text-white fs-6" id="offerModalTitle">Loading...</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" target="_blank" id="offerDirectBtn" class="btn btn-sm btn-primary py-1 px-3 d-flex align-items-center gap-1" style="font-size: 13px;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Open in New Tab
                            </a>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0 text-center position-relative overflow-hidden" style="flex: 1 1 auto; background: #0e1217;">
                        <div id="spinner" class="position-absolute top-50 start-50 translate-middle py-5" style="z-index: 10;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="text-secondary mt-2" style="font-size: 14px">Connecting to offerwall...</div>
                        </div>

                        <div id="iframeOffer" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endpush
@endonce

@script
<script>
    let offerModal = document.getElementById('offerPartnerModal')
    offerModal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget
        let url = button.getAttribute('data-bs-url')
        let sdk = button.getAttribute('data-bs-sdk')
        const titleEl = offerModal.querySelector('#offerModalTitle')
        const directBtn = offerModal.querySelector('#offerDirectBtn')
        const spinner = offerModal.querySelector('#spinner')
        const iframeContainer = offerModal.querySelector('#iframeOffer')

        if (spinner) spinner.style.display = 'block'
        if (titleEl) titleEl.textContent = button.getAttribute('data-bs-title') || 'Offerwall'

        if (!url && !sdk) {
            if (spinner) spinner.style.display = 'none'
            if (directBtn) directBtn.style.display = 'none'
            if (iframeContainer) {
                iframeContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fa-solid fa-exclamation-triangle text-warning" style="font-size: 2rem"></i>
                        <p class="mt-3 text-white">This offerwall is currently unavailable.</p>
                    </div>
                `;
            }
            return
        }

        if (url) {
            if (directBtn) {
                directBtn.href = url
                directBtn.style.display = 'inline-flex'
            }

            if (iframeContainer) {
                iframeContainer.innerHTML = `
                    <iframe src="${url}"
                            id="activeOfferwallIframe"
                            style="width:100%; height:100%; border:none; display:block;"
                            allow="camera; microphone; geolocation; clipboard-read; clipboard-write; fullscreen"
                            allowfullscreen
                            loading="eager"></iframe>
                `;

                const iframe = iframeContainer.querySelector('#activeOfferwallIframe');
                if (iframe) {
                    iframe.onload = function () {
                        if (spinner) spinner.style.display = 'none';
                    };
                    // Quick fade out fallback: reveal iframe after max 1.5s so user is never stuck
                    setTimeout(function () {
                        if (spinner) spinner.style.display = 'none';
                    }, 1500);
                }
            }
            return
        }

        if (window.inAppWebView === undefined) {
            if (spinner) spinner.style.display = 'none'
            if (directBtn) directBtn.style.display = 'none'
            if (iframeContainer) {
                iframeContainer.innerHTML = `
                    <div class="text-center p-4">
                        <p class="text-white">Please open the app to view this offer</p>
                        <div class="text-center">
                            <img src="{{ asset('assets/img/qr.png') }}" alt="qr-code" style="width: 200px; height: 200px">
                        </div>
                    </div>
                `;
            }
            return
        }

        if (sdk && window.inAppWebView === undefined) {
            if (spinner) spinner.style.display = 'none'
            if (directBtn) directBtn.style.display = 'none'
            if (iframeContainer) {
                iframeContainer.innerHTML = `
                    <div class="text-center p-4">
                        <p class="text-white">Please open the app to view this offer</p>
                        <div class="text-center">
                            <img src="{{ asset('assets/img/qr.png') }}" alt="qr-code" style="width: 200px; height: 200px">
                        </div>
                    </div>
                `;
            }
        } else {
            const data = {userId: {{ auth()->id() ??  -1 }}, ...JSON.parse(sdk)}
            setTimeout(() => {
                if (spinner) spinner.style.display = 'none'
                $('#offerPartnerModal').modal('hide');
            }, 500);

            window.inAppWebView.postMessage(JSON.stringify(data))
        }
    })
</script>
@endscript


