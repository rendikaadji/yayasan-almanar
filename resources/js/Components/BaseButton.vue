<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'ghost'].includes(value),
  },
  href: {
    type: String,
    default: null,
  },
});

const isExternal = computed(() => {
  return props.href && (props.href.startsWith('http://') || props.href.startsWith('https://'));
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-brand-sage text-brand-stone hover:bg-brand-teal focus:ring-brand-gold border border-transparent';
    case 'secondary':
      return 'bg-brand-slate text-brand-teal hover:bg-brand-stone hover:text-brand-sage focus:ring-brand-gold border border-brand-moss/30';
    case 'ghost':
      return 'bg-transparent text-brand-sage hover:bg-brand-slate/40 hover:text-brand-teal focus:ring-brand-sage border border-transparent';
    default:
      return 'bg-brand-sage text-brand-stone hover:bg-brand-teal focus:ring-brand-gold';
  }
});

const baseClasses = 'inline-flex items-center justify-center px-6 py-2.5 rounded font-utility text-sm font-semibold tracking-wide uppercase transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-95 disabled:opacity-50 disabled:pointer-events-none cursor-pointer';
</script>

<template>
  <a
    v-if="href && isExternal"
    :href="href"
    :class="[baseClasses, variantClasses]"
    target="_blank"
    rel="noopener noreferrer"
  >
    <slot />
  </a>
  <Link
    v-else-if="href"
    :href="href"
    :class="[baseClasses, variantClasses]"
  >
    <slot />
  </Link>
  <button
    v-else
    :type="type"
    :class="[baseClasses, variantClasses]"
  >
    <slot />
  </button>
</template>
