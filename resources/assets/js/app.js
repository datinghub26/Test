// import '../../js/bootstrap';
import Swiper from "swiper";
import {
    Navigation,
    Pagination,
    Autoplay,
    EffectCreative,
    EffectFade,
    EffectCoverflow,
    EffectCards
} from "swiper/modules";
import 'swiper/css';
import '../../../vendor/masmerise/livewire-toaster/resources/js'; // 👈
// import data from '@emoji-mart/data/sets/1/apple.json';
import {Picker} from 'emoji-mart'
// import 'slick-carousel'
// import 'slick-carousel/slick/slick.css'
import '../../js/app.js';

window.addEventListener('load', function () {
    if(typeof localStorage !== 'undefined' && localStorage.getItem('isCoin') == null) {
        localStorage.setItem('isCoin', '1');
    }

    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.classList.add('hidden');
        setTimeout(() => preloader.style.display = 'none', 500); // Remove from DOM after fade-out
    }

    const {PerfectScrollbar} = window
    if (PerfectScrollbar) {
        document.querySelectorAll('.modal-dialog-scrollable .modal-body').forEach(el => {
            new PerfectScrollbar(el, {
                wheelPropagation: false
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    function initChatPerfectScrollbar() {
        const {PerfectScrollbar} = window;
        if (PerfectScrollbar) {
            const chatBox = document.querySelector('.custom-chat-box .custom-card-body');
            if (chatBox) {
                new PerfectScrollbar(chatBox, {
                    wheelPropagation: false
                });
            }
        }
    }

    function scrollToBottom() {
        let messages = $('.message');
        if (!messages.length > 0) {
            return;
        }

        let scrollBottomPos = messages.last().position().top;
        console.log("scrollBottomPos: " + scrollBottomPos);
        $('.custom-chat-box .custom-card-body').animate({scrollTop: scrollBottomPos}, 1000);
    }

    document.addEventListener('chat-init', function () {
        initChatPerfectScrollbar();
        scrollToBottom();

        const emojiPickerBtn = document.getElementById('emoji-picker-btn');
        if (emojiPickerBtn) {
            emojiPickerBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                console.log('emoji picker button clicked');
                toggleEmojiPicker();
            });
        }

        // Send Button Click
        const sendBtn = document.getElementById('send-message-btn');
        if (sendBtn) {
            sendBtn.addEventListener('click', hideEmojiPicker);
        }
    });

    window.addEventListener('scroll-chat-to-bottom', scrollToBottom);

    // Emoji Picker Initialization
    const picker = new Picker({
        // data,
        // set: 'apple',
        onEmojiSelect: (emoji) => {
            const input = document.querySelector('#message-to-send');
            if (input) {
                input.value += emoji.native;
                input.dispatchEvent(new Event('input'));
            }
        },
        onClickOutside: () => {
            hideEmojiPicker(true);
        }
    });

    function hideEmojiPicker(flex = false) {
        const emojiPickerElement = document.querySelector('em-emoji-picker');
        if (!emojiPickerElement) return;
        const root = emojiPickerElement.shadowRoot.querySelector("#root");
        if (!emojiPickerElement || !root) return;

        if (flex && root.style.display === 'flex') {
            root.style.display = 'none';
            return;
        }

        root.style.display = 'none';
    }

    // Toggle Emoji Picker Visibility
    function toggleEmojiPicker() {
        const emojiPicker = document.getElementById('emoji-picker');
        if (!emojiPicker.contains(picker)) {
            emojiPicker.appendChild(picker);
            return;
        }

        const root = document.querySelector("em-emoji-picker").shadowRoot.querySelector("#root");
        if (root) {
            root.style.display = root.style.display === 'none' ? 'flex' : 'none';
        }
    }

    // Hide Emoji Picker when clicked outside
    document.addEventListener('click', function (event) {
        const emojiPicker = document.querySelector('em-emoji-picker');
        const emojiPickerBtn = document.getElementById('emoji-picker-btn');
        if (emojiPicker && !emojiPicker.contains(event.target) && event.target !== emojiPickerBtn) {
            hideEmojiPicker();
        }
    });
});

window.swiper = function (selector, options = {}) {
    return new Swiper(selector, {
        modules: [Navigation, Pagination, Autoplay, EffectCreative, EffectFade, EffectCards],
        slidesPerView: 'auto',
        ...options
    });
}

window.timer = function (timestamp) {
    return {
        targetTime: timestamp,
        timeLeft: '...',

        init() {
            this.countdown();
        },

        countdown() {
            const interval = setInterval(() => {
                const now = Math.floor(new Date().getTime() / 1000);
                const distance = this.targetTime - now;

                const days = Math.floor(distance / (60 * 60 * 24));
                const hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
                const minutes = Math.floor((distance % (60 * 60)) / 60);
                const seconds = distance % 60;

                this.timeLeft = '';
                if (days > 0) this.timeLeft += days + "d ";
                if (days > 0 || hours > 0) this.timeLeft += hours + "h ";
                if (days > 0 || hours > 0 || minutes > 0) this.timeLeft += minutes + "m ";
                this.timeLeft += seconds + "s";

                if (distance < 0) {
                    clearInterval(interval);
                    this.timeLeft = "EXPIRED";
                }
            }, 100);
        }
    };
}

window.decodeHtml = function (string) {
    return String(string).replace(/&amp;/g, '&')
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&nbsp;/g, ' ')
        .replace(/&shy;/g, '')
        .replace(/&euro;/g, '€')
        .replace(/&copy;/g, '©')
        .replace(/&reg;/g, '®')
        .replace(/&trade;/g, '™')
        .replace(/&cent;/g, '¢')
        .replace(/&pound;/g, '£');
}

