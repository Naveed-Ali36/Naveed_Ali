import { getSupabaseAdmin, sendJson } from '../../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') return res.status(200).end();

  if (req.method !== 'GET') {
    return sendJson(res, 405, { success: false, message: 'Method not allowed' });
  }

  try {
    const supabase = getSupabaseAdmin();
    const { data, error } = await supabase
      .from('portfolio_content')
      .select('content')
      .eq('id', 'main')
      .maybeSingle();

    if (error) throw error;

    if (!data?.content || Object.keys(data.content).length === 0) {
      return sendJson(res, 200, {
        success: true,
        content: null,
        fallback: true,
        message: 'No content in database yet. Run supabase/schema.sql and seed script.'
      });
    }

    return sendJson(res, 200, { success: true, content: data.content });
  } catch (err) {
    return sendJson(res, 500, { success: false, message: err.message || 'Server error' });
  }
}
