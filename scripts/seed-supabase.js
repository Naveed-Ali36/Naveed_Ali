import { readFileSync } from 'fs';
import { getSupabaseAdmin } from '../lib/supabase.js';

const content = JSON.parse(readFileSync(new URL('../data/content.default.json', import.meta.url), 'utf8'));

const supabase = getSupabaseAdmin();
const { error } = await supabase
  .from('portfolio_content')
  .upsert({ id: 'main', content, updated_at: new Date().toISOString() });

if (error) {
  console.error('Seed failed:', error.message);
  process.exit(1);
}

console.log('Portfolio content seeded successfully.');
