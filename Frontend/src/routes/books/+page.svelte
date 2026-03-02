<script lang="ts">
    import { onMount } from 'svelte';
    import { api } from '$lib/api.js';
    import { authStore, type AuthUser } from '$lib/auth.js';
    import { goto } from '$app/navigation';
    import { get } from 'svelte/store';

    interface Book {
        id: number;
        title: string;
        author: string;
        isbn: string;
        genre: string;
        year: number;
        available_copies: number;
    }

    interface BooksResponse {
        data: Book[];
    }

    let editingId: number | null = null;

    let user: AuthUser | null = null;
    let books: Book[] = [];
    let loading = true;
    let error = '';

    let showForm = false;
    let form = { title: '', author: '', isbn: '', genre: '', year: new Date().getFullYear(), total_copies: 1,  };
    let formError = '';
    let formLoading = false;

    onMount(async () => {
        const auth = get(authStore);
        if (!auth.token) { goto('/login'); return; }
        user = auth.user;
        await loadBooks();
    });

    async function loadBooks(): Promise<void> {
        loading = true;
        try {
            const res = await api.get<BooksResponse>('/books');
            books = res.data;
        } catch {
            error = 'Bücher konnten nicht geladen werden.';
        } finally {
            loading = false;
        }
    }

    async function borrow(bookId: number): Promise<void> {
        try {
            await api.post('/loans', { book_id: bookId });
            await loadBooks();
        } catch (err: unknown) {
            const e = err as { message?: string };
            error = e.message ?? 'Ausleihe fehlgeschlagen.';
        }
    }

    async function saveEdit(book: Book) {
        await api.put(`/books/${book.id}`, {
            title: book.title,
            author: book.author,
            genre: book.genre,
            year: book.year,
            available_copies: book.available_copies, 
            isbn: book.isbn,
            total_copies: book.available_copies,
        });
        editingId = null;
        await loadBooks();
    }

    async function deleteBook(bookId: number): Promise<void> {
        if (!confirm('Buch wirklich löschen?')) return;
        try {
            await api.delete(`/books/${bookId}`);
            books = books.filter(b => b.id !== bookId);
        } catch (err: unknown) {
            const e = err as { message?: string };
            error = e.message ?? 'Löschen fehlgeschlagen.';
        }
    }

    async function createBook(): Promise<void> {
        formError = '';
        formLoading = true;
        try {
            await api.post('/books', { ...form });
            showForm = false;
            form = { title: '', author: '', isbn: '', genre: '', year: new Date().getFullYear(), total_copies: 1 };
            await loadBooks();
        } catch (err: unknown) {
            const e = err as { message?: string };
            formError = e.message ?? 'Erstellen fehlgeschlagen.';
        } finally {
            formLoading = false;
        }
    }
</script>

