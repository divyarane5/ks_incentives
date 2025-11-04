<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
            ©
            <script>document.write(new Date().getFullYear());</script>
            <a href="{{ $activeBusinessUnit->website_url ?? 'https://keystonerealestateadvisory.com/' }}"
               target="_blank"
               class="footer-link fw-bolder">
               {{ $activeBusinessUnit->name ?? 'Keystone Real Estate Advisory' }}
            </a>
        </div>
        <div>
            @if(!empty($activeBusinessUnit->tagline))
                <span class="text-muted">{{ $activeBusinessUnit->tagline }}</span>
            @endif
        </div>
    </div>
</footer>
