import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { IonicModule } from '@ionic/angular';
import { forkJoin } from 'rxjs';
import { ZoneService } from '../../../services/api/zone.service';
import { TableService } from '../../../services/api/table.service';
import { OrderService } from '../../../services/api/order.service';
import { PinModalComponent } from '../../../components/pin-modal/pin-modal.component';
import { DinersModalComponent } from '../../../components/diners-modal/diners-modal.component';
import { WaiterModalComponent } from '../../../components/waiter-modal/waiter-modal.component';

@Component({
  selector: 'app-floor',
  standalone: true,
  imports: [CommonModule, IonicModule, PinModalComponent, DinersModalComponent, WaiterModalComponent],
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

  showWaiterModal = false;
  showPinModal = false;
  showDinersModal = false;
  selectedTable: any = null;
  selectedWaiter: any = null;
  validatedUser: any = null;

  ngOnInit() {
    this.loadData();
  }

  ionViewWillEnter() {
    // Recargar datos cada vez que la página está a punto de entrar
    // Esto se ejecuta cuando regresas de una navegación
    console.log('ionViewWillEnter triggered - reloading data');
    this.loadData();
  }

  ionViewDidLoad() {
    // Adicional: también recargar cuando la vista se carga
    console.log('ionViewDidLoad triggered - reloading data');
    this.loadData();
  }

  loadData() {
    console.log('FloorPage: Starting to load data...');
    forkJoin({
      zones: this.zoneService.getAllTpv(),
      tables: this.tableService.getAllTpv(),
      openOrders: this.orderService.getAllOpen(),
    }).subscribe({
      next: ({ zones, tables, openOrders }) => {
        console.log('FloorPage: Data loaded successfully');
        console.log('  - Zones:', zones.length);
        console.log('  - Tables:', tables.length);
        console.log('  - Open Orders:', openOrders.length, openOrders);
        
        this.zones = zones;
        this.tables = tables;
        this.openOrders = openOrders;
        
        // Log estado de cada mesa
        tables.forEach(table => {
          const isOccupied = this.isOccupied(table.uuid);
          console.log(`  Mesa ${table.name}: ${isOccupied ? 'OCUPADA' : 'LIBRE'}`);
        });
      },
      error: (err) => {
        console.error('FloorPage: Error loading data', err);
      },
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

  // Paso 1: click en mesa → mostrar modal de camareros
  onTableClick(table: any) {
    this.selectedTable = table;
    this.showWaiterModal = true;
  }

  // Paso 2: seleccionar camarero → mostrar PIN
  onWaiterSelected(waiter: any) {
    this.selectedWaiter = waiter;
    this.showWaiterModal = false;
    this.showPinModal = true;
  }

  onWaiterCancelled() {
    this.showWaiterModal = false;
    this.selectedTable = null;
    this.selectedWaiter = null;
  }

  // Paso 3: PIN validado → si mesa libre, comensales; si ocupada, navegar
  onPinValidated(user: any) {
    this.validatedUser = user;
    this.showPinModal = false;

    if (this.isOccupied(this.selectedTable.uuid)) {
      this.router.navigate(['/tpv/order', this.selectedTable.uuid], {
        state: { user: this.validatedUser },
      });
    } else {
      this.showDinersModal = true;
    }
  }

  onPinCancelled() {
    this.showPinModal = false;
    this.selectedTable = null;
    this.selectedWaiter = null;
    this.validatedUser = null;
  }

  // Paso 4: comensales confirmados → abrir order → navegar
  onDinersConfirmed(diners: number) {
    this.showDinersModal = false;
    this.orderService.openOrder(this.selectedTable.uuid, this.validatedUser.id, diners).subscribe({
      next: () => {
        this.router.navigate(['/tpv/order', this.selectedTable.uuid], {
          state: { user: this.validatedUser },
        });
      },
      error: (err: any) => console.error('Error opening order:', err),
    });
  }

  onDinersCancelled() {
    this.showDinersModal = false;
    this.selectedTable = null;
    this.selectedWaiter = null;
    this.validatedUser = null;
  }
}
