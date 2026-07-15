<script setup>
import { computed } from 'vue';

const props = defineProps({
  hoverable: {
    type: Boolean,
    default: true,
  },
  bg: {
    type: String,
    default: 'white',
    validator: (value) => ['white', 'slate'].includes(value),
  },
});

const bgClasses = computed(() => {
  return props.bg === 'slate' ? 'bg-brand-slate' : 'bg-white';
});

const hoverClasses = computed(() => {
  return props.hoverable
    ? 'hover:-translate-y-1 hover:shadow-lg hover:shadow-brand-sage/5 transition-all duration-300'
    : '';
});
</script>

<template>
  <div
    :class="[
      bgClasses,
      hoverClasses,
      'relative flex flex-col rounded overflow-hidden border border-brand-moss/10 h-full group'
    ]"
  >
    <!-- Accent Top Line -->
    <div class="h-1 w-full bg-transparent group-hover:bg-brand-gold transition-colors duration-300"></div>

    <!-- Image Slot (if any) -->
    <div v-if="$slots.image" class="relative overflow-hidden w-full">
      <slot name="image" />
    </div>

    <!-- Body -->
    <div class="flex-1 p-6 flex flex-col">
      <!-- Category/Tag Slot -->
      <div v-if="$slots.tag" class="mb-3">
        <slot name="tag" />
      </div>

      <!-- Title Slot -->
      <div v-if="$slots.title" class="mb-2">
        <slot name="title" />
      </div>

      <!-- Content/Description Slot -->
      <div class="flex-1 text-brand-navy/80 text-sm leading-relaxed mb-4">
        <slot />
      </div>

      <!-- Footer/Action Slot -->
      <div v-if="$slots.footer" class="mt-auto pt-4 border-t border-brand-moss/10 flex items-center justify-between">
        <slot name="footer" />
      </div>
    </div>

    <!-- Interactive Gold Border Outline on Hover -->
    <div
      v-if="hoverable"
      class="absolute inset-0 border border-brand-gold/0 group-hover:border-brand-gold/40 rounded transition-colors duration-300 pointer-events-none"
    ></div>
  </div>
</template>
