<script lang="ts">
    import { onMount } from 'svelte';
    import { api } from '$lib/api.js';
    import { authStore, type AuthUser } from '$lib/auth.js';
    import { goto } from '$app/navigation';
    import { get } from 'svelte/store';

    interface Loan {
        id: number;
        status: 'active' | 'returned' | 'overdue';
        due_date: string;
        returned_at: string | null;
        book: { id: number; title: string; author: string };
        user?: { id: number; first_name: string; last_name: string };
    }

    interface LoansResponse {
        data: Loan[];
    }

    let user: AuthUser | null = null;
    let loans: Loan[] = [];
    let loading = true;
    let error = '';

    onMount(async () => {
        const auth = get(authStore);
        if (!auth.token) { goto('/login'); return; }
        user = auth.user;
        await loadLoans();
    });

    async function loadLoans(): Promise<void> {
        loading = true;
        try {
            const res = await api.get<LoansResponse>('/loans');
            loans = res.data;
        } catch {
            error = 'Ausleihen konnten nicht geladen werden.';
        } finally {
            loading = false;
        }
    }

    async function returnLoan(loanId: number): Promise<void> {
        try {
            await api.patch(`/loans/${loanId}/return`);
            await loadLoans();
        } catch (err: unknown) {
            const e = err as { message?: string };
            error = e.message ?? 'Rückgabe fehlgeschlagen.';
        }
    }

    function statusLabel(status: Loan['status']): string {
        return { active: 'Aktiv', returned: 'Zurückgegeben', overdue: 'Überfällig' }[status];
    }

    function statusClass(status: Loan['status']): string {
        return {
            active: 'bg-blue-100 text-blue-700',
            returned: 'bg-gray-100 text-gray-500',
            overdue: 'bg-red-100 text-red-600',
        }[status];
    }
</script>

<div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="/dashboard" class="text-sm text-blue-600 hover:underline">← Dashboard</a>
            <h1 class="text-2xl font-bold mt-1">
                {user?.role === 'librarian' ? 'Alle Ausleihen' : 'Meine Ausleihen'}
            </h1>
        </div>

        {#if error}<p class="text-red-500 text-sm mb-4">{error}</p>{/if}

        {#if loading}
            <p class="text-gray-500 text-sm">Lade Ausleihen...</p>
        {:else if loans.length === 0}
            <p class="text-gray-500 text-sm">Keine Ausleihen vorhanden.</p>
        {:else}
            <div class="space-y-3">
                {#each loans as loan (loan.id)}
                <div class="bg-white rounded shadow p-4 flex justify-between items-start">
                    <div>
                        <p class="font-medium">{loan.book.title}</p>
                        <p class="text-sm text-gray-500">{loan.book.author}</p>
                        {#if user?.role === 'librarian' && loan.user}
                            <p class="text-xs text-gray-400 mt-1">
                                {loan.user.first_name} {loan.user.last_name}
                            </p>
                        {/if}
                        <p class="text-xs text-gray-400 mt-1">
                            Fällig: {new Date(loan.due_date).toLocaleDateString('de-DE')}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-2 ml-4">
                        <span class="text-xs px-2 py-0.5 rounded {statusClass(loan.status)}">
                            {statusLabel(loan.status)}
                        </span>
                        {#if loan.status === 'active'}
                            <button on:click={() => returnLoan(loan.id)}
                                    class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                Zurückgeben
                            </button>
                        {/if}
                    </div>
                </div>
                {/each}
            </div>
        {/if}
    </div>
</div>
