import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent } from '@ionic/angular/standalone';
import { SidebarComponent } from '../../../components/sidebar/sidebar.component';
import { CashShiftService } from '../../../services/api/cash-shift.service';
import { CashShiftSummary, ClosedCashShiftSummary } from '../../../types/cash-shift.model';

@Component({
  selector: 'app-cash-shifts',
  standalone: true,
  imports: [IonContent, CommonModule, FormsModule, SidebarComponent],
  templateUrl: './cash-shifts.page.html',
  styleUrls: ['./cash-shifts.page.scss'],
})
export class CashShiftsPage implements OnInit {
  private cashShiftService = inject(CashShiftService);

  currentShift: CashShiftSummary | null = null;
  lastClosedShift: ClosedCashShiftSummary | null = null;
  loading = false;

  openingCash = 0;
  openingNotes = '';
  countedCash = 0;
  closingNotes = '';

  ngOnInit(): void {
    this.loadCurrentShift();
  }

  loadCurrentShift() {
    this.loading = true;
    this.cashShiftService.getCurrent().subscribe({
      next: (shift) => {
        this.currentShift = shift;
        this.loading = false;
      },
      error: () => {
        this.currentShift = null;
        this.loading = false;
      },
    });
  }

  openShift() {
    this.cashShiftService.open(this.openingCash, this.openingNotes || undefined).subscribe({
      next: (shift) => {
        this.currentShift = shift;
        this.openingCash = 0;
        this.openingNotes = '';
      },
    });
  }

  closeShift() {
    if (!this.currentShift) {
      return;
    }

    this.cashShiftService.close(this.currentShift.uuid, this.countedCash, this.closingNotes || undefined).subscribe({
      next: (shift) => {
        this.lastClosedShift = shift;
        this.currentShift = null;
        this.countedCash = 0;
        this.closingNotes = '';
      },
    });
  }

  formatCurrency(cents: number): string {
    return (cents / 100).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
  }

  formatDate(value: string): string {
    return new Date(value).toLocaleString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }
}