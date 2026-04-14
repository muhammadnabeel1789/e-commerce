<div id="fs-splash" class="fixed inset-0 flex items-center justify-center overflow-hidden transition-opacity duration-700" style="z-index: 999999; background-color: #ffffff;">
    
    <div class="relative z-10 flex flex-col items-center justify-center">
        
        {{-- Animated Logo Icon --}}
        @if(isset($shop_logo) && $shop_logo)
            <img src="{{ asset($shop_logo) }}" alt="{{ $shop_name ?? 'Logo' }}" class="w-32 h-32 md:w-40 md:h-40 mb-6 object-contain animate-slide-up-text" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));" />
            <div class="text-center overflow-hidden inline-block">
                <h1 class="text-3xl md:text-5xl font-black text-black tracking-[0.1em] mb-2 uppercase animate-slide-up-text">
                    {{ $shop_name ?? 'FASHION STORE' }}
                </h1>
            </div>
        @else
            <div class="w-32 h-32 md:w-40 md:h-40 mb-6 flex items-center justify-center relative perspective-[1000px]">
                <svg viewBox="0 0 100 80" class="w-full h-full drop-shadow-xl overflow-visible" xmlns="http://www.w3.org/2000/svg">
                  
                  <!-- Left half (Origami folding from left) -->
                  <g class="animate-fold-left" style="transform-origin: 50% 50%;">
                      <path d="M 50 10 L 30 20 L 15 45 L 25 55 L 35 40 L 45 70 L 58 52 Z" fill="#d0c3b0" />
                      <path d="M 30 20 L 15 45 L 25 55 Z" fill="#bbae97" /> 
                  </g>
                  
                  <!-- Right half (Origami folding from right) -->
                  <g class="animate-fold-right" style="transform-origin: 50% 50%;">
                      <path d="M 45 35 L 55 10 L 75 20 L 90 45 L 80 55 L 70 40 L 60 70 Z" fill="#bd904d" />
                      <path d="M 75 20 L 90 45 L 80 55 Z" fill="#a87f42" />
                  </g>

                  <!-- Decorative Pin (Draws in) -->
                  <g class="animate-draw-pin">
                      <circle cx="50" cy="45" r="3" fill="#ffffff" />
                      <line x1="53" y1="45" x2="70" y2="45" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                  </g>
                </svg>
            </div>

            {{-- Typography --}}
            <div class="text-center overflow-hidden">
                <?php $p = explode(' ', $shop_name ?? 'FASHION STORE'); ?>
                <h1 class="text-4xl md:text-5xl font-black text-black tracking-[0.1em] mb-2 uppercase animate-slide-up-text inline-block">
                    {{ $p[0] }}
                </h1>
            </div>
            @if(count($p) > 1)
            <?php array_shift($p); ?>
            <div class="text-center overflow-hidden">
                <p class="text-[#417b9b] text-sm md:text-base tracking-[0.4em] uppercase font-bold mt-1 animate-slide-up-text" style="animation-delay: 0.2s; opacity: 0; animation-fill-mode: forwards;">
                    {{ implode(' ', $p) }}
                </p>
            </div>
            @endif
        @endif
    </div>
</div>

<style>
    /* Origami Folding Animations */
    @keyframes foldLeft {
        0% { transform: translateX(-60px) rotateY(-45deg) scale(0.8); opacity: 0; }
        50% { transform: translateX(5px) rotateY(10deg) scale(1.02); opacity: 1; }
        100% { transform: translateX(0) rotateY(0deg) scale(1); opacity: 1; }
    }
    .animate-fold-left {
        animation: foldLeft 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes foldRight {
        0% { transform: translateX(60px) rotateY(45deg) scale(0.8); opacity: 0; }
        50% { transform: translateX(-5px) rotateY(-10deg) scale(1.02); opacity: 1; }
        100% { transform: translateX(0) rotateY(0deg) scale(1); opacity: 1; }
    }
    .animate-fold-right {
        animation: foldRight 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        animation-delay: 0.1s;
        opacity: 0;
    }

    @keyframes drawPin {
        0% { transform: scaleX(0); opacity: 0; }
        100% { transform: scaleX(1); opacity: 1; }
    }
    .animate-draw-pin {
        animation: drawPin 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        animation-delay: 0.4s;
        opacity: 0;
        transform-origin: 50px 45px;
    }

    @keyframes slideUpText {
        0% { transform: translateY(100%); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-up-text {
        animation: slideUpText 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    #fs-splash.fade-out {
        opacity: 0;
        pointer-events: none;
        transform: scale(1.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var splash = document.getElementById('fs-splash');
            if (splash) {
                splash.classList.add('fade-out');
                setTimeout(function() {
                    splash.remove();
                }, 500);
            }
        }, 1000); // exactly 1 second before fading
    });
</script>