import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class FamilyService {
  private apiUrl = `${environment.apiUrl}/families`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<any[]> {
    return this.http.get<any[]>(this.apiUrl);
  }

  getAllTpv(): Observable<any[]> {
    return this.http.get<any[]>(`${environment.apiUrl}/tpv/families`);
  }

  create(name: string): Observable<any> {
    return this.http.post(this.apiUrl, { name, active: true });
  }

  update(uuid: string, name: string): Observable<any> {
    return this.http.put(`${this.apiUrl}/${uuid}`, { name });
  }

  activate(uuid: string): Observable<any> {
    return this.http.patch(`${this.apiUrl}/${uuid}/activate`, {});
    }

    deactivate(uuid: string): Observable<any> {
    return this.http.patch(`${this.apiUrl}/${uuid}/deactivate`, {});
    }

  delete(uuid: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${uuid}`);
  }
}