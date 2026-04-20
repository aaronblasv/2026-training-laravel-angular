import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Log } from '../../types/log.model';

export interface LogFilters {
  action?: string;
  user_id?: string;
}

export interface LogsResponse {
  logs: Log[];
  total: number;
}

@Injectable({
  providedIn: 'root'
})
export class LogService {
  private apiUrl = `${environment.apiUrl}/logs`;

  constructor(private http: HttpClient) {}

  getAll(filters?: LogFilters): Observable<LogsResponse> {
    const params: Record<string, string> = {};

    if (filters?.action) {
      params['action'] = filters.action;
    }

    if (filters?.user_id) {
      params['user_id'] = filters.user_id;
    }

    return this.http.get<LogsResponse>(this.apiUrl, { params });
  }
}
