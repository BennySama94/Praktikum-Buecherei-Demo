<script lang="ts">
    import { onMount } from 'svelte';
    import { authStore, clearAuth, type AuthUser } from '$lib/auth.js';
    import { goto } from '$app/navigation';
    import { get } from 'svelte/store';

    let user: AuthUser | null = null;

    onMount(() => {
        const auth = get(authStore);
        if (!auth.token) { goto('/login'); return; }
        user = auth.user;
    });

    function logout(): void {
        clearAuth();
        goto('/login');
    }
</script>

{#if user}
<div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Bücherei</h1>
        </div>

        <p class="text-gray-600 mb-6">
            Willkommen, <span class="font-medium">{user.first_name} {user.last_name}</span>
            <span class="ml-2 text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">
                {user.role === 'librarian' ? 'Bibliothekar' : 'Mitglied'}
            </span>
        </p>

        <nav class="space-y-2">
            <a href="/books"
               class="block px-4 py-3 rounded border hover:bg-gray-50 text-sm font-medium">
                📚 Bücher durchsuchen
            </a>
            <a href="/loans"
               class="block px-4 py-3 rounded border hover:bg-gray-50 text-sm font-medium">
                {user.role === 'librarian' ? '📋 Alle Ausleihen' : '📋 Meine Ausleihen'}
            </a>
        </nav>
    </div>
</div>
{/if}
