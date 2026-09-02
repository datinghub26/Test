<div>
    @auth
        <div class="col-lg-2 col-6 mb-4">
            <div class="card fade-in-scale h-100 bg-dark bg-opacity-50"
                 x-data="{ is_coin: localStorage.getItem('isCoin') }"
                 x-on:update-coins.window="is_coin = $event.detail.isCoin">

                <div class="card-body text-center">
                    <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                        <x-heroicon-s-credit-card class="text-primary" width="20px"/>
                    </div>
                    <span class="card-title mb-2 text-white d-flex align-items-center justify-content-center f-md">
                        <x-coins :coins="auth()->user()->points" size="14px"/>
                    </span>
                    <small class="text-primary f-md">Available for withdrawal</small>
                </div>
            </div>
        </div>
    @endauth
    <div class="card">
        <div class="card-header pb-0">
            <div class="card-title h5 fw-bold text-white d-flex align-items-center gap-1">
                <x-heroicon-s-shopping-cart class="text-primary" width="25px"/>
                Shop
            </div>
        </div>

        <div class="card-body">
            @forelse($methodsGrouped as $category => $methods)
                <p class="text-white fw-bold ms-1 pt-4">{{ $category }}</p>
                <div class="card-container pb-4">
                    @foreach($methods as $method)
                        <div class="shop-card card fade-in-scale text-white text-warp"
                             @auth
                                 x-data="{ method: @js($method) }"
                             x-on:click="$dispatch('select-cashout-method', method)"
                             @else
                                 data-bs-toggle="modal" data-bs-target="#authModal"
                             @endauth

                             style=" background: {{ $method->bg_color }}">

                            <div class="card-body text-center align-items-center d-flex pb-0 mb-0">
                                <img src="{{ Storage::url($method->image) }}"
                                     class="w-100 my-3 object-fit-contain mw-100" alt="">
                            </div>

                            <div class="card-footer text-center px-0 pt-0 mt-0 d-flex justify-content-center">
                                <span class="mb-1 fw-normal text-white h6 text-truncate d-block text-center"
                                      style="font-size: 14px; width: calc(100% - 20px)">{{ $method->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <x-empty-state message="No available methods for withdrawal. Please try again later."/>
            @endforelse
        </div>
    </div>


    <div wire:ignore.self
         class="modal fade"
         id="shopModal"
         tabindex="-1"
         style="z-index: 999999999;"
         x-data="{
            method: null,
            selectedPriceIdx: 0,
            amount: 0,
            to_money_str: function (amount) {
                return Number(amount / 1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            },
            to_money: function (amount) {
                return Number(amount / 1000).toFixed(2);
            },
            to_coins: function (price) {
                return Number(price * 1000).toFixed(0);
            },
            to_coins_str: function (price) {
                return Number(price * 1000).toLocaleString();
            },
            get_percentage: function (amount, percentage) {
                return Number(amount * percentage / 100);
            },
            get_total_price: function (fees) {
                let amount = Number(this.amount);
                let percentage = this.get_percentage(amount, fees);
                return amount + percentage;
            },
            reset: function () {
                this.method = null;
                this.prevMethod = null;
                this.selectedPriceIdx = 0;
                this.amount = 0;
            }
         }"
         x-init="$('#shopModal').on('hidden.bs.modal', () => { reset(); });"
         x-on:update-coins.window="
            let isCoin = $event.detail.isCoin;
            if(isCoin == '1') {
                $data.amount = $data.to_coins($data.amount);
            } else {
                $data.amount = $data.to_money($data.amount);
            }
         "
         x-on:close-cashout-modal.window="$('#shopModal').modal('hide');"
         x-on:select-cashout-method.window="
            method = $event.detail
            if(method?.prices?.length > 0) {
                selectedPriceIdx = method.prices[0].id
                amount = is_coin == '1' ? to_coins(method.prices[0].price) : method.prices[0].price
            }

            $nextTick(() => {
                $('#shopModal').modal('show');
            });
         ">

        <div class="modal-dialog modal-dialog-centered modal-sm-full modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center justify-content-center">
                        <span class="p-2 rounded-circle d-flex justify-content-center align-items-center"
                              :style="'background: ' + method?.bg_color + '; width: 40px; height: 40px;'">
                            <img :src="method?.image ? '{{ asset('storage') }}/' + method.image : ''"
                                 :alt="method?.name"
                                 class="object-fit-contain w-100 h-100">
                        </span>
                        <span class="text-white fw-bolder ms-2"
                              style="font-size: large" x-text="method?.name"></span>
                    </h5>
                    <button type="button"
                            class="btn-close bg-label-primary d-flex align-items-center justify-content-center"
                            style="background: var(--bs-base); border: none; color: white"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-times" style="font-weight: 600"></i>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <template x-if="method?.payment_note">
                        <div class="row mb-2">
                            <span class="text-base small" x-text="method?.payment_note"></span>
                        </div>
                    </template>

                    {{--  price cards  --}}
                    <template x-if="method?.prices.length > 0">
                        <div class="py-2 ">
                            <p class="form-label text-body fw-bold text-uppercase mb-2 pt-2">Select Amount</p>
                            <template x-for="(price, idx) in method.prices" :key="idx">
                                <div class="d-flex">
                                    <div class="card w-100 mb-2"
                                         @click="selectedPriceIdx = price.id; is_coin == '1' ? amount = to_coins(price.price) : amount = price.price"
                                         style="border: 2px solid transparent"
                                         :style="{
                                                                                border: selectedPriceIdx == price.id ? '2px solid var(--bs-primary)' : '2px solid transparent',
                                                                                background: method.bg_color
                                                                            }">

                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between px-3">
                                                <img :src="'{{ asset('storage') }}/' + method.image"
                                                     :alt="price.price"
                                                     class=""
                                                     width="20px"
                                                >

                                                <div class="d-flex align-items-center justify-content-center">
                                                                                    <span
                                                                                        class="mb-0 fw-bold text-white h6"
                                                                                        style="font-size: 14px"
                                                                                        x-text="is_coin == '1' ? to_coins_str(price.price) : '$' + price.price"></span>

                                                    <img x-show="is_coin == '1'"
                                                         src="{{ asset('assets/img/coin.png') }}"
                                                         alt="coins"
                                                         width="13px" class="ms-1"/>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--                                <div class="row">--}}
                                {{--                                <div class="col-md-3 col-4 py-1 px-1">--}}
                                {{--                                    <div class="card"--}}
                                {{--                                         :style="{--}}
                                {{--                                                'border': selectedPriceIdx == price.id ? '2px solid var(--bs-primary)' : '2px solid transparent',--}}
                                {{--                                                'background': method.bg_color--}}
                                {{--                                             }"--}}
                                {{--                                         @click="selectedPriceIdx = price.id; is_coin == '1' ? amount = to_coins(price.price) : amount = price.price"--}}
                                {{--                                    >--}}
                                {{--                                        <div class="card-body text-center">--}}
                                {{--                                            <img :src="'{{ asset('storage') }}/' + method.image"--}}
                                {{--                                                 :alt="price.price"--}}
                                {{--                                                 class="card-img-top my-3 p-4 object-fit-contain" alt=""--}}
                                {{--                                                 style="width: 100%;">--}}

                                {{--                                            <div class="d-flex align-items-center justify-content-center">--}}
                                {{--                                                    <span class="mb-0 fw-bold text-white h6"--}}
                                {{--                                                          style="font-size: 14px"--}}
                                {{--                                                          x-text="is_coin == '1' ? to_coins_str(price.price) : '$' + price.price"></span>--}}

                                {{--                                                <img x-show="is_coin == '1'"--}}
                                {{--                                                     src="{{ asset('assets/img/coin.png') }}"--}}
                                {{--                                                     alt="coins"--}}
                                {{--                                                     width="12px" class="ms-1"/>--}}
                                {{--                                            </div>--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                {{--                                </div>--}}

                                {{--                                <div--}}
                                {{--                                    class="shop-card shop-price-card card text-white"--}}
                                {{--                                    :class="{ 'active': selectedPriceIdx == price.id }"--}}
                                {{--                                    @click="selectedPriceIdx = price.id; is_coin == '1' ? amount = to_coins(price.price) : amount = price.price"--}}
                                {{--                                    :style="'background: ' + method.bg_color">--}}
                                {{--                                    <div class="card-body text-center align-items-center d-flex pb-0 mb-0">--}}
                                {{--                                        <img :src="'{{ asset('storage') }}/' + method.image"--}}
                                {{--                                             :alt="price.price"--}}
                                {{--                                             class="card-img-top my-3 object-fit-contain mw-100" alt="">--}}
                                {{--                                    </div>--}}
                                {{--                                    <div class="card-footer text-center px-0 pt-0 mt-0">--}}
                                {{--                                        <div class="d-flex align-items-center justify-content-center">--}}
                                {{--                                            <span class="mb-0 fw-bold text-white h6"--}}
                                {{--                                                  style="font-size: 14px"--}}
                                {{--                                                  x-text="is_coin == '1' ? to_coins_str(price.price) : '$' + price.price"></span>--}}

                                {{--                                            <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}"--}}
                                {{--                                                 alt="coins"--}}
                                {{--                                                 width="12px" class="ms-1"/>--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                            </template>
                        </div>
                    </template>

                    <div class="row mt-2">
                        <div class="mb-2">
                            <label class="form-label text-body fw-bold text-uppercase"
                                   x-text="method?.payment_title ? method?.payment_title : 'Your ' + method?.name + ' Address'">
                            </label>
                            <input wire:model="address"
                                   @class(['form-control', 'is-invalid' => $errors->has('address')])
                                   type="text">

                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{--  amount  --}}
                        <template x-if="method?.prices.length == 0">
                            <div class="mb-2">
                                <label class="form-label text-body fw-bold text-uppercase"
                                       x-text="is_coin == '1' ? 'Amount coins' : 'Amount USD'"></label>
                                <input @class(['form-control', 'is-invalid' => $errors->has('amount')])
                                       wire:model="amount"
                                       x-model="amount"
                                       type="number">

                                @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="d-flex align-items-center gap-1 mt-2 text-secondary small">
                                    <span
                                        x-text="is_coin == '1' ? 'Minimum: ' + method?.minimum : 'Minimum: $' + to_money_str(method?.minimum)"></span>
                                    <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}"
                                         alt="coins"
                                         width="12px" class="me-1"/>
                                </div>
                            </div>


                        </template>

                        {{--  fees  --}}
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-body">Fee</span>
                            <span class="d-flex align-items-center text-body fw-bold">
                                <img x-show="is_coin == '1'"
                                     src="{{ asset('assets/img/coin.png') }}"
                                     alt="coins"
                                     width="12px" class="me-1">
                                <span
                                    x-text="is_coin == '1' ? get_percentage(amount, method?.fee).toLocaleString() : '$' + get_percentage(amount, method?.fee).toFixed(2)"></span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-body">Price</span>
                            <span class="d-flex align-items-center text-body fw-bold">
                                <img x-show="is_coin == '1'"
                                     src="{{ asset('assets/img/coin.png') }}"
                                     alt="coins"
                                     width="12px" class="me-1">
                                <span
                                    x-text="is_coin == '1' ? get_total_price(method?.fee).toLocaleString() : '$' + get_total_price(method?.fee).toFixed(2)"></span>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2"
                                @click="$wire.cashout(Number(amount), method?.id, is_coin)">Withdraw
                        </button>
                        {{--                                >Withdraw</button>--}}


                        {{--<form wire:submit="cashout()">--}}

                        {{--                                @if(count($selectedMethod->prices) == 0)--}}
                        {{--                                    <div class="mb-2">--}}
                        {{--                                        <label class="form-label text-body fw-bold text-uppercase">Amount</label>--}}
                        {{--                                        <input wire:model.live.debounce.250ms="amount"--}}
                        {{--                                               @class(['form-control', 'is-invalid' => $errors->has('amount')])--}}
                        {{--                                               type="number">--}}
                        {{--                                        @if($selectedMethod->minimum)--}}
                        {{--                                            <div class="d-flex align-items-center gap-1 mt-2 text-secondary small">--}}
                        {{--                                                <span--}}
                        {{--                                                    x-text="is_coin == '1' ? 'Minimum: {{ number_format($selectedMethod->minimum) }}' : 'Minimum: ${{ to_money_str($selectedMethod->minimum, 2) }}'">--}}
                        {{--                                                </span>--}}
                        {{--                                                <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}"--}}
                        {{--                                                     alt="coins"--}}
                        {{--                                                     width="12px" class="me-1"/>--}}
                        {{--                                            </div>--}}
                        {{--                                        @endif--}}

                        {{--                                        @error('amount')--}}
                        {{--                                        <div class="invalid-feedback">{{ $message }}</div>--}}
                        {{--                                        @enderror--}}
                        {{--                                    </div>--}}
                        {{--                                @endif--}}

                        {{--                                @php--}}
                        {{--                                    $fees = percentage_value($amount, $selectedMethod->fee);--}}
                        {{--                                @endphp--}}
                        {{--                                <div class="d-flex justify-content-between mt-2">--}}
                        {{--                                    <span class="text-body">Fee</span>--}}
                        {{--                                    <span class="d-flex align-items-center text-body fw-bold">--}}
                        {{--                                        <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}" alt="coins"--}}
                        {{--                                             width="12px" class="me-1">--}}
                        {{--                                        <span--}}
                        {{--                                            x-text="is_coin == '1' ? '{{ $fees }}' : '${{ to_money_str($fees, 2) }}'"></span>--}}
                        {{--                                    </span>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="d-flex justify-content-between">--}}
                        {{--                                    <span class="text-body">Price</span>--}}
                        {{--                                    <span class="d-flex align-items-center text-body fw-bold">--}}
                        {{--                                        <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}" alt="coins"--}}
                        {{--                                             width="12px" class="me-1">--}}
                        {{--                                        <span--}}
                        {{--                                            x-text="is_coin == '1' ? '{{ $amount + $fees }}' : '${{ to_money_str($amount + $fees, 2) }}'"></span>--}}
                        {{--                                    </span>--}}
                        {{--                                </div>--}}

                        {{--                                <button type="submit" class="btn btn-primary w-100 mt-2">Withdraw</button>--}}
                        {{--                            </div>--}}
                        {{--                        </form>--}}

                    </div>

                    {{--                @if($selectedMethod)--}}
                    {{--                    <div class="modal-header">--}}
                    {{--                        <h5 class="modal-title d-flex align-items-center justify-content-center">--}}
                    {{--                            <img src="{{ Storage::url($selectedMethod->image) }}" class="object-fit-contain p-2" alt=""--}}
                    {{--                                 style="background: {{ $selectedMethod->bg_color }}; border-radius: 10px; width: 40px">--}}
                    {{--                            <span class="text-white fw-bolder ms-2"--}}
                    {{--                                  style="font-size: large">{{ $selectedMethod->name }}</span>--}}
                    {{--                        </h5>--}}
                    {{--                        <button type="button"--}}
                    {{--                                class="btn-close bg-label-primary d-flex align-items-center justify-content-center"--}}
                    {{--                                style="background: #161f2c; border: none; color: white"--}}
                    {{--                                data-bs-dismiss="modal" aria-label="Close">--}}
                    {{--                            <i class="fa-solid fa-times" style="font-weight: 600"></i>--}}
                    {{--                        </button>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="modal-body pt-0">--}}
                    {{--                        @if($selectedMethod->payment_note)--}}
                    {{--                            <div class="row mb-2">--}}
                    {{--                                <span class="text-base small">--}}
                    {{--                                    {{ $selectedMethod->payment_note }}--}}
                    {{--                                </span>--}}
                    {{--                            </div>--}}
                    {{--                        @endif--}}

                    {{--                        <form wire:submit="cashout()">--}}
                    {{--                            @if(count($selectedMethod->prices) > 0)--}}
                    {{--                                <div class="card-container py-2">--}}
                    {{--                                    @foreach($selectedMethod->prices as $price)--}}
                    {{--                                        <div--}}
                    {{--                                            @class(["shop-card shop-price-card card text-white", 'active' => $selectedPrice->id == $price->id])--}}
                    {{--                                            wire:click="selectPrice({{ $price->id }})"--}}
                    {{--                                            style="background: {{ $selectedMethod->bg_color }}">--}}
                    {{--                                            <div class="card-body text-center align-items-center d-flex pb-0 mb-0">--}}
                    {{--                                                <img src="{{ Storage::url($selectedMethod->image) }}"--}}
                    {{--                                                     class="card-img-top my-3 object-fit-contain mw-100" alt="">--}}
                    {{--                                            </div>--}}
                    {{--                                            <div class="card-footer text-center px-0 pt-0 mt-0">--}}
                    {{--                                    <span class="mb-0 fw-bold text-white h6"--}}
                    {{--                                          style="font-size: 14px">{{ '$' . number_format($price->price, 2) }}</span>--}}
                    {{--                                            </div>--}}
                    {{--                                        </div>--}}
                    {{--                                    @endforeach--}}
                    {{--                                </div>--}}
                    {{--                            @endif--}}

                    {{--                            <div class="row mt-2">--}}
                    {{--                                <div class="mb-2">--}}
                    {{--                                    <label class="form-label text-body fw-bold text-uppercase">--}}
                    {{--                                        {{ !empty($selectedMethod->payment_title) ? $selectedMethod->payment_title : "Your {$selectedMethod->name} Address" }}--}}
                    {{--                                    </label>--}}
                    {{--                                    <input wire:model="address"--}}
                    {{--                                           @class(['form-control', 'is-invalid' => $errors->has('address')])--}}
                    {{--                                           type="text">--}}

                    {{--                                    @error('address')--}}
                    {{--                                    <div class="invalid-feedback">{{ $message }}</div>--}}
                    {{--                                    @enderror--}}
                    {{--                                </div>--}}

                    {{--                                @if(count($selectedMethod->prices) == 0)--}}
                    {{--                                    <div class="mb-2">--}}
                    {{--                                        <label class="form-label text-body fw-bold text-uppercase">Amount</label>--}}
                    {{--                                        <input wire:model.live.debounce.250ms="amount"--}}
                    {{--                                               @class(['form-control', 'is-invalid' => $errors->has('amount')])--}}
                    {{--                                               type="number">--}}
                    {{--                                        @if($selectedMethod->minimum)--}}
                    {{--                                            <div class="d-flex align-items-center gap-1 mt-2 text-secondary small">--}}
                    {{--                                                <span--}}
                    {{--                                                    x-text="is_coin == '1' ? 'Minimum: {{ number_format($selectedMethod->minimum) }}' : 'Minimum: ${{ to_money_str($selectedMethod->minimum, 2) }}'">--}}
                    {{--                                                </span>--}}
                    {{--                                                <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}"--}}
                    {{--                                                     alt="coins"--}}
                    {{--                                                     width="12px" class="me-1"/>--}}
                    {{--                                            </div>--}}
                    {{--                                        @endif--}}

                    {{--                                        @error('amount')--}}
                    {{--                                        <div class="invalid-feedback">{{ $message }}</div>--}}
                    {{--                                        @enderror--}}
                    {{--                                    </div>--}}
                    {{--                                @endif--}}

                    {{--                                @php--}}
                    {{--                                    $fees = percentage_value($amount, $selectedMethod->fee);--}}
                    {{--                                @endphp--}}
                    {{--                                <div class="d-flex justify-content-between mt-2">--}}
                    {{--                                    <span class="text-body">Fee</span>--}}
                    {{--                                    <span class="d-flex align-items-center text-body fw-bold">--}}
                    {{--                                        <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}" alt="coins"--}}
                    {{--                                             width="12px" class="me-1">--}}
                    {{--                                        <span--}}
                    {{--                                            x-text="is_coin == '1' ? '{{ $fees }}' : '${{ to_money_str($fees, 2) }}'"></span>--}}
                    {{--                                    </span>--}}
                    {{--                                </div>--}}
                    {{--                                <div class="d-flex justify-content-between">--}}
                    {{--                                    <span class="text-body">Price</span>--}}
                    {{--                                    <span class="d-flex align-items-center text-body fw-bold">--}}
                    {{--                                        <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}" alt="coins"--}}
                    {{--                                             width="12px" class="me-1">--}}
                    {{--                                        <span--}}
                    {{--                                            x-text="is_coin == '1' ? '{{ $amount + $fees }}' : '${{ to_money_str($amount + $fees, 2) }}'"></span>--}}
                    {{--                                    </span>--}}
                    {{--                                </div>--}}

                    {{--                                <button type="submit" class="btn btn-primary w-100 mt-2">Withdraw</button>--}}
                    {{--                            </div>--}}
                    {{--                        </form>--}}
                    {{--                    </div>--}}
                    {{--                @endif--}}
                </div>
            </div>
        </div>
    </div>

</div>


