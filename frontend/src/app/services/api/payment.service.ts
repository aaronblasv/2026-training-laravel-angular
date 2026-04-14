import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PaymentService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost:8000/api';

  registerPayment(orderUuid: string, amount: number, method: string, description?: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/orders/${orderUuid}/payments`, {
      amount,
      method,
      description: description || null,
    });
  }

  generateInvoice(orderUuid: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/orders/${orderUuid}/generate-invoice`, {});
  }
}
