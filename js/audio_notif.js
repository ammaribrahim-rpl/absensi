/**
 * audio_notif.js — Audio Notification Engine untuk Sistem Absensi
 * Mendukung 5 event suara: Absen Masuk, Istirahat, Terlambat, Sisa 5 Menit, Pulang.
 * Menggunakan Audio File (.wav/.m4a/.mp3) + Web Speech API + Web Audio Synth Fallback.
 */

const AbsenAudio = (function () {
    const isSubFolder = window.location.pathname.includes('/karyawan/') || 
                        window.location.pathname.includes('/admin/') || 
                        window.location.pathname.includes('/owner/') ||
                        window.location.pathname.includes('/absen/');
    const basePath = isSubFolder ? '../audio/' : 'audio/';

    const audioSources = {
        absen_masuk: [
            basePath + 'absen_masuk.wav',
            basePath + 'absen_masuk.m4a',
            basePath + 'absen_masuk.mp3'
        ],
        istirahat: [
            basePath + 'istirahat.wav',
            basePath + 'istirahat.m4a',
            basePath + 'istirahat.mp3'
        ],
        terlambat: [
            basePath + 'terlambat.wav',
            basePath + 'terlambat.m4a',
            basePath + 'terlambat.mp3'
        ],
        sisa_5menit: [
            basePath + 'sisa_5menit.wav',
            basePath + 'sisa_5menit.m4a',
            basePath + 'sisa_5menit.mp3'
        ],
        pulang: [
            basePath + 'pulang.wav',
            basePath + 'pulang.m4a',
            basePath + 'pulang.mp3'
        ]
    };

    const speechTexts = {
        absen_masuk: "Absen masuk berhasil. Selamat bekerja!",
        istirahat: "Selamat beristirahat. Harap kembali tepat waktu.",
        terlambat: "Perhatian, anda tercatat terlambat. Harap perhatikan waktu anda.",
        sisa_5menit: "Waktu istirahat tersisa lima menit lagi. Harap bersiap kembali bekerja.",
        pulang: "Absen pulang berhasil. Terima kasih atas kerja keras anda hari ini, hati-hati di jalan."
    };

    let audioContext = null;

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
    }

    ['click', 'touchstart', 'keydown'].forEach(evt => {
        document.addEventListener(evt, () => initAudioContext(), { once: true, passive: true });
    });

    // Fallback: Web Speech API (Suara Bahasa Indonesia Jernih & Nyaring)
    function speakText(type) {
        if (!('speechSynthesis' in window)) return false;
        try {
            window.speechSynthesis.cancel(); // Hentikan ucapan sebelumnya jika ada
            const text = speechTexts[type] || "Notifikasi absensi.";
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.95;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;

            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID'));
            if (idVoice) utterance.voice = idVoice;

            window.speechSynthesis.speak(utterance);
            return true;
        } catch (e) {
            return false;
        }
    }

    // Fallback: Web Audio Synth Chime
    function playSynthFallback(type) {
        try {
            initAudioContext();
            if (!audioContext) return;
            const now = audioContext.currentTime;

            if (type === 'terlambat') {
                [0, 0.25, 0.5].forEach((offset, idx) => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(idx % 2 === 0 ? 520 : 380, now + offset);
                    gain.gain.setValueAtTime(0.3, now + offset);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + offset + 0.2);
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(now + offset);
                    osc.stop(now + offset + 0.2);
                });
            } else {
                const freqs = [523.25, 659.25, 783.99]; // C5, E5, G5
                freqs.forEach((freq, idx) => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.type = 'sine';
                    const startTime = now + idx * 0.15;
                    osc.frequency.setValueAtTime(freq, startTime);
                    gain.gain.setValueAtTime(0.25, startTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.35);
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(startTime);
                    osc.stop(startTime + 0.35);
                });
            }
        } catch (e) {}
    }

    function playAudioFile(type, sourceIndex = 0) {
        const sources = audioSources[type] || [];
        if (sourceIndex >= sources.length) {
            if (!speakText(type)) {
                playSynthFallback(type);
            }
            return;
        }

        const src = sources[sourceIndex];
        const audio = new Audio();
        audio.src = src;
        audio.preload = 'auto';
        audio.volume = 1.0;

        const playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                // Success
            }).catch(err => {
                if (err.name === 'NotAllowedError') {
                    showUnlockPrompt(type);
                } else {
                    playAudioFile(type, sourceIndex + 1);
                }
            });
        }

        audio.onerror = function () {
            playAudioFile(type, sourceIndex + 1);
        };
    }

    function showUnlockPrompt(type) {
        const existing = document.getElementById('absenAudioPrompt');
        if (existing) return;

        const banner = document.createElement('div');
        banner.id = 'absenAudioPrompt';
        banner.style.cssText = `
            position: fixed; bottom: 20px; right: 20px; z-index: 99999;
            background: #170d2b; color: #fff; padding: 12px 18px;
            border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.35);
            display: flex; align-items: center; gap: 12px; font-family: sans-serif;
            font-size: 13px; border: 1px solid rgba(255,255,255,0.15);
        `;
        banner.innerHTML = `
            <span>🔊 <strong>Aktifkan Suara Notifikasi</strong></span>
            <button id="btnEnableAudio" style="
                background: #7e22ce; color: #fff; border: none; padding: 6px 14px;
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

    return {
        play: function (type) {
            initAudioContext();
            playAudioFile(type, 0);
        },
        playAbsenMasuk: function () { this.play('absen_masuk'); },
        playIstirahat: function () { this.play('istirahat'); },
        playTerlambat: function () { this.play('terlambat'); },
        playSisa5Menit: function () { this.play('sisa_5menit'); },
        playPulang: function () { this.play('pulang'); },
        test: function (type) {
            initAudioContext();
            this.play(type);
        }
    };
})();

window.AbsenAudio = AbsenAudio;
