// Lead page: show the call outcome form after tapping Call.
document.querySelectorAll('[data-call-button]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('call-outcome');
        if (form) {
            setTimeout(() => {
                form.classList.remove('hidden');
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 400);
        }
    });
});

// Lead page: sub-stage list, follow-up date and project picker follow the chosen stage.
const stageSelect = document.querySelector('[data-stage-select]');
if (stageSelect) {
    const sub = document.querySelector('[data-substage-select]');
    const followUp = document.querySelector('[data-followup]');
    const followLabel = document.querySelector('[data-followup-label]');
    const projectPick = document.querySelector('[data-project-pick]');
    const labels = { callback: 'Callback on', meeting: 'Meeting on', site_visit: 'Site visit on' };

    const refresh = (keepCurrent) => {
        const opt = stageSelect.selectedOptions[0];
        const children = JSON.parse(opt.dataset.children || '[]');
        const current = keepCurrent ? sub.dataset.current : '';
        sub.innerHTML = '';
        sub.classList.toggle('hidden', children.length === 0);
        sub.required = children.length > 0;
        if (children.length) {
            sub.add(new Option('Choose reason', ''));
            children.forEach((c) => sub.add(new Option(c.name, c.id, false, String(c.id) === String(current))));
        }
        const closed = opt.dataset.closed === '1';
        const schedule = opt.dataset.schedule;
        followUp.classList.toggle('hidden', closed);
        followLabel.textContent = labels[schedule] || 'Next follow-up';
        followUp.querySelector('input').required = !!schedule && !closed;
        projectPick.classList.toggle('hidden', !['meeting', 'site_visit'].includes(schedule) && opt.dataset.booked !== '1');
    };

    stageSelect.addEventListener('change', () => refresh(false));
    refresh(true);

    document.querySelectorAll('[data-quick-time]').forEach((b) =>
        b.addEventListener('click', () => (followUp.querySelector('input').value = b.dataset.quickTime)),
    );
}
