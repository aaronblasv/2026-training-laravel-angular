import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { CashShiftSummary, ClosedCashShiftSummary } from '../../types/cash-shift.model';

@Injectable({ providedIn: 'root' })
export class CashShiftService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  getCurrent(): Observable<CashShiftSummary | null> {
    return this.http.get<CashShiftSummary | null>(`${this.apiUrl}/cash-shifts/current`);
  }

  open(openingCash: number, notes?: string): Observable<CashShiftSummary> {
    return this.http.post<CashShiftSummary>(`${this.apiUrl}/cash-shifts`, {
      opening_cash: openingCash,
      notes: notes ?? null,
    });
  }

  close(cashShiftUuid: string, countedCash: number, notes?: string): Observable<ClosedCashShiftSummary> {
    return this.http.post<ClosedCashShiftSummary>(`${this.apiUrl}/cash-shifts/${cashShiftUuid}/close`, {
      counted_cash: countedCash,
      notes: notes ?? null,
    });
  }
}