{{-- Popup sukses beranimasi — tampil sekali dari flash session('success') --}}
@if (session('success'))
<div id="success-popup" class="sp-overlay" role="alert">
  <style>
    .sp-overlay {
      position: fixed; inset: 0; z-index: 120;
      display: flex; align-items: center; justify-content: center;
      padding: 1rem;
      background: rgba(4, 18, 13, .38);
      -webkit-backdrop-filter: blur(3px); backdrop-filter: blur(3px);
      animation: spFade .3s ease both;
      transition: opacity .3s ease;
    }
    .sp-overlay.sp-hide { opacity: 0; pointer-events: none; }
    .sp-card {
      width: 100%; max-width: 350px;
      padding: 30px 30px 24px;
      text-align: center;
      border-radius: 26px;
      background: rgba(255, 255, 255, .82);
      -webkit-backdrop-filter: blur(20px) saturate(1.5); backdrop-filter: blur(20px) saturate(1.5);
      border: 1px solid rgba(255, 255, 255, .85);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9), 0 35px 80px rgba(6, 40, 30, .35);
      animation: spPop .5s cubic-bezier(.2, 1.35, .35, 1) both;
      transition: transform .3s ease;
    }
    .sp-overlay.sp-hide .sp-card { transform: scale(.92) translateY(8px); }
    .sp-medal {
      position: relative;
      width: 76px; height: 76px;
      margin: 0 auto 16px;
      border-radius: 24px;
      display: flex; align-items: center; justify-content: center;
      background: linear-gradient(135deg, #34d399, #0d9488);
      box-shadow: 0 18px 35px rgba(13, 148, 136, .38), inset 0 1px 0 rgba(255, 255, 255, .35);
      animation: spMedal .55s cubic-bezier(.2, 1.5, .35, 1) .1s both;
    }
    .sp-medal::after {
      content: ''; position: absolute; inset: -9px;
      border-radius: 30px;
      border: 2px solid rgba(52, 211, 153, .45);
      animation: spRing 1.6s ease-out .35s both;
    }
    .sp-medal svg { width: 42px; height: 42px; }
    .sp-circle {
      fill: none; stroke: rgba(255, 255, 255, .35); stroke-width: 2.4;
      stroke-dasharray: 151; stroke-dashoffset: 151;
      animation: spDraw .6s ease .3s forwards;
    }
    .sp-check {
      fill: none; stroke: #fff; stroke-width: 3.4;
      stroke-linecap: round; stroke-linejoin: round;
      stroke-dasharray: 40; stroke-dashoffset: 40;
      animation: spDraw .45s ease .75s forwards;
    }
    .sp-title {
      font-size: 21px; font-weight: 800; letter-spacing: -.02em; color: #14231f;
      animation: spUp .45s ease .35s both;
    }
    .sp-msg {
      margin-top: 6px; font-size: 13.5px; line-height: 1.55; color: #5b6d67;
      animation: spUp .45s ease .45s both;
    }
    .sp-bar {
      margin-top: 20px; height: 4px; border-radius: 999px;
      background: rgba(6, 60, 45, .08); overflow: hidden;
    }
    .sp-bar span {
      display: block; height: 100%; width: 100%;
      border-radius: 999px;
      background: linear-gradient(90deg, #34d399, #14b8a6);
      transform-origin: left;
      animation: spShrink 4.2s linear forwards;
    }
    @keyframes spFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes spPop { from { opacity: 0; transform: scale(.88) translateY(16px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    @keyframes spMedal { from { transform: scale(.4); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @keyframes spRing { from { transform: scale(.8); opacity: 1; } to { transform: scale(1.25); opacity: 0; } }
    @keyframes spDraw { to { stroke-dashoffset: 0; } }
    @keyframes spUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes spShrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }
  </style>
  <div class="sp-card">
    <div class="sp-medal">
      <svg viewBox="0 0 52 52" aria-hidden="true">
        <circle class="sp-circle" cx="26" cy="26" r="24" />
        <path class="sp-check" d="M15 27l7.5 7.5L37 19" />
      </svg>
    </div>
    <h3 class="sp-title">Berhasil!</h3>
    <p class="sp-msg">{{ session('success') }}</p>
    <div class="sp-bar"><span></span></div>
  </div>
</div>
<script>
  (function () {
    var popup = document.getElementById('success-popup');
    if (!popup) return;
    var dismissed = false;
    function dismiss() {
      if (dismissed) return;
      dismissed = true;
      popup.classList.add('sp-hide');
      setTimeout(function () { popup.remove(); }, 320);
    }
    setTimeout(dismiss, 4200);
    popup.addEventListener('click', dismiss);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') dismiss(); });
  })();
</script>
@endif
