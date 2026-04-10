import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { IonicModule } from '@ionic/angular';
import { forkJoin } from 'rxjs';
import { ZoneService } from '../../../services/api/zone.service';
import { TableService } from '../../../services/api/table.service';
import { OrderService } from '../../../services/api/order.service';

@Component({
  selector: 'app-floor',
  standalone: true,
  imports: [CommonModule, IonicModule],
  templateUrl: './floor.page.html',
  styleUrls: ['./floor.page.scss'],
})
export class FloorPage implements OnInit {
  private router = inject(Router);
  private zoneService = inject(ZoneService);
  private tableService = inject(TableService);
  private orderService = inject(OrderService);

  zones: any[] = [];
  tables: any[] = [];
  openOrders: any[] = [];
  selectedZoneUuid: string | null = null;

  ngOnInit() {
    this.loadData();
  }

    loadData() {
    forkJoin({
        zones: this.zoneService.getAllTpv(),
        tables: this.tableService.getAllTpv(),
        openOrders: this.orderService.getAllOpen(),
    }).subscribe({
        next: ({ zones, tables, openOrders }) => {
        this.zones = zones;
        this.tables = tables;
        this.openOrders = openOrders;
        },
        error: (err) => console.error(err),
    });
    }

  get filteredTables() {
    if (!this.selectedZoneUuid) return this.tables;
    return this.tables.filter(t => t.zoneId === this.selectedZoneUuid);
  }

  selectZone(uuid: string | null) {
    this.selectedZoneUuid = uuid;
  }

  isOccupied(tableUuid: string): boolean {
    return this.openOrders.some(o => o.tableId === tableUuid);
  }

  getOrder(tableUuid: string) {
    return this.openOrders.find(o => o.tableId === tableUuid);
  }

  getZoneName(zoneId: string): string {
    return this.zones.find(z => z.uuid === zoneId)?.name ?? '';
  }

  onTableClick(table: any) {
    this.router.navigate(['/tpv/order', table.uuid]);
  }
}