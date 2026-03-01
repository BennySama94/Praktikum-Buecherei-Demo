<script lang="ts">
    import { api } from '$lib/api.js';
    import { goto } from '$app/navigation';

    interface RegisterForm {
        first_name: string;
        last_name: string;
        email: string;
        password: string;
        password_confirmation: string;
        address: string;
        zip: string;
        town: string;
        phone: string;
    }

    let form: RegisterForm = {
        first_name: '', last_name: '',
        email: '', password: '', password_confirmation: '',
        address: '', zip: '', town: '', phone: '',
    };

    let errors: Partial<Record<keyof RegisterForm | 'general', string[]>> = {};
    let loading = false;

    async function handleSubmit(e: SubmitEvent): Promise<void> {
        e.preventDefault();
        errors = {};
        loading = true;

        try {
            await api.post('/auth/register', form);
            goto('/login');
        } catch (err: unknown) {
            const apiErr = err as { errors?: typeof errors; message?: string };
            errors = apiErr.errors ?? { general: [apiErr.message ?? 'Fehler'] };
        } finally {
            loading = false;
        }
    }
</script>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-10">
    <div class="bg-white p-8 rounded shadow w-full max-w-lg">
        <h1 class="text-2xl font-bold mb-6">Registrieren</h1>

        {#if errors.general}
            <p class="text-red-500 text-sm mb-4">{errors.general[0]}</p>
        {/if}

        <form on:submit={handleSubmit} class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1" for="first_name">Vorname</label>
                    <input id="first_name" type="text" bind:value={form.first_name} required
                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    {#if errors.first_name}<p class="text-red-500 text-xs mt-1">{errors.first_name[0]}</p>{/if}
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="last_name">Nachname</label>
                    <input id="last_name" type="text" bind:value={form.last_name} required
                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    {#if errors.last_name}<p class="text-red-500 text-xs mt-1">{errors.last_name[0]}</p>{/if}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="email">E-Mail</label>
                <input id="email" type="email" bind:value={form.email} required
                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                {#if errors.email}<p class="text-red-500 text-xs mt-1">{errors.email[0]}</p>{/if}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1" for="password">Passwort</label>
                    <input id="password" type="password" bind:value={form.password} required
                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    {#if errors.password}<p class="text-red-500 text-xs mt-1">{errors.password[0]}</p>{/if}
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="password_confirmation">Passwort bestätigen</label>
                    <input id="password_confirmation" type="password" bind:value={form.password_confirmation} required
                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="address">Adresse</label>
                <input id="address" type="text" bind:value={form.address} required
                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                {#if errors.address}<p class="text-red-500 text-xs mt-1">{errors.address[0]}</p>{/if}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1" for="zip">PLZ</label>
                    <input id="zip" type="text" bind:value={form.zip} required
                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    {#if errors.zip}<p class="text-red-500 text-xs mt-1">{errors.zip[0]}</p>{/if}
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="town">Ort</label>
                    <input id="town" type="text" bind:value={form.town} required
                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    {#if errors.town}<p class="text-red-500 text-xs mt-1">{errors.town[0]}</p>{/if}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="phone">Telefon</label>
                <input id="phone" type="text" bind:value={form.phone} required
                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                {#if errors.phone}<p class="text-red-500 text-xs mt-1">{errors.phone[0]}</p>{/if}
            </div>

            <button
                type="submit"
                disabled={loading}
                class="w-full bg-blue-600 text-white py-2 rounded text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
            >
                {loading ? 'Wird registriert...' : 'Registrieren'}
            </button>
        </form>

        <p class="text-sm mt-4 text-center">
            Bereits registriert? <a href="/login" class="text-blue-600 hover:underline">Anmelden</a>
        </p>
    </div>
</div>
