@extends('layouts.app')

@php
  $pageTitle = 'Programs | ZABIDA';
  $pageDescription = "ZABIDA's focus areas: peace, socio-economic development, gender and development, environment, disaster risk reduction, human rights, and communication.";
@endphp

@section('content')

<section class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">What we do</p>
  <h1 class="font-serif text-4xl md:text-5xl mb-6 leading-tight">Our programs, projects &amp; activities</h1>
  <p class="text-lg text-ink/70 max-w-2xl leading-relaxed">Every focus area below is carried out jointly by ZABIDA's member NGOs, each contributing the networks and field experience built up since 2007.</p>
</section>

<section class="max-w-6xl mx-auto px-6 pb-20 md:pb-28 grid md:grid-cols-3 gap-x-12 gap-y-14">
  @foreach ([
    ['accent' => 'border-gold', 'title' => 'Peace', 'items' => ['Peace education in schools and barangays', 'Capacity building for conflict transformation', 'Interreligious dialogue and advocacy', 'Annual Mindanao Week of Peace advocacy', 'Local & regional peace & security networking']],
    ['accent' => 'border-clay', 'title' => 'Socio-economic development for poverty reduction', 'items' => ['Financing services program', 'Enterprise development mentoring', 'Campo Vida agri-learning and extension services support', 'Financial literacy training', 'Socialized housing', 'Mobilizing investments for poverty eradication, peace building and sustainability — partner: GIZ']],
    ['accent' => 'border-palm', 'title' => 'Gender & development', 'items' => ['GAD planning, budgeting, audit mentoring', 'Anti-VAWC & VAC advocacy', 'Capacity building for GAD learning sessions']],
    ['accent' => 'border-violet', 'title' => 'Environment', 'items' => ['Mobilization for community environmental actions', 'Monitoring environmental conservation']],
    ['accent' => 'border-gold', 'title' => 'Barangay disaster risk reduction & management', 'items' => ['Capacity building for BLGUs and sectoral groups for BDRRM planning, relief operations, and logistical support']],
    ['accent' => 'border-clay', 'title' => 'Social cohesion', 'items' => ['Organizing and strengthening of ZC Anti-VAWC Alliance', 'Organizing and strengthening of inter-barangay alliance', 'CSO & sectoral groups organizing and empowerment', 'Partnership in local governance on project monitoring and evaluation', 'BDRRM capacity enhancement and implementation', 'LGU, BLGU support for local investment programming', 'Organizing and strengthening of youth volunteers for peace, good governance and human rights']],
    ['accent' => 'border-palm', 'title' => 'Human rights', 'items' => ['Policy review & advocacy', 'Human rights promotion & access', 'IP Sama-Badjao youth rights advocacy — partner: ASMAE', 'Children and youth rights advocacy']],
    ['accent' => 'border-violet', 'title' => 'Communication', 'items' => ['Peace Works', 'Radio advocacy — PAZ Talks!', 'Knowledge products']],
  ] as $area)
    <div class="border-t-2 {{ $area['accent'] }} pt-5">
      <h2 class="font-serif text-xl mb-3">{{ $area['title'] }}</h2>
      <ul class="text-ink/70 leading-relaxed space-y-1.5 text-sm list-disc list-inside">
        @foreach ($area['items'] as $item)
          <li>{{ $item }}</li>
        @endforeach
      </ul>
    </div>
  @endforeach
</section>

<section class="bg-ink text-paper py-20 md:py-28">
  <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-[0.4fr_0.6fr] gap-12 md:gap-20">
    <div>
      <p class="font-mono text-xs uppercase tracking-[0.2em] text-paper/50 mb-4">Signature event</p>
      <h2 class="font-serif text-3xl md:text-4xl leading-tight">Zamboanga Week of Peace</h2>
    </div>
    <div class="text-lg text-paper/75 leading-relaxed space-y-5">
      <p>Celebration runs from the last Thursday of November until the first Wednesday of December every year, with activities including the Peace Weaver Awards, Historical Journey, Youth Peace Camp, Culture of Peace Trainings, Peace Trek, Peacetival of Learnings and Talents, and the Peace Summit.</p>
      <p class="text-base text-paper/60">In 2021, the City Government of Zamboanga enacted Ordinance 552 declaring this same period as the Zamboanga Week of Peace.</p>
    </div>
  </div>
</section>

