<script setup>
import { computed } from 'vue';

const props = defineProps({
  bg: {
    type: String,
    default: 'transparent',
    validator: (value) => ['transparent', 'stone', 'slate', 'teal'].includes(value),
  },
  compact: {
    type: Boolean,
    default: false,
  },
});

const bgClasses = computed(() => {
  switch (props.bg) {
    case 'stone':
      return 'bg-brand-stone text-brand-navy';
    case 'slate':
      return 'bg-brand-slate text-brand-navy';
    case 'teal':
      return 'bg-brand-teal text-brand-stone';
    case 'transparent':
    default:
      return 'bg-transparent text-brand-navy';
  }
});

const paddingClasses = computed(() => {
  return props.compact ? 'py-8 md:py-12' : 'py-16 md:py-24';
});
</script>

<template>
  <section :class="[bgClasses, paddingClasses, 'w-full transition-colors duration-300']">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <slot />
    </div>
  </section>
</template>
