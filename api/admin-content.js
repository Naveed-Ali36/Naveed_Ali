import { getSupabaseAdmin, verifyAdmin, sendJson } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'PUT, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') return res.status(200).end();

  if (req.method !== 'PUT') {
    return sendJson(res, 405, { success: false, message: 'Method not allowed' });
  }

  try {
    const user = await verifyAdmin(req);
    if (!user) return sendJson(res, 401, { success: false, message: 'Unauthorized' });

    let body = req.body;
    if (typeof body === 'string') body = JSON.parse(body);
    const content = body.content;
    if (!content) return sendJson(res, 422, { success: false, message: 'Missing content' });

    const supabase = getSupabaseAdmin();
    const { error } = await supabase
      .from('portfolio_content')
      .upsert({ id: 'main', content, updated_at: new Date().toISOString() });

    if (error) throw error;
    return sendJson(res, 200, { success: true, message: 'Content saved' });
  } catch (err) {
    return sendJson(res, 500, { success: false, message: err.message || 'Server error' });
  }
}
