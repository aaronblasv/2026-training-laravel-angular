import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonContent } from '@ionic/angular/standalone';
import { SidebarComponent } from '../../../components/sidebar/sidebar.component';
import { LogService } from '../../../services/api/log.service';

@Component({
  selector: 'app-logs',
  templateUrl: './logs.page.html',
  styleUrls: ['./logs.page.scss'],
  standalone: true,
  imports: [IonContent, CommonModule, SidebarComponent]
})
export class LogsPage implements OnInit {

  private logService = inject(LogService);

  logs: any[] = [];
  total = 0;

  ngOnInit() {
    this.loadLogs();
  }

  loadLogs() {
    this.logService.getAll().subscribe({
      next: (data: any) => {
        this.logs = data.logs ?? data;
        this.total = data.total ?? this.logs.length;
      },
      error: (err: any) => console.error(err)
    });
  }

  getActionLabel(action: string): string {
    const labels: { [key: string]: string } = {
      'order.opened': 'Pedido abierto',
      'order.closed': 'Pedido cerrado',
      'order.cancelled': 'Pedido cancelado',
      'payment.registered': 'Pago registrado',
      'invoice.generated': 'Factura generada',
    };
    return labels[action] ?? action;
  }

  getActionClass(action: string): string {
    if (action.includes('opened') || action.includes('generated')) return 'badge-green';
    if (action.includes('closed') || action.includes('registered')) return 'badge-blue';
    if (action.includes('cancelled')) return 'badge-red';
    return 'badge-gray';
  }
}
