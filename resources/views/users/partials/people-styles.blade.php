{{-- KeyPeople: shared look for the employee profile, edit and salary pages (phone first). --}}
<style>
.kp { --kp-navy: var(--rs-navy, #011e48); --kp-gold: var(--rs-gold, #c3b246); --kp-line: var(--rs-line, #e6eaf2); --kp-muted: var(--rs-muted, #6b778c); --kp-text: var(--rs-text, #1f2a3d); max-width: 1180px; }
.kp .kp-crumb { font-size: .85rem; color: var(--kp-muted); margin-bottom: .75rem; display: flex; align-items: center; gap: .35rem; flex-wrap: wrap; }
.kp .kp-crumb a { color: var(--kp-muted); }

/* Hero */
.kp-hero { background: var(--kp-navy); color: #fff; border-radius: 14px; padding: 18px 16px 16px; position: relative; overflow: hidden; margin-bottom: 14px; }
.kp-hero::after { content: ""; position: absolute; right: -60px; top: -60px; width: 200px; height: 200px; border-radius: 50%; background: color-mix(in srgb, var(--kp-gold) 22%, transparent); pointer-events: none; }
.kp-hero-top { display: flex; gap: 14px; align-items: center; position: relative; z-index: 1; }
.kp-avatar { width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 3px solid var(--kp-gold); background: #fff; flex-shrink: 0; }
.kp-avatar-initials { width: 76px; height: 76px; border-radius: 50%; border: 3px solid var(--kp-gold); background: color-mix(in srgb, #fff 14%, transparent); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 700; flex-shrink: 0; }
.kp-hero h1 { color: #fff !important; font-size: 1.25rem !important; margin: 0 0 2px; font-weight: 700; }
.kp-hero .kp-sub { color: color-mix(in srgb, #fff 78%, transparent); font-size: .875rem; line-height: 1.35; }
.kp-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.kp-chip { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 600; padding: 3px 9px; border-radius: 999px; background: color-mix(in srgb, #fff 14%, transparent); color: #fff; white-space: nowrap; }
.kp-chip.ok { background: #e7f6ec; color: #1a7f3c; }
.kp-chip.warn { background: #fff4e0; color: #9a5b00; }
.kp-chip.bad { background: #fde8e8; color: #b42318; }
.kp-chip.gold { background: var(--kp-gold); color: var(--kp-navy); }
.kp-actions { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 14px; position: relative; z-index: 1; }
.kp-act { display: flex; flex-direction: column; align-items: center; gap: 3px; padding: 9px 4px; border-radius: 10px; background: color-mix(in srgb, #fff 10%, transparent); color: #fff !important; font-size: .72rem; font-weight: 600; text-decoration: none; border: 0; }
.kp-act i { font-size: 1.3rem; }
.kp-act:hover { background: color-mix(in srgb, #fff 18%, transparent); }
.kp-act.disabled { opacity: .4; pointer-events: none; }
.kp-hero-links { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; position: relative; z-index: 1; }
.kp-hero-links .btn { flex: 1 1 auto; }
.kp .btn-kp { background: var(--kp-gold); color: var(--kp-navy); font-weight: 700; border: 0; }
.kp .btn-kp:hover { filter: brightness(.95); color: var(--kp-navy); }
.kp .btn-kp-ghost { background: color-mix(in srgb, #fff 12%, transparent); color: #fff; border: 1px solid color-mix(in srgb, #fff 25%, transparent); font-weight: 600; }
.kp .btn-kp-ghost:hover { background: color-mix(in srgb, #fff 20%, transparent); color: #fff; }

/* Key facts */
.kp-facts { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 14px; }
.kp-fact { background: #fff; border: 1px solid var(--kp-line); border-radius: 12px; padding: 10px 12px; min-width: 0; }
.kp-fact .l { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--kp-muted); font-weight: 600; }
.kp-fact .v { font-weight: 700; color: var(--kp-navy); font-size: .95rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.kp-fact .s { font-size: .75rem; color: var(--kp-muted); }

/* Section pills (sticky, scroll sideways on phones) */
.kp-tabs { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; padding: 6px; margin: 0 0 14px; background: #fff; border: 1px solid var(--kp-line); border-radius: 12px; position: sticky; top: 8px; z-index: 5; list-style: none; }
.kp-tabs::-webkit-scrollbar { display: none; }
.kp-tabs .nav-link { white-space: nowrap; border-radius: 8px !important; padding: 7px 12px; font-size: .85rem; font-weight: 600; color: var(--kp-muted); border: 0 !important; background: transparent; display: inline-flex; align-items: center; gap: 5px; }
.kp-tabs .nav-link.active { background: var(--kp-navy) !important; color: #fff !important; }

/* Panels and fields */
.kp-panel { background: #fff; border: 1px solid var(--kp-line); border-radius: 12px; margin-bottom: 14px; }
.kp-panel-h { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 12px 14px; border-bottom: 1px solid var(--kp-line); }
.kp-panel-h h2 { font-size: .95rem !important; margin: 0; color: var(--kp-navy); display: flex; align-items: center; gap: 6px; }
.kp-panel-h h2 i { color: var(--kp-gold); font-size: 1.1rem; }
.kp-panel-h .kp-note { font-size: .75rem; color: var(--kp-muted); }
.kp-panel-b { padding: 4px 14px 10px; }
.kp-fields { display: grid; grid-template-columns: 1fr; margin: 0; }
.kp-field { padding: 9px 0; border-bottom: 1px dashed var(--kp-line); min-width: 0; }
.kp-field:last-child { border-bottom: 0; }
.kp-field dt { font-size: .72rem; color: var(--kp-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 1px; }
.kp-field dd { margin: 0; color: var(--kp-text); font-weight: 500; overflow-wrap: anywhere; }
.kp-field dd.empty { color: #b0b8c6; font-weight: 400; }
.kp-field dd a { font-weight: 600; }
.kp-mask-btn { border: 0; background: none; color: var(--kp-navy); font-size: .75rem; font-weight: 600; padding: 0 0 0 6px; text-decoration: underline; }

/* Completeness */
.kp-complete { display: flex; gap: 12px; align-items: center; }
.kp-ring { --p: 0; width: 54px; height: 54px; border-radius: 50%; background: conic-gradient(var(--kp-gold) calc(var(--p) * 1%), var(--kp-line) 0); display: grid; place-items: center; flex-shrink: 0; }
.kp-ring span { width: 42px; height: 42px; border-radius: 50%; background: #fff; display: grid; place-items: center; font-size: .8rem; font-weight: 700; color: var(--kp-navy); }
.kp-missing { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 4px; }
.kp-missing span { font-size: .72rem; background: #fff4e0; color: #9a5b00; border-radius: 6px; padding: 2px 7px; }

/* Lists: documents, history, team */
.kp-list { list-style: none; margin: 0; padding: 0; }
.kp-list li { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--kp-line); }
.kp-list li:last-child { border-bottom: 0; }
.kp-list .ic { width: 36px; height: 36px; border-radius: 10px; background: var(--rs-navy-soft, #eef1f6); color: var(--kp-navy); display: grid; place-items: center; font-size: 1.15rem; flex-shrink: 0; }
.kp-list .bd { flex: 1; min-width: 0; }
.kp-list .t { font-weight: 600; color: var(--kp-text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.kp-list .m { font-size: .78rem; color: var(--kp-muted); }
.kp-list .r { text-align: right; flex-shrink: 0; font-size: .8rem; }
.kp-timeline { list-style: none; margin: 0; padding: 0 0 0 16px; border-left: 2px solid var(--kp-line); }
.kp-timeline li { position: relative; padding: 0 0 14px 12px; }
.kp-timeline li::before { content: ""; position: absolute; left: -23px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #fff; border: 3px solid var(--kp-line); }
.kp-timeline li.now::before { border-color: var(--kp-gold); }
.kp-timeline .t { font-weight: 600; }
.kp-timeline .m { font-size: .78rem; color: var(--kp-muted); }
.kp-empty { color: var(--kp-muted); font-size: .85rem; padding: 10px 0; }

/* Money */
.kp-money { font-variant-numeric: tabular-nums; }
.kp-pay-hero { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 12px 0 4px; }
.kp-pay-hero .kp-fact .v { font-size: 1.05rem; }
.kp-bar { display: flex; height: 10px; border-radius: 99px; overflow: hidden; background: var(--kp-line); margin: 8px 0 6px; }
.kp-bar span { display: block; height: 100%; }
.kp-legend { display: flex; flex-wrap: wrap; gap: 4px 12px; font-size: .75rem; color: var(--kp-muted); }
.kp-legend i { display: inline-block; width: 9px; height: 9px; border-radius: 3px; margin-right: 4px; vertical-align: middle; }

/* Forms (edit page) */
.kp-form .form-label { font-size: .78rem; font-weight: 600; color: var(--kp-muted); margin-bottom: 3px; }
.kp-form .form-control, .kp-form .form-select { min-height: 44px; border-radius: 10px; font-size: 16px; }
.kp-form textarea.form-control { min-height: 70px; }
.kp-form .form-text { font-size: .75rem; }
.kp-form .kp-section { scroll-margin-top: 70px; }
.kp-form .kp-panel-b { padding: 14px; }
.kp-form .readonly-calc { background: #f6f8fb !important; }
.kp-savebar { position: sticky; bottom: 0; z-index: 6; background: #fff; border-top: 1px solid var(--kp-line); padding: 10px 14px calc(10px + env(safe-area-inset-bottom)); margin: 0 -14px; display: flex; gap: 8px; box-shadow: 0 -4px 14px rgba(16, 24, 40, .06); }
.kp-savebar .btn { flex: 1; min-height: 46px; font-weight: 700; border-radius: 10px; }
.kp-jump { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; padding: 6px; margin-bottom: 14px; background: #fff; border: 1px solid var(--kp-line); border-radius: 12px; position: sticky; top: 8px; z-index: 5; }
.kp-jump::-webkit-scrollbar { display: none; }
.kp-jump a { white-space: nowrap; padding: 7px 12px; border-radius: 8px; font-size: .85rem; font-weight: 600; color: var(--kp-muted); text-decoration: none; }
.kp-jump a.on { background: var(--kp-navy); color: #fff; }
.kp-switch { display: flex; align-items: center; justify-content: space-between; gap: 10px; min-height: 44px; padding: 6px 0; }
.kp-switch .form-check-input { width: 2.6em; height: 1.4em; margin: 0; }

@media (min-width: 768px) {
    .kp-hero { padding: 22px 24px; }
    .kp-avatar, .kp-avatar-initials { width: 96px; height: 96px; }
    .kp-hero h1 { font-size: 1.5rem !important; }
    .kp-actions { grid-template-columns: repeat(4, 88px); justify-content: start; }
    .kp-facts { grid-template-columns: repeat(4, 1fr); }
    .kp-fields { grid-template-columns: 1fr 1fr; column-gap: 28px; }
    .kp-fields.one { grid-template-columns: 1fr; }
    .kp-pay-hero { grid-template-columns: repeat(4, 1fr); }
    .kp-savebar { margin: 0; border-radius: 12px; border: 1px solid var(--kp-line); }
    .kp-savebar .btn { flex: 0 0 auto; min-width: 160px; }
}
</style>
