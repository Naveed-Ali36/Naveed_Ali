import { getSupabaseAdmin, sendJson } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') return res.status(200).end();

  if (req.method !== 'POST') {
    return sendJson(res, 405, { success: false, message: 'Method not allowed' });
  }

  try {
    let body = req.body;
    if (typeof body === 'string') body = JSON.parse(body);

    const name = (body.name || '').trim();
    const email = (body.email || '').trim();
    const message = (body.message || '').trim();
    const projectType = (body.project_type || '').trim();

    if (!name || !email || !message) {
      return sendJson(res, 422, { success: false, message: 'Please fill all required fields.' });
    }

    const supabase = getSupabaseAdmin();
    const { error } = await supabase.from('messages').insert({
      name,
      email,
      project_type: projectType,
      message
    });

    if (error) throw error;
    return sendJson(res, 200, { success: true, message: 'Message sent successfully!' });
  } catch (err) {
    return sendJson(res, 500, { success: false, message: err.message || 'Server error' });
  }
}
