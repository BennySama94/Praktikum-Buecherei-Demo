<script lang="ts">
    import { goto } from '$app/navigation';
    import { get } from 'svelte/store';
    import { authStore } from '$lib/auth.js';
    import { onMount } from 'svelte';

    onMount(() => {
        const { token } = get(authStore);
        goto(token ? '/dashboard' : '/login');
    });
</script>