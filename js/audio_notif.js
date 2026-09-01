/**
 * audio_notif.js — Audio Notification Engine untuk Sistem Absensi
 * Mendukung audio file (.mp3, .wav, .ogg) dengan fallback Web Audio API synthesizer
 */

const AbsenAudio = (function () {
    // Tentukan base path audio relatif terhadap halaman saat ini
    const isKaryawanOrAdmin = window.location.pathname.includes('/karyawan/') || 
                              window.location.pathname.includes('/admin/') || 
                              window.location.pathname.includes('/owner/') ||
                              window.location.pathname.includes('/absen/');
    const basePath = isKaryawanOrAdmin ? '../audio/' : 'audio/';

    // Daftar file audio yang dicoba secara berurutan
    const audioSources = {
        terlambat: [
            basePath + 'terlambat.wav',
            basePath + 'terlambat.m4a',
            basePath + 'terlambat.mp3',
            basePath + 'terlambat.ogg'
        ],
        sisa_5menit: [
            basePath + 'sisa_5menit.wav',
            basePath + 'sisa_5menit.m4a',
            basePath + 'sisa_5menit.mp3',
            basePath + 'sisa_5menit.ogg',
            basePath + 'warning_5menit.mp3'
        ]
    };

    let audioContext = null;
    let isUnlocked = false;

    // Inisialisasi & unlock Web Audio Context pada interaksi pertama pengguna
    function initAudioContext() {
        if (!audioContext) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                audioContext = new AudioCtx();
            }
        }
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
        isUnlocked = true;
    }

    // Auto-unlock pada event interaksi pertama
    ['click', 'touchstart', 'keydown'].forEach(evt => {
        document.addEventListener(evt, () => initAudioContext(), { once: true, passive: true });
    });

    // Fallback Synthesizer menggunakan Web Audio API jika file audio tidak ditemukan
    function playSynthFallback(type) {
        try {
            initAudioContext();
            if (!audioContext) return;

            const now = audioContext.currentTime;

            if (type === 'terlambat') {
                // Alarm nada peringatan terlambat (2 beep turun tegas)
                [0, 0.25, 0.5].forEach((offset, idx) => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(idx % 2 === 0 ? 520 : 380, now + offset);
                    gain.gain.setValueAtTime(0.2, now + offset);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + offset + 0.2);
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(now + offset);
                    osc.stop(now + offset + 0.2);
                });
            } else if (type === 'sisa_5menit') {
                // Chime nada ramah pengingat 5 menit (3 nada naik lembut)
                const freqs = [523.25, 659.25, 783.99]; // C5, E5, G5
                freqs.forEach((freq, idx) => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.type = 'sine';
                    const startTime = now + idx * 0.15;
                    osc.frequency.setValueAtTime(freq, startTime);
                    gain.gain.setValueAtTime(0.18, startTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.35);
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(startTime);
                    osc.stop(startTime + 0.35);
                });
            }
        } catch (e) {
            console.warn('[AbsenAudio] Synth fallback error:', e);
        }
    }

    // Fungsi memutar audio dari file dengan fallback ke format lain atau synth
    function playAudioFile(type, sourceIndex = 0) {
        const sources = audioSources[type] || [];
        if (sourceIndex >= sources.length) {
            // Semua file audio gagal dimuat -> Jalankan synth fallback
            playSynthFallback(type);
            return;
        }

        const src = sources[sourceIndex];
        const audio = new Audio();
        audio.src = src;
        audio.preload = 'auto';

        const playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                // Berhasil diputar
            }).catch(err => {
                // Jika error karena file 404 / decode error, coba file berikutnya
                if (err.name === 'NotSupportedError' || err.name === 'MediaError' || !audio.duration) {
                    playAudioFile(type, sourceIndex + 1);
                } else if (err.name === 'NotAllowedError') {
                    // Browser memblokir autoplay karena belum ada interaksi pengguna
                    console.info('[AbsenAudio] Autoplay blocked, showing prompt to interact.');
                    showUnlockPrompt(type);
                } else {
                    playAudioFile(type, sourceIndex + 1);
                }
            });
        }

        audio.onerror = function () {
            // Coba ekstensi berikutnya
            playAudioFile(type, sourceIndex + 1);
        };
    }

    // Prompt kecil jika autoplay dicegah oleh browser
    function showUnlockPrompt(type) {
        const existing = document.getElementById('absenAudioPrompt');
        if (existing) return;

        const banner = document.createElement('div');
        banner.id = 'absenAudioPrompt';
        banner.style.cssText = `
            position: fixed; bottom: 20px; right: 20px; z-index: 99999;
            background: #1e2228; color: #fff; padding: 12px 18px;
            border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            display: flex; align-items: center; gap: 12px; font-family: sans-serif;
            font-size: 13px; animation: slideUp 0.3s ease;
        `;
        banner.innerHTML = `
            <span>🔊 <strong>Aktifkan Suara Notifikasi</strong></span>
            <button id="btnEnableAudio" style="
                background: #4f46e5; color: #fff; border: none; padding: 6px 12px;
                border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 12px;
            ">Putar Suara</button>
            <button id="btnCloseAudioPrompt" style="
                background: transparent; color: #9ca3af; border: none; font-size: 16px;
                cursor: pointer; padding: 0 4px;
            ">&times;</button>
        `;
        document.body.appendChild(banner);

        document.getElementById('btnEnableAudio').addEventListener('click', () => {
            initAudioContext();
            playAudioFile(type, 0);
            banner.remove();
        });

        document.getElementById('btnCloseAudioPrompt').addEventListener('click', () => {
            banner.remove();
        });
    }

    // Public API
    return {
        play: function (type) {
            initAudioContext();
            playAudioFile(type, 0);
        },
        playTerlambat: function () {
            this.play('terlambat');
        },
        playSisa5Menit: function () {
            this.play('sisa_5menit');
        },
        test: function (type) {
            initAudioContext();
            this.play(type);
        }
    };
})();

// Ekspor ke global window
window.AbsenAudio = AbsenAudio;
