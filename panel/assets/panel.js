import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.49.1/+esm';

let supabaseClient = null;

async function loadConfig() {
  if (window.SUPABASE_CONFIG?.url && window.SUPABASE_CONFIG?.anonKey) {
    return window.SUPABASE_CONFIG;
  }

  try {
    const res = await fetch('/api/config');
    if (res.ok) {
      const cfg = await res.json();
      if (cfg.supabaseUrl && cfg.supabaseAnonKey) {
        return { url: cfg.supabaseUrl, anonKey: cfg.supabaseAnonKey };
      }
    }
  } catch {
    // fall through to error below
  }

  throw new Error('Supabase config missing. Check panel/assets/config.js or Vercel env variables.');
}

export async function getSupabase() {
  if (supabaseClient) return supabaseClient;

  const cfg = await loadConfig();
  supabaseClient = createClient(cfg.url, cfg.anonKey, {
    auth: {
      persistSession: true,
      autoRefreshToken: true,
      detectSessionInUrl: true,
      storage: window.localStorage
    }
  });

  return supabaseClient;
}

export async function getSession() {
  const supabase = await getSupabase();
  const { data, error } = await supabase.auth.getSession();
  if (error) throw error;
  return data.session;
}

export async function requireAuth() {
  try {
    const session = await getSession();
    if (!session) {
      window.location.href = 'login.html';
      return null;
    }
    return session;
  } catch (err) {
    console.error(err);
    window.location.href = 'login.html?error=config';
    return null;
  }
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
  const text = await res.text();

  let data;
  try {
    data = text ? JSON.parse(text) : {};
  } catch {
    throw new Error(text.slice(0, 120) || `Request failed (${res.status})`);
  }

  if (!res.ok && !data.message) {
    data.message = `Request failed (${res.status})`;
  }

  return data;
}

export async function logout() {
  const supabase = await getSupabase();
  await supabase.auth.signOut();
  window.location.href = 'login.html';
}

export function showAlert(el, type, message) {
  if (!el) return;
  el.className = `alert alert-${type}`;
  el.textContent = message;
}
