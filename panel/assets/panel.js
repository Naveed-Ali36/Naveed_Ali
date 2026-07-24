import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.49.1/+esm';

let supabaseClient = null;

export async function getSupabase() {
  if (supabaseClient) return supabaseClient;

  const res = await fetch('/api/config');
  const cfg = await res.json();

  if (!cfg.supabaseUrl || !cfg.supabaseAnonKey) {
    throw new Error('Supabase is not configured on Vercel. Add environment variables.');
  }

  supabaseClient = createClient(cfg.supabaseUrl, cfg.supabaseAnonKey);
  return supabaseClient;
}

export async function getSession() {
  const supabase = await getSupabase();
  const { data } = await supabase.auth.getSession();
  return data.session;
}

export async function requireAuth() {
  const session = await getSession();
  if (!session) {
    window.location.href = 'login.html';
    return null;
  }
  return session;
}

export async function apiFetch(url, options = {}) {
  const session = await getSession();
  const headers = {
    'Content-Type': 'application/json',
    ...(options.headers || {})
  };
  if (session?.access_token) {
    headers.Authorization = `Bearer ${session.access_token}`;
  }
  const res = await fetch(url, { ...options, headers });
  return res.json();
}

export async function logout() {
  const supabase = await getSupabase();
  await supabase.auth.signOut();
  window.location.href = 'login.html';
}
