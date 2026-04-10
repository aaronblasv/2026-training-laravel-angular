import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ZoneService {
  private apiUrl = `${environment.apiUrl}/zones`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<any[]> {
    return this.http.get<any[]>(this.apiUrl);
  }

  create(name: string): Observable<any> {
    return this.http.post(this.apiUrl, { name });
  }

  update(uuid: string, name: string): Observable<any> {
    return this.http.put(`${this.apiUrl}/${uuid}`, { name });
  }

  delete(uuid: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${uuid}`);
  }

  getAllTpv(): Observable<any[]> {
    return this.http.get<any[]>(`${environment.apiUrl}/tpv/zones`);
    }
}