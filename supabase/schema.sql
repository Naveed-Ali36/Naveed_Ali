-- Run this in Supabase Dashboard → SQL Editor

create table if not exists portfolio_content (
  id text primary key default 'main',
  content jsonb not null,
  updated_at timestamptz default now()
);

create table if not exists messages (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  email text not null,
  project_type text default '',
  message text not null,
  read boolean default false,
  created_at timestamptz default now()
);

create table if not exists analytics (
  visit_date date primary key default (timezone('utc', now()))::date,
  visits integer not null default 0
);

alter table portfolio_content enable row level security;
alter table messages enable row level security;
alter table analytics enable row level security;

drop policy if exists "public read content" on portfolio_content;
create policy "public read content" on portfolio_content
  for select using (true);

drop policy if exists "auth manage content" on portfolio_content;
create policy "auth manage content" on portfolio_content
  for all using (auth.role() = 'authenticated');

drop policy if exists "public insert messages" on messages;
create policy "public insert messages" on messages
  for insert with check (true);

drop policy if exists "auth read messages" on messages;
create policy "auth read messages" on messages
  for select using (auth.role() = 'authenticated');

drop policy if exists "auth update messages" on messages;
create policy "auth update messages" on messages
  for update using (auth.role() = 'authenticated');

drop policy if exists "auth delete messages" on messages;
create policy "auth delete messages" on messages
  for delete using (auth.role() = 'authenticated');

-- Seed default content (run once)
insert into portfolio_content (id, content)
values ('main', '{}'::jsonb)
on conflict (id) do nothing;

-- After running, use Supabase Table Editor to paste JSON from data/content.default.json
-- OR run: node scripts/seed-supabase.js (after setting env vars)
