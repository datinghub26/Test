<div>
    <audio id="adminCashoutAudio" src="{{ asset('assets/sounds/coin-withdraw.mp3') }}" preload="auto"></audio>

    <script>
        (function () {
            let audioUnlocked = false;

            function getAudio() {
                return document.getElementById('adminCashoutAudio');
            }

            // Unlock audio on first user interaction to satisfy browser autoplay policy
            function unlockAudio() {
                if (audioUnlocked) return;
                const audio = getAudio();
                if (audio) {
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        audioUnlocked = true;
                        window.removeEventListener('click', unlockAudio);
                        window.removeEventListener('keydown', unlockAudio);
                    }).catch(() => {});
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

            // Watch for Filament notifications appearing in DOM
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1) {
                            const text = (node.innerText || node.textContent || '').toLowerCase();
                            if (text.includes('cashout') || text.includes('withdrawal')) {
                                const id = node.getAttribute('data-id') || text.substring(0, 30);
                                window.playAdminCashoutSound(id);
                            }
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                observer.observe(document.body, { childList: true, subtree: true });
            });
            if (document.body) {
                observer.observe(document.body, { childList: true, subtree: true });
            }
        })();
    </script>
</div>
