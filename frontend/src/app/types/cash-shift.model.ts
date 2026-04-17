export interface CashShiftSummary {
  uuid: string;
  status: 'open' | 'closed';
  opening_cash: number;
  cash_total: number;
  card_total: number;
  bizum_total: number;
  refund_total: number;
  expected_cash: number;
  notes?: string | null;
  opened_at: string;
}

export interface ClosedCashShiftSummary extends CashShiftSummary {
  counted_cash: number;
  cash_difference: number;
  closed_at: string;
}