<section class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Our objectives</p>
  <h2 class="font-serif text-3xl md:text-4xl mb-12">What we're working towards</h2>
  <ul class="grid md:grid-cols-2 gap-6 text-ink/75 leading-relaxed">
    <li class="border-l-2 border-gold pl-5">Increase social and institutional capacities of community partners to effectively govern and manage their own affairs.</li>
    <li class="border-l-2 border-clay pl-5">Provide community partners with more access to basic social services.</li>
    <li class="border-l-2 border-palm pl-5">Provide assistance to community partners for economic undertakings such as livelihood and employment generation activities.</li>
    <li class="border-l-2 border-violet pl-5">Enhance the sustainable utilization and management of ecological resources.</li>
    <li class="border-l-2 border-gold pl-5 md:col-span-2">Enhance capacities of local communities, civil society organizations, and government sectors in peace-building and conflict transformation.</li>
  </ul>
</section>

<section class="bg-paper border-y border-ink/10 py-20 md:py-28">
  <div class="max-w-6xl mx-auto px-6">
    <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Our core values</p>
    <h2 class="font-serif text-3xl md:text-4xl mb-12">Z-A-B-I-D-A</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-8 text-ink/75 leading-relaxed">
      <p><span class="font-serif text-2xl text-clay">Z</span>est for unity — encouraging unity among diverse communities and stakeholders, fostering togetherness and collaboration to achieve common goals.</p>
      <p><span class="font-serif text-2xl text-clay">A</span>ccountability — emphasizing responsibility and transparency in all actions and decisions to ensure trust and integrity within the alliance.</p>
      <p><span class="font-serif text-2xl text-clay">B</span>uilding bridges — prioritizing bridges between different socio-economic, ethno-religious, and gender groups to promote understanding, dialogue, and reconciliation.</p>
      <p><span class="font-serif text-2xl text-clay">I</span>nclusivity — committing to inclusivity by valuing the input and participation of all community members, regardless of gender, age, ethnicity, or socio-economic status.</p>
      <p><span class="font-serif text-2xl text-clay">D</span>iversity appreciation — celebrating and respecting the rich cultural and social diversity within the region as a source of strength and resilience.</p>
      <p><span class="font-serif text-2xl text-clay">A</span>dvocacy for justice — advocating for justice and the protection of human rights to create a fair and equitable environment for all.</p>
    </div>
  </div>
</section>

<section class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Beyond programs</p>
  <h2 class="font-serif text-3xl md:text-4xl mb-12">Other services</h2>
  <div class="grid sm:grid-cols-2 gap-x-12 gap-y-3 text-ink/75">
    @foreach (['Peace and Development Research and Publication', 'IEC materials production', 'Wellness session facilitation', 'Work immersion venue', 'Centro de Paz Function Hall', 'Campo Vida Agricultural Learning Center with dormitories'] as $service)
      <p class="border-b border-ink/10 pb-3">{{ $service }}</p>
    @endforeach
  </div>
</section>

<section class="bg-ink text-paper py-20 md:py-28">
  <div class="max-w-6xl mx-auto px-6">
    <p class="font-mono text-xs uppercase tracking-[0.2em] text-paper/50 mb-4">Governance</p>
    <h2 class="font-serif text-3xl md:text-4xl mb-12">Our leaders</h2>
    <dl class="divide-y divide-paper/15">
      @foreach ([
        ['Dr Grace J. Rebollos', 'President'],
        ['Atty Jose Manuel S. Mamauag', 'Chairperson'],
        ['Fr Dante C. Boringgot', 'Vice President'],
        ['Mr Ricardo M. Limbaga', 'Secretary'],
        ['Mr Alejandro L. Cabading', 'Treasurer'],
        ['Atty Eduardo F. Sanson', 'Board of Trustees'],
        ['Nagdilaab Foundation Inc.', 'Board of Trustees'],
        ['Fr Angel C Calvo', 'Former President'],
      ] as [$name, $role])
        <div class="flex justify-between items-center py-4 text-paper/85">
          <span class="font-serif text-lg">{{ $name }}</span>
          <span class="font-mono text-xs uppercase tracking-wide text-paper/50">{{ $role }}</span>
        </div>
      @endforeach
    </dl>
  </div>
</section>

<section class="bg-paper py-20 md:py-28 text-center">
  <div class="max-w-2xl mx-auto px-6">
    <h2 class="font-serif text-3xl md:text-4xl mb-6">Interested in partnering on a program?</h2>
    <a href="{{ route('contact') }}" class="inline-block bg-ink text-paper px-8 py-3.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors">Get in touch</a>
  </div>
</section>

@endsection
