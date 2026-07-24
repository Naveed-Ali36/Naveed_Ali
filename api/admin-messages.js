import { getSupabaseAdmin, verifyAdmin, sendJson } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, PATCH, DELETE, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') return res.status(200).end();

  try {
    const user = await verifyAdmin(req);
    if (!user) return sendJson(res, 401, { success: false, message: 'Unauthorized' });

    const supabase = getSupabaseAdmin();

    if (req.method === 'GET') {
      const { data, error } = await supabase
        .from('messages')
        .select('*')
        .order('created_at', { ascending: false });
      if (error) throw error;
      return sendJson(res, 200, { success: true, messages: data });
    }

    let body = req.body;
    if (typeof body === 'string') body = JSON.parse(body || '{}');

    if (req.method === 'PATCH') {
      const { id, read } = body;
      const { error } = await supabase.from('messages').update({ read: !!read }).eq('id', id);
      if (error) throw error;
      return sendJson(res, 200, { success: true });
    }

    if (req.method === 'DELETE') {
      const { id } = body;
      const { error } = await supabase.from('messages').delete().eq('id', id);
      if (error) throw error;
      return sendJson(res, 200, { success: true });
    }

    return sendJson(res, 405, { success: false, message: 'Method not allowed' });
  } catch (err) {
    return sendJson(res, 500, { success: false, message: err.message || 'Server error' });
  }
}
