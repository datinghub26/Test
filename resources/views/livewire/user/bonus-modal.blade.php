<div>
    <div
        wire:ignore.self
        class="modal fade"
        id="bonusModal"
        tabindex="-1"
        aria-labelledby="bonusModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content ">
                <div class="modal-header mb-0 pb-0">
                    <h5 class="modal-title text-body f-md">Redeem Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="card">
                        <div class="card-body">
                            <form wire:submit.prevent="redeem">
                                <div class="mb-3">
                                    <input
                                        @class(['form-control', 'is-invalid' => $errors->has('code')])
                                        type="text"
                                        id="code"
                                        wire:model.defer="code"
                                        placeholder="Enter your bonus code"
                                    >
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Claim</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
