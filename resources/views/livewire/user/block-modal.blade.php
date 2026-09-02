<div wire:poll.10s.keepalive>
    <div
        wire:ignore.self
        data-bs-keyboard="false"
        data-bs-backdrop="static"
        class="modal fade"
        id="blockModal"
        tabindex="-1"
        aria-labelledby="blockModalLabel"
        aria-hidden="true"
        x-data="{ show: @entangle('show').live, message: @entangle('message').live }"
        x-init="() => {
            if (show) {
                $('#blockModal').modal('show');
            }

            $watch('show', value => {
                if (value) {
                    $('#blockModal').modal('show');
                } else {
                    $('#blockModal').modal('hide');
                }
            });
         }">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-danger">
                <div class="modal-body text-center">
                    <i class="fa-solid fa-user-slash text-white" style="font-size: 30px"></i>
                    <h5 class="f-md mt-3 text-white" x-text="message"></h5>
                </div>
            </div>
        </div>
    </div>
</div>
