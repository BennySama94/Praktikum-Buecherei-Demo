import { writable } from 'svelte/store';
import { browser } from '$app/environment';

export interface AuthUser {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    role: 'member' | 'librarian';
}

export interface AuthState {
    token: string | null;
    user: AuthUser | null;
}

const stored = browser
    ? (JSON.parse(localStorage.getItem('auth') ?? 'null') as AuthState | null)
    : null;

export const authStore = writable<AuthState>(stored ?? { token: null, user: null });

authStore.subscribe((value) => {
    if (browser) {
        if (value.token) {
            localStorage.setItem('auth', JSON.stringify(value));
        } else {
            localStorage.removeItem('auth');
        }
    }
});

export function setAuth(token: string, user: AuthUser): void {
    authStore.set({ token, user });
}

export function clearAuth(): void {
    authStore.set({ token: null, user: null });
}
