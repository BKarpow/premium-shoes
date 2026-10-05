<script setup>
defineProps({
    modelValue: [Number, String, null],
    categories: {
        type: Array,
        required: true,
    },
    error: String,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label class="block text-sm mb-1 text-slate-300">Категорія товару</label>
        <select
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-amber-500 transition"
            required
        >
            <option value="" disabled>Оберіть категорію</option>
            <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
                :class="{ 'font-bold text-amber-400': category.is_parent }"
            >
                {{ category.name }}
            </option>
        </select>

        <!-- Виведення помилки валідації -->
        <div v-if="error" class="text-red-400 text-xs mt-1">{{ error }}</div>
    </div>
</template>
