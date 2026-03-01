import { authStore } from './auth.js';
import { get } from 'svelte/store';

const BASE_URL = '/api/v1';

interface ApiError {
    status: number;
    errors: Record<string, string[]> | null;
    message: string;
}

async function request<T>(method: string, path: string, body: unknown = null): Promise<T> {
    const token = get(authStore).token;

    const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options: RequestInit = { method, headers };
    if (body) options.body = JSON.stringify(body);

    const res = await fetch(`${BASE_URL}${path}`, options);
    const data = await res.json();

    if (!res.ok) {
        throw {
            status: res.status,
            errors: data.errors ?? null,
            message: data.message ?? 'Fehler',
        } satisfies ApiError;
    }

    return data as T;
}

export const api = {
    get:    <T>(path: string)                        => request<T>('GET',    path),
    post:   <T>(path: string, body: unknown)         => request<T>('POST',   path, body),
    put:    <T>(path: string, body: unknown)         => request<T>('PUT',    path, body),
    patch:  <T>(path: string, body: unknown = null)  => request<T>('PATCH',  path, body),
    delete: <T>(path: string)                        => request<T>('DELETE', path),
};
