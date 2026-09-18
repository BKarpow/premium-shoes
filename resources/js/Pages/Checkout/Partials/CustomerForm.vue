<script setup>
defineProps({
  form: Object,
});
</script>

<template>
  <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl backdrop-blur-sm space-y-4">
    <h2 class="text-lg font-bold text-white flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500/10 text-amber-400 text-xs border border-amber-500/20">1</span>
      Контактні дані
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1">Ім'я</label>
        <input
          v-model="form.first_name"
          type="text"
          class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          placeholder="Іван"
        />
        <span v-if="form.errors.first_name" class="text-xs text-red-400 mt-1 block">{{ form.errors.first_name }}</span>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1">Прізвище</label>
        <input
          v-model="form.last_name"
          type="text"
          class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          placeholder="Петренко"
        />
        <span v-if="form.errors.last_name" class="text-xs text-red-400 mt-1 block">{{ form.errors.last_name }}</span>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1">Телефон</label>
        <input
          v-model="form.phone"
          type="tel"
          class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          placeholder="+38 (063) 000-00-00"
        />
        <span v-if="form.errors.phone" class="text-xs text-red-400 mt-1 block">{{ form.errors.phone }}</span>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1">Email (необов'язково)</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          placeholder="example@gmail.com"
        />
        <span v-if="form.errors.email" class="text-xs text-red-400 mt-1 block">{{ form.errors.email }}</span>
      </div>
    </div>
  </div>
</template>
