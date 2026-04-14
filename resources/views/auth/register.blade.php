<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Fashion Store</title>
    <link href="https://fonts.bunny.net/css?family=dm-serif-display:400,400i|dm-sans:300,400,500,600,700" rel="stylesheet" />
    <style>
        :root { --cream:#faf7f2;--dark:#1c1917;--accent:#c8a96e;--accent-dark:#a07840;--muted:#78716c;--border:#e7e0d8; }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DM Sans',sans-serif;background:var(--cream);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem;}
        .container{width:100%;max-width:1040px;margin:auto;}
        .main-card{background:white;border-radius:1.5rem;box-shadow:0 32px 64px -12px rgba(28,25,23,.12);display:grid;grid-template-columns:1.1fr 1fr;overflow:hidden;border:1px solid var(--border);}
        .left-section{background:var(--dark);position:relative;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-end;padding:3rem;min-height:700px;}
        .left-bg{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(28,25,23,.2) 0%,rgba(28,25,23,.78) 100%),url('https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=800') center/cover no-repeat;}
        .left-content{position:relative;z-index:1;}
        .badge{display:inline-flex;align-items:center;background:rgba(200,169,110,.2);border:1px solid rgba(200,169,110,.4);border-radius:2rem;padding:.35rem 1rem;margin-bottom:1.5rem;}
        .badge span{font-size:.75rem;font-weight:600;color:var(--accent);letter-spacing:.1em;text-transform:uppercase;}
        .left-content h2{font-family:'DM Serif Display',serif;font-size:2.5rem;color:white;line-height:1.2;margin-bottom:1rem;}
        .left-content p{color:rgba(255,255,255,.65);font-size:.95rem;line-height:1.7;margin-bottom:2rem;}
        .stats-row{display:flex;gap:2rem;}
        .stat-number{font-family:'DM Serif Display',serif;font-size:1.75rem;color:var(--accent);display:block;}
        .stat-label{font-size:.75rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.08em;}
        .right-section{padding:3rem 3.5rem;display:flex;flex-direction:column;justify-content:center;}
        .logo-section{display:flex;align-items:center;gap:.75rem;margin-bottom:2rem;}
        .logo-icon{width:44px;height:44px;background:var(--dark);border-radius:.75rem;display:flex;align-items:center;justify-content:center;}
        .logo-icon svg{width:22px;height:22px;}
        .logo-name{font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--dark);}
        .form-header{margin-bottom:1.5rem;}
        .form-header h2{font-family:'DM Serif Display',serif;font-size:2rem;color:var(--dark);margin-bottom:.4rem;}
        .form-header p{color:var(--muted);font-size:.9rem;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
        .form-group{margin-bottom:1rem;}
        .form-label{display:block;font-weight:600;color:var(--dark);margin-bottom:.4rem;font-size:.85rem;}
        .form-input{width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:.6rem;font-size:.9rem;font-family:'DM Sans',sans-serif;transition:all .25s;background:var(--cream);color:var(--dark);}
        .form-input:focus{outline:none;border-color:var(--accent);background:white;box-shadow:0 0 0 3px rgba(200,169,110,.15);}
        .form-input.error{border-color:#f87171;}
        .field-error{font-size:.78rem;color:#dc2626;margin-top:.3rem;}
        .pw-wrap{position:relative;}
        .pw-wrap .form-input{padding-right:3rem;}
        .toggle-pw{position:absolute;right:.9rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;transition:color .2s;}
        .toggle-pw:hover{color:var(--dark);}
        .toggle-pw svg{width:18px;height:18px;}
        .strength-bar{height:3px;border-radius:2px;background:var(--border);margin-top:.35rem;overflow:hidden;}
        .strength-fill{height:100%;border-radius:2px;transition:width .3s,background .3s;width:0%;}
        .btn-primary{width:100%;padding:.9rem;background:var(--dark);color:white;border:none;border-radius:.6rem;font-size:.95rem;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;letter-spacing:.02em;transition:all .25s;margin-top:.5rem;}
        .btn-primary:hover{background:#2d2a28;transform:translateY(-1px);box-shadow:0 8px 20px rgba(28,25,23,.2);}
        .divider{display:flex;align-items:center;gap:1rem;margin:1.25rem 0;}
        .divider-line{flex:1;height:1px;background:var(--border);}
        .divider span{font-size:.78rem;color:var(--muted);white-space:nowrap;}
        .nav-links{text-align:center;font-size:.85rem;color:var(--muted);}
        .nav-links a{color:var(--accent-dark);font-weight:600;text-decoration:none;}
        .nav-links a:hover{text-decoration:underline;}
        @media(max-width:860px){.main-card{grid-template-columns:1fr}.left-section{display:none}}
        @media(max-width:560px){body{padding:1rem}.right-section{padding:2rem 1.5rem}.form-row{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="container"><div class="main-card">
    <div class="left-section">
        <div class="left-bg"></div>
        <div class="left-content">
            <div class="badge"><span>✦ Fashion Store</span></div>
            <h2>Bergabung &amp; Nikmati Koleksi Eksklusif</h2>
            <p>Daftar sekarang dan dapatkan akses ke ribuan produk fashion premium pilihan dengan harga terbaik.</p>
        </div>
    </div>
    <div class="right-section">
                <div class="logo-section" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2.5rem; margin-top: -1rem;">
                    @if(isset($shop_logo) && $shop_logo)
                        <img src="{{ asset($shop_logo) }}" alt="Logo" style="height: 48px; object-fit: contain;">
                    @else
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 100 80" style="width: 100%; height: 100%; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));" xmlns="http://www.w3.org/2000/svg">
                              <path d="M 50 10 L 30 20 L 15 45 L 25 55 L 35 40 L 45 70 L 58 52 Z" fill="#d0c3b0" />
                              <path d="M 30 20 L 15 45 L 25 55 Z" fill="#bbae97" /> 
                              <path d="M 45 35 L 55 10 L 75 20 L 90 45 L 80 55 L 70 40 L 60 70 Z" fill="#bd904d" />
                              <path d="M 75 20 L 90 45 L 80 55 Z" fill="#a87f42" />
                              <circle cx="50" cy="45" r="3" fill="#ffffff" />
                              <line x1="53" y1="45" x2="70" y2="45" stroke="#ffffff" stroke-width="2" />
                            </svg>
                        </div>
                    @endif
                    <div style="display: flex; flex-direction: column; justify-content: center;">
                        <?php $p = explode(' ', $shop_name ?? 'FASHION STORE'); ?>
                        <span style="font-family: sans-serif; font-weight: 800; color: #0c2a47; font-size: 1.25rem; line-height: 1; letter-spacing: 0.1em; text-transform: uppercase;">
                            {{ $p[0] }}
                        </span>
                        @if(count($p) > 1)
                        <?php array_shift($p); ?>
                        <span style="font-family: sans-serif; color: #417b9b; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; margin-top: 0.25rem;">
                            {{ implode(' ', $p) }}
                        </span>
                        @endif
                    </div>
                </div>
        <div class="form-header">
            <h2>Buat Akun Baru</h2>
            <p>Isi data diri Anda untuk mulai berbelanja.</p>
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input id="name" class="form-input @error('name') error @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukan Nama Anda" />
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" class="form-input @error('email') error @enderror" type="email" name="email" value="{{ old('email') }}" required placeholder="Masukan Email Anda" />
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">No. HP <span style="color:var(--muted);font-weight:400"></span></label>
                <input id="phone" class="form-input @error('phone') error @enderror" type="text" name="phone" value="{{ old('phone') }}" placeholder="Masukan Nomor Hp Anda" />
                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="pw-wrap">
                        <input id="password" class="form-input @error('password') error @enderror" type="password" name="password" required placeholder="Min. 8 karakter" oninput="checkStr(this.value)" />
                        <button type="button" class="toggle-pw" onclick="togglePwd('password')">
                            <svg id="eye-password" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-off-password" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="sfill"></div></div>
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="pw-wrap">
                        <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required placeholder="Ulangi password" />
                        <button type="button" class="toggle-pw" onclick="togglePwd('password_confirmation')">
                            <svg id="eye-password_confirmation" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-off-password_confirmation" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-primary">Daftar Sekarang</button>
        </form>
        <div class="divider"><div class="divider-line"></div><span>Sudah punya akun?</span><div class="divider-line"></div></div>
        <div class="nav-links"><a href="{{ route('login') }}">← Masuk ke Akun</a></div>
    </div>
</div></div>
<script>
function togglePwd(id){const f=document.getElementById(id),on=document.getElementById('eye-'+id),off=document.getElementById('eye-off-'+id);if(f.type==='password'){f.type='text';on.style.display='none';off.style.display='block';}else{f.type='password';on.style.display='block';off.style.display='none';}}
function checkStr(v){const f=document.getElementById('sfill');let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;const c=['#f87171','#fb923c','#facc15','#22c55e'],w=['25%','50%','75%','100%'];f.style.width=s>0?w[s-1]:'0%';f.style.background=s>0?c[s-1]:'transparent';}
</script>
</body>
</html>