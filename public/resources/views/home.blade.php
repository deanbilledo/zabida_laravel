@extends('layouts.app')

@php
  $pageTitle = 'ZABIDA | Zamboanga-Basilan Integrated Development Alliance';
  $pageDescription = 'ZABIDA is a consortium of local NGOs working for peace and development across the Zamboanga Peninsula and Basilan.';
  $currentPage = 'home';
@endphp

@section('content')

<!-- Hero -->
<section id="home" class="min-h-[calc(100vh-80px)] max-w-6xl mx-auto px-6 flex items-center" aria-label="Introduction">
  <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-16 items-center w-full">
    <div>
      <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-6">
        Zamboanga&ndash;Basilan Integrated Development Alliance &middot; Since 2007
      </p>
      <h1 class="font-serif text-[2.75rem] sm:text-6xl md:text-7xl leading-[1.03] mb-8">
        Many<br>
        organizations.<br>
        <span class="italic text-clay">One</span> alliance.
      </h1>
      <p class="text-lg text-ink/70 max-w-md leading-relaxed mb-10">
        ZABIDA brings together member NGOs working side by side on peace, human
        rights, and community development across Zamboanga City, the Zamboanga
        Peninsula, Basilan, Sulu, and Tawi-Tawi.
      </p>
      <div class="flex flex-wrap gap-4">
        <a href="#blog" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
          Browse Recent Posts
        </a>
        <a href="{{ route('contact') }}" class="border border-ink px-6 py-3 text-sm uppercase tracking-wide hover:bg-ink hover:text-paper transition-colors">
          Get involved
        </a>
      </div>
    </div>
    <div class="hidden md:flex justify-center lg:justify-end">
      <img src="{{ asset('assets/images/zabida_consortium_logo.png') }}" alt="ZABIDA consortium logo" class="w-full max-w-[320px] h-auto">
    </div>
  </div>
</section>

<!-- About -->
<section id="about" class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <div class="grid md:grid-cols-[0.4fr_0.6fr] gap-12 md:gap-20">
    <div>
      <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">About</p>
      <h2 class="font-serif text-3xl md:text-4xl leading-tight">Empowering Communities For Human Security</h2>
    </div>
    <div class="text-lg text-ink/75 leading-relaxed space-y-5">
      <p>ZABIDA started in 2007 as a consortium of four non-government organizations &mdash; Katilingban sa Kalambuan, Inc. (KKI), Peace Advocates Zamboanga (PAZ), and Reach Out to Others Foundation (ROOF) in Zamboanga City, together with Nagdilaab Foundation Inc. (NFI) based in Basilan &mdash; committed to uplifting the quality of life of disadvantaged sectors across both provinces.</p>
      <p>Since then it has engaged in initiatives spanning peace, human rights, democratic governance, community empowerment, and disaster preparedness and risk reduction, reaching communities that no single organization could reach alone.</p>
      <blockquote class="font-serif italic text-2xl text-ink border-l-2 border-clay pl-6 my-8">&ldquo;Together, we create stronger, safer, and more resilient communities.&rdquo;</blockquote>
      <p class="text-base text-ink/60">The alliance is also growing to include Verde Zamboanga and Ganda Gaddung in Basilan, focused on environment-related campaigns and advocacy, plus Campo Vida for sustainable agriculture, and allied partners Youth Solidarity for Peace and Jovenes Allianza de Zamboanga.</p>
    </div>
  </div>
</section>

<!-- Vision / Mission -->
<section class="max-w-6xl mx-auto px-6 pb-20 md:pb-28 grid md:grid-cols-2 gap-10">
  <div class="border-t-2 border-gold pt-6">
    <h3 class="font-serif text-2xl mb-3">Our vision</h3>
    <p class="text-ink/70 leading-relaxed">A consortium of committed and responsive development partners working together towards peaceful, resilient, and empowered communities in the Zamboanga Peninsula and BaSulTa.</p>
  </div>
  <div class="border-t-2 border-clay pt-6">
    <h3 class="font-serif text-2xl mb-3">Our mission</h3>
    <p class="text-ink/70 leading-relaxed">We commit ourselves to work together to empower the vulnerable sectors towards a dignified life &mdash; with enhanced social and institutional capacities for gender equity, participative and accountable governance, improved security, and environmental sustainability.</p>
  </div>
