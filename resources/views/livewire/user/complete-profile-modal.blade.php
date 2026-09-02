<div>
    <div
        x-data="{
                avatars: {{ json_encode($this->avatars()) }},
                names: {{ json_encode($this->names()) }},
                currentAvatar: '{{ $this->avatars()[$this->avatar] }}',
                currentAvatarIdx: {{ $this->avatar }},
                currentUsername: '{{ $this->username }}',
                randomize() {
                    this.currentUsername = this.names[Math.floor(Math.random() * this.names.length)];
                },
                save() {
                    $wire.username = this.currentUsername;
                    $wire.avatar = this.currentAvatarIdx;
                    $wire.call('save');
                },
                selectAvatar(avatar) {
                    this.currentAvatar = this.avatars[avatar];
                    this.currentAvatarIdx = avatar;
                }
            }"
        wire:ignore.self
        data-bs-keyboard="false" data-bs-backdrop="static"
        class="modal fade show"
        id="completeProfileModal"
        tabindex="-1"
        aria-labelledby="completeProfileModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="d-flex col-12  align-items-center p-sm-5 p-4">
                        <div class="w-px-400 mx-auto">
                            <p class="mb-4">Choose your username and avatar, and start the adventure! 🚀</p>

                            <form class="mb-3">
                                <div class="mb-3">
                                    <label for="email" class="form-label text-body">Username</label>
                                    <div class="input-group input-group-merge has-validation">
                                        <input @class(['form-control', 'is-invalid' => $errors->has('username')])
                                               x-model="currentUsername"
                                               type="text"
                                               placeholder="Choose your username">

                                        <span class="input-group-text cursor-pointer">
                                           <button @click="randomize" class="btn btn-primary"
                                                   type="button">Randomize</button>
                                        </span>

                                        @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label text-body" for="password">Avatar</label>
                                    </div>

                                    <div class="avatar-container">
                                        <template x-for="(value, index) in avatars" :key="index">
                                            <div @click="selectAvatar(index + 1)"
                                                 :class="{'avatar-item': true, 'selected': currentAvatarIdx === index + 1}">
                                                <img :src="value" alt="Avatar">
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary w-100"
                                            @click.prevent="save">Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $('#completeProfileModal').modal('show');
</script>
@endscript
