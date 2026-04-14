<div class="flex items-center gap-3">
    @if(isset($shop_logo) && $shop_logo)
        <img src="{{ asset($shop_logo) }}" alt="{{ $shop_name ?? 'Logo' }}" class="w-auto h-11 object-contain" />
    @else
        <div class="w-12 h-12 flex items-center justify-center flex-shrink-0">
            <svg viewBox="0 0 100 80" class="w-full h-full drop-shadow-sm" xmlns="http://www.w3.org/2000/svg">
                <!-- Left half (Beige) -->
                <path d="M 50 10 L 30 20 L 15 45 L 25 55 L 35 40 L 45 70 L 58 52 Z" fill="#d0c3b0" />
                <path d="M 30 20 L 15 45 L 25 55 Z" fill="#bbae97" />

                <!-- Right half (Gold/Brown) -->
                <path d="M 45 35 L 55 10 L 75 20 L 90 45 L 80 55 L 70 40 L 60 70 Z" fill="#bd904d" />
                <path d="M 75 20 L 90 45 L 80 55 Z" fill="#a87f42" />

                <!-- Horizontal bar & dot -->
                <circle cx="50" cy="45" r="3" fill="#ffffff" />
                <line x1="53" y1="45" x2="70" y2="45" stroke="#ffffff" stroke-width="2" />
            </svg>
        </div>
        <div class="flex flex-col justify-center">
            <?php    $parts = explode(' ', $shop_name ?? 'FASHION STORE'); ?>
            <span class="font-extrabold text-[#0c2a47] text-xl leading-none tracking-[0.1em] uppercase">
                {{ $parts[0] }}
            </span>
            @if(count($parts) > 1)
                <?php        array_shift($parts); ?>
                <span class="text-[#417b9b] text-[0.65rem] font-bold tracking-[0.25em] uppercase mt-1">
                    {{ implode(' ', $parts) }}
                </span>
            @endif
        </div>
    @endif
</div>