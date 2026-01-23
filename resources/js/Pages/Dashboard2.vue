<script setup>

</script>
<template>


  <!-- HERO -->
  <section class="relative overflow-hidden bg-slate-950 text-white">
    
    <!-- Fondo degradado -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-700 via-purple-700 to-slate-900"></div>

    <!-- Glow decorativo -->
    <div class="absolute top-[-200px] left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-pink-500/20 blur-[160px] rounded-full"></div>

    <div class="relative max-w-6xl mx-auto px-6 py-28 text-center">

      <!-- Badge superior -->
      <div class="mb-8">
        <span class="inline-block bg-white/10 backdrop-blur px-4 py-2 rounded-full text-sm text-gray-200 border border-white/10">
          Pestx v4.0 Talk – Mira la presentación →
        </span>
      </div>

      <!-- Título principal -->
      <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">
        The elegant PHP testing framework <br class="hidden md:block"/>
        with built-in 
        <span class="text-pink-500 relative">
          browser testing
          <span class="absolute left-0 bottom-1 w-full h-2 bg-pink-500/30 blur-sm"></span>
        </span>
      </h1>

      <!-- Subtítulo -->
      <p class="max-w-2xl mx-auto text-lg text-gray-300 mb-10">
        Pest is a testing framework focused on simplicity, carefully designed
        to bring back the joy of testing in PHP.
      </p>

      <!-- Botones -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="#" class="px-8 py-4 rounded-full bg-pink-500 hover:bg-pink-600 transition font-semibold text-lg shadow-lg shadow-pink-500/30">
          Start Testing
        </a>
        <a href="#" class="px-8 py-4 rounded-full bg-white/10 hover:bg-white/20 transition font-semibold text-lg border border-white/20">
          Source Code
        </a>
      </div>

    </div>
  </section>


</template>
