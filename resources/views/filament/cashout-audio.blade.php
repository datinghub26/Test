<div>
    <audio id="adminCashoutAudio" src="{{ asset('assets/sounds/coin-withdraw.mp3') }}" preload="auto"></audio>

    <script>
        (function () {
            let audioUnlocked = false;

            function getAudio() {
                return document.getElementById('adminCashoutAudio');
            }

            // Unlock audio on first user interaction silently
            function unlockAudio() {
                if (audioUnlocked) return;
                const audio = getAudio();
                if (audio) {
                    const originalMuted = audio.muted;
                    audio.muted = true;
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        audio.muted = originalMuted;
                        audioUnlocked = true;
                        window.removeEventListener('click', unlockAudio);
                        window.removeEventListener('keydown', unlockAudio);
                    }).catch(() => {
                        audio.muted = originalMuted;
                    });
                }
            }

            window.addEventListener('click', unlockAudio, { once: false });
            window.addEventListener('keydown', unlockAudio, { once: false });

            window.playAdminCashoutSound = function (notificationId) {
                try {
                    const playedKey = 'played_admin_cashouts';
                    let played = [];
                    try {
                        played = JSON.parse(localStorage.getItem(playedKey) || '[]');
                    } catch (e) {
                        played = [];
                    }

                    if (notificationId && played.includes(notificationId)) {
                        return; // Prevent duplicate sound playback for the same event
                    }

                    const audio = getAudio();
                    if (audio) {
                        audio.currentTime = 0;
                        audio.volume = 0.5;
                        const promise = audio.play();
                        if (promise !== undefined) {
                            promise.then(() => {
                                if (notificationId) {
                                    played.push(notificationId);
                                    if (played.length > 50) played.shift();
                                    localStorage.setItem(playedKey, JSON.stringify(played));
                                }
                            }).catch((err) => {
                                console.log('Autoplay restriction: waiting for user interaction.', err);
                            });
                        }
                    }
                } catch (err) {
                    console.warn('Cashout audio notice:', err);
                }
            };

            // Listen for window event dispatched by Filament or Livewire
            window.addEventListener('cashout-notification', function (e) {
                const id = e.detail?.id || e.detail?.cashout_id || ('cashout_' + Date.now());
                window.playAdminCashoutSound(id);
            });
        })();
    </script>
</div>
