import { createClient } from '@supabase/supabase-js';

export function getServerEnvError() {
  const missing = [];
  if (!process.env.SUPABASE_URL) missing.push('SUPABASE_URL');
  if (!process.env.SUPABASE_SERVICE_ROLE_KEY) missing.push('SUPABASE_SERVICE_ROLE_KEY');
  if (!missing.length) return null;
  return `Missing on Vercel: ${missing.join(', ')}. Add them in Project Settings → Environment Variables, then redeploy.`;
}

export function getSupabaseAdmin() {
  const envError = getServerEnvError();
  if (envError) throw new Error(envError);

  return createClient(process.env.SUPABASE_URL, process.env.SUPABASE_SERVICE_ROLE_KEY);
}

export function getSupabaseAnon() {
  const url = process.env.SUPABASE_URL;
  const key = process.env.SUPABASE_ANON_KEY;
  if (!url || !key) throw new Error('Missing Supabase anon credentials');
  return createClient(url, key);
}

export async function verifyAdmin(req) {
  const envError = getServerEnvError();
  if (envError) return { user: null, message: envError, status: 500 };

  const header = req.headers.authorization || req.headers.Authorization;
  if (!header?.startsWith('Bearer ')) {
    return { user: null, message: 'Unauthorized', status: 401 };
  }

  try {
    const supabase = getSupabaseAdmin();
    const { data, error } = await supabase.auth.getUser(header.slice(7));
    if (error || !data.user) {
      return { user: null, message: 'Session expired. Log out and log in again.', status: 401 };
    }
    return { user: data.user, message: null, status: 200 };
  } catch (err) {
    return { user: null, message: err.message || 'Auth check failed', status: 500 };
  }
}

export function sendJson(res, status, body) {
  res.statusCode = status;
  res.setHeader('Content-Type', 'application/json');
  res.end(JSON.stringify(body));
}