</section>

<!-- Video placeholder -->
<section class="max-w-6xl mx-auto px-6 pb-20 md:pb-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Watch</p>
  <h2 class="font-serif text-3xl md:text-4xl mb-8">See the alliance at work</h2>
  <div class="relative aspect-video w-full bg-ink/5 border border-ink/10 rounded-lg overflow-hidden flex items-center justify-center">
    {{-- Replace this block with an <iframe> (YouTube/Facebook embed) or a
         <video> tag once a feature video is ready to publish. Left as a
         clearly-labeled placeholder rather than a broken/empty box. --}}
    <div class="text-center px-6">
      <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-ink text-paper flex items-center justify-center" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
      </div>
      <p class="font-mono text-xs uppercase tracking-wide text-ink/50">Video coming soon</p>
      <p class="text-ink/60 text-sm mt-1">In the meantime, watch us on <a href="https://www.youtube.com/zabidaorg" target="_blank" rel="noopener noreferrer" class="underline hover:text-clay">YouTube</a>.</p>
    </div>
  </div>
</section>

<!-- Member NGOs -->
<section id="partners" class="bg-ink text-paper py-20 md:py-28">
  <div class="max-w-6xl mx-auto px-6">
    <p class="font-mono text-xs uppercase tracking-[0.2em] text-paper/50 mb-4">The alliance</p>
    <h2 class="font-serif text-3xl md:text-4xl mb-14">Our member organizations</h2>

    <div class="divide-y divide-paper/15">
      @foreach ([
        ['accent' => 'bg-gold', 'name' => 'KKI', 'desc' => "Katilingban sa Kalambuan, Inc. — promotes women and children's rights and socialized housing.", 'img' => 'Katilingban.png'],
        ['accent' => 'bg-clay', 'name' => 'PAZ', 'desc' => 'Peace Advocates Zamboanga — a non-profit engaged in the promotion of peace, interreligious dialogue, and advocacy.', 'img' => 'paz_logo.jpg'],
        ['accent' => 'bg-palm', 'name' => 'ROOF', 'desc' => 'Reach Out to Others Foundation — promotes sustainable agriculture and the welfare of marginalized sectors.', 'img' => 'roof_logo.png'],
        ['accent' => 'bg-violet', 'name' => 'NFI', 'desc' => 'Nagdilaab Foundation Inc. — capability building, conflict transformation, dialogue, cultural contextualization, peacebuilding, and human rights in Basilan.', 'img' => 'nagdilaab_logo.png'],
        ['accent' => 'bg-violet', 'name' => 'Campo Vida', 'desc' => 'Campo Vida Agricultural Learning Center Inc. — promotes sustainable agriculture, farmer education, livelihood and enterprise development, environmental stewardship, and inclusive community empowerment.', 'img' => 'nagdilaab_logo.png'],
      ] as $org)
        <div class="grid sm:grid-cols-[auto_1fr_auto] gap-4 sm:gap-8 py-7 items-center">
          <div class="w-1.5 h-14 {{ $org['accent'] }}" aria-hidden="true"></div>
          <div>
            <h3 class="font-serif text-2xl mb-1">{{ $org['name'] }}</h3>
            <p class="text-paper/60 text-sm">{{ $org['desc'] }}</p>
          </div>
          <img src="{{ asset('assets/images/'.$org['img']) }}" alt="" class="h-14 w-14 object-contain hidden sm:block" loading="lazy">
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Programs preview -->
<section id="programs" class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <div class="flex items-end justify-between mb-14 flex-wrap gap-4">
    <div>
      <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">What we do</p>
      <h2 class="font-serif text-3xl md:text-4xl">Focus areas</h2>
    </div>
    <a href="{{ route('activities') }}" class="text-sm uppercase tracking-wide border-b border-ink hover:text-clay hover:border-clay transition-colors">See all programs &rarr;</a>
  </div>

  <div class="grid md:grid-cols-2 gap-x-16 gap-y-12">
    <div class="border-t-2 border-gold pt-5">
      <h3 class="font-serif text-xl mb-2">Peace</h3>
      <p class="text-ink/70 leading-relaxed">Peace education, conflict transformation, interreligious dialogue, and the Annual Mindanao Week of Peace advocacy.</p>
    </div>
    <div class="border-t-2 border-clay pt-5">
      <h3 class="font-serif text-xl mb-2">Socio-economic development</h3>
      <p class="text-ink/70 leading-relaxed">Financing services, enterprise mentoring, Campo Vida agri-learning, financial literacy, and socialized housing.</p>
    </div>
    <div class="border-t-2 border-palm pt-5">
      <h3 class="font-serif text-xl mb-2">Gender &amp; development</h3>
      <p class="text-ink/70 leading-relaxed">GAD planning, budgeting and audit mentoring, plus Anti-VAWC and VAC advocacy.</p>
    </div>
    <div class="border-t-2 border-violet pt-5">
      <h3 class="font-serif text-xl mb-2">Human rights</h3>
      <p class="text-ink/70 leading-relaxed">Policy review, human rights promotion, and IP Sama-Badjao youth rights advocacy with partner ASMAE.</p>
    </div>
  </div>
