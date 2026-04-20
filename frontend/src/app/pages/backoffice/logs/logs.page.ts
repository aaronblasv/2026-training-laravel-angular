import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent } from '@ionic/angular/standalone';
import { SidebarComponent } from '../../../components/sidebar/sidebar.component';
import { LogService } from '../../../services/api/log.service';
import { UserService } from '../../../services/api/user.service';
import { Log } from '../../../types/log.model';
import { User } from '../../../types/user.model';

@Component({
  selector: 'app-logs',
  templateUrl: './logs.page.html',
  styleUrls: ['./logs.page.scss'],
  standalone: true,
  imports: [IonContent, CommonModule, FormsModule, SidebarComponent]
})
export class LogsPage implements OnInit {

  private logService = inject(LogService);
  private userService = inject(UserService);

  logs: Log[] = [];
  users: User[] = [];
  total = 0;
  loading = false;
  filters = {
    action: '',
    user_id: '',
  };
  readonly actionLabels: Record<string, string> = {
    'cash_shift.closed': 'Caja cerrada',
    'cash_shift.opened': 'Caja abierta',
    'invoice.generated': 'Factura generada',
    'order.cancelled': 'Pedido cancelado',
    'order.closed': 'Pedido cerrado',
    'order.discount.updated': 'Descuento de pedido actualizado',
    'order.opened': 'Pedido abierto',
    'order.transferred': 'Pedido transferido',
    'payment.registered': 'Pago registrado',
  };

  ngOnInit() {
    this.loadUsers();
    this.loadLogs();
  }

  loadLogs() {
    this.loading = true;
    this.logService.getAll({
      action: this.filters.action || undefined,
      user_id: this.filters.user_id || undefined,
    }).subscribe({
      next: (data) => {
        this.logs = data.logs ?? data;
        this.total = data.total ?? this.logs.length;
        this.loading = false;
      },
      error: (err: any) => {
        this.loading = false;
        console.error(err);
      }
    });
  }

  loadUsers() {
    this.userService.getAll().subscribe({
      next: (users) => {
        this.users = users.filter(user => user.active).sort((left, right) => left.name.localeCompare(right.name, 'es'));
      },
      error: (err) => console.error(err),
    });
  }

  get actionOptions(): Array<{ value: string; label: string }> {
    const actions = new Set<string>(Object.keys(this.actionLabels));

    this.logs.forEach(log => {
      if (log.action) {
        actions.add(log.action);
      }
    });

    return Array.from(actions)
      .sort((left, right) => this.getActionLabel(left).localeCompare(this.getActionLabel(right), 'es'))
      .map(action => ({ value: action, label: this.getActionLabel(action) }));
  }

  onFiltersChanged() {
    this.loadLogs();
  }

  clearFilters() {
    this.filters = {
      action: '',
      user_id: '',
    };
    this.loadLogs();
  }

  getActionLabel(action: string): string {
    return this.actionLabels[action] ?? action;
  }

  getActionClass(action: string): string {
    if (action.includes('cash_shift')) return 'badge-amber';
    if (action.includes('opened') || action.includes('generated')) return 'badge-green';
    if (action.includes('closed') || action.includes('registered')) return 'badge-blue';
    if (action.includes('cancelled')) return 'badge-red';
    return 'badge-gray';
  }
}
