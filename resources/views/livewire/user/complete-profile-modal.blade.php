<div>
    <style>
        #completeProfileModal .modal-dialog {
            max-width: 410px;
            margin: 1.25rem auto;
        }
        #completeProfileModal .modal-content {
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: #131823 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        #completeProfileModal .avatar-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 6px;
            max-height: 185px;
            overflow-y: auto;
            padding: 6px 4px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
        #completeProfileModal .avatar-item {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.15s ease-in-out;
            margin: 0 auto;
        }
        #completeProfileModal .avatar-item:hover {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.06);
        }
        #completeProfileModal .avatar-item img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        #completeProfileModal .avatar-item.selected {
            border-color: #37e780 !important;
            background: rgba(55, 231, 128, 0.15) !important;
            box-shadow: 0 0 10px rgba(55, 231, 128, 0.35);
        }
        #completeProfileModal .avatar-container::-webkit-scrollbar {
            width: 5px;
        }
        #completeProfileModal .avatar-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 4px;
        }
        #completeProfileModal .avatar-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 4px;
        }
        #completeProfileModal .avatar-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>

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
                    <div class="d-flex col-12 align-items-center p-3 p-sm-4">
                        <div class="w-100 mx-auto">
                            <p class="mb-3 text-center small text-secondary">Choose your username and avatar, and start the adventure! 🚀</p>

                            <form class="mb-0">
                                <div class="mb-3">
                                    <label for="username" class="form-label text-body small fw-semibold">Username</label>
                                    <div class="input-group input-group-merge has-validation">
                                        <input @class(['form-control', 'is-invalid' => $errors->has('username')])
                                               x-model="currentUsername"
                                               type="text"
                                               placeholder="Choose your username">

                                        <span class="input-group-text cursor-pointer p-1">
                                           <button @click="randomize" class="btn btn-sm btn-primary"
                                                   type="button">Randomize</button>
                                        </span>

                                        @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label text-body small fw-semibold mb-0">Avatar</label>
                                        <span class="text-secondary" style="font-size: 11px;">Scroll for more</span>
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
