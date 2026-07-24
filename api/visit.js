import { getSupabaseAdmin, sendJson } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');

  if (req.method === 'OPTIONS') return res.status(200).end();
  if (req.method !== 'POST') return sendJson(res, 405, { success: false });

  try {
    const supabase = getSupabaseAdmin();
    const today = new Date().toISOString().slice(0, 10);

    const { data: existing } = await supabase
      .from('analytics')
      .select('visits')
      .eq('visit_date', today)
      .maybeSingle();

    const visits = (existing?.visits || 0) + 1;

    const { error } = await supabase
      .from('analytics')
      .upsert({ visit_date: today, visits });

    if (error) throw error;
    return sendJson(res, 200, { success: true });
  } catch {
    return sendJson(res, 200, { success: true });
  }
}
