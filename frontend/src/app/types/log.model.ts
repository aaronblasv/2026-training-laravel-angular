export interface Log {
  uuid: string;
  user_id: string | null;
  user_name?: string | null;
  action: string;
  entity_type: string | null;
  entity_uuid: string | null;
  data?: Record<string, unknown> | null;
  ip_address: string | null;
  created_at: string;
}
