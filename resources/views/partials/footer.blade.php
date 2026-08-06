<div class="flex h-1.5 w-full" aria-hidden="true">
  <div class="flex-1 bg-gold"></div>
  <div class="flex-1 bg-clay"></div>
  <div class="flex-1 bg-ink"></div>
  <div class="flex-1 bg-palm"></div>
  <div class="flex-1 bg-violet"></div>
</div>

<footer class="bg-ink text-paper py-14" role="contentinfo">
  <div class="max-w-6xl mx-auto px-6">
    <div class="grid md:grid-cols-4 gap-10 mb-10">
      <div>
        <div class="flex items-center gap-2 mb-4">
          <img src="{{ asset('assets/images/zabida_logo.png') }}" alt="ZABIDA logo" width="48" height="48" class="h-9 w-auto object-contain" onerror="this.style.display='none'" loading="lazy">
          <span class="font-serif text-lg">ZABIDA</span>
        </div>
        <p class="text-paper/50 text-sm leading-relaxed">Zamboanga&ndash;Basilan Integrated Development Alliance, Inc. Working for peace and development since 2007.</p>
      </div>
      <div>
        <h3 class="font-mono text-xs uppercase tracking-wide text-paper/50 mb-4">Quick links</h3>
        <ul class="space-y-2 text-sm text-paper/70">
          <li><a href="{{ url('/') }}#about" class="hover:text-gold">About</a></li>
          <li><a href="{{ url('/') }}#partners" class="hover:text-gold">Member NGOs</a></li>
          <li><a href="{{ route('activities') }}" class="hover:text-gold">Programs</a></li>
          <li><a href="{{ route('activities.posts') }}" class="hover:text-gold">Journal</a></li>
          <li><a href="{{ route('contact') }}" class="hover:text-gold">Contact</a></li>
        </ul>
      </div>
      <div>
        <h3 class="font-mono text-xs uppercase tracking-wide text-paper/50 mb-4">Resources</h3>
       <ul class="space-y-2 text-sm text-paper/70">
            <li><a href="{{ route('publications.index') }}" class="hover:text-gold">PeaceWorks &amp; Knowledge Products</a></li>
            
            @guest
                {{-- Visible only to users who are NOT logged in --}}
                <li><a href="{{ route('admin.login') }}" class="hover:text-gold">Admin login</a></li>
            @endguest

            @auth
                {{-- Visible only to logged-in users --}}
                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gold">Admin Dashboard</a></li>
            @endauth

        </ul>
      </div>
      <div>
        <h3 class="font-mono text-xs uppercase tracking-wide text-paper/50 mb-4">Contact</h3>
        <ul class="space-y-2 text-sm text-paper/70">
          <li>Macrohon Compound, Suterville,<br>San Jose Gusu, Zamboanga City</li>
          <li><a href="tel:0629902410" class="hover:text-gold">0629902410</a></li>
          <li><a href="mailto:zabidamail.ph@gmail.com" class="hover:text-gold">zabidamail.ph@gmail.com</a></li>
          <li><a href="https://www.facebook.com/zabidadotorg/" target="_blank" rel="noopener noreferrer" class="hover:text-gold">facebook.com/zabida.org</a></li>
          <li><a href="https://www.youtube.com/zabidaorg" target="_blank" rel="noopener noreferrer" class="hover:text-gold">youtube.com/zabidaorg</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-paper/15 pt-8 text-center text-sm text-paper/40 font-mono">
      &copy; <span id="current-year">{{ date('Y') }}</span> ZABIDA &mdash; Empowering Communities For Human Security
    </div>
  </div>
</footer>