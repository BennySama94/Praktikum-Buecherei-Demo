<script lang="ts">
    import { api } from '$lib/api.js';
    import { setAuth, type AuthUser } from '$lib/auth.js';
    import { goto } from '$app/navigation';

    interface LoginResponse {
        token: string;
        user: AuthUser;
    }

    let email = '';
    let password = '';
    let error = '';
    let loading = false;

    async function handleSubmit(e: SubmitEvent): Promise<void> {
        e.preventDefault();
        error = '';
        loading = true;

        try {
            const data = await api.post<LoginResponse>('/auth/login', { email, password });
            setAuth(data.token, data.user);
            goto('/dashboard');
        } catch (err: unknown) {
            const apiErr = err as { message?: string };
            error = apiErr.message ?? 'Login fehlgeschlagen.';
        } finally {
            loading = false;
        }
    }
</script>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6">Anmelden</h1>

        {#if error}
            <p class="text-red-500 text-sm mb-4">{error}</p>
        {/if}

        <form on:submit={handleSubmit} class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="email">E-Mail</label>
                <input
                    id="email"
                    type="email"
                    bind:value={email}
                    required
                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password">Passwort</label>
                <input
                    id="password"
                    type="password"
                    bind:value={password}
                    required
                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <button
                type="submit"
                disabled={loading}
                class="w-full bg-blue-600 text-white py-2 rounded text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
            >
                {loading ? 'Wird angemeldet...' : 'Anmelden'}
            </button>
        </form>

        <p class="text-sm mt-4 text-center">
            Noch kein Konto? <a href="/register" class="text-blue-600 hover:underline">Registrieren</a>
        </p>
    </div>
</div>