</section>

<!-- PeaceWorks and Knowledge Products preview -->
<section class="max-w-6xl mx-auto px-6 py-20 md:py-28 border-t border-ink/10">
  <div class="flex items-end justify-between mb-14 flex-wrap gap-4">
    <div>
      <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Read</p>
      <h2 class="font-serif text-3xl md:text-4xl">PeaceWorks &amp; Knowledge Products</h2>
    </div>
    <a href="{{ route('publications.index') }}" class="text-sm uppercase tracking-wide border-b border-ink hover:text-clay hover:border-clay transition-colors">Browse the archive &rarr;</a>
  </div>

  @if ($latestPublications->isEmpty())
    <p class="text-ink/50">No publications uploaded yet — check back soon.</p>
  @else
    <div class="grid sm:grid-cols-3 gap-8">
      @foreach ($latestPublications as $publication)
        <a href="{{ route('publications.index') }}" class="group block border border-ink/10 hover:border-clay transition-colors p-5">
          <p class="font-mono text-xs uppercase tracking-wide text-ink/40 mb-2">{{ $publication->category }}</p>
          <h3 class="font-serif text-lg group-hover:text-clay transition-colors">{{ $publication->title }}</h3>
          <p class="text-sm text-ink/50 mt-2">{{ $publication->published_at->format('M Y') }} &middot; {{ $publication->formattedSize() }}</p>
        </a>
      @endforeach
    </div>
  @endif
</section>

<!-- Journal / Blog -->
<section id="blog" class="max-w-6xl mx-auto px-6 py-20 md:py-28 border-t border-ink/10">
  <div class="flex items-end justify-between mb-14 flex-wrap gap-4">
    <div>
      <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Latest updates</p>
      <h2 class="font-serif text-3xl md:text-4xl">Activities</h2>
    </div>
    <a href="{{ route('activities.posts') }}" class="group inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-ink/70 hover:text-clay transition-colors">
      <span>View all activities</span>
      <span class="text-base group-hover:translate-x-1 transition-transform">&rarr;</span>
    </a>
  </div>

  <div id="blog-grid" class="divide-y divide-ink/10">
    @forelse ($posts as $post)
      <article class="grid grid-cols-1 sm:grid-cols-[80px_1fr_120px] md:grid-cols-[100px_1fr_160px] gap-4 sm:gap-6 items-start py-8">
        <p class="font-mono text-sm text-ink/40">{{ $post->published_at->format('M Y') }}</p>
        <div>
          <h3 class="font-serif text-2xl mb-2 hover:text-clay transition-colors">
            <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
          </h3>
          <p class="text-ink/60 leading-relaxed">{{ $post->excerpt }}</p>
          @if ($post->images->count() > 1)
            <p class="text-xs font-mono uppercase tracking-wide text-ink/40 mt-2">{{ $post->images->count() }} photos</p>
          @endif
        </div>
        <div class="aspect-square w-full rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center border border-ink/10">
          <img src="{{ $post->coverImageUrl() }}" alt="" class="w-full h-full object-contain p-1">
        </div>
      </article>
    @empty
      <p class="text-ink/50 py-10">No journal entries yet &mdash; check back soon, or follow along on <a href="https://www.facebook.com/zabidadotorg/" class="underline hover:text-clay" target="_blank" rel="noopener noreferrer">Facebook</a>.</p>
    @endforelse
  </div>
</section>

@endsection
