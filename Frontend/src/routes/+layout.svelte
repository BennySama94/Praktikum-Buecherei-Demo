<script lang="ts">
    import { authStore, clearAuth, type AuthUser } from '$lib/auth.js';
    import { goto } from '$app/navigation';
    import { derived } from 'svelte/store';
    import '../app.css';

    const user = derived(authStore, ($auth) => $auth.user);
    const token = derived(authStore, ($auth) => $auth.token);

    function logout(): void {
        clearAuth();
        goto('/login');
    }
</script>

{#if $token}
    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="/dashboard" class="text-base font-semibold text-gray-800 hover:text-blue-600">
                📚 Bücherei
            </a>
            <a href="/books"
               class="text-sm text-gray-600 hover:text-blue-600">
                Bücher
            </a>
            <a href="/loans"
               class="text-sm text-gray-600 hover:text-blue-600">
                {$user?.role === 'librarian' ? 'Alle Ausleihen' : 'Meine Ausleihen'}
            </a>
        </div>

        <div class="flex items-center gap-4">
            {#if $user}
                <span class="text-sm text-gray-500">
                    {$user.first_name} {$user.last_name}
                    <span class="ml-1 text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded">
                        {$user.role === 'librarian' ? 'Bibliothekar' : 'Mitglied'}
                    </span>
                </span>
            {/if}
            <button on:click={logout}
                    class="text-sm text-red-500 hover:underline">
                Abmelden
            </button>
        </div>
    </nav>
{/if}

<main>
    <slot />
</main>