<div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="/dashboard" class="text-sm text-blue-600 hover:underline">← Dashboard</a>
                <h1 class="text-2xl font-bold mt-1">Bücher</h1>
            </div>
            {#if user?.role === 'librarian'}
                <button
                    on:click={() => showForm = !showForm}
                    class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700"
                >
                    {showForm ? 'Abbrechen' : '+ Neues Buch'}
                </button>
            {/if}
        </div>

        {#if showForm}
            <div class="bg-white rounded shadow p-6 mb-6">
                <h2 class="font-semibold mb-4">Neues Buch anlegen</h2>
                {#if formError}
                    <p class="text-red-500 text-sm mb-3">{formError}</p>
                {/if}
                <form on:submit|preventDefault={createBook} class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1" for="title">Titel</label>
                        <input
                            id="title"
                            bind:value={form.title}
                            required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="author">Autor</label>
                        <input
                            id="author"
                            bind:value={form.author}
                            required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="isbn">ISBN</label>
                        <input
                            id="isbn"
                            bind:value={form.isbn}
                            required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="genre">Genre</label>
                        <input
                            id="genre"
                            bind:value={form.genre}
                            required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="year">Jahr</label>
                        <input
                            id="year"
                            type="number"
                            bind:value={form.year}
                            required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="copies">Exemplare</label>
                        <input
                            id="copies"
                            type="number"
                            min="1"
                            bind:value={form.total_copies}
                            required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <button
                            type="submit"
                            disabled={formLoading}
                            class="bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700 disabled:opacity-50"
                        >
                            {formLoading ? 'Speichern...' : 'Speichern'}
                        </button>
                    </div>
                </form>
            </div>
        {/if}

        {#if error}
            <p class="text-red-500 text-sm mb-4">{error}</p>
        {/if}

        {#if loading}
            <p class="text-gray-500 text-sm">Lade Bücher...</p>
        {:else if books.length === 0}
            <p class="text-gray-500 text-sm">Keine Bücher vorhanden.</p>
        {:else}
            <div class="space-y-3">
                {#each books as book (book.id)}
                    <div class="bg-white rounded shadow p-4 flex justify-between items-start">
                        <div>
                            <p class="font-medium">{book.title}</p>
                            <p class="text-sm text-gray-500">{book.author} · {book.genre} · {book.year}</p>
                            <p class="text-xs text-gray-400 mt-1">ISBN: {book.isbn}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 ml-4">
                            <span class="text-xs {book.available_copies > 0 ? 'text-green-600' : 'text-red-500'}">
                                {book.available_copies > 0 ? `${book.available_copies} verfügbar` : 'Nicht verfügbar'}
                            </span>
                            {#if user?.role === 'member' && book.available_copies > 0}
                                <button
                                    on:click={() => borrow(book.id)}
                                    class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                                >
                                    Ausleihen
                                </button>
                            {/if}

                            {#if user?.role === 'librarian'}
                                <button
                                    class="text-blue-600 hover:underline text-sm"
                                    on:click={() => editingId = book.id}
                                >
                                    Bearbeiten
                                </button>

                                <button
                                    on:click={() => deleteBook(book.id)}
                                    class="text-xs text-red-500 hover:underline"
                                >
                                    Löschen
                                </button>
                            {/if}
                        </div>
                    </div>

                    {#if editingId === book.id}
                        <div class="bg-gray-50 rounded shadow p-4 mt-2">
                            <form on:submit|preventDefault={() => saveEdit(book)} class="mt-2 space-y-2">
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="edit-title-{book.id}">Buchtitel</label>
                                    <input
                                        id="edit-title-{book.id}"
                                        bind:value={book.title}
                                        class="border rounded px-2 py-1 w-full text-sm"
                                        placeholder="Titel"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="edit-author-{book.id}">Autor</label>
                                    <input
                                        id="edit-author-{book.id}"
                                        bind:value={book.author}
                                        class="border rounded px-2 py-1 w-full text-sm"
                                        placeholder="Autor"
                                    />
                                </div>
                              <div>
                                    <label class="block text-sm font-medium mb-1" for="edit-isbn-{book.id}">ISBN</label>
                                    <input
                                        id="edit-isbn-{book.id}"
                                        bind:value={book.isbn}
                                        class="border rounded px-2 py-1 w-full text-sm"
                                        placeholder="ISBN"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="edit-genre-{book.id}">Genre</label>
                                    <input
                                        id="edit-genre-{book.id}"
                                        bind:value={book.genre}
                                        class="border rounded px-2 py-1 w-full text-sm"
                                        placeholder="Genre"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="edit-copies-{book.id}">Verfügbare Exemplare</label>
                                    <input
                                        id="edit-copies-{book.id}"
                                        bind:value={book.available_copies}
                                        type="number"
                                        class="border rounded px-2 py-1 w-full text-sm"
                                        placeholder="Verfügbare Exemplare"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="edit-year-{book.id}">Erscheinungsjahr</label>
                                    <input
                                        id="edit-year-{book.id}"
                                        bind:value={book.year}
                                        type="number"
                                        class="border rounded px-2 py-1 w-full text-sm"
                                        placeholder="Jahr"
                                    />
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Speichern</button>
                                    <button type="button" on:click={() => editingId = null} class="text-gray-500 text-sm">Abbrechen</button>
                                </div>
                            </form>
                        </div>
                    {/if}
                {/each}
            </div>
        {/if}
    </div>
</div>
