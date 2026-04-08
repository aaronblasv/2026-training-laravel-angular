import { Injectable, Injector } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { BaseApiService } from './base-api.service';

@Injectable({
  providedIn: 'root',
})
export class AuthService extends BaseApiService {

  constructor(injector: Injector) {
    super(injector);
  }

  login(email: string, password: string): Observable<any> {
    return this.httpCall('/auth/login', { email, password }, 'post').pipe(
      tap((response: any) => {
        if (response?.token) {
          localStorage.setItem('token', response.token);
        }
        if (response?.role) {
          localStorage.setItem('role', response.role);
        }
      })
    );
  }

  getRole(): string | null {
    return localStorage.getItem('role');
  }

  logout(): Observable<any> {
    return this.httpCall('/auth/logout', null, 'post').pipe(
      tap(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
      })
    );
  }

  me(): Observable<any> {
    return this.httpCall('/auth/me', null, 'get');
  }

  isAuthenticated(): boolean {
    return !!localStorage.getItem('token');
  }

  register(name: string, email: string, password: string, passwordConfirmation: string): Observable<any> {
    return this.httpCall('/users', { name, email, password, password_confirmation: passwordConfirmation }, 'post');
    }
}