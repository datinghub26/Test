@props(['offer', 'isLocked' => false])
@php
    $isLocked = auth()->check() && $isLocked;
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

{{--            <span class="badge position-absolute top-0 end-0 text-end fw-bold"--}}
                {{--                  style="background-color: {{ $offer->badge_bg_color }}">--}}
                {{ $offer->badge }}
            </span>
        @endif
    </div>

    <div class="card-body text-center align-items-center d-flex justify-content-center position-relative pb-0">
        @if($isLocked)
            <img src="{{ Storage::url($offer->image) }}" class="w-100 my-3" alt="{{ $offer->name }}"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='{{ asset('assets/img/placeholder-provider.svg') }}';"
                 style="filter: blur(2px) brightness(70%);">

            <div class="position-absolute text-white">
                <x-heroicon-s-lock-closed style="width: 38px; height:  38px"/>
                <p class="fw-bold" style="font-size: 11px">Unlock at level {{ $offer->unlock_level }}</p>
            </div>
        @else
            <img src="{{ Storage::url($offer->image) }}" class="w-100 my-3" alt="{{ $offer->name }}"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='{{ asset('assets/img/placeholder-provider.svg') }}';">
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

@once
    @push('modals')
        <div class="modal fade" id="offerPartnerModal" tabindex="-1" aria-labelledby="offerPartnerModal"
             aria-hidden="true" style="z-index: 9999999;">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down" style="max-width: 92vw; margin: auto;">
                <div class="modal-content bg-dark" style="height: 90vh; max-height: 90vh; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column;">
                    <div class="modal-header py-2 px-3 border-secondary" style="flex: 0 0 auto;">
                        <a href="" target="_blank" class="modal-title d-flex align-items-center text-white" id="offerLink">
                            <x-heroicon-s-arrow-top-right-on-square width="22px"/>
                            <span class="ms-2 fw-semibold"></span>
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 text-center position-relative" style="flex: 1 1 auto; height: calc(90vh - 55px); overflow: hidden;">
                        <div id="spinner" class="py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="text-secondary mt-2" style="font-size: 14px">Loading offer...</div>
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
        offerModal.querySelector('.modal-body #spinner').style.display = 'block'
        offerModal.querySelector('.modal-title span').textContent = button.getAttribute('data-bs-title')

        if (!url && !sdk) {
            offerModal.querySelector('.modal-body #spinner').style.display = 'none'
            offerModal.querySelector('.modal-title').href = '#'
            offerModal.querySelector('.modal-body #iframeOffer').innerHTML = `
                <div class="text-center py-5">
                    <i class="fa-solid fa-exclamation-triangle" style="font-size: 1.5rem"></i>
                    <p class="mt-2">There's an error occurred!</p>
                </div>
            `;
            return
        }

        if (url) {
            offerModal.querySelector('.modal-title').href = url
            offerModal.querySelector('.modal-body #iframeOffer').innerHTML = `
            <iframe src="${url}" style="width:100%; height:100%; min-height: calc(90vh - 55px); border:none; display: none;" frameborder="0"
                    onload="document.getElementById('spinner').style.display='none'; this.style.display = 'block'"
                    onerror="this.textContent='error happen'"></iframe>
        `;
            return
        }

        if (window.inAppWebView === undefined) {
            offerModal.querySelector('.modal-body #spinner').style.display = 'none'
            offerModal.querySelector('.modal-body #iframeOffer').innerHTML = `
                <div class="text-center p-4">
                    <p class="">Please open the app to view this offer</p>
                    <div class="text-center">
                        <img src="{{ asset('assets/img/qr.png') }}" alt="qr-code" style="width: 200px; height: 200px">
                    </div>
                </div>
            `;
            return
        }


        if (sdk && window.inAppWebView === undefined) {
            offerModal.querySelector('.modal-body #spinner').style.display = 'none'
            offerModal.querySelector('.modal-body #iframeOffer').innerHTML = `
                <div class="text-center p-4">
                    <p class="">Please open the app to view this offer</p>
                    <div class="text-center">
                        <img src="{{ asset('assets/img/qr.png') }}" alt="qr-code" style="width: 200px; height: 200px">
                    </div>
                </div>
            `;
        } else {
            offerModal.querySelector('.modal-title').href = '#'
            const data = {userId: {{ auth()->id() ??  -1 }}, ...JSON.parse(sdk)}
            setTimeout(() => {
                offerModal.querySelector('.modal-body #spinner').style.display = 'none'
                $('#offerPartnerModal').modal('hide');
            }, 500);

            window.inAppWebView.postMessage(JSON.stringify(data))
        }
    })
</script>
@endscript

