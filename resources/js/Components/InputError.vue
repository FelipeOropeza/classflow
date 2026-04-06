<script setup lang="ts">
defineProps<{
  message?: string | string[];
}>();
</script>

<template>
  <Transition name="error-fade">
    <p
      v-if="message && (Array.isArray(message) ? message.length > 0 : true)"
      class="input-error-msg"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      {{ Array.isArray(message) ? message[0] : message }}
    </p>
  </Transition>
</template>

<style scoped>
.input-error-msg {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  font-weight: 700;
  color: #ef4444;
  margin-top: 5px;
  margin-left: 4px;
}

.error-fade-enter-active,
.error-fade-leave-active {
  transition: all 0.2s ease;
}
.error-fade-enter-from,
.error-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
