import { getSupabaseAdmin, verifyAdmin, sendJson } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Authorization');

  if (req.method === 'OPTIONS') return res.status(200).end();

  try {
    const user = await verifyAdmin(req);
    if (!user) return sendJson(res, 401, { success: false, message: 'Unauthorized' });

    const supabase = getSupabaseAdmin();

    const [contentRes, messagesRes, analyticsRes] = await Promise.all([
      supabase.from('portfolio_content').select('content').eq('id', 'main').maybeSingle(),
      supabase.from('messages').select('id, read'),
      supabase.from('analytics').select('*').order('visit_date', { ascending: false }).limit(14)
    ]);

    const content = contentRes.data?.content || {};
    const messages = messagesRes.data || [];
    const analytics = analyticsRes.data || [];

    const unread = messages.filter(m => !m.read).length;
    const totalVisits = analytics.reduce((sum, row) => sum + (row.visits || 0), 0);
    const today = new Date().toISOString().slice(0, 10);
    const todayVisits = analytics.find(r => r.visit_date === today)?.visits || 0;

    return sendJson(res, 200, {
      success: true,
      stats: {
        projects: (content.projects || []).filter(p => p.active !== false).length,
        experience: (content.experience || []).length,
        education: (content.education || []).length,
        messages: messages.length,
        unread_messages: unread,
        total_visits: totalVisits,
        today_visits: todayVisits,
        visits_chart: Object.fromEntries(analytics.map(r => [r.visit_date, r.visits]))
      }
    });
  } catch (err) {
    return sendJson(res, 500, { success: false, message: err.message || 'Server error' });
  }
}